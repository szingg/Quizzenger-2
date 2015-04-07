<?php

namespace quizzenger\utilities {
	/**
	 * Utility class for text formatting.
	**/
	class FormatUtility {
		/**
		 * Prevents any objects from being created.
		**/
		private function __construct() {
			//
		}

		/**
		 * Formats the specified number according to the parameters.
		 * @param int $value Floating point number to be formatted.
		 * @param int $decimals Number of decimal places.
		 * @return string Returns the specified number correctly formatted.
		**/
		public static function formatNumber($value, $decimals) {
			return number_format($value, $decimals, '.', "'");
		}
	} // class FormatUtility
} // namespace quizzenger\utilities

?>
