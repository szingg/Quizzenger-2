<?php

use \quizzenger\Settings as Settings;
class UserScoreModel {
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

	public function getUserScore($userId) {
		$result = $this->mysqli->s_query('SELECT total_score, bonus_score'
			. ' FROM userscoreview WHERE id=?',
			['i'], [$userId], false);

		return $this->mysqli->getSingleResult($result);
	}

	public function getUserScoreAllCategories($userId) {
		$pmp = (new Settings($this->mysqli->database()))->getSingle('q.scoring.producer-multiplier');
		$result = $this->mysqli->s_query('SELECT category_id, category_name,'
			. '     (FLOOR(producer_score*?+consumer_score)) AS category_score,'
			. '     user_count, user_rank'
			. '     FROM (SELECT c.id AS category_id,'
			. '    	    c.name AS category_name,'
			. '         u.producer_score AS producer_score,'
			. '         u.consumer_score AS consumer_score,'
			. '         (SELECT COUNT(*) FROM userscore AS us'
			. '             WHERE us.category_id=c.id) AS user_count,'
			. '         (SELECT COUNT(*) FROM userscore AS us'
			. '             WHERE us.category_id=u.category_id'
			. '                 AND us.producer_score*?+us.consumer_score'
			. '                     >=u.producer_score*?+u.consumer_score) AS user_rank'
			. '     FROM userscore AS u'
			. '     LEFT JOIN category AS c ON (c.id=u.category_id)'
			. '     WHERE u.user_id=? AND (u.producer_score>0 OR u.consumer_score>0)'
			. '     ORDER BY c.name'
			. ' ) AS general',
			['d', 'd', 'd', 'i'],
			[$pmp, $pmp, $pmp, $userId], false);

		return $this->mysqli->getQueryResultArray($result);
	}

	public function getLeadingTrailingUsers($userId) {
		$result = $this->mysqli->s_query('SELECT * FROM user',
			[], []);

		// TODO: Calculation has to be based on total score.
		//       Consider using the Reporting Model.
		return $this->mysqli->getQueryResultArray($result);
	}
}
?>
