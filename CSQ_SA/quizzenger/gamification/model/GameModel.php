<?php

namespace quizzenger\gamification\model {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;
	use \SqlHelper as SqlHelper;
	use \QuizModel as QuizModel;

	/*	@author Simon Zingg
	 *	The GameModel provides data which is used for games.
	 *	Each method which modifies the database (INSERT, UPDATE, DELETE) checks if input parameters are set.
	 */
	class GameModel {
		private $mysqli;
		private $quizModel;

		public function __construct(SqlHelper $mysqli, QuizModel $quizModel) {
			$this->mysqli = $mysqli;
			$this->quizModel = $quizModel;
		}

		/*
		 * Adds a new game to a given quiz.
		 * Checks if current user has permission to generate a game for this quiz.
		 * @param $quiz_id
		 * @return Returns new gamesession_id if successful, else null 
		*/
		public function getNewGameSessionId($quiz_id, $name){
			if(isset($quiz_id, $name) && $this->quizModel->userIDhasPermissionOnQuizId($quiz_id,$_SESSION ['user_id'])){
				log::info('Getting New Game Session for Quiz-ID :'.$quiz_id);
				return $this->mysqli->s_insert("INSERT INTO gamesession (name, quiz_id) VALUES (?, ?)",array('s','i'),array($name, $quiz_id));
			}
			else{
				log::warning('Unauthorized try to add new Gamesession for Quiz-ID :'.$question_id);
				return null;
			}
		}

		public function startGame($game_id){
			if(isset($game_id) && $this->quizModel->userIDhasPermissionOnQuizId($quiz_id,$_SESSION ['user_id'])){
				log::info('Start Game with ID :'.$game_id);
				$this->mysqli->s_query("UPDATE gamesession SET has_started=CURRENT_TIMESTAMP WHERE id=?",array('i'),array($game_id));
			}
			else{
				log::warning('Unauthorized try to add new Gamesession for Quiz-ID :'.$question_id);
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
			$result = $this->mysqli->s_query("SELECT g.id as game_id, g.name as gamename, created_on, has_started, quiz_id, ".
					"user_id as owner_id, q.name as quizname, created as quiz_created_on FROM gamesession g, quiz q ".
					"WHERE g.id = ? AND g.quiz_id = q.id",['i'],[$game_id]);
			return $this->mysqli->getQueryResultArray($result);
		}

		/*
		 * Gets all open games.
		 */
		public function getOpenGames(){
			$result = $this->mysqli->query('SELECT g.id, g.name, u.username, session.members FROM gamesession g '.
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
		 * Checks if user is permitted to modify the given game
		 * @return Returns true if permitted, else false
		 */
		public function userIDhasPermissionOnGameId($user_id, $game_id){
			$gameOwner = $this->getGameOwnerByGameId($game_id);
			if($gameOwner == null) return null;
			else return $gameOwner == $user_id;
		}

	} // class GameModel
} // namespace quizzenger\gamification\model

?>