<?php
		/*
		 * Checks if user is logged in.
		 * Redirects to login page if not
		 */
		function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				$requestArray = $_REQUEST;
				$pageBefore = $requestArray['view'];
				unset($requestArray['view']);
				foreach ($requestArray as $key => $value) {
					$pageBefore = $pageBefore.'||'.$key.'='.$value;
				}
				redirect('./index.php?view=login&pageBefore=' . $pageBefore);
			}
		}

		/*
		 * Redirects to the error page
		 * @param $errorCode specify the errorCode which will be displayed as message for the user. Default is 'err_unknown'
		 */
		function redirectToErrorPage($errorCode = 'err_unkown'){
			redirect('./index.php?view=error&err='.$errorCode);
		}

		/*
		 * Redirects to a the given url.
		 * @param $url specify the desired url. Default is './index.php'
		 */
		function redirect($url = './index.php'){
			session_write_close();
			header('Location: '.$url);
			die ();
		}

		/*
		 * @param $time in format 'H:i:s' means hh:mm:ss. eg. 19:36:15
		 */
		function timeToSeconds($time)
		{
			list($hours, $mins, $secs) = explode(':', $time);
			return ($hours * 3600 ) + ($mins * 60 ) + $secs;
		}

		/*
		 * Returns a string like '1 Std 6 Min 5 Sek'
		* @param $sec total seconds
		*/
		function formatSeconds($sec)
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
		function formatTime($time)
		{
			list($hours, $minutes, $seconds) = explode(':', $time);
			$hours = (int) $hours;
			$minutes = (int) $minutes;
			$seconds = (int) $seconds;
			return ($hours > 0?$hours.' Std ':'').($minutes > 0?$minutes.' Min ':'').($seconds > 0?$seconds.' Sek':'');
		}

 ?>