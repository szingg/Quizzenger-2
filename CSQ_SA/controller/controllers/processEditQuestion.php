<?php
	// only author and mods can edit
	if (! $questionModel->userIDhasPermissionOnQuestionID ( $this->request ['opquestion_form_question_id'], $_SESSION ['user_id'] )) {
		header ( 'Location: ./index.php?view=error&err=err_not_authorized_questionedit' );
		die ();
	}

	$viewInner->setTemplate ( 'blankContent' );

	if(!(isset($_POST['opquestion_form_question_id'])) || !(isset($_POST['opquestion_form_chosenCategory']))){
		$this->logger->log ( "Invalid POST request made (processEditQuestion)", Logger::WARNING );
		die ( 'Invalid Request. Please stop this' );
	}

	$questionModel->newQuestionHistory($_POST['opquestion_form_question_id'],$_SESSION ['user_id'],"Editiert");
	$questionModel->opQuestionWithAnswers ( $answerModel,$categoryModel, $tagModel, "edit",$_POST['opquestion_form_chosenCategory']);

	header ( 'Location: ./index.php?view=question&id='.$_POST['opquestion_form_question_id']);
?>