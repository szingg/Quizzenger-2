<?php

namespace quizzenger\scoring {
	use \mysqli as mysqli;

	class ScoreDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		public function dispatch($event, $arguments = []) {
			//
		}
	} // class ScoreDispatcher
} // namespace quizzenger\scoring

?>
