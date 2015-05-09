<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class MyquizzesController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			PermissionUtility::checkLogin();

			$this->view->setTemplate ( 'quizlist' );

			if (isset ( $this->request ['quizname'] )) { // Neues Quiz erstellen
				$quiz_id = ModelCollection::quizModel()->createQuiz ( $this->request ['quizname'], $_SESSION ['user_id'] );
				if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] )) {
					foreach ( $this->request ['addtoquiz'] as $question ) {
						ModelCollection::quizModel()->addQuestionToQuiz ( $quiz_id, $question );
					}
				}
				NavigationUtility::redirect('?view=mycontent&quizid='.$quiz_id.'&info=mes_add_questions_to_quiz#myquizzes');
			}

			if(isset($this->request['copyquiz'])){ //COPYING QUIZ
				$copiedQuizID= ModelCollection::quizModel()->copyQuiz($_SESSION['user_id'], $this->request['copyquiz']);
				NavigationUtility::redirect('?view=mycontent&quizid='.$copiedQuizID.'#myquizzes');
			}

			if(isset($this->request['savegeneratedquiz'])){ // SAVING GENERATED QUIZ
				$questions=$_SESSION ['questions'.$this->request['savegeneratedquiz']];
				$copiedQuizID= ModelCollection::quizModel()->saveGeneratedQuiz($_SESSION['user_id'], $questions);
				NavigationUtility::redirect('?view=mycontent&quizid='.$copiedQuizID.'#myquizzes');
			}

			if(isset($this->request['quizNameField'])){ //EDIT QUIZ NAME
				$editedQuizId = $this->request['editQuizNameID'];
				if(isset($this->request['editQuizNameID'])
						&& ModelCollection::quizModel()->userIDhasPermissionOnQuizID($editedQuizId, $_SESSION['user_id']) ){
					ModelCollection::quizModel()->setQuizName($this->request['editQuizNameID'],$this->request['quizNameField']);
					NavigationUtility::redirect('?view=mycontent&quizid='.$editedQuizId.'#myquizzes');
				}
			}

			//markEditedQuiz
			if(isset($this->request['quizid'])){
				$this->view->assign ( 'markQuiz', $this->request['quizid']);
			}

			$quizzesList = ModelCollection::quizListModel()->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
			$quizzes = array ();
			foreach ( $quizzesList as $key => $quiz ) {
				$quizzes [$key] ['id'] = $quiz ['id'];
				$quizzes [$key] ['name'] = $quiz ['name'];
				$quizzes [$key] ['performances'] = ModelCollection::quizModel()->getNumberOfPerformances ( $quiz ['id'] );
				$quizzes [$key] ['questions'] = ModelCollection::quizModel()->getNumberOfQuestions ( $quiz ['id'] );
			}
			$this->view->assign ( 'quizzes', $quizzes);

			return $this->view->loadTemplate();
		}

	} // class MyquizzesController
} // namespace quizzenger\controller\controllers

?>