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
			header('Location: '.$url);
			die ();
		}

 ?>