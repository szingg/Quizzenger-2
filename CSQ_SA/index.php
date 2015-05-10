<?php
	define("QUIZZENGER_ROOT", dirname(__FILE__));

	require_once 'quizzenger/autoloader.php';

	load_includes();
	https_only();

	$GLOBALS["time_start"] = microtime(true);

	$log = new Logger();
	\quizzenger\logging\Log::set($log);

	$request = array_merge ( $_GET, $_POST );

	if (isset ( $request ['type'] ) && $request ['type'] == "ajax") {
		$controller = new AjaxController ( $request ,$log);
	} else {
		$controller = new Controller ( $request,$log );
	}

	echo $controller->display ();


	// -----------------------


	function https_only(){ // can be turned off in settings
		if ((FORCE_HTTPS_CONNECTION && $_SERVER ['HTTPS'] != "on" )) {
			header ( "HTTP/1.1 301 Moved Permanently" );
			$redirect = "https://" . $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
			header ( "Location: $redirect" );
			die ();
		}
	}

	function load_includes(){
		include('includes/config.php');
		include('includes/logger.php');
		include('includes/sqlhelper.php');
		include('controller/controller.php');
		include('controller/ajaxController.php');
		//include('model/model.php');
		include('view/view.php');
		include('model/sessionmodel.php');
		include('model/ratingmodel.php');
		include('model/tagmodel.php');
		include('model/registrationmodel.php');
		include('model/questionlistmodel.php');
		include('model/questionmodel.php');
		include('model/categorymodel.php');
		include('model/usermodel.php');
		include('model/answermodel.php');
		include('model/quizlistmodel.php');
		include('model/quizmodel.php');
		include('model/userscoremodel.php');
		include('model/moderationmodel.php');
		include('model/reportmodel.php');
		include('model/reportingmodel.php');
	}

?>
