<?php

namespace quizzenger\dispatching {
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\dispatching\UserEvent as UserEvent;

	/**
	 * This class accumulates the scores for individual users based on events
	 * that have been fired. The scores are automatically updated in the Database.
	**/
	class ScoreDispatcher {
		/**
		 * Holds an instance to the database connection.
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

			if($statement->execute())
				Log::info("Added bonus score ($producerScore, $consumerScore) to user $userId.");
			else
				Log:error("Could not grant bonus score for user $userId.");
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

			if($statement->execute())
				Log::info("Added score ($producerScore, $consumerScore) to user $userId for category $categoryId.");
			else
				Log::error("Could not grant score to user $userId for category $categoryId.");
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
				case 'question-answered-correct':
				case 'game-question-answered-correct':
					$this->dispatchWithCategory($event, $producerScore, $consumerScore);
					break;

				default:
					Log::warning("Score for event '$eventName' could not be dispatched.");
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
