<?php
		use \quizzenger\utilities\PermissionUtility as PermissionUtility;
		use \quizzenger\utilities\NavigationUtility as NavigationUtility;

		PermissionUtility::checkLogin();
		$qid = $this->request ['quizid'];

		$quizModel->checkIfQuizIDExists($qid);  // if not exists -> redirect db_query_failed

		if (! $quizModel->userIDhasPermissionOnQuizID ( $qid , $_SESSION ['user_id'] )) {
			NavigationUtility::redirect('?view=error&err=err_not_authorized_quizdetail');
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