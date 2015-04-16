<?php

namespace quizzenger\dispatching {
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\dispatching\UserEvent as UserEvent;

	/**
	 * This class is used to dispatch scores to individual users
	 * based on user events that occured within the system.
	**/
	class ScoreDispatcher {
		private $mysqli;

		/**
		 * Creates the object based on an existing database connection.
		**/
		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		/**
		 * Dispatches the actual scores for the event.
		 * @param UserEvent $event Event that has triggered the dispatching.
		 * @param int $producerScore Producer Score to be added.
		 * @param int $consumerScore Consumer Score to be added.
		**/
		private function dispatchScore(UserEvent $event, $producerScore, $consumerScore) {
			$eventName = $event->name();
			if($event->name() !== 'question-answered-correct') {
				Log::warning("Score for event '$eventName' cannot be dispatched.");
				return;
			}

			$statement = $this->mysqli->prepare('INSERT INTO userscore (user_id, category_id, producer_score, consumer_score)'
				. ' VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE'
				. ' producer_score=producer_score+VALUES(producer_score), consumer_score=consumer_score+VALUES(consumer_score)');

			$userId = $event->user();
			$categoryId = $event->get('category');
			$statement->bind_param('iiii', $userId, $categoryId,
				$producerScore, $consumerScore);

			if($statement->execute())
				Log::info("Added score ($producerScore, $consumerScore) to user $userId.");
			else
				Log::error('Could not update score.');
		}

		/**
		 * Dispatches the scores for the specified event.
		 * @param UserEvent $event Event that has triggered the dispatching.
		**/
		public function dispatch(UserEvent $event) {
			$statement = $this->mysqli->prepare('SELECT producer_score, consumer_score'
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
