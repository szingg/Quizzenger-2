<?php

namespace quizzenger\dispatching {
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\Settings as Settings;
	use \quizzenger\dispatching\UserEvent as UserEvent;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	/**
	 * This class accumulates the scores for individual users based on events
	 * that have been fired. The scores are automatically updated in the Database.
	**/
	class ScoreDispatcher {
		/**
		 * Holds an instance to the database connection.
		 * @var SqlHelper
		**/
		private $mysqli;

		/**
		 * Creates the object based on an existing database connection.
		 * @param mysqli $mysqli Existing database connection.
		**/
		public function __construct(SqlHelper $mysqli) {
			$this->mysqli = $mysqli;
		}

		/**
		 * Promotes the specified user to a moderator for the passed category
		 * if the score requirements are met.
		 * @param int $userId User ID.
		 * @param int $categoryId Category ID.
		**/
		public function promoteUserIfEligible($userId, $categoryId) {
			$threshold = (new Settings($this->mysqli->database()))->getSingle('q.scoring.moderator-threshold');
			$statement= $this->mysqli->database()->prepare('INSERT IGNORE INTO moderation (user_id, category_id)'
				. ' SELECT ?, ? FROM userscore WHERE (SELECT (SUM(producer_score)+SUM(consumer_score)) FROM userscore'
				. '     WHERE user_id=? AND category_id=? GROUP BY user_id) >= ?');

			$statement->bind_param('iiiii', $userId, $categoryId, $userId, $categoryId, $threshold);
			if(!$statement->execute())
				Log::info('Could not execute user promotion');
		}

		/**
		 * Dispatches a bonus score to the event-specified user.
		 * @param UserEvent $event Event to be handled.
		 * @param int $producerScore Producer score to be added as bonus score.
		 * @param int $consumerScore Consumer score to be added as bonus score.
		**/
		private function dispatchBonusScore(UserEvent $event, $producerScore, $consumerScore) {
			$statement = $this->mysqli->database()->prepare('UPDATE user'
				. ' SET bonus_score=bonus_score+?'
				. ' WHERE id=?');

			$userId = $event->user();
			$bonusScore = $producerScore + $consumerScore;
			$statement->bind_param('ii', $bonusScore, $userId);

			if($statement->execute()){
				Log::info("Added bonus score ($producerScore, $consumerScore) to user $userId.");
				MessageQueue::pushPersistent($userId, 'q.message.score-received', ['score'=>$bonusScore]);
			}
			else{
				Log::error("Could not grant bonus score for user $userId.");
			}
		}

		/**
		 * Dispatches a score to the event-specified user.
		 * @param UserEvent $event Event to be handled.
		 * @param int $producerScore Producer score to be granted to the user.
		 * @param int $consumerScore Consumer score to be granted to the user.
		**/
		private function dispatchWithCategory(UserEvent $event, $producerScore, $consumerScore) {
			$statement = $this->mysqli->database()->prepare('INSERT INTO userscore (user_id, category_id, producer_score, consumer_score)'
				. ' VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE'
				. ' producer_score=producer_score+VALUES(producer_score), consumer_score=consumer_score+VALUES(consumer_score)');

			$userId = $event->user();
			$categoryId = $event->get('category');
			$statement->bind_param('iiii', $userId, $categoryId,
				$producerScore, $consumerScore);

			if($statement->execute()){
				Log::info("Added score ($producerScore, $consumerScore) to user $userId for category $categoryId.");
				MessageQueue::pushPersistent($userId, 'q.message.score-received', ['score'=>($producerScore + $consumerScore)]);
			}
			else{
				Log::error("Could not grant score to user $userId for category $categoryId.");
			}

			$this->promoteUserIfEligible($userId, $categoryId);
		}

		/**
		 * Tells if a user already got score today for a specific correct answered question.
		 * @param UserEvent $event Event to be handled. Has to contain a 'questionid'
		 * @return boolean Returns true if already got score today. Else false.
		 */
		private function alreadyGotScoreToday(UserEvent $event){
			$statement = $this->mysqli->database()->prepare('SELECT COUNT(id) as count FROM questionperformance'
			 	.' WHERE question_id=? AND user_id=? AND questionCorrect <> 0 AND DATE(timestamp) = CURDATE()');

			$questionid = $event->get('questionid');
			$userId = $event->user();
			$statement->bind_param('ii', $questionid, $userId);

			if($statement->execute() === false){
				return true;
			}
			if($result = $statement->get_result()) {
				if($result->fetch_object()->count == 0) {
					return false;
				}
			}
			return true;
		}

		/**
		 * Dispatches the actual scores for the event.
		 * @param UserEvent $event Event that has triggered the dispatching.
		 * @param int $producerScore Producer Score to be added.
		 * @param int $consumerScore Consumer Score to be added.
		**/
		private function dispatchScore(UserEvent $event, $producerScore, $consumerScore) {
			$eventName = $event->name();
			if($producerScore == 0 && $consumerScore == 0) {
				Log::info("Skipped score dispatching for event '$eventName'.");
				return;
			}

			switch($eventName) {
				case 'always':
				case 'game-start':
				case 'game-end':
					$this->dispatchBonusScore($event, $producerScore, $consumerScore);
					break;

				case 'question-created':
				case 'question-removed':
				case 'question-rated':
					$this->dispatchWithCategory($event, $producerScore, $consumerScore);
					break;
				case 'question-answered-correct':
				case 'game-question-answered-correct':
					if($this->alreadyGotScoreToday($event) === false){
						$this->dispatchWithCategory($event, $producerScore, $consumerScore);
					}
					break;

				default:
					Log::warning("Score for event '$eventName' could not be dispatched.");
					break;
			}
		}

		/**
		 * Dispatches the specified event and initiates score accumulation.
		 * @param UserEvent $event The event that has been fired and is now to be dispatched.
		**/
		public function dispatch(UserEvent $event) {
			$statement = $this->mysqli->database()->prepare('SELECT producer_score, consumer_score'
				. ' FROM eventtrigger WHERE name=? LIMIT 1');

			$trigger = $event->name();
			$statement->bind_param('s', $trigger);

			if($statement->execute() && $result = $statement->get_result()) {
				if($fetched = $result->fetch_object())
					$this->dispatchScore($event, $fetched->producer_score, $fetched->consumer_score);
				else
					Log::error('Could not fetch trigger information.');
			}
			else {
				Log::error('Could not execute DB query.');
			}
		}
	} // class ScoreDispatcher
} // namespace quizzenger\dispatching

?>
