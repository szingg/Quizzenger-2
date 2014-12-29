<?php
	$viewInner->setTemplate ( 'login' );
	// no double logins
	if($GLOBALS['loggedin']){
		header('Location: index.php?info=mes_login_already');
		die();
	}
?>