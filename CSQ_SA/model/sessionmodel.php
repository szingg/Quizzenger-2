<?php
class SessionModel {
	
	var $mysqli; 
	var $logger;
	
	function __construct($mysqliP,$logP){
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	
	
	public function processLogin($email,$password) {
		if (isset ( $GLOBALS ['loggedin'] ) && $GLOBALS ['loggedin']) { // no manual / spoofed / replayed double logins
			header ( 'Location: ./index.php?info=mes_login_already' );
			die ();
		}
		if (!is_null($email) && !is_null($password) ) {
			$password =  hash ( 'sha512', $password );
			$loginResult = $this->login ( $email, $password, $this->mysqli );
			
			if ($loginResult == 1) {
				$this->logger->log ( "User logged in sucessfully ", Logger::INFO );
				$pageBefore = filter_input(INPUT_GET, 'pageBefore', $filter = FILTER_SANITIZE_SPECIAL_CHARS);
				if (is_null($pageBefore)){
					$pageBefore='default';
				}
				header ( 'Location: ./index.php?view='.$pageBefore.'&info=mes_login_success' );
				die (); 
			} elseif ($loginResult == - 1) {
				$this->logger->log ( "User tried to log in with bad credentials, email: ".$email, Logger::WARNING );
				header ( 'Location: ./index.php?view=error&err=err_login_bad_credentials' );
				die ();
			} elseif ($loginResult == - 2) {
				$this->logger->log ( "User has reached maximum login tries, email: ".$email, Logger::WARNING );
				header ( 'Location: ./index.php?view=error&err=err_login_tries_exceeded' );
				die ();
			} elseif ($loginResult == - 3) {
					$this->logger->log ( "Inactive User tried to login, email: ".$email, Logger::INFO );
					header ( 'Location: ./index.php?view=error&err=err_login_inactive' );
					die ();
			} else {
				$this->logger->log ( "User tried to log in with bad credentials (unkown return from login), email: ".$email, Logger::WARNING );
				header ( 'Location: ./index.php?view=error&err=err_login_bad_credentials' );
				die ();
			}
		} else {
			$this->logger->log ( "Invalid POST request made", Logger::WARNING );
			die ( 'Invalid Request. Please stop this' );
		}
	}
	
	function login($email, $password, $mysqli) {
	
		if ($stmt = $mysqli->prepare ( "SELECT id, username, password, salt, inactive,superuser FROM user WHERE email = ? LIMIT 1" )) {
			$stmt->bind_param ( 's', $email );
			$stmt->execute ();
			$stmt->store_result ();
	
			$stmt->bind_result ( $user_id, $username, $db_password, $salt, $inactive, $superuser );
			$stmt->fetch ();
	
			// hash the password with the unique salt.
			$password = hash ( 'sha512', $password . $salt );
	
			if ($stmt->num_rows == 1) {
				// If the user exists we check if the account is locked
				// from too many login attempts
					
				if ($this->checkbrute ( $user_id, $mysqli ) == true) {
					// Account is locked
					// Send an email to user saying their account is locked
					return -2;
				} else {
					// Check if the password in the database matches
					// the password the user submitted.
					if ($db_password == $password) {
						if($inactive){
							return -3;
						}
						// Password is correct!
						// Get the user-agent string of the user.
						$user_browser = $_SERVER ['HTTP_USER_AGENT'];
						// XSS protection as we might print those values
						$user_id = preg_replace ( "/[^0-9]+/", "", $user_id );
						$_SESSION ['user_id'] = $user_id;
						$username = preg_replace ( "/[^a-zA-Z0-9_\-]+/", "", $username );
						$_SESSION ['username'] = $username;
						$_SESSION ['login_string'] = hash ( 'sha512', $password . $user_browser );
						$_SESSION ['email'] = $email;
						$_SESSION['superuser']=($superuser==1?true:false);
						// Login successful.
						return 1;
					} else {
						// Password is not correct
						// We record this attempt in the database
						$now = time ();
						$mysqli->query ( "INSERT INTO login_attempts(user_id, time)
								VALUES ('$user_id', '$now')" );
						return -1;
					}
				}
			} else {
				// No user exists.
				return false;
			}
		}
	}
	function checkbrute($user_id, $mysqli) {
		if (!BRUTE_FORCE_CHECK){
			return false;
		}
	
		$valid_attempts = time() - (BRUTE_FORCE_COOLDOWN );
	
		if ($stmt = $mysqli->prepare ( "SELECT time
				FROM login_attempts
				WHERE user_id = ?
				AND time > '$valid_attempts'" )) {
				$stmt->bind_param ( 'i', $user_id );
	
				$stmt->execute ();
				$stmt->store_result ();
	
				if ($stmt->num_rows >= BRUTE_FORCE_MAX_ATTEMPTS) {
					return true;
				} else {
					return false;
				}
		}
	}
	
	function logout() {
		//Clean up properly in orde to destroy session for good
		$_SESSION = array (); // Unset all session values

		$params = session_get_cookie_params ();	// get session parameters so we an delete the cookie
	
		// Renders it invalid / deleted
		setcookie ( session_name (), '', time () - 42000, $params ["path"], $params ["domain"], $params ["secure"], $params ["httponly"] );
	
		// Bye!
		session_destroy ();
		header('Location: index.php?info=mes_logout_success');
		die();
	}
	

	// Because session_start is rather unsecure
	function sec_session_start() {
		$session_name = 'sec_session_id';
		$secure = SECURE;
		// This stops JavaScript being able to access the session id, no session hijacking please
		$httponly = true;
		// We want to use cookies only
		if (ini_set ( 'session.use_only_cookies', 1 ) === FALSE) {
			die ( "Could not initiate a safe session, ini_set failed" );
		}
		$cookieParams = session_get_cookie_params ();
		session_set_cookie_params ( $cookieParams ["lifetime"], $cookieParams ["path"], $cookieParams ["domain"], $secure, $httponly );
		session_name ( $session_name );
		session_start ();
		session_regenerate_id (); // we dont want any old ones anymore!
		
		if ($this->login_check ( $this->mysqli )) {
			$GLOBALS ['loggedin'] = true;
		} else {
			$GLOBALS ['loggedin'] = false;
			$_SESSION['user_id'] = 1; // ID=1 is Guest User
		}
		
	}
	
	
	function login_check($mysqli) {
	
		if (isset ( $_SESSION ['user_id'], $_SESSION ['username'], $_SESSION ['login_string'] )) {
			$user_id = $_SESSION ['user_id'];
			$login_string = $_SESSION ['login_string'];
			$username = $_SESSION ['username'];
	
			$user_browser = $_SERVER ['HTTP_USER_AGENT'];
	
			if ($stmt = $mysqli->prepare ( "SELECT password FROM user WHERE id = ? LIMIT 1" )) {
				$stmt->bind_param ( 'i', $user_id );
                $stmt->execute ();
                $stmt->store_result ();
				if ($stmt->num_rows == 1) {
					$stmt->bind_result ( $password );
					$stmt->fetch ();
					$login_check = hash ( 'sha512', $password . $user_browser );
                    if ($login_check == $login_string) {
                    	return true;
                    } else {
                    	return false;
                    }
				} else {
                	return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	

	
	function esc_url($url) {
		if ('' == $url) {
			return $url;
		}
	
		$url = preg_replace ( '|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url );
	
		$strip = array (
				'%0d',
				'%0a',
				'%0D',
				'%0A'
		);
		$url = ( string ) $url;
	
		$count = 1;
		while ( $count ) {
			$url = str_replace ( $strip, '', $url, $count );
		}
	
		$url = str_replace ( ';//', '://', $url );
	
		$url = htmlentities ( $url );
	
		$url = str_replace ( '&amp;', '&#038;', $url );
		$url = str_replace ( "'", '&#039;', $url );
	
		if ($url [0] !== '/') {
			// We're only interested in relative links from $_SERVER['PHP_SELF']
			return '';
		} else {
			return $url;
		}
	}
}
?>
