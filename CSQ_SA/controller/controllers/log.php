<?php
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
		header ( 'Location: ./index.php');
		die ();
	}

?>