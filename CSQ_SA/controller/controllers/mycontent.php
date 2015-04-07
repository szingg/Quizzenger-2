<?php
	if (! $GLOBALS ['loggedin']) {
		header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
		die ();
	}

	include("myquestions.php");
	include("myquizzes.php");
	loadGameView($this->mysqli, $viewInner);

	$viewInner->setTemplate ( 'mycontent' );

	function loadGameView($mysqli, $viewInner){
		$gameView = new \View();
		$gameView->setTemplate ( 'gamelist' );

		//$this->sqlhelper = new SqlHelper(log::get());
		$gameModel = new \quizzenger\gamification\model\GameModel($mysqli);
		$games = $gameModel->getGamesByUser($_SESSION['user_id']);
		$gameView->assign( 'games', $games);

		$viewInner->assign ( 'gamelist', $gameView->loadTemplate() );
	}
?>