<?php

namespace quizzenger\controlling {
	use \mysqli as mysqli;
	use \quizzenger\dispatching\UserEvent as UserEvent;
	use \quizzenger\dispatching\EventDispatcher as EventDispatcher;

	/**
	 * Represents the controller that allows events to be fired, dispatched and processed.
	**/
	class EventController {
		/**
		 * Holds an instance of the EventDispatcher class.
		**/
		private static $dispatcher;

		/**
		 * Sets up the controller by passing a database connection.
		 * @param mysqli $mysqli Represents an active connection to the database.
		**/
		public static function setup(mysqli $mysqli) {
			self::$dispatcher = new EventDispatcher($mysqli);
		}

		/**
		 * Fires the specified event for a specific user with associated arguments.
		 * @param string $name The name of the event / trigger.
		 * @param int $userId The ID of the user that triggered the event.
		 * @param array $arguments A number of arguments required for the event in question.
		**/
		public static function fire($name, $userId, array $arguments = []) {
			$event = new UserEvent($name, $userId);
			foreach($arguments as $key => $value) {
				$event->set($key, $value);
			}

			self::$dispatcher->fire($event);
		}
	} // class EventController
} // namespace quizzenger\controlling

?>
