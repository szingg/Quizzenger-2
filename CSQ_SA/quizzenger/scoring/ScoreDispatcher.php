<?php

namespace quizzenger\scoring {
	use \mysqli as mysqli;
	use \quizzenger\data\ArgumentCollection as ArgumentCollection;

	class ScoreDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		public function dispatch($event, ArgumentCollection $arguments) {
			//
		}
	} // class ScoreDispatcher
} // namespace quizzenger\scoring

?>
