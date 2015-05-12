<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\controller\controllers\helper\QuestionTagHelper as QuestionTagHelper;

	class QuestionlistController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
		$this->view->setTemplate ( 'questionlist' );
			if (isset ( $this->request ['category'] )) {
				$questions = ModelCollection::questionListModel()->getQuestionsByCategoryID ( $this->request ['category'] );
			} else if (isset ( $this->request ['user'] )) {
				$questions = ModelCollection::questionListModel()->getQuestionsByUserID ( $this->request ['user'] );
			} else if (isset ($this->request ['search'])){
				$questions = ModelCollection::questionListModel()->searchQuestions($this->request['search']);
			}

			$questionTagHelper = new QuestionTagHelper($this->view);
			$questionTagHelper->process($questions);
//			include("helper/question_tag.php");

			$this->view->assign('questions', $questions);
			$this->view->assign('template', 'questionlist');

			if ($GLOBALS ['loggedin']) {
				$quizzes = ModelCollection::quizListModel()->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
				$this->view->assign ( 'quizzes', $quizzes );

				if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] ) && isset ( $this->request ['quiz_id'] ) && $this->request ['quiz_id'] > 0) {
					$this->view->assign ( 'message', count ( $this->request ['addtoquiz'] ) . " Fragen wurden hinzugef&uuml;gt." );
					foreach ( $this->request ['addtoquiz'] as $question ) {
						ModelCollection::quizModel()->addQuestionToQuiz ( $this->request ['quiz_id'], $question );
					}
				}
			}
			return $this->view->loadTemplate();
		}

	} // class QuestionlistController
} // namespace quizzenger\controller\controllers
?>