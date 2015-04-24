<?php

namespace quizzenger\utilities {
	/**
	 * Utility class to simplify page navigation.
	**/
	class NavigationUtility {
		/**
		 * Prevents any objects from being created.
		**/
		private function __construct() {
			//
		}

		/*
		 * Redirects to the error page
		 * @param $errorCode specify the errorCode which will be displayed as message for the user. Default is 'err_unknown'
		 */
		public static function redirectToErrorPage(){
			NavigationUtility::redirect('./index.php?view=error');
		}

		/*
		 * Redirects to a the given url.
		 * @param $url specify the desired url. Default is './index.php'
		 */
		public static function redirect($url = './index.php'){
			session_write_close();
			header('Location: '.$url);
			die ();
		}
	} // class NavigationUtility
} // namespace quizzenger\utilities

?>
