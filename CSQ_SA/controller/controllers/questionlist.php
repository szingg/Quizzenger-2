<?php
	$viewInner->setTemplate ( 'questionlist' );
	if (isset ( $this->request ['category'] )) {
		$questions = $questionListModel->getQuestionsByCategoryID ( $this->request ['category'] );
	} else if (isset ( $this->request ['user'] )) {
		$questions = $questionListModel->getQuestionsByUserID ( $this->request ['user'] );
	} else if (isset ($this->request ['search'])){
		$questions = $questionListModel->searchQuestions($this->request['search']);
	}

	include("helper/question_tag.php");

	$viewInner->assign ( 'questions', $questions );
	if ($GLOBALS ['loggedin']) {
		$quizzes = $quizListModel->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
		$viewInner->assign ( 'quizzes', $quizzes );

		if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] ) && isset ( $this->request ['quiz_id'] ) && $this->request ['quiz_id'] > 0) {
			$viewInner->assign ( 'message', count ( $this->request ['addtoquiz'] ) . " Fragen wurden hinzugef&uuml;gt." );
			foreach ( $this->request ['addtoquiz'] as $question ) {
				$quizModel->addQuestionToQuiz ( $this->request ['quiz_id'], $question );
			}
		}
	}
?>