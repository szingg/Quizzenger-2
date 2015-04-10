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
				'precision' => 2,
				'decimal' => '.',
				'thousands' => '\''
			];
		}

		private function mergeOptions(array $merge) {
			$this->options = array_merge($this->options, $merge);
		}

		private function formatUnknownError($hint) {
			return $this->options['unknown'] . $hint . $this->options['unknown'];
		}

		private function replace($name, $type, $arguments) {
			if(!array_key_exists($name, $arguments))
				return $this->formatUnknownError('name');

			$value = $arguments[$name];
			switch(mb_strtolower($type)) {
				case 'string':
					return (string)$value;

				case 'integer':
					return (string)(int)$value;

				case 'double':
					return number_format($value, $this->options['precision'], $this->options['decimal'], $this->options['thousands']);

				default:
					return $this->formatUnknownError('type');
			}
		}

		public function format($input, array $arguments) {
			$matches = [];
			$offset = 0;
			$output = '';
			while(preg_match('/\{\{(\w+)(?:#([\w\d_-]+))?\}\}/', $input, $matches, PREG_OFFSET_CAPTURE, $offset)) {
				$name = $matches[1][0];
				$type = (isset($matches[2][0]) ? $matches[2][0] : 'string');

				$matchStart = $matches[0][1];
				$matchLength = strlen($matches[0][0]);

				// Add part between last match and current match.
				$output .= substr($input, $offset, $matchStart - $offset);

				// Replace the match with the associated value.
				$output .= $this->replace($name, $type, $arguments);

				$offset = $matchStart + $matchLength;
			}
			$output .= substr($input, $offset);
			return $output;
		}
	} // class MessageQueue
} // namespace quizzenger\messages

?>
