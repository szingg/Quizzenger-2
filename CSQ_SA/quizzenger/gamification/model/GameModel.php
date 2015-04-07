<?php

namespace quizzenger\gamification\model {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \quizzenger\logging\Log as Log;
	use \SqlHelper as SqlHelper;

	/*	@author Simon Zingg
	 *	The GameModel provides data which is used for games.
	 *	Each method which modifies the database (INSERT, UPDATE, DELETE) checks if input parameters are set.
	 */
	class GameModel {
		private $mysqli;

		public function __construct(SqlHelper $mysqli) {
			$this->mysqli = $mysqli;
		}

		/*
		 * Adds a new game to a given quiz.
		 * @precondition Please check if current user has permission to generate a game for this quiz.
		 * @param $quiz_id Quiz Id
		 * @param $name Gamename
		 * @param $duration Gameduration as string in Format HH:MM:SS
		 * @return Returns new gamesession_id if successful, else null
		*/
		public function getNewGameSessionId($quiz_id, $name, $duration){
			if(isset($quiz_id, $name, $duration)){
				log::info('Getting New Game Session for Quiz-ID :'.$quiz_id);
				return $this->mysqli->s_insert("INSERT INTO gamesession (name, quiz_id, duration) VALUES (?, ?, ?)",array('s','i','s'),array($name, $quiz_id, $duration));
			}
			else{
				return null;
			}
		}

		/*
		 * Starts the Game
		 * Method checks Permission
		 * @param $game_id
		 * @return if false return null
		 */
		public function startGame($game_id){
			if(isset($game_id) && $this->userIDhasPermissionOnGameId($_SESSION ['user_id'], $game_id)){
				log::info('Start Game with ID :'.$game_id);
				$this->mysqli->s_query("UPDATE gamesession SET has_started=CURRENT_TIMESTAMP WHERE id=?",array('i'),array($game_id));
			}
			else{
				log::warning('Unauthorized try to start game id :'.game_id);
				return null;
			}
		}

		/*
		 * Stops the Game
		 * Method checks Permission
		 * @param $game_id
		 * @return if false return null
		 */
		public function stopGame($game_id){
			if(isset($game_id) && $this->userIDhasPermissionOnGameId($_SESSION ['user_id'], $game_id)){
				log::info('Stop Game with ID :'.$game_id);
				$this->mysqli->s_query("UPDATE gamesession SET is_finished=CURRENT_TIMESTAMP WHERE id=?",array('i'),array($game_id));
			}
			else{
				log::warning('Unauthorized try to stop game id :'.game_id);
				return null;
			}
		}

		/*
		 * @return Returns username of the gameowner if successful, else null
		 */
		public function getGameOwnerByGameId($game_id){
			$result = $this->mysqli->s_query("SELECT user_id FROM gamesession g, quiz q WHERE g.quiz_id = q.id and g.id=?",array('i'),array($game_id));
			$resultArray = $this->mysqli->getQueryResultArray($result);
			if($result->num_rows > 0 && isset($resultArray[0]['user_id'])){
				return $resultArray[0]['user_id'];
			}
			else return null;
		}

		/*
		 * Gets all members of a game
		 */
		public function getGameMembersByGameId($game_id){
			$result = $this->mysqli->s_query("SELECT g.user_id, u.username as member FROM gamemember g, user u WHERE g.gamesession_id = ? AND g.user_id = u.id",array('i'),array($game_id));
			return $this->mysqli->getQueryResultArray($result);
		}

		public function isGameMember($user_id, $game_id){
			$result = $this->mysqli->s_query("SELECT * FROM gamemember WHERE gamesession_id = ? AND user_id = ?",['i','i'],[$game_id, $user_id]);
			return $result->num_rows > 0;
		}

		/*
		 * Gets game info. For more information about the columns consult the query
		 */
		public function getGameInfoByGameId($game_id){
			$result = $this->mysqli->s_query("SELECT g.id as game_id, g.name as gamename, created_on, "
					." has_started, is_finished, duration, ADDTIME(has_started, duration) as gameend, quiz_id, "
					."user_id as owner_id, q.name as quizname, created as quiz_created_on FROM gamesession g, quiz q "
					."WHERE g.id = ? AND g.quiz_id = q.id",['i'],[$game_id]);
			return $this->mysqli->getQueryResultArray($result);
		}

