<?php
class QuizListModel {
	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function getUserQuizzesByUserID($userId) {
		$result = $this->mysqli->s_query("SELECT * FROM quiz WHERE `user_id` =?",array('i'),array($userId));
		return $this->mysqli->getQueryResultArray($result);
	}

	public function getNumberOfQuestions($quizId) {
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM quiztoquestion WHERE `quiz_id` =?",array('i'),array($quizId));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}

	public function getUserQuizzesByUserIDCount($userId) {
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM quiz WHERE `user_id` = ?",array('i'),array($userId));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}
}
?>
