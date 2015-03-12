<?php

namespace quizzenger\controllers {
	use \mysqli as mysqli;
	use \quizzenger\scoring\ScoreDispatcher as ScoreDispatcher;
	use \quizzenger\achievements\AchievementDispatcher as AchievementDispatcher;

	class EventController {
		private $mysqli;
		private $scoreDispatcher;
		private $achievementDispatcher;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
			$this->scoreDispatcher = new ScoreDispatcher($this->mysqli);
			$this->achievementDispatcher = new AchievementDispatcher($this->mysqli);
		}
	} // class EventController
} // namespace quizzenger\controllers

?>