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
		 * @param object $message Message required by the translation. Has to contain a type and an arguments property.
		 * @return string Returns the message with added correctly formatted translation as text property.
		**/
		public static function translateSingle($message){
			$messages = self::translate([$message]);
			if(empty($messages))
				return null;

			return $messages[0];
		}

		/**
		 * Retrieves several translations from the database and formats there texts accordingly.
		 * @param array $messages Messages required by the translation. Have to contain a type and an arguments property.
		 * @return string Returns the messages with added correctly formatted translation as text property.
		**/
		public static function translate(array $messages){
			if(empty($messages))
				return [];

			$placeholders = str_repeat('?,', count($messages) - 1) . '?';
			$statement = self::$database->prepare("SELECT type, text FROM translation WHERE type IN ($placeholders)");
			if(!$statement) {
				Log::error('Could not prepare statement.');
				return null;
			}

			$arguments = [str_repeat('s', count($messages))];
			foreach($messages as $key => $value) {
				$arguments[] = &$messages[$key]->type;
			}

			call_user_func_array([$statement, 'bind_param'], $arguments);

			if(!$statement->execute() || !($result = $statement->get_result())) {
				Log::error('Could not retrieve settings.');
				return null;
			}
			else {
				$text = [];
				while($current = $result->fetch_object()) {
					$text[$current->type] = $current->text;
				}
				foreach($messages as $key => $value){
					$messages[$key]->text = self::$formatter->format($text[$value->type], $value->arguments);
				}
				return $messages;
			}
		}
	} // class TextTranslator
} // namespace quizzenger\messages

?>
