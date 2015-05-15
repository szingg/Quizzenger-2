<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\controller\controllers\helper\QuestionTagHelper as QuestionTagHelper;

	class MyquestionsController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			PermissionUtility::checkLogin();

			$this->view->setTemplate ( 'questionlist' );

			$questions = ModelCollection::questionListModel()->getQuestionsByUserID ( $_SESSION ['user_id'] );
			$this->view->assign ( 'questions', $questions );
			$this->view->assign ( 'myquestions', "myquestions" );
			$quizzes = ModelCollection::quizListModel()->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
			$this->view->assign ( 'quizzes', $quizzes );


			$questionTagHelper = new QuestionTagHelper($this->view);
			$questionTagHelper->process($questions);

			if (isset ( $this->request ['addtoquiz'] ) && is_array ( $this->request ['addtoquiz'] ) && isset ( $this->request ['quiz_id'] ) && $this->request ['quiz_id'] > 0) {
				foreach ( $this->request ['addtoquiz'] as $question ) {
					ModelCollection::quizModel()->addQuestionToQuiz ( $this->request ['quiz_id'], $question );
				}
			}
			if(isset ($this->request['id'])){
				$this->view->assign ('addedQuestion', $this->request['id']); // shows row green in table
			}
			return $this->view->loadTemplate();
		}

	} // class MyquestionsController
} // namespace quizzenger\controller\controllers

?>