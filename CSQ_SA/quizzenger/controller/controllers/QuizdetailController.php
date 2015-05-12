<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	class QuizdetailController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			PermissionUtility::checkLogin();
			$qid = $this->request ['quizid'];

			ModelCollection::quizModel()->checkIfQuizIDExists($qid);  // if not exists -> redirect db_query_failed

			if (! ModelCollection::quizModel()->userIDhasPermissionOnQuizID ( $qid , $_SESSION ['user_id'] )) {
				NavigationUtility::redirect('?view=error&err=err_not_authorized_quizdetail');
			}

			$this->view->setTemplate ( 'quizdetail' );

			$performances = ModelCollection::quizModel()->getPerformances ( $qid, ModelCollection::userModel() );
			$questions = ModelCollection::quizModel()->getQuestionsByQuizID ( $qid );
			$quizinfo = array (
					'quizid' => $qid,
					'quizname' => ModelCollection::quizModel()->getQuizName ( $qid )
			);

			$this->view->assign ( 'performances', $performances );
			$this->view->assign ( 'questions', $questions );
			$this->view->assign ( 'quizinfo', $quizinfo );
			return $this->view->loadTemplate();
		}

	} // class QuizdetailController
} // namespace quizzenger\controller\controllers

?>