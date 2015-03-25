<?php

namespace quizzenger\scoring {
	use \mysqli as mysqli;
	use \quizzenger\data\UserEvent as UserEvent;

	class ScoreDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		private function dispatchScore($userId, $producerScore, $consumerScore) {
			//
		}

		public function dispatch(UserEvent $event) {
			$statement = $this->mysqli->prepare('SELECT producer_score, consumer_score'
				. ' FROM eventtrigger WHERE name=? LIMIT 1');

			$trigger = $event->name();
			$statement->bind_param('s', $trigger);

			if($statement->execute() && $result = $statement->get_result()) {
				if($fetched = $result->fetch_object())
					$this->dispatchScore($event->user(), $fetched->producer_score, $fetched->consumer_score);
				else
					Log::error('Could not fetch trigger information.');
			}
			else {
				Log::error('Could not execute DB query.');
			}
		}
	} // class ScoreDispatcher
} // namespace quizzenger\scoring

?>