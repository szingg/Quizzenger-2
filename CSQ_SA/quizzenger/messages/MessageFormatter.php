<?php

namespace quizzenger\messages {
	/**
	 * This class provides the functionality of replacing placeholders within a message
	 * using specified parameters to create messages for different values.
	**/
	class MessageFormatter {
		/**
		 * Represents an array of formatting options used by the parser.
		 * @var array
		**/
		private $options;

		/**
		 * Creates the object with the specified options.
		 * The following options will be considered:
		 *   unknown:   Indicator for name and type errors ('????').
		 *   precision: Number of decimal places used for decimal numbers ("2").
		 *   decimal:   Decimal seperator (".").
		 *   thousands: Character used for digit grouping ("'").
		 * @param array $options A number of options used for formatting.
		**/
		public function __construct(array $options = []) {
			$this->resetOptions();
			$this->mergeOptions($options);
		}

		/**
		 * Resets all options to their default values.
		**/
		private function resetOptions() {
			$this->options = [
				'unknown' => '????',
				'precision' => 2,
				'decimal' => '.',
				'thousands' => '\''
			];
		}

		/**
		 * Merges the specified array into the options array,
		 * replacing default options with new values.
		 * @param array $merge Array to merge with the existing options array.
		 **/
		private function mergeOptions(array $merge) {
			$this->options = array_merge($this->options, $merge);
		}

		/**
		 * Wraps the error indicator around an error hint.
		 * @param string $hint Hint that indicates the error.
		 * @return string Returns a formatted error string to be inserted.
		**/
		private function formatUnknownError($hint) {
			return $this->options['unknown'] . $hint . $this->options['unknown'];
		}

		/**
		 * Replaces the matched field with its defined value
		 * after formatting the value according to the type.
		 * @param string $name Name of the field.
		 * @param string $type Type of the field that determines the format.
		 * @param array $arguments An array that holds fiels and their values.
		 * @return Returns the formatted value of the field.
		 **/
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
					return number_format($value, $this->options['precision'],
						$this->options['decimal'], $this->options['thousands']);
				case 'img':
					return '<img src="' . ACHIEVEMENT_PATH . DIRECTORY_SEPARATOR . $value . ACHIEVEMENT_IMAGE_EXTENSION . '" />';
				default:
					return $this->formatUnknownError('type');
			}
		}

		/**
		 * Formats the specified input string by applying the defined fields.
		 * @param string $input Input text to be parsed.
		 * @param array $arguments An array that contains all fields and their values.
		 * @return Returns the input where all fields have been replaced with their formatted value.
		**/
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
