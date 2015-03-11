<?php
class QuizListModel{

	var $mysqli;
	var $logger;

	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	function getUserQuizzesByUserID($userId){
		$result = $this->mysqli->s_query("SELECT * FROM quiz WHERE `user_id` =?",array('i'),array($userId));
		return $this->mysqli->getQueryResultArray($result);
	}


	function getNumberOfQuestions($quizId){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM quiztoquestion WHERE `quiz_id` =?",array('i'),array($quizId));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}

	function getUserQuizzesByUserIDCount($userId){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM quiz WHERE `user_id` = ?",array('i'),array($userId));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}


}
?>