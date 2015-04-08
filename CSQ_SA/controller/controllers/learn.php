<?php
	if (! $GLOBALS ['loggedin']) {
		header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
		die ();
	}
	/*
	$viewInner->setTemplate ( 'generatequiz' );
	if (isset ( $this->request ['type'] )) {
		$type = $this->request ['type'];
	} else {
		$type = SINGLECHOICE_TYPE;
	}
	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );
	$totalCount = $categoryModel->getTotalQuestionCount();
	$viewInner->assign ( 'totalCount', $totalCount);
	$viewInner->assign ( 'roots', $roots );
	$viewInner->assign ( 'mode', 'generator' );
	$viewInner->assign ( 'type', $type );
	*/
	//$this->viewOuter->assign ( 'csq_content', $viewInner->loadTemplate () );


	$viewInner->setTemplate ( 'learn' ); //learn contains Tabs
	//Quiz View
	$viewQuiz = new View();
	$viewQuiz->setTemplate ( 'generatequiz' );

	if (isset ( $this->request ['type'] )) {
		$type = $this->request ['type'];
	} else {
		$type = SINGLECHOICE_TYPE;
	}
	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );
	$totalCount = $categoryModel->getTotalQuestionCount();

	$viewQuiz->assign ( 'totalCount', $totalCount);
	$viewQuiz->assign ( 'roots', $roots );
	$viewQuiz->assign ( 'mode', 'generator' );
	$viewQuiz->assign ( 'type', $type );
	$viewInner->assign( 'quiz_tab' , $viewQuiz->loadTemplate());
	//Game View
	$viewGame = new View();
	$viewGame->setTemplate ( 'gamelobby' );
	$GameModel = new \quizzenger\gamification\model\GameModel($this->mysqli, $quizModel);
	$openGames = $GameModel->getOpenGames();
	$viewGame->assign ( 'openGames', $openGames );
	$activeGames = $GameModel->getActiveGamesByUser($_SESSION ['user_id']);
	$viewGame->assign ( 'activeGames', $activeGames ); //if(count($activeGames) > 0)
	$quizzes = $quizListModel->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
	$viewGame->assign ( 'quizzes', $quizzes );

	$viewInner->assign( 'game_tab' , $viewGame->loadTemplate());




?>