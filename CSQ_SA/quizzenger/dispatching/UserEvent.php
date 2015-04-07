<?php

namespace quizzenger\dispatching {
	use \InvalidArgumentException as InvalidArgumentException;

	/**
	 * Contains information for a specific event initiated by a user.
	 * Instances of this class represent such events.
	**/
	class UserEvent {
		/**
		 * Techincal name of the event.
		**/
		private $name;

		/**
		 * ID of the user that caused the event to be fired.
		**/
		private $userId;

		/**
		 * Arguments associated with the event.
		**/
		private $arguments;

		/**
		 * Creates the user event and assigns name and user ID.
		**/
		public function __construct($name, $userId) {
			$this->name = $name;
			$this->userId = $userId;
			$this->arguments = [];
		}

		/**
		 * Gets the name of the event.
		 * @return string Name of the event.
		**/
		public function name() {
			return $this->name;
		}

		/**
		 * Gets the ID of the user that caused the event to be fired.
		 * @return int User ID.
		**/
		public function user() {
			return $this->userId;
		}

		/**
		 * Gets the value of the argument with the specified name.
		 * @param string $name Name of the argument.
		 * @return string Value of the specified argument.
		 * @throws InvalidArgumentException Will be thrown if the specified argument has not been defined.
		**/
		public function get($name) {
			if(array_key_exists($name, $this->arguments) === false)
				throw new InvalidArgumentException("The requested argument '$name' has not been defined.");

			return $this->arguments[$name];
		}

		/**
		 * Sets the value for the specified argument.
		 * @param string $name Name of the argument.
		 * @param string $value Value of the argument.
		**/
		public function set($name, $value) {
			$this->arguments[$name] = $value;
		}
	} // class UserEvent
} // namespace quizzenger\dispatching

?>
