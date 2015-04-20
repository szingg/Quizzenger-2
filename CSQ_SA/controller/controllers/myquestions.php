<?php
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;

	PermissionUtility::checkLogin();

	$viewInner->setTemplate ( 'questionlist' );

	$questions = $questionListModel->getQuestionsByUserID ( $_SESSION ['user_id'] );
	$viewInner->assign ( 'questions', $questions );
	$viewInner->assign ( 'myquestions', "myquestions" );
	$quizzes = $quizListModel->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
	$viewInner->assign ( 'quizzes', $quizzes );


	include("helper/question_tag.php");

	if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] ) && isset ( $this->request ['quiz_id'] ) && $this->request ['quiz_id'] > 0) {
		foreach ( $this->request ['addtoquiz'] as $question ) {
			$quizModel->addQuestionToQuiz ( $this->request ['quiz_id'], $question );
		}
	}
	if(isset ($this->request['id'])){
		$viewInner->assign ('addedQuestion', $this->request['id']); // shows row green in table
		$viewInner->assign('pointsearned',QUESTION_CREATED_SCORE);
	}
?>