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

	public function getUserScore($userId) {
		$result = $this->mysqli->s_query('SELECT total_score, bonus_score, '
			. ' (SELECT COUNT(id) FROM userscoreview) AS total_users'
			. ' FROM userscoreview WHERE id=?',
			['i'], [$userId], false);

		return $this->mysqli->getSingleResult($result);
	}

	public function getUserScoreAllCategories($userId) {
		$pmp = (new Settings($this->mysqli->database()))->getSingle('q.scoring.producer-multiplier');
		$result = $this->mysqli->s_query('SELECT category_id, category_name,'
			. ' (FLOOR(producer_score*?+consumer_score)) AS category_score,'
			. ' user_count, user_rank'
			. ' FROM (SELECT c.id AS category_id,'
			. '     c.name AS category_name,'
			. '     u.producer_score AS producer_score,'
			. '     u.consumer_score AS consumer_score,'
			. '     (SELECT COUNT(*) FROM userscore AS us'
			. '         WHERE us.category_id=c.id) AS user_count,'
			. '     (SELECT COUNT(*) FROM userscore AS us'
			. '         WHERE us.category_id=u.category_id'
			. '             AND us.producer_score*?+us.consumer_score'
			. '                 >=u.producer_score*?+u.consumer_score) AS user_rank'
			. '     FROM userscore AS u'
			. '     LEFT JOIN category AS c ON (c.id=u.category_id)'
			. '     WHERE u.user_id=? AND (u.producer_score>0 OR u.consumer_score>0)'
			. '     ORDER BY c.name'
			. ' ) AS general',
			['d', 'd', 'd', 'i'],
			[$pmp, $pmp, $pmp, $userId], false);

		return $this->mysqli->getQueryResultArray($result);
	}

	public function getRankinglistAllCategories($userId){
		$result = $this->mysqli->s_query('SELECT a.id, username, rank, user_rank, total_score, a.category_id, name'
			.' FROM('
			.' SELECT id, username, category_id, total_score, '
			.' @rank:=CASE WHEN @currentcat <> category_id THEN 1 ELSE @rank+1 END AS rank, '
   			.' @currentcat:=category_id AS currentcat '
			.' FROM ('
				.' SELECT * FROM rankinglistallcategoriesview WHERE category_id IN '
				.' (SELECT category_id FROM rankinglistallcategoriesview WHERE id=?) '
				.' ORDER BY category_id, total_score DESC'
			.' ) r, '
			.' (SELECT @rank:= -1) s, '
  			.' (SELECT @currentcat:= -1) c) as a '
			.' JOIN category c ON c.id = category_id '
			.' JOIN ('
				.' SELECT category_id, MAX(user_rank) as user_rank FROM '
				.' (SELECT id, username, category_id, total_score, '
				.' @rank:=CASE WHEN @currentcat <> category_id THEN 1 ELSE @rank+1 END AS rank, '
				.' @user_rank:=IF(id=?,@rank,0) AS user_rank, '
   				.' @currentcat:=category_id AS currentcat '
				.' FROM ('
					.' SELECT * FROM rankinglistallcategoriesview WHERE category_id IN'
					.' (SELECT category_id FROM rankinglistallcategoriesview WHERE id=?)'
					.' ORDER BY category_id, total_score DESC'
				.' ) r, '
				.' (SELECT @rank:= -1) s, '
  				.' (SELECT @currentcat:= -1) c) AS subquery '
  				.' GROUP BY category_id'
			.' ) b ON b.category_id = a.category_id'
 			.' WHERE rank >= user_rank-5 AND rank <= user_rank+5'
		,['i','i','i'],[$userId, $userId, $userId]);
		$resultArray = [];
		while ( $row = $result->fetch_assoc () ) {
			if(! isset($resultArray[$row['category_id']])){
				$resultArray[$row['category_id']] = [];
			}
			array_push($resultArray[$row['category_id']], $row);
		}
		return $resultArray;
	}

	public function getLeadingTrailingUsers($userId) {
		$result = $this->mysqli->s_query('SELECT id, CAST(rank AS UNSIGNED) AS rank, username, total_score'
			. ' FROM (SELECT @rank:=@rank+1 AS rank, @user_rank=IF(id=?,@rank,0) AS user_rank, id,'
			. '     username, IFNULL(total_score, 0) AS total_score'
			. '     FROM (SELECT @rank:=0, @user_rank:=0, id, username, total_score '
			. '         FROM userscoreview'
			. '         ORDER BY total_score DESC'
			. '     ) AS general_inner'
			. ' ) AS general_outer'
			. ' WHERE rank >= user_rank-5 AND rank <= user_rank+5'
			. ' ORDER BY rank ASC'
			, ['i'], [$userId]);

		return $this->mysqli->getQueryResultArray($result);
		

		/*
		$result = $this->mysqli->s_query('SELECT id, CAST(rank AS UNSIGNED) AS rank, username, total_score, '
			. ' (SELECT id, MAX(user_rank) as user_rank FROM '
			. ' 	(SELECT @rank:=@rank+1 AS rank, @user_rank=IF(id=?,@rank,0) AS user_rank, id,'
			. '     username, IFNULL(total_score, 0) AS total_score'
			. '     FROM (SELECT * FROM userscoreview ORDER BY total_score DESC) u,'
			. '		(SELECT @rank:=0) r'
			. '	) as subquery) as user_rank'
			. ' FROM (SELECT @rank:=@rank+1 AS rank, id,'
			. '     username, IFNULL(total_score, 0) AS total_score'
			. '     FROM (SELECT * FROM userscoreview ORDER BY total_score DESC) u,'
			. '		(SELECT @rank:=0) r'
			. ' ) AS a'
			. ' WHERE rank >= user_rank-5 AND rank <= user_rank+5'
			. ' ORDER BY rank ASC'
			, ['i'], [$userId]);

		return $this->mysqli->getQueryResultArray($result); */
	}
}
?>
