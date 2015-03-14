<?php

namespace quizzenger\data {
	use \InvalidArgumentException as InvalidArgumentException;

	class ArgumentCollection {
		private $collection;

		public function __construct() {
			$this->collection = [];
		}

		public function get($name) {
			if(array_key_exists($name, $this->collection) === false)
				throw new InvalidArgumentException('The requested argument has not been defined.');

			return $this->collection[$name];
		}

		public function set($name, $value) {
			$this->collection[$name] = $value;
		}
	} // class ArgumentCollection
} // namespace quizzenger\data

?>
