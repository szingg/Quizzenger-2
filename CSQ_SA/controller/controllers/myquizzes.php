<?php
	if ($GLOBALS ['loggedin']) {
		$viewInner->setTemplate ( 'quizlist' );

		if (isset ( $this->request ['quizname'] )) { // Neues Quiz erstellen
			$quiz_id = $quizModel->createQuiz ( $this->request ['quizname'], $_SESSION ['user_id'] );
			if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] )) {
				foreach ( $this->request ['addtoquiz'] as $question ) {
					$quizModel->addQuestionToQuiz ( $quiz_id, $question );
				}
			}
			redirect('?view=mycontent&quizid='.$quiz_id.'&info=mes_add_questions_to_quiz#myquizzes');
		}

		if(isset($this->request['copyquiz'])){ //COPYING QUIZ
			$copiedQuizID=$quizModel->copyQuiz($_SESSION['user_id'], $this->request['copyquiz']);
			redirect('?view=mycontent&quizid='.$copiedQuizID.'#myquizzes');
		}

		if(isset($this->request['savegeneratedquiz'])){ // SAVING GENERATED QUIZ
			$questions=$_SESSION ['questions'.$this->request['savegeneratedquiz']];
			$copiedQuizID=$quizModel->saveGeneratedQuiz($_SESSION['user_id'], $questions);
			redirect('?view=mycontent&quizid='.$copiedQuizID.'#myquizzes');
		}

		if(isset($this->request['quizNameField'])){ //EDIT QUIZ NAME
			$editedQuizId = $this->request['editQuizNameID'];
			if(isset($this->request['editQuizNameID'])
					&& $quizModel->userIDhasPermissionOnQuizID($editedQuizId, $_SESSION['user_id']) ){
				$quizModel->setQuizName($this->request['editQuizNameID'],$this->request['quizNameField']);
				redirect('?view=mycontent&quizid='.$editedQuizId.'#myquizzes');
			}
		}

		//markEditedQuiz
		if(isset($this->request['quizid'])){
			$viewInner->assign ( 'markQuiz', $this->request['quizid']);
		}

		$quizzesList = $quizListModel->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
		$quizzes = array ();
		foreach ( $quizzesList as $key => $quiz ) {
			$quizzes [$key] ['id'] = $quiz ['id'];
			$quizzes [$key] ['name'] = $quiz ['name'];
			$quizzes [$key] ['performances'] = $quizModel->getNumberOfPerformances ( $quiz ['id'] );
			$quizzes [$key] ['questions'] = $quizModel->getNumberOfQuestions ( $quiz ['id'] );
		}
	} else {
		header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
		die ();
	}

	$viewInner->assign ( 'quizzes', $quizzes);
?>