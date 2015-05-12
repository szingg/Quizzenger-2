<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\controlling\EventController as EventController;

	class ProcessNewQuestionController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'blankContent' );

			if(isset($_POST['opquestion_form_chosenCategory'],$_POST['opquestion_form_chosenCategoryName'],$_POST['opquestion_form_chosenCategory_parent_id']) && $GLOBALS ['loggedin']){
				$chosenCategory= $_POST['opquestion_form_chosenCategory'];
				if($_POST['opquestion_form_chosenCategory']==-1){ // user wants a new category
					$chosenCategory=ModelCollection::categoryModel()->createCategory($_POST['opquestion_form_chosenCategoryName'],$_POST['opquestion_form_chosenCategory_parent_id']);
				}

				$addedQuestion = ModelCollection::questionModel()->opQuestionWithAnswers ("new", $chosenCategory);
				if($addedQuestion > 0)
				{
					EventController::fire('question-created', $_SESSION['user_id'], [
						'category' => $chosenCategory
					]);
					NavigationUtility::redirect('./index.php?view=mycontent&id='.$addedQuestion);
				}
			}else{
				$this->logger->log ( "Invalid POST request made (processNewQuestion)", Logger::WARNING );
				die ( 'Invalid Request. Please stop this' );
			}

			return $this->view->loadTemplate();
		}

	} // class ProcessNewQuestionController
} // namespace quizzenger\controller\controllers

?>
