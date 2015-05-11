<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	if (isset ( $this->request ['session_id'] )) {
		$session_id = $this->request ['session_id'];

		$viewInner->setTemplate ( 'quizend' );

		$quiz_id=$_SESSION['quiz_id'. $session_id];
		$score = $quizModel->getSingleChoiceScore ( $session_id, $_SESSION ['quiz_id'. $session_id] );
		if($quiz_id==-1){
			$questions=$_SESSION ['questions'.$session_id];
			$maxScore=	count($questions);
		}else{
			$maxScore = $quizModel->getMaxSingleChoiceScore ( $_SESSION ['quiz_id'. $session_id] );
		}

		$viewInner->assign ( 'score', $score );
		$viewInner->assign ( 'maxScore', $maxScore );
		$viewInner->assign ('quiz_id', $quiz_id);
		$viewInner->assign ('session_id',$session_id);

	}else {
		MessageQueue::pushPersistent($_SESSION['user_id'], 'err_db_query_failed');
		NavigationUtility::redirectToErrorPage();
	}
?>