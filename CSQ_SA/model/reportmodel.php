<?php
class ReportModel{
	var $mysqli;
	var $logger;
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	function checkIfUserAlreadyDoneReport($type, $object_id, $by_user_id) {
		if($type=="question"){
			$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM report WHERE question_id=? AND by_user_id=? AND doneon IS NULL )",array('i','i'),array($object_id,$by_user_id));
		} else if($type=="rating"){
			$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM report WHERE rating_id=? AND by_user_id=? AND doneon IS NULL )",array('i','i'),array($object_id,$by_user_id));
		} else if($type=="user"){
			$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM report WHERE user_id=? AND by_user_id=? AND doneon IS NULL )",array('i','i'),array($object_id,$by_user_id));
		}
		$result= array_values($this->mysqli->getSingleResult($result));
		return ($result[0]=="1");
	}

	function addReport($type, $object_id, $message, $by_user_id, $category_id = "NULL"){
		if(! $this->checkIfUserAlreadyDoneReport($type,$object_id,$by_user_id)){
			if($type=="question"){
				return $this->mysqli->s_insert("INSERT INTO report (question_id, category_id, message, by_user_id) VALUES (?, ?, ?, ?)", array('i', 'i', 's', 'i'), array($object_id, $category_id, $message, $by_user_id));
			} else if($type=="rating"){
				return $this->mysqli->s_insert("INSERT INTO report (rating_id, category_id, message, by_user_id) VALUES (?, ?, ?, ?)", array('i', 'i', 's', 'i'), array($object_id, $category_id, $message, $by_user_id));
			} else if($type=="user"){
				return $this->mysqli->s_insert("INSERT INTO report (user_id, message, by_user_id) VALUES (?, ?, ?)", array('i',  's', 'i'), array($object_id, $message, $by_user_id));
			}
		}else{
			$this->logger->log("Can't add multiple reports to one object for one user. obj id:". $object_id, Logger::INFO);
		}
	}

	function getQuestionReportsByCategory($category){
		$result = $this->mysqli->s_query("SELECT report.question_id, question.user_id, question.questiontext, COUNT(*), user.username FROM report INNER JOIN question ON report.question_id=question.id INNER JOIN user ON question.user_id=user.id WHERE report.category_id=? AND report.question_id IS NOT NULL AND doneon IS NULL GROUP BY report.question_id", array('i'), array($category));
		return $this->mysqli->getQueryResultArray($result);
	}

	function getRatingReportsByCategory($category){
		$result = $this->mysqli->s_query("SELECT report.rating_id, rating.user_id, rating.comment, COUNT(*), user.username FROM report INNER JOIN rating ON report.rating_id=rating.id INNER JOIN user ON rating.user_id=user.id WHERE report.category_id=? AND report.rating_id IS NOT NULL AND doneon IS NULL GROUP BY report.rating_id", array('i'), array($category));
		return $this->mysqli->getQueryResultArray($result);
	}

	function getQuestionReportsByUser($user_id){
		$result = $this->mysqli->s_query("SELECT report.question_id, question.questiontext, COUNT(*) FROM report INNER JOIN question ON report.question_id=question.id WHERE question.user_id=? AND report.question_id IS NOT NULL AND doneon IS NULL GROUP BY report.question_id", array('i'), array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}

	function getRatingReportsByUser($user_id){
		$result = $this->mysqli->s_query("SELECT report.rating_id, rating.comment, COUNT(*) FROM report INNER JOIN rating ON report.rating_id=rating.id WHERE rating.user_id=? AND report.rating_id IS NOT NULL AND doneon IS NULL GROUP BY report.rating_id", array('i'), array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}

	function getReportedUsers(){
		$result = $this->mysqli->s_query("SELECT report.user_id, COUNT(*) , user.username , report.date, report.message FROM report  INNER JOIN user ON report.user_id=user.id WHERE doneby IS NULL GROUP BY report.user_id ", array(), array());
		return $this->mysqli->getQueryResultArray($result);
	}

	function getReportsByObject($object_id, $type){
		if($type == "question"){
			$result = $this->mysqli->s_query("SELECT user.username, report.date, report.message FROM report INNER JOIN user ON user.id=report.by_user_id WHERE question_id=? AND doneon IS NULL", array('i'), array($object_id));
		} else if ($type == "rating"){
			$result = $this->mysqli->s_query("SELECT user.username, report.date, report.message FROM report INNER JOIN user ON user.id=report.by_user_id WHERE rating_id=? AND doneon IS NULL", array('i'), array($object_id));
		} else if ($type == "user"){
			$result = $this->mysqli->s_query("SELECT user.username, report.date, report.message FROM report INNER JOIN user ON user.id=report.by_user_id WHERE user_id=? AND doneon IS NULL", array('i'), array($object_id));
		} else {
			return array();
		}
		return $this->mysqli->getQueryResultArray($result);
	}

	function removeReportsByObject($object_id, $type, $user_id){

		if($type == "question"){
			$this->logger->log("Moderation done by user_id ". $user_id ." on question with id ". $object_id, Logger::INFO);
			return $this->mysqli->s_query("UPDATE report SET doneon='". date("Y-m-d H:i:s") ."', doneby=? WHERE question_id=?", array('i','i'), array($user_id, $object_id));
		} else if($type == "rating"){
			$this->logger->log("Moderation done by user_id ". $user_id ." on rating with id ". $object_id, Logger::INFO);
			return $this->mysqli->s_query("UPDATE report SET doneon='". date("Y-m-d H:i:s") ."', doneby=? WHERE rating_id=?", array('i','i'), array($user_id, $object_id));
		} else if($type=="user"){
			$this->logger->log("Moderation done by superuser user_id ". $user_id ." setting user inactive for user with id: ". $object_id, Logger::INFO);
			$this->logger->log("UPDATE report SET doneon='". date("Y-m-d H:i:s") ."', doneby=".$user_id." WHERE user_id=".$object_id , Logger::INFO);
			return $this->mysqli->s_query("UPDATE report SET doneon='". date("Y-m-d H:i:s") ."', doneby=? WHERE user_id=?", array('i','i'), array($user_id, $object_id));
		}
		$this->logger->log("Unkown type in removeReportsByObject in reportmodel.php", Logger::ERROR);

	}
}
?>