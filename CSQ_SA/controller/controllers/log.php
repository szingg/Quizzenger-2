<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	$viewInner->setTemplate ( 'log' );
	if($_SESSION['superuser']){
		$logFiles = scandir(getcwd()."/".LOGPATH);
		$logArray = array();
		foreach ($logFiles as $key => $file){
			if(substr($file, -4)!=".php" && $file !="." && $file !=".."){
				array_push($logArray,$file);
			}
		}
		$viewInner->assign ( 'logfiles', $logArray );
	}else{
		NavigationUtility::redirect();
	}

?>