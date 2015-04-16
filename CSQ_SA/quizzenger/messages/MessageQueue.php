<?php

namespace quizzenger\messages {
	use \stdClass as stdClass;
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;

	/**
	 * Represents a simple message queue that stores messages to be displayed to the user.
	**/
	class MessageQueue {
		/**
		 * Holds an instance to an active database connection.
		 * @var mysqli
		**/
		private static $database;

		/**
		 * The queue that stores all static (i.e. non-persistent messages).
		 * @var array
		**/
		private static $staticQueue = [];

		/**
		 * Prevents any objects from being constructed.
		**/
		private function __construct() {
			//
		}

		/**
		 * Removes the message with the specified ID from the database table.
		 * @param int $id ID of the message to remove.
		 * @return boolean Returns 'true' on success, 'false' otherwise.
		**/
		private static function deletePersistentMessage($id) {
			$statement = self::$database->prepare('DELETE FROM message WHERE id=?');
			$statement->bind_param('i', $id);

			if(!$statement->execute()) {
				Log::error('Could not delete retrieved message.');
				return false;
			}

			return true;
		}

		/**
		 * Initializes the queue with an active connection to the database.
		 * @param mysqli $database Represents the database connection to be used.
		**/
		public static function setup(mysqli $database) {
			self::$database = $database;
		}

		/**
		 * Pushes a message into the static message.
		 * @param string $type The type of the message.
		 * @param array $arguments Holds the message arguments that are passed along with the type.
		**/
		public static function pushStatic($type, array $arguments = []) {
			$message = new stdClass();
			$message->userId = 0;
			$message->type = $type;
			$message->static = false;
			$message->arguments = $arguments;
			self::$staticQueue[] = $message;
		}

		/**
		 * Pushes a message into the message log that is persisted on the database.
		 * @param int $user ID of the user that is the receiver of the message.
		 * @param string $type The type of the message.
		 * @param array $arguments Holds the message arguments that are passed along with the type.
		 * @return boolean Returns 'true' on success, 'false' otherwise.
		**/
		public static function pushPersistent($userId, $type, array $arguments = []) {
			$statement = self::$database->prepare('INSERT INTO message (user_id, type, arguments) VALUES (?, ?, ?)');

			// Encode JSON and force single-level only.
			$formattedArguments = json_encode($arguments, 0, 1);
			$statement->bind_param('iss', $userId, $type, $formattedArguments);

			if(!$statement->execute()) {
				Log::error('Could not insert message.');
				return false;
			}

			return true;
		}

		/**
		 * Clears both the static and the persistent message queues and returns
		 * and array of all messages that have been stored.
		 * @param int $userId ID of the user whose messages shall be retrieved.
		 * @return array Returns an array of objects representing individual messages.
		**/
		public static function popAll($userId) {
			$statement = self::$database->prepare('SELECT id, user_id, type, arguments FROM message'
				. ' WHERE user_id=?');

			$statement->bind_param('i', $userId);

			if(!$statement->execute()) {
				Log::error('Could not retrieve messages.');
				return null;
			}

			$persistentMessages = [];
			$result = $statement->get_result();
			while($current = $result->fetch_object()) {
				$message = new stdClass();
				$message->userId = $current->user_id;
				$message->type = $current->type;
				$message->static = true;
				$message->arguments = json_decode($current->arguments);

				$persistentMessages[] = $message;
				self::deletePersistentMessage($current->id);
			}

			$combinedMessages = array_merge(self::$staticQueue, $persistentMessages);
			self::$staticQueue = [];

			return $combinedMessages;
		}
	} // class MessageQueue
} // namespace quizzenger\messages

?>
