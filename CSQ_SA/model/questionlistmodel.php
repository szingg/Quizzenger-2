<?php

class QuestionListModel{

	var $mysqli;
	var $logger;
	
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	
	public function getAllQuestions(){ 
		$result = $this->mysqli->s_query("SELECT * FROM question",array(),array(),true);
		while($row = $result->fetch_array(MYSQLI_ASSOC)){
			$this->entries[]=$row;
		}
		return $this->entries;
	}
	
	
	function getQuestionsByCategoryID($id){
		$result = $this->mysqli->s_query("SELECT * FROM question WHERE category_id=?",array('i'),array($id));
		return $result;
	}
	
	function getQuestionsByUserID($id){
		$result = $this->mysqli->s_query("SELECT * FROM question WHERE user_id=?",array('i'),array($id));
		return $result;
	}
	
	function getQuestionsByUserIDCount($id){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM question WHERE user_id=?",array('i'),array($id));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}
	
	function searchQuestions($searchText){
		$pattern = "%". $searchText ."%";
		$result = $this->mysqli->s_query("SELECT q.*
											FROM question q
											LEFT JOIN (
												SELECT question_id, GROUP_CONCAT( tag.tag SEPARATOR ', ' ) AS tags
												FROM tagtoquestion ttq
												INNER JOIN tag ON tag.id = ttq.tag_id
												GROUP BY question_id
											) t ON q.id = t.question_id
											WHERE q.questiontext LIKE (?)
											OR t.tags LIKE (?);",array('s','s'),array($pattern, $pattern));
		return $result;
	}

}
?>
