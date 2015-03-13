<?php

namespace quizzenger\achievements {
	use \mysqli as mysqli;

	class AchievementDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		public function dispatch($event, $arguments = []) {
			//
		}
	} // class AchievementDispatcher
} // namespace quizzenger\achievements

?>
