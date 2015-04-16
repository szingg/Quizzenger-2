<?php

namespace quizzenger\dispatching {
	use \SqlHelper as SqlHelper;
	use \quizzenger\dispatching\UserEvent as UserEvent;
	use \quizzenger\dispatching\ScoreDispatcher as ScoreDispatcher;
	use \quizzenger\dispatching\AchievementDispatcher as AchievementDispatcher;

	/**
	 * Forwards incoming events to both the score dispatcher and the achievement dispatcher.
	 * It furthermore fires the 'always' event for each incoming event.
	**/
	class EventDispatcher {
		/**
		 * Holds an instance of the database connection.
		 * @var SqlHelper
		**/
		private $mysqli;

		/**
		 * The score dispatcher used to accumulate scores.
		 * @var ScoreDispatcher
		**/
		private $scoreDispatcher;

		/**
		 * The achievement dispatcher that iterates through all non-granted achievements
		 * of the specific type of the event.
		 * @var AchievementDispatcher
		**/
		private $achievementDispatcher;

		/**
		 * Creates the object based on an existing database connection.
		 * @param Database $mysqli connection to be used for dispatching.
		**/
		public function __construct(SqlHelper $mysqli) {
			$this->mysqli = $mysqli;
			$this->scoreDispatcher = new ScoreDispatcher($this->mysqli);
			$this->achievementDispatcher = new AchievementDispatcher($this->mysqli);
		}

		/**
		 * Forwards the specified event to both the score dispatcher and the achievement dispatcher.
		 * @param UserEvent $event The event that is to be dispatched.
		**/
		private function dispatch(UserEvent $event) {
			$this->scoreDispatcher->dispatch($event);
			$this->achievementDispatcher->dispatch($event);
		}

		/**
		 * Fires the 'always' event.
		 * @param UserEvent $cause The reason why the 'always' event has been fired.
		**/
		private function fireAlways(UserEvent $cause) {
			$this->dispatch(new UserEvent('always', $cause->user()));
		}

		/**
		 * Fires and initiates dispatching of the specified event.
		 * @param UserEvent $event The event to be fired and dispatched.
		**/
		public function fire(UserEvent $event) {
			$this->dispatch($event);
			$this->fireAlways($event);
		}
	} // class EventDispatcher
} // namespace quizzenger\dispatching

?>
