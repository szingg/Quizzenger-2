<?php

namespace quizzenger\controlling {
	use \mysqli as mysqli;
	use \quizzenger\dispatching\UserEvent as UserEvent;
	use \quizzenger\dispatching\EventDispatcher as EventDispatcher;

	class EventController {
		private static $dispatcher;

		public static function setup(mysqli $mysqli) {
			self::$dispatcher = new EventDispatcher($mysqli);
		}

		public static function fire($name, $userId, array $arguments = []) {
			$event = new UserEvent($name, $userId);
			foreach($arguments as $key => $value) {
				$event->set($key, $value);
			}

			return self::$dispatcher->fire($event);
		}
	} // class EventController
} // namespace quizzenger\controlling

?>
