<?php

namespace quizzenger\dispatching {
	use \stdClass as stdClass;

	/**
	 * Represents a simple message queue that stores messages to be displayed to the user.
	**/
	class MessageQueue {
		/**
		 * The queue that stores all added messages in order.
		**/
		private static $queue = [];

		/**
		 * Prevents any objects from being constructed.
		**/
		private function __construct() {
			//
		}

		/**
		 * Pushes a message into the queue.
		 * @param string $text The message to be displayed to the user.
		 * @param string $type The alert type that determines the styles to be used.
		**/
		public static function push($text, $type) {
			$message = new stdClass();
			$message->type = $type;
			$message->content = 'NOT FULLY IMPLEMENTED YET: ' . $text;
			self::$queue[] = $message;
		}

		/**
		 * Removes the first element from the queue and returns it.
		 * @return string Returns the first message that has been queued up.
		**/
		public static function pop() {
			if(empty(self::$queue))
				return null;

			return array_shift(self::$queue);
		}
	} // class MessageQueue
} // namespace quizzenger\dispatching

?>
