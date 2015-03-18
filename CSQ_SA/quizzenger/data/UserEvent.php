<?php

namespace quizzenger\data {
	use \InvalidArgumentException as InvalidArgumentException;

	class UserEvent {
		private $name;
		private $userId;
		private $arguments;

		public function __construct($name, $userId) {
			$this->name = $name;
			$this->userId = $userId;
			$this->arguments = [];
		}

		public function name() {
			return $this->name;
		}

		public function user() {
			return $this->userId;
		}

		public function get($name) {
			if(array_key_exists($name, $this->arguments) === false)
				throw new InvalidArgumentException('The requested argument has not been defined.');

			return $this->arguments[$name];
		}

		public function set($name, $value) {
			$this->arguments[$name] = $value;
		}
	} // class UserEvent
} // namespace quizzenger\data

?>
