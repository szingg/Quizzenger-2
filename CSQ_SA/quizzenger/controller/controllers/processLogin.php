<?php
	$viewInner->setTemplate ( 'blankContent' );

	if(isset($_POST['login_form_password'])){
		$password = $_POST['login_form_password'];
	}else{
		$password = null;
	}
	if(isset($_POST['login_form_email'])){
		$email = $_POST['login_form_email'];
	}else{
		$email = null;
	}

	$sessionModel->processLogin ($email,$password);
	$messages = MessageQueue::popAll($_SESSION['user_id']);
	$bla = 1;
?>