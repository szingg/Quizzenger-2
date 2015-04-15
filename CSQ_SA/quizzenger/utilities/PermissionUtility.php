<?php

namespace quizzenger\utilities {
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	/**
	 * Utility class to simplify page navigation.
	**/
	class PermissionUtility {
		/**
		 * Prevents any objects from being created.
		**/
		private function __construct() {
			//
		}

		/*
		 * Checks if user is logged in.
		 * Redirects to login page if not
		 */
		public static function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				$requestArray = $_REQUEST;
				$pageBefore = $requestArray['view'];
				unset($requestArray['view']);
				foreach ($requestArray as $key => $value) {
					$pageBefore = $pageBefore.'||'.$key.'='.$value;
				}
				NavigationUtility::redirect('./index.php?view=login&pageBefore=' . $pageBefore);
			}
		}

	} // class PermissionUtility
} // namespace quizzenger\utilities

?>
