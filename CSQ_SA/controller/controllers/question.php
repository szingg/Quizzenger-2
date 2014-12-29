<?php

	$viewInner->setTemplate ( 'question' );
	$questionID= $this->request ['id'];
	
	$question = $questionModel->getQuestion ( $questionID );
	$viewInner->assign ( 'question', $question );
	$questionHistory = $questionModel->getHistoryForQuestionByID($questionID);
	$viewInner->assign ( 'questionhistory', $questionHistory );
	 
	$categoryName = $categoryModel->getNameByID ( $question ['category_id'] );
	$viewInner->assign ( 'category', $categoryName );
	
	$answers = $answerModel->getAnswersByQuestionID ( $questionID );
	$viewInner->assign ( 'answers', $answers );
	
	$viewInner->assign ( 'questionID', $questionID );
	
	$author = $userModel->getUsernameByID ( $question ['user_id'] );
	$viewInner->assign ( 'author', $author );
	$viewInner->assign ( 'user_id',$question ['user_id']);
	
	$tags = $tagModel->getAllTagsByQuestionID ( $questionID );
	$viewInner->assign ( 'tags', $tags );
	
	$alreadyReported= $reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
	$viewInner->assign ('alreadyreported',$alreadyReported);
	
	
	if(isset($this->request['questionReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['questionreportDescription'])){
			$reportModel->addReport("question", $question['id'], $this->request['questionreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("question", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}
		
	if(isset($this->request['ratingReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['ratingreportDescription'])){
			$reportModel->addReport("rating", $question['id'], $this->request['ratingreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("rating", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}
	
	
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
?>