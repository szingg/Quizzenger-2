<?php

namespace quizzenger\utilities {
	class FormatUtility {
		private function __construct() {
			//
		}

		public static function formatNumber($value, $decimals) {
			return number_format($value, $decimals, '.', "'");
		}
	} // class FormatUtility
} // namespace quizzenger\utilities

?>
