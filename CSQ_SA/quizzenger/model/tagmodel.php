<?php

class TagModel {
	private $mysqli;
	private $logger;

	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function getAllTagsByQuestionID($questionID) {
		$result = $this->mysqli->s_query("SELECT t.tag FROM tagtoquestion ttq LEFT JOIN tag t ON (ttq.tag_id = t.id) WHERE question_id=?",array('i'),array($questionID));
		return $this->mysqli->getQueryResultArray($result);
	}

	public function newTag($tag,$questionID) {
		if($tag!=""){
			$tagAlreadyThere = $this->mysqli->s_query("SELECT * FROM tag WHERE tag=?",array('s'),array($tag));
			$tagAlreadyThere = $this->mysqli->getSingleResult($tagAlreadyThere);

			if($tagAlreadyThere==null){
				$tagID=$this->mysqli->s_insert("INSERT INTO tag (tag) VALUES (?)",array('s'),array($tag));
				$this->logger->log ( "Creating Tag with ID :".$tag, Logger::INFO );
			}else{ // tag already exists, don't create new one - we don't want duplicates in the DB
				$tagID=$tagAlreadyThere['id'];
			}
			$this->newTagToQuestion($questionID,$tagID);
		}
	}

	public function removeAllTagsOfQuestionById($questionID) {
		$this->logger->log ( "Removing all Tags of Question:".$questionID, Logger::INFO );
		return $this->mysqli->s_insert("DELETE FROM tagtoquestion WHERE question_id=?",array('i'),array($questionID));
	}

	public function newTagToQuestion($questionID,$tagID) {
		$this->logger->log ( "Creating Tagtoquestion with Tag ID :".$tagID." and Question ID:".$questionID, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO tagtoquestion (question_id, tag_id) VALUES (?,?)",array('i','i'),array($questionID,$tagID));
	}
}

?>
