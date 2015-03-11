<?php

class UserScoreModel{
	var $mysqli;
	var $logger;
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	function hasUserScoredQuestion($question_id,$user_id){
		$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM questionperformance WHERE question_id=? AND user_id=? AND questionCorrect <> 0)",array('i','i'),array($question_id,$user_id));
		$result= array_values($this->mysqli->getSingleResult($result));
		return ($result[0]=="1");
	}
	function addScoreToCategory($user_id, $category_id, $score, $moderationModel){
		$this->logger->log("Adding Userscore (". $score .") for category_id ". $category_id ." from user ". $user_id, Logger::INFO);
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM userscore WHERE user_id =? AND category_id = ?", array('i', 'i'), array($user_id, $category_id));
		if($this->mysqli->getSingleResult($result)['COUNT(*)'] == 0){
			$this->mysqli->s_insert("INSERT INTO userscore (user_id, category_id, score) VALUES (?, ?, ?)", array('i', 'i', 'i'), array($user_id, $category_id, $score));
			$newscore=$score;
		}
		else {
			$newscore = $score + $this->getCategoryScore($user_id, $category_id);
			$this->mysqli->s_query("UPDATE userscore SET score=? WHERE user_id =? AND category_id = ?", array('i', 'i', 'i'), array($newscore, $user_id, $category_id));
		}
		$moderationModel->checkPromotion($user_id, $category_id, $newscore);
	}

	function getCategoryScore($user_id, $category_id){
		$result = $this->mysqli->s_query("SELECT score FROM userscore WHERE user_id =? AND category_id = ?", array('i', 'i'), array($user_id, $category_id));
		$catscore = $this->mysqli->getSingleResult($result)['score'];
		return ($catscore==null)?0:$catscore;
	}

	function getUserScore($user_id){
		$result = $this->mysqli->s_query("SELECT SUM(score) FROM userscore WHERE user_id=?", array('i'), array($user_id));
		$score =$this->mysqli->getSingleResult($result)['SUM(score)'];
		return ($score==null)?0:$score;
	}

	function getUserScoreAllCategories($user_id){
		$result = $this->mysqli->s_query("SELECT category.name, category.id,userscore.score FROM userscore LEFT JOIN category ON userscore.category_id=category.id WHERE user_id=? ORDER BY category.name", array('i'), array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}
}
?>