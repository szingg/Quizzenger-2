<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	// no double logins
	if($GLOBALS['loggedin']){
		MessageQueue::pushPersistent($_SESSION['user_id'], 'mes_login_already');
		NavigationUtility::redirect();
	}

	$viewInner->setTemplate ( 'login' );
?>