<?php

namespace quizzenger {
	use \mysqli as mysqli;

	/**
	 * Sets and retrieves application-wide settings.
	**/
	class Settings {
		/**
		 * Holds an instance to the database connection.
		 * @var mysqli
		**/
		private $mysqli;

		/**
		 * Creates the object based on an existing database connection.
		**/
		private function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}
	} // class Settings
} // namespace quizzenger

?>
