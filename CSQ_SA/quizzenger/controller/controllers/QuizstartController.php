<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class QuizstartController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			$this->view->setTemplate ( 'quizstart' );

			$session_id = ModelCollection::quizModel()->getNewSessionId ($this->request ['quizid']);

			$_SESSION ['quiz_id'. $session_id] = $this->request ['quizid'];
			$_SESSION ['questions'. $session_id] = ModelCollection::quizModel()->getQuestionArray ( $this->request ['quizid'] );
			$_SESSION ['counter'. $session_id] = 0;

			if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
				$firstUrl = "?view=question&id=" . $_SESSION ['questions'. $session_id] [0] . "&session_id=". $session_id;
			} else {
				$firstUrl = "?view=quizend";
			}

			$quizinfo = array (
					'quizid' => $this->request ['quizid'],
					'quizname' => ModelCollection::quizModel()->getQuizName ( $this->request ['quizid'] ),
					'firstUrl' => $firstUrl
			);

			$this->view->assign ( 'quizinfo', $quizinfo );
			return $this->view->loadTemplate();
		}

	} // class QuizstartController
} // namespace quizzenger\controller\controllers

?>