		/*
		 * Gets all open games.
		 */
		public function getOpenGames(){
			$result = $this->mysqli->query('SELECT g.id, g.name, u.username, session.members, g.duration FROM gamesession g '.
					'JOIN quiz q ON g.quiz_id = q.id '.
					'JOIN user u ON q.user_id = u.id '.
					'LEFT JOIN (SELECT gamesession_id, count(user_id) AS members FROM gamemember '.
					'GROUP BY gamesession_id) AS session ON g.id = session.gamesession_id '.
					'WHERE g.has_started IS NULL');
			return $this->mysqli->getQueryResultArray($result);
		}

		/*
		 * User join a game.
		 *   //TODO:
		 * @return Returns 0 if successful else you will be redirected to the error page because entry already exists. Returns null when no input parameters are passed.
		 */
		public function userJoinGame($user_id, $game_id){
			if(! isset($user_id, $game_id)) return null;
			log::info('User joins game ID:'.$game_id);
			return $this->mysqli->s_insert("INSERT INTO gamemember (gamesession_id, user_id) VALUES (?, ?)",array('i','i'),array($game_id, $user_id));
		}

		/*
		* @return Always returns false, because query didn't get any results when delete
		*/
		public function userLeaveGame($user_id, $game_id){
			if(! isset($user_id, $game_id)) return false;
			log::info('User leaves game ID:'.$game_id);
			return $this->mysqli->s_query("DELETE FROM gamemember WHERE gamesession_id=? AND user_id=?",array('i','i'),array($game_id, $user_id));
		}

		/*
		 * @return Returns true when has started, otherwise false
		 */
		public function gameHasStarted($game_id){
			$result = $this->mysqli->s_query("SELECT has_started FROM gamesession WHERE id=?",['i'],[$game_id]);
			$resultArray = $this->mysqli->getQueryResultArray($result);
			if($result->num_rows > 0){
				return isset($resultArray[0]['has_started']);
			}
			else return false;
		}

		/*
		 * Checks if user is permitted to modify the given game
		 * @return Returns true if permitted, else false
		 */
		public function userIDhasPermissionOnGameId($user_id, $game_id){
			$gameOwner = $this->getGameOwnerByGameId($game_id);
			if($gameOwner == null) return null;
			else return $gameOwner == $user_id;
		}

