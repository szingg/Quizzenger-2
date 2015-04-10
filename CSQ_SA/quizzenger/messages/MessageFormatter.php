<?php

namespace quizzenger\messages {
	/**
	 * This class provides the functionality of replacing placeholders within a message
	 * using specified parameters to create messages for different values.
	**/
	class MessageFormatter {
		private $options;

		public function __construct(array $options = []) {
			$this->resetOptions();
			$this->mergeOptions($options);
		}

		private function resetOptions() {
			$this->options = [
				'unknown' => '????',
				'precision' => 2
			];
		}

		private function mergeOptions(array $merge) {
			$this->options = array_merge($this->options, $merge);
		}

		public function format($input, array $arguments) {
			return $input;
		}
	} // class MessageQueue
} // namespace quizzenger\messages

?>
