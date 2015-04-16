<?php

namespace quizzenger\messages {
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;

	/**
	 * Provides the functionality to retrieve translations from the database
	 * and format them correctly according to their required arguments.
	**/
	class TextTranslator {
		/**
		 * Holds an instance to an active database connection.
		 * @var mysqli
		**/
		private static $database;

		/**
		 * The instance that is used to format the translation correctly.
		 * @var MessageFormatter
		**/
		private static $formatter;

		/**
		 * Prevents any objects from being constructed.
		**/
		private function __construct() {
			//
		}

		/**
		 * Initializes the queue with an active connection to the database.
		 * @param mysqli $database Represents the database connection to be used.
		**/
		public static function setup(mysqli $database, MessageFormatter $formatter) {
			self::$database = $database;
			self::$formatter = $formatter;
		}

		/**
		 * Retrieves the specified translation from the database and formats the text accordingly.
		 * @param string $type Type of the translation to retrieve.
		 * @param array $arguments Arguments required by the translation.
		 * @return string Returns the correctly formatted translation text.
		**/
		public static function translate($type, array $arguments = []) {
			$statement = self::$database->prepare('SELECT text FROM translation WHERE type=? LIMIT 1');

			$statement->bind_param('s', $type);
			if(!$statement->execute()) {
				Log::error('Could not retrieve translation');
				return null;
			}

			$result = $statement->get_result();
			$translation = $result->fetch_object()->text;

			return self::$formatter->format($translation, $arguments);
		}
	} // class TextTranslator
} // namespace quizzenger\messages

?>
