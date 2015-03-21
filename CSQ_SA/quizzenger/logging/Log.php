<?php

namespace quizzenger\logging {
	require_once 'includes/logger.php';

	use \Logger as Logger;
	class Log {
		private static $legacy;

		private function __construct() {
			//
		}

		public static function set(Logger $legacy) {
			self::$legacy = $legacy;
		}
		
		public static function get(){
			return self::$legacy;
		}

		public static function info($message) {
			self::$legacy->log($message, Logger::INFO);
		}

		public static function warning($message) {
			self::$legacy->log($message, Logger::WARNING);
		}

		public static function error($message) {
			self::$legacy->log($message, Logger::ERROR);
		}

		public static function fatal($message) {
			self::$legacy->log($message, Logger::FATAL);
		}
	} // class Log
} // namespace quizzenger\logging

?>