		/*
		 * Gets the game report
		 * @return array with columns questionAnswered, questionAnsweredCorrect, totalQuestions, totalTimeInSec, timePerQuestion, user_id, username
		 */
		public function getGameReport($game_id){
			$result = $this->mysqli->s_query('SELECT @rank:=@rank+1 AS rank, SUM(weight) AS questionAnswered,'
					.' SUM(CASE WHEN questionCorrect = 100 THEN weight ELSE 0 END) AS questionAnsweredCorrect,'
					.' total.totalQuestions, time.totalTimeInSec, time.totalTimeInSec/COUNT(q.gamesession_id) AS timePerQuestion,'
					.' q.user_id, u.username FROM gamemember m'
					.' LEFT JOIN questionperformance q ON q.gamesession_id = m.gamesession_id AND q.user_id = m.user_id'
					.' LEFT JOIN user u ON u.id = m.user_id'
					.' LEFT JOIN ('
						.' SELECT @rank := 0, gamesession.id, gamesession.quiz_id, SUM(weight) AS totalQuestions '
						.' FROM gamesession, quiztoquestion'
						.' WHERE gamesession.quiz_id = quiztoquestion.quiz_id AND gamesession.id = ?) AS total'
					.' ON total.id = m.gamesession_id'
					.' LEFT JOIN quiztoquestion qq ON qq.quiz_id = total.quiz_id AND qq.question_id = q.question_id'
					.' LEFT JOIN ('
						.' SELECT user_id, TIMESTAMPDIFF(SECOND,g.has_started,MAX(timestamp)) AS totalTimeInSec'
						.' FROM questionperformance q, gamesession g'
						.' WHERE q.gamesession_id = g.id AND q.gamesession_id = ?'
						.' GROUP BY q.user_id) AS time'
					.' ON time.user_id = m.user_id'
					.' WHERE m.gamesession_id = ?'
					.' GROUP BY m.user_id'
					.' ORDER BY questionAnsweredCorrect DESC',['i','i','i'],[$game_id,$game_id,$game_id]);
			return $this->mysqli->getQueryResultArray($result);
			/*
			 * jetzt mit totalTime = gameFinished
			 * TIMESTAMPDIFF(SECOND,g.has_started,(CASE WHEN g.is_finished IS NOT NULL THEN g.is_finished ELSE MAX(timestamp) END)) AS totalTimeInSec
			 *
			 * SELECT @rank:=@rank+1 AS rank, SUM(weight) AS questionAnswered, SUM(CASE WHEN questionCorrect = 100 THEN weight ELSE 0 END) AS questionAnsweredCorrect, total.totalQuestions, time.totalTimeInSec, time.totalTimeInSec/COUNT(q.gamesession_id) AS timePerQuestion, q.user_id, u.username FROM gamemember m LEFT JOIN questionperformance q ON q.gamesession_id = m.gamesession_id AND q.user_id = m.user_id LEFT JOIN user u ON u.id = m.user_id LEFT JOIN ( SELECT @rank := 0, gamesession.id, gamesession.quiz_id, SUM(weight) AS totalQuestions  FROM gamesession, quiztoquestion WHERE gamesession.quiz_id = quiztoquestion.quiz_id AND gamesession.id = 37)
AS total ON total.id = m.gamesession_id LEFT JOIN quiztoquestion qq ON qq.quiz_id = total.quiz_id AND qq.question_id = q.question_id LEFT JOIN ( SELECT user_id, TIMESTAMPDIFF(SECOND,g.has_started,(CASE WHEN g.is_finished IS NOT NULL THEN g.is_finished ELSE MAX(timestamp) END)) AS totalTimeInSec FROM questionperformance q, gamesession g WHERE q.gamesession_id = g.id AND q.gamesession_id = 37
GROUP BY q.user_id) AS time ON time.user_id = m.user_id WHERE m.gamesession_id = 37 GROUP BY m.user_id  ORDER BY questionAnsweredCorrect DESC

			 ............................
			 jetzt mit gewichteten punkten

			select SUM(weight) as answered, SUM(CASE WHEN questionCorrect = 100 THEN weight ELSE 0 END) as answerCorrect, total.totalQuestion, time.totalTimeInSec, time.totalTimeInSec/count(q.gamesession_id) as timePerQuestion, m.user_id, u.username from gamemember m
			left join questionperformance q on q.gamesession_id = m.gamesession_id and q.user_id = m.user_id
			left join user u on u.id = m.user_id
			left join (select gamesession.id, gamesession.quiz_id, sum(weight) as totalQuestion from gamesession, quiztoquestion where gamesession.quiz_id = quiztoquestion.quiz_id and gamesession.id = 37) as total on m.gamesession_id = total.id
			left join quiztoquestion qq on qq.quiz_id = total.quiz_id and qq.question_id = q.question_id
			left join (select  user_id, max(timestamp)-g.has_started as totalTimeInSec from questionperformance q, gamesession g
			where q.gamesession_id = g.id and q.gamesession_id = 37
			group by  q.user_id) as time on time.user_id = m.user_id
			where m.gamesession_id = 37
			group by m.user_id


			 neue abfrage
			 select count(q.gamesession_id) as answered, count(CASE WHEN questionCorrect = 100 THEN 1 END) as answerCorrect, total.totalQuestion, time.totalTimeInSec, time.totalTimeInSec/count(q.gamesession_id) as timePerQuestion, m.user_id, u.username from gamemember m
			left join questionperformance q on q.gamesession_id = m.gamesession_id
			left join user u on u.id = m.user_id
			left join (select gamesession.id, count(question_id) as totalQuestion from gamesession, quiztoquestion where gamesession.quiz_id = quiztoquestion.quiz_id and gamesession.id = 20) as total on m.gamesession_id = total.id
			left join (select  user_id, max(timestamp)-g.has_started as totalTimeInSec from questionperformance q, gamesession g
			where q.gamesession_id = g.id and q.gamesession_id = 20
			group by  q.user_id) as time on time.user_id = m.user_id
			where m.gamesession_id = 20
			group by m.user_id

			alte abfrage
			select count(gamesession_id) as answered, count(CASE WHEN questionCorrect = 100 THEN 1 END) as answerCorrect, total.totalQuestion, time.totalTimeInSec, time.totalTimeInSec/count(gamesession_id) as timePerQuestion, q.user_id, u.username from questionperformance q
			join user u on u.id = q.user_id
			join (select gamesession.id, count(question_id)as totalQuestion from gamesession, quiztoquestion where gamesession.quiz_id = quiztoquestion.quiz_id and gamesession.id = 37) as total on q.gamesession_id = total.id
			join (select  user_id, max(timestamp)-g.has_started as totalTimeInSec from questionperformance q, gamesession g
			where q.gamesession_id = g.id and q.gamesession_id = 37
			group by  q.user_id) as time on time.user_id = q.user_id
			where q.gamesession_id = 37
			group by q.user_id

			 */
		}

	} // class GameModel
} // namespace quizzenger\gamification\model

?>