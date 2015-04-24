<?php
class ModerationModel {
	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function isModerator($user_id, $category_id) {
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM moderation WHERE user_id=? AND category_id=? AND inactive=0",array('i', 'i'),array($user_id, $category_id));
		return ($this->mysqli->getSingleResult($result)['COUNT(*)'] > 0 ? true : false);
	}

	public function getModeratedCategories($user_id) {
		$result = $this->mysqli->s_query("SELECT category_id FROM moderation WHERE user_id=? AND inactive=0",array('i'),array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}

	public function getModeratedCategoryNames($user_id) {
		$result = $this->mysqli->s_query("SELECT category.name FROM moderation LEFT JOIN category ON category.id=moderation.category_id WHERE user_id=? AND inactive=0",array('i'),array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}
}
?>
