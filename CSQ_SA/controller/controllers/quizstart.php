<?php
	$viewInner->setTemplate ( 'quizstart' );

	$session_id = $quizModel->getNewSessionId ($this->request ['quizid']);

	$_SESSION ['quiz_id'. $session_id] = $this->request ['quizid'];
	$_SESSION ['questions'. $session_id] = $quizModel->getQuestionArray ( $this->request ['quizid'] );
	$_SESSION ['counter'. $session_id] = 0;

	if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
		$firstUrl = "?view=question&amp;id=" . $_SESSION ['questions'. $session_id] [0] . "&amp;session_id=". $session_id;
	} else {
		$firstUrl = "?view=quizend";
	}

	$quizinfo = array (
			'quizid' => $this->request ['quizid'],
			'quizname' => $quizModel->getQuizName ( $this->request ['quizid'] ),
			'firstUrl' => $firstUrl
	);

	$viewInner->assign ( 'quizinfo', $quizinfo );
?>