<?php
		checkLogin();
		$qid = $this->request ['quizid'];

		$quizModel->checkIfQuizIDExists($qid);  // if not exists -> redirect db_query_failed

		if (! $quizModel->userIDhasPermissionOnQuizID ( $qid , $_SESSION ['user_id'] )) {
			header ( 'Location: ./index.php?view=error&err=err_not_authorized_quizdetail' );
			die ();
		}

		$viewInner->setTemplate ( 'quizdetail' );

		$performances = $quizModel->getPerformances ( $qid, $userModel );
		$questions = $quizModel->getQuestionsByQuizID ( $qid );
		$quizinfo = array (
				'quizid' => $qid,
				'quizname' => $quizModel->getQuizName ( $qid )
		);

		$viewInner->assign ( 'performances', $performances );
		$viewInner->assign ( 'questions', $questions );
		$viewInner->assign ( 'quizinfo', $quizinfo );
?>