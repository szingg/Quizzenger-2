<?php

	$questionID= $this->request ['id'];

	//viewQuestionInfo
	$viewQuestionInfo= new View();
	$viewQuestionInfo->setTemplate('questioninfo');

	$question = $questionModel->getQuestion ( $questionID );
	$viewQuestionInfo->assign( 'question', $question );

	$questionHistory = $questionModel->getHistoryForQuestionByID($questionID);
	$viewQuestionInfo->assign( 'questionhistory', $questionHistory );

	$author = $userModel->getUsernameByID ( $question ['user_id'] );
	$viewQuestionInfo->assign ( 'author', $author );
	$viewQuestionInfo->assign ( 'user_id',$question ['user_id']);

	$tags = $tagModel->getAllTagsByQuestionID ( $questionID );
	$viewQuestionInfo->assign ( 'tags', $tags );

	$viewInner->assign( 'questioninfo', $viewQuestionInfo->loadTemplate());


	//innerView
	$viewInner->setTemplate ( 'question' );

	$viewInner->assign ( 'questionID', $questionID );
	$viewInner->assign ( 'question', $question );
	$categoryName = $categoryModel->getNameByID ( $question ['category_id'] );
	$viewInner->assign ( 'category', $categoryName );

	$answers = $answerModel->getAnswersByQuestionID ( $questionID );
	//randomize array
	mt_srand(time());
	$order = array_map(create_function('$val', 'return mt_rand();'), range(1, count($answers)));
	$_SESSION['questionorder'][$questionID] = $order;
	array_multisort($order, $answers);
	$viewInner->assign ( 'answers', $answers );

	$alreadyReported= $reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
	$viewInner->assign ('alreadyreported',$alreadyReported);

	//set message
	if(isset($this->request['questionReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['questionreportDescription'])){
			$reportModel->addReport("question", $question['id'], $this->request['questionreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("question", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}

	//set message
	if(isset($this->request['ratingReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['ratingreportDescription'])){
			$reportModel->addReport("rating", $question['id'], $this->request['ratingreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("rating", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}

	//case user makes quizsession
	if (isset ( $this->request ['session_id'] )) {

		$session_id = $this->request ['session_id'];
		$questionCount= count ( $_SESSION ['questions'. $session_id] );
		$currentCounter= $_SESSION ['counter'. $session_id];
		$progress = round ( 100 * ($currentCounter / $questionCount) );
		$sessionString = "&amp;session_id=" . $session_id . "";
		$viewInner->assign ( 'progress', $progress );
		$viewInner->assign ( 'questioncount', $questionCount );
		$viewInner->assign ( 'currentcounter', $currentCounter );
		$weight= $quizModel->getWeightOfQuestionInQuiz($questionID, $_SESSION['quiz_id'. $session_id] );
		$viewInner->assign ( 'weight', $weight);
	} else {
		$sessionString = "";
	}
	$viewInner->assign ( 'session_id', $sessionString );

	$linkToSolution = '?view=solution&id='.$questionID.$sessionString;
	$viewInner->assign ( 'linkToSolution', $linkToSolution);
?>