<?php
	$viewInner->setTemplate ( 'blankContent' );

	if(isset($_POST ['change_password_form_password'])){
		$password = $_POST ['change_password_form_password'];
	}else{
		$this->logger->log ( "Invalid POST request made (processEditQuestion)", Logger::WARNING );
		die ( 'Invalid Request. Please stop this' );
	}

	$userModel->processChangepassword ($password); // checks if user is logged in too
?>