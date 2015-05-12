<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	class QuizendController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			if (isset ( $this->request ['session_id'] )) {
				$session_id = $this->request ['session_id'];

				$this->view->setTemplate ( 'quizend' );

				$quiz_id=$_SESSION['quiz_id'. $session_id];
				$score = ModelCollection::quizModel()->getSingleChoiceScore ( $session_id, $_SESSION ['quiz_id'. $session_id] );
				if($quiz_id==-1){
					$questions=$_SESSION ['questions'.$session_id];
					$maxScore=	count($questions);
				}else{
					$maxScore = ModelCollection::quizModel()->getMaxSingleChoiceScore ( $_SESSION ['quiz_id'. $session_id] );
				}

				$this->view->assign ( 'score', $score );
				$this->view->assign ( 'maxScore', $maxScore );
				$this->view->assign ('quiz_id', $quiz_id);
				$this->view->assign ('session_id',$session_id);

			}else {
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_db_query_failed');
				NavigationUtility::redirectToErrorPage();
			}
			return $this->view->loadTemplate();
		}

	} // class QuizendController
} // namespace quizzenger\controller\controllers
?>