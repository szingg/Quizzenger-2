<?php

namespace quizzenger\scoring {
	use \mysqli as mysqli;
	use \quizzenger\data\UserEvent as UserEvent;

	class ScoreDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		public function dispatch(UserEvent $event) {
			//
		}
	} // class ScoreDispatcher
} // namespace quizzenger\scoring

?>