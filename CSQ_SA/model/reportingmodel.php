<?php

class ReportingModel {

	private $mysqli;
	private $logger;

	public function __construct($mysqli, $log) {
		$this->mysqli = $mysqli;
		$this->logger = $log;
	}

	public function getUserList() {
		return $this->mysqli->s_query('SELECT user.id, user.username, user.created_on,'
			. ' (SELECT settings.value FROM settings WHERE settings.name="q.scoring.producer-multiplier" LIMIT 1) as producer_multiplier,'
			. ' (SELECT rank.name FROM rank WHERE rank.threshold<=(producer_score+consumer_score)*producer_multiplier OR rank.threshold=0'
			. '     ORDER BY rank.threshold DESC LIMIT 1) AS rank,'
			. ' SUM(userscore.producer_score) AS producer_score,'
			. ' SUM(userscore.consumer_score) AS consumer_score'
			. ' FROM user'
			. ' LEFT JOIN (userscore) ON (user.id=userscore.user_id)'
			. ' GROUP BY user.id'
			. ' ORDER BY user.id ASC',
			[], [], false);
	}

	public function getQuestionList() {
		return $this->mysqli->s_query('SELECT id, questiontext, created, lastModified AS last_modified,'
			. ' ROUND(difficulty, 2) AS difficulty, (rating / ratingcount) AS rating,'
			. ' "n/a" AS solved_count'
			. ' FROM question ORDER BY created ASC',
			[], [], false);
	}

	public function getAuthorList() {
		return $this->mysqli->s_query('SELECT username FROM user'
			. ' WHERE id IN (SELECT user_id FROM question)'
			. ' ORDER BY username ASC',
			[], [], false);
	}
}
?>
