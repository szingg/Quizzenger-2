<?php
	//Question Report
	if(isset($this->request['questionReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['questionreportDescription'])){
			$reportModel->addReport("question", $question['id'], $this->request['questionreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("question", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}
	//Comment Report
	if(isset($this->request['ratingReport']) && $GLOBALS ['loggedin']){
		$viewInner->assign ('message', mes_sent_report);
		if(isset($this->request['ratingreportDescription'])){
			$reportModel->addReport("rating", $this->request['ratingReport'], $this->request['ratingreportDescription'], $_SESSION['user_id'], $question['category_id']);
		} else {
			$reportModel->addReport("rating", $this->request['ratingReport'], NULL, $_SESSION['user_id'], $question['category_id']);
		}
	}
		
?>