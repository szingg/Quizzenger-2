<?php
namespace quizzenger\logging {
	require_once 'includes/logger.php';
	use \Logger as Logger;

	/**
	 * An improved logger class that is still based on the legacy logger.
	 * This class is supposed to be extended at some point in order to become
	 * independent and replace the legacy logger.
	**/
	class Log {
		/**
		 * Holds an instance to the legacy logger class.
		**/
		private static $legacy;

		/**
		 * Prevents any objects from being created.
		**/
		private function __construct() {
			//
		}

		/**
		 * Sets the legacy logger.
		 * @param Logger $legacy Instance of the legacy logger class.
		**/
		public static function set(Logger $legacy) {
			self::$legacy = $legacy;
		}

		/**
		 * Gets the legacy logger.
		 **/
		public static function get() {
			return self::$legacy;
		}



		/**
		 * Writes an information entry into the log file.
		 * @param string $message Message to be written.
		**/
		public static function info($message) {
			self::$legacy->log($message, Logger::INFO);
		}

		/**
		 * Writes a warning entry into the log file.
		 * @param string $message Message to be written.
		**/
		public static function warning($message) {
			self::$legacy->log($message, Logger::WARNING);
		}

		/**
		 * Writes an error entry into the log file.
		 * @param string $message Message to be written.
		**/
		public static function error($message) {
			self::$legacy->log($message, Logger::ERROR);
		}

		/**
		 * Writes a fatal error entry into the log file.
		 * @param string $message Message to be written.
		**/
		public static function fatal($message) {
			self::$legacy->log($message, Logger::FATAL);
		}
	} // class Log
} // namespace quizzenger\logging

?>