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
			$viewInner->assign ( 'markQuiz', $quiz_id );
		}
			
		if(isset($this->request['copyquiz'])){ //COPYING QUIZ
			$copiedQuizID=$quizModel->copyQuiz($_SESSION['user_id'], $this->request['copyquiz']);
			$viewInner->assign ( 'markQuiz', $copiedQuizID );
		}
			
		if(isset($this->request['savegeneratedquiz'])){ // SAVING GENERATED QUIZ
			$questions=$_SESSION ['questions'.$this->request['savegeneratedquiz']];
			$copiedQuizID=$quizModel->saveGeneratedQuiz($_SESSION['user_id'], $questions);
			$viewInner->assign ( 'markQuiz', $copiedQuizID );
		}		
		
		if(isset($this->request['quizNameField']) && $GLOBALS ['loggedin']){
			if(isset($this->request['editQuizNameID'])){ // TODO check owner 
				$quizModel->setQuizName($this->request['editQuizNameID'],$this->request['quizNameField']);
			} 
			$viewInner->assign ( 'markQuiz', $this->request['editQuizNameID']);
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