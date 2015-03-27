<?php

class ReportingModel {

	private $mysqli;
	private $logger;

	public function __construct($mysqli, $log) {
		$this->mysqli = $mysqli;
		$this->logger = $log;
	}

	public function getUserList($categoryId) {
		if($categoryId == "" || $categoryId == 0) {
			return $this->mysqli->s_query('SELECT user.id, user.username, user.created_on,'
				. ' (SELECT settings.value FROM settings WHERE settings.name="q.scoring.producer-multiplier" LIMIT 1) as producer_multiplier,'
				. ' (SELECT rank.name FROM rank WHERE rank.threshold<=(producer_score+consumer_score)*producer_multiplier OR rank.threshold=0'
				. '     ORDER BY rank.threshold DESC LIMIT 1) AS rank,'
				. ' (SELECT rank.image FROM rank WHERE rank.name=rank) AS rank_image,'
				. ' (SELECT rank.threshold FROM rank WHERE rank.name=rank) AS rank_threshold,'
				. ' SUM(userscore.producer_score) AS producer_score,'
				. ' SUM(userscore.consumer_score) AS consumer_score'
				. ' FROM user'
				. ' LEFT JOIN (userscore) ON (user.id=userscore.user_id)'
				. ' GROUP BY user.id'
				. ' ORDER BY user.id ASC',
				[], [], false);
		}
		else {
			return $this->mysqli->s_query('SELECT user.id, user.username, DATE(user.created_on) AS created_on,'
				. ' (SELECT settings.value FROM settings WHERE settings.name="q.scoring.producer-multiplier" LIMIT 1) as producer_multiplier,'
				. ' "" AS rank,'
				. ' "" AS rank_image,'
				. ' (SELECT rank.threshold FROM rank WHERE rank.name=rank) AS rank_threshold,'
				. ' SUM(userscore.producer_score) AS producer_score,'
				. ' SUM(userscore.consumer_score) AS consumer_score'
				. ' FROM user'
				. ' LEFT JOIN (userscore) ON (user.id=userscore.user_id)'
				. ' WHERE userscore.category_id=?'
				. ' GROUP BY user.id'
				. ' ORDER BY user.id ASC',
				['s'], [$categoryId], false);
		}
	}

	public function getQuestionList() {
		return $this->mysqli->s_query('SELECT question.id, question.questiontext,'
			. ' DATE(question.created) AS created, DATE(question.lastModified) AS last_modified,'
			. ' question.difficulty, question.rating, question.ratingcount,'
			. ' user.username AS author, category.name AS category, COUNT(questionperformance.question_id) AS solved_count'
			. ' FROM question'
			. ' JOIN user ON (user.id=question.user_id)'
			. ' JOIN category ON (category.id=question.category_id)'
			. ' JOIN questionperformance ON (questionperformance.question_id=question.id)'
			. ' GROUP BY questionperformance.question_id'
			. ' ORDER BY created ASC',
			[], [], false);
	}

	public function getAuthorList() {
		return $this->mysqli->s_query('SELECT username FROM user'
			. ' WHERE id IN (SELECT user_id FROM question)'
			. ' ORDER BY username ASC',
			[], [], false);
	}

	public function getCategoryList() {
		return $this->mysqli->s_query('SELECT DISTINCT userscore.category_id AS id, category.name'
			. ' FROM userscore'
			. ' JOIN category ON (category.id=userscore.category_id)'
			. ' ORDER BY category.name ASC',
			[], [], false);
	}
}
?>
