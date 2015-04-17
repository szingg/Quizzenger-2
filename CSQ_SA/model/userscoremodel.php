<?php

class UserScoreModel{
	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function hasUserScoredQuestion($question_id, $user_id) {
		$result = $this->mysqli->s_query('SELECT EXISTS (SELECT 1 FROM questionperformance'
			. ' WHERE question_id=? AND user_id=? AND questionCorrect <> 0)',
			['i','i'], [$question_id, $user_id]);

		$result = array_values($this->mysqli->getSingleResult($result));
		return ($result[0] == "1");
	}

	public function getCategoryScore($user_id, $category_id) {
		$result = $this->mysqli->s_query('SELECT score FROM userscore WHERE user_id=? AND category_id=?',
			['i', 'i'], [$user_id, $category_id]);
		$catscore = $this->mysqli->getSingleResult($result)['score'];
		return ($catscore == null) ? 0 : $catscore;
	}

	public function getUserScore($user_id) {
		$result = $this->mysqli->s_query('SELECT SUM(score) FROM userscore WHERE user_id=?',
			['i'], [$user_id]);
		$score = $this->mysqli->getSingleResult($result)['SUM(score)'];
		return ($score == null) ? 0 : $score;
	}

	public function getUserScoreAllCategories($user_id) {
		$result = $this->mysqli->s_query('SELECT category.name, category.id, userscore.score,'
			. ' (SELECT COUNT(*) FROM userscore AS score_rank '
			. '     WHERE score_rank.category_id=userscore.category_id AND score_rank.score>=userscore.score) AS rank,'
			. ' (SELECT COUNT(*) FROM userscore AS score_user'
			. '     WHERE score_user.category_id=userscore.category_id) AS user_count'
			. ' FROM userscore'
			. ' LEFT JOIN category ON userscore.category_id=category.id'
			. ' WHERE user_id=? AND userscore.score>0'
			. ' ORDER BY category.name',
			['i'], [$user_id]);

		return $this->mysqli->getQueryResultArray($result);
	}
}
?>
