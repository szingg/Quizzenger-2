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

		/*
		 * @param $time in format 'H:i:s' means hh:mm:ss. eg. 19:36:15
		 */
		public static function timeToSeconds($time)
		{
			list($hours, $mins, $secs) = explode(':', $time);
			return ($hours * 3600 ) + ($mins * 60 ) + $secs;
		}

		/*
		 * Returns a string like '1 Std 6 Min 5 Sek'
		* @param $sec total seconds
		*/
		public static function formatSeconds($sec)
		{
			$hours = (int) ($sec / 3600);
			$sec = $sec % 3600;
			$minutes = (int) ($sec / 60);
			$seconds = $sec % 60;
			return ($hours > 0?$hours.' Std ':'').($minutes > 0?$minutes.' Min ':'').($seconds > 0?$seconds.' Sek':'');
		}

		/*
		 * Returns a string like '1 Std 6 Min 5 Sek'
		 * @param $time mysql time. format like '10:00:43'
		 */
		public static function formatTime($time)
		{
			list($hours, $minutes, $seconds) = explode(':', $time);
			$hours = (int) $hours;
			$minutes = (int) $minutes;
			$seconds = (int) $seconds;
			return ($hours > 0?$hours.' Std ':'').($minutes > 0?$minutes.' Min ':'').($seconds > 0?$seconds.' Sek':'');
		}
	} // class FormatUtility
} // namespace quizzenger\utilities

?>
