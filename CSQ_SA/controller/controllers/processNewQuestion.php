<?php
	use \quizzenger\controlling\EventController as EventController;

	$viewInner->setTemplate ( 'blankContent' );

	if(isset($_POST['opquestion_form_chosenCategory'],$_POST['opquestion_form_chosenCategoryName'],$_POST['opquestion_form_chosenCategory_parent_id']) && $GLOBALS ['loggedin']){
		$chosenCategory= $_POST['opquestion_form_chosenCategory'];
		if($_POST['opquestion_form_chosenCategory']==-1){ // user wants a new category
			$chosenCategory=$categoryModel->createCategory($_POST['opquestion_form_chosenCategoryName'],$_POST['opquestion_form_chosenCategory_parent_id']);
		}

		$addedQuestion = $questionModel->opQuestionWithAnswers ( $answerModel,$categoryModel, $tagModel, "new", $chosenCategory);
		if($addedQuestion > 0)
		{
			EventController::fire('question-created', $_SESSION['user_id'], [
				'category' => $chosenCategory
			]);
			header ( 'Location: ./index.php?view=myquestions&id='.$addedQuestion );
		}
	}else{
		$this->logger->log ( "Invalid POST request made (processNewQuestion)", Logger::WARNING );
		die ( 'Invalid Request. Please stop this' );
	}
?>
