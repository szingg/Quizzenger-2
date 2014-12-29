<?php
class RegistrationModel {
	
	var $mysqli;
	var $logger;
	
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	
	public function processRegistration($username,$email,$password) {
		$error_msg = "";
		if ((is_null($username)) || (is_null($email)) || (is_null($password))) {
			$this->logger->log ( "Error trying to register : Missing fields", Logger::ERROR );
			header ( 'Location: ./index.php?view=error&err=err_missing_input');
			die ();
		}else{
			// sanitize and validate the data passed in
			if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
				$error_msg = "err_register_invalid_mail";
			}
			$password = hash ( 'sha512', $password );
			
			// Username validity isn't checked, only sanitized
				
			$prep_stmt = "SELECT id FROM user WHERE email = ? LIMIT 1";
			$stmt = $this->mysqli ->prepare ( $prep_stmt );
				
			// check if mail is already registered
			if ($stmt) {
				$stmt->bind_param ( 's', $email );
				$stmt->execute ();
				$stmt->store_result ();
				if ($stmt->num_rows == 1) {
					$error_msg = "err_register_existing_information";
				}
			} else {
				$error_msg = "err_register_check";
			}
			$stmt->close ();
				
			// check if username is already registered
			$prep_stmt = "SELECT id FROM user WHERE username = ? LIMIT 1";
			$stmt = $this->mysqli ->prepare ( $prep_stmt );
				
			if ($stmt) {
				$stmt->bind_param ( 's', $username );
				$stmt->execute ();
				$stmt->store_result ();
				if ($stmt->num_rows == 1) {
					$error_msg = "err_register_existing_information";
				}
			} else {
				$error_msg = "err_register_check";
			}
			$stmt->close ();
				
			if (empty ( $error_msg )) {
				// We don't need to set seed since PHP 5.2.1
				// Uniqid for more entropy due to mt_rand not being 100% top notch
				$random_salt = hash ( 'sha512', uniqid ( mt_rand (), true ) );
				$password = hash ( 'sha512', $password . $random_salt );
			
				if ($insert_stmt = $this->mysqli->prepare ( "INSERT INTO user (username, email, password, salt) VALUES (?, ?, ?, ?)" )) {
					$insert_stmt->bind_param ( 'ssss', $username, $email, $password, $random_salt );
					if (! $insert_stmt->execute ()) {
						$this->logger->log ( "Error trying to register (insert). SQL Error: " . $this->mysqli ->error(), Logger::ERROR );
						header ( 'Location: ./index.php?view=error&err=err_register_insert' );
						die ();
					}
				}
				$this->logger->log ( "User registered sucessfully", Logger::INFO );
				header ( 'Location: ./index.php?info=mes_register_success' );
				die ();
			} else {
				$this->logger->log ( "Error trying to register :" . $error_msg, Logger::ERROR );
				header ( 'Location: ./index.php?view=error&err='.$error_msg );
				die ();
			}
		}
	}
}