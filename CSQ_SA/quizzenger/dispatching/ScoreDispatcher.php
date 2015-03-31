<?php

namespace quizzenger\dispatching {
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\dispatching\UserEvent as UserEvent;

	class ScoreDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		private function dispatchScore(UserEvent $event, $producerScore, $consumerScore) {
			// TODO: Implement proper dispatching via plugins.
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
