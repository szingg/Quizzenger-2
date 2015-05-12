<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	class ProcessGenerateQuizController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			PermissionUtility::checkLogin();
			$this->view->setTemplate ( 'blankContent' );

			if(isset($_POST['quiz_generator_form_category'])){
				$categories_id= $_POST['quiz_generator_form_category'];
			}else{
				$categories_id=null;
			}
			if(isset($_POST['quiz_generator_form_difficulty'])){
				$difficulty=$_POST['quiz_generator_form_difficulty'] ;
			}else{
				$difficulty =null;
			}
			$maxCount= $_POST['quiz_generator_form_count'];
			$searchMode= $_POST['quiz_generator_form_mode'];

			$questions = ModelCollection::quizModel()->generateQuiz(ModelCollection::categoryModel(),$maxCount,$searchMode,$categories_id,$difficulty);

			if(empty($questions)){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'mes_no_results');
				NavigationUtility::redirect('./index.php?view=generatequiz');
			}

			$session_id = ModelCollection::quizModel()->getNewSessionId (-1);

			$_SESSION ['quiz_id'. $session_id] = -1;
			$_SESSION ['questions'. $session_id] =$questions;
			$_SESSION ['counter'. $session_id] = 0;

			NavigationUtility::redirect('./index.php?view=question&id='.$_SESSION ['questions'. $session_id] [0]."&session_id=".$session_id);

			return $this->view->loadTemplate();
		}

	} // class ProcessGenerateQuizController
} // namespace quizzenger\controller\controllers

?>