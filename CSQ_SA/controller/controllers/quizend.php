<?php
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
		header('Location: ./index.php?view=error&err=err_db_query_failed');
	}
?>