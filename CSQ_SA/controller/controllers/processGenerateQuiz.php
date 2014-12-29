<?php
	if ($GLOBALS ['loggedin']) {
		$viewInner->setTemplate ( 'blankContent' );
				
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
		
		$questions = $quizModel->generateQuiz($categoryModel,$maxCount,$searchMode,$categories_id,$difficulty);
	
		if(empty($questions)){
			header ( 'Location: ./index.php?view=generatequiz&info=mes_no_results');
			die();
		}	
		
		$session_id = $quizModel->getNewSessionId (-1);
				
		$_SESSION ['quiz_id'. $session_id] = -1;
		$_SESSION ['questions'. $session_id] =$questions;
		$_SESSION ['counter'. $session_id] = 0;

		
		header ( 'Location: ./index.php?view=question&id='.$_SESSION ['questions'. $session_id] [0]."&session_id=".$session_id);
		die();
		
	} else {
		header ( 'Location: ./index.php?view=login');
		die ();
	}
	
?>