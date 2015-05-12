<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	class ProcessEditQuestionController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			// only author and mods can edit
			if (! ModelCollection::questionModel()->userIDhasPermissionOnQuestionID ( $this->request ['opquestion_form_question_id'], $_SESSION ['user_id'] )) {
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized_questionedit');
				NavigationUtility::redirectToErrorPage();
			}

			$this->view->setTemplate ( 'blankContent' );

			if(!(isset($_POST['opquestion_form_question_id'])) || !(isset($_POST['opquestion_form_chosenCategory']))){
				$this->logger->log ( "Invalid POST request made (processEditQuestion)", Logger::WARNING );
				die ( 'Invalid Request. Please stop this' );
			}

			ModelCollection::questionModel()->newQuestionHistory($_POST['opquestion_form_question_id'],$_SESSION ['user_id'],"Editiert");
			ModelCollection::questionModel()->opQuestionWithAnswers ("edit",$_POST['opquestion_form_chosenCategory']);

			NavigationUtility::redirect('./index.php?view=question&id='.$_POST['opquestion_form_question_id']);
		}

	} // class ProcessEditQuestionController
} // namespace quizzenger\controller\controllers
?>