<?php

class AnswerModel{
	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function getAnswersByQuestionID($qid) {
		$result = $this->mysqli->s_query("SELECT * FROM answer WHERE question_id=? ",array('i'),array($qid),true);
		return $this->mysqli->getQueryResultArray($result);
	}

	public function getCorrectAnswer($qid) {
		$result = $this->mysqli->s_query("SELECT * FROM answer WHERE question_id=? AND correctness = 100",array('i'),array($qid));
		return $this->mysqli->getSingleResult($result)['id'];
	}

	public function newAnswer($correctness,$text,$explanation, $question_id) {
		$this->logger->log ( "Create Answer for Question ID: ".$question_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO answer (correctness, text, explanation, question_id) VALUES (?, ?, ?, ?)",array('i','s','s','i'),array($correctness,$text,$explanation, $question_id));
	}

	public function editAnswer($correctness,$text,$explanation, $answer_id) {
		$this->logger->log ( "Updating Answer with ID ".$answer_id, Logger::INFO );
		return $this->mysqli->s_query("UPDATE answer SET correctness=?, text=?, explanation=?  WHERE id=? ",array('i','s','s','i'),array($correctness,$text,$explanation,$answer_id));
	}
}
?>
