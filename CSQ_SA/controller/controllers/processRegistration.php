<?php
	$viewInner->setTemplate ( 'blankContent' );
	
	$username = filter_input ( INPUT_POST, 'register_form_username', FILTER_SANITIZE_STRING );
	$email = filter_input ( INPUT_POST, 'register_form_email', FILTER_SANITIZE_EMAIL );
	$email = filter_var ( $email, FILTER_VALIDATE_EMAIL );
	$password = filter_input ( INPUT_POST, 'register_form_password', FILTER_SANITIZE_STRING );
	
	$registrationModel->processRegistration($username,$email,$password);
	
?>