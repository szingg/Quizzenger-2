<?php

namespace quizzenger\controllers {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \quizzenger\data\UserEvent as UserEvent;
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

		private function dispatch(UserEvent $event) {
			$this->scoreDispatcher->dispatch($event);
			$this->achievementDispatcher->dispatch($event);
		}

		private function fireAlways(UserEvent $cause) {
			$this->dispatch(new UserEvent('always', $cause->user()));
		}

		public function fire(UserEvent $event) {
			$this->dispatch($event);
			$this->fireAlways($event);
		}
	} // class EventController
} // namespace quizzenger\controllers

?>
