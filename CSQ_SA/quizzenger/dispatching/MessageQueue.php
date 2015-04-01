<?php

namespace quizzenger\dispatching {
	use \stdClass as stdClass;
	class MessageQueue {
		private static $queue = [];

		private function __construct() {
			//
		}

		public static function push($text, $type) {
			$message = new stdClass();
			$message->type = $type;
			$message->content = 'NOT FULLY IMPLEMENTED YET: ' . $text;
			self::$queue[] = $message;
		}

		public static function pop() {
			if(empty(self::$queue))
				return null;

			return array_shift(self::$queue);
		}
	} // class MessageQueue
} // namespace quizzenger\dispatching

?>
