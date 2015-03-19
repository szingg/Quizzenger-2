<?php

namespace quizzenger\gamification\model {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	//use \quizzenger\data\UserEvent as UserEvent;
	//use \quizzenger\scoring\ScoreDispatcher as ScoreDispatcher;
	//use \quizzenger\achievements\AchievementDispatcher as AchievementDispatcher;

	class GameModel {
		private $mysqli;
		//private $scoreDispatcher;
		//private $achievementDispatcher;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
			//$this->scoreDispatcher = new ScoreDispatcher($this->mysqli);
			//$this->achievementDispatcher = new AchievementDispatcher($this->mysqli);
		}
	} // class GameModel
} // namespace quizzenger\gamification\model

?>