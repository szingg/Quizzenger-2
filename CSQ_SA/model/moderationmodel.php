<?php
class ModerationModel{
	var $mysqli;
	var $logger;
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	function isModerator($user_id, $category_id){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM moderation WHERE user_id=? AND category_id=? AND inactive=0",array('i', 'i'),array($user_id, $category_id));
		return ($this->mysqli->getSingleResult($result)['COUNT(*)'] > 0 ? true : false);
	}

	function checkPromotion($user_id, $category_id, $score){
		if($score >= MODERATION_SCORE && !$this->isModerator($user_id, $category_id)){
			$this->addModerator($user_id, $category_id);
		}
	}

	function addModerator($user_id, $category_id){
		return $this->mysqli->s_insert("INSERT INTO moderation (user_id, category_id) VALUES (?, ?)",array('i','i'),array($user_id, $category_id));
	}

	function getModeratedCategories($user_id){
		$result = $this->mysqli->s_query("SELECT category_id FROM moderation WHERE user_id=? AND inactive=0",array('i'),array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}

	function getModeratedCategoryNames($user_id){
		$result = $this->mysqli->s_query("SELECT category.name FROM moderation LEFT JOIN category ON category.id=moderation.category_id WHERE user_id=? AND inactive=0",array('i'),array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}
}
?>