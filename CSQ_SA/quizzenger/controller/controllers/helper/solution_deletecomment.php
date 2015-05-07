<?php
	if(isset($this->request['ratingRemove']) && $GLOBALS ['loggedin'] ){
		if($userIsModHere){
			if(isset($this->request['removalExplanation'])){
				$ratingModel->removeComment($this->request['ratingRemove'],$_SESSION ['username'],$this->request['removalExplanation']);
			} else {
				$ratingModel->removeComment($this->request['ratingRemove'],$_SESSION ['username'],"");
			}
		}else{
			$this->logger->log ( "User tried to remove comment but is no mod!", Logger::WARNING );
		}
	}
?>