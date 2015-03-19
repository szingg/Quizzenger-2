<?php

namespace quizzenger\gamification\model {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \Logger as Logger;
	use \SqlHelper as SqlHelper;
	use \QuizModel as QuizModel;
	//use \quizzenger\data\UserEvent as UserEvent;
	//use \quizzenger\scoring\ScoreDispatcher as ScoreDispatcher;
	//use \quizzenger\achievements\AchievementDispatcher as AchievementDispatcher;

	/*	@author Simon Zingg
	 *	The GameModel provides data which is used for games.
	 */
	class GameModel {
		private $mysqli;
		private $quizModel;
		private $logger;
		//private $scoreDispatcher;
		//private $achievementDispatcher;

		public function __construct(SqlHelper $mysqli, QuizModel $quizModel) {
			$this->mysqli = $mysqli;
			$this->quizModel = $quizModel;
			$this->logger = new Logger();
			//$this->scoreDispatcher = new ScoreDispatcher($this->mysqli);
			//$this->achievementDispatcher = new AchievementDispatcher($this->mysqli);
		}
		
		/*
		 * Adds a new game to a given quiz.
		 * Checks if current user has permission to generate a game for this quiz.
		 * @param $quiz_id
		 * @return Returns new gamesession_id if successful, else null 
		*/
		public function getNewGameSessionId($quiz_id, $name){
			if(isset($quiz_id, $name) && $this->quizModel->userIDhasPermissionOnQuizId($quiz_id,$_SESSION ['user_id'])){
				$this->logger->log ( "Getting New Game Session for Quiz-ID :".$quiz_id, Logger::INFO );
				return $this->mysqli->s_insert("INSERT INTO gamesession (name, quiz_id) VALUES (?, ?)",array('s','i'),array($name, $quiz_id));
			}
			else{
				$this->logger->log ( "Unauthorized try to add new Gamesession for Quiz-ID :".$question_id, Logger::WARNING );
				return null;
			}
		}
		
		public function startGame($game_id){
			$this->logger->log ( "Start Game with ID :".$game_id, Logger::INFO );
			$this->mysqli->s_query("UPDATE gamesession SET has_started=CURRENT_TIMESTAMP WHERE id=?",array('i'),array($game_id));
		}
		
		/*
		 * @return Returns username of the gameowner if successful, else null
		 */
		public function getGameOwnerByGameId($game_id){
			$result = $this->mysqli->s_query("Select user_id from gamesession g, quiz q where g.quiz_id = q.id and g.id=?",array('i'),array($game_id));
			$resultArray = $this->mysqli->getQueryResultArray($result);
			if($result->num_rows > 0 && isset($resultArray[0]['user_id'])){
				return $resultArray[0]['user_id'];
			}
			else return null;
		}
		
		/*
		 * Gets all open games.
		 * @return Always returns false, because query didn't get any results when delete
		 */
		public function getOpenGames(){
			$result = $this->mysqli->query('Select g.name, u.username, session.members from gamesession g '.
					'join quiz q on g.quiz_id = q.id '.
					'join user u on q.user_id = u.id '.
					'left join (select gamesession_id, count(user_id) as members from gamemember '.
					'group by gamesession_id) as session  on g.id = session.gamesession_id '.
					'where g.has_started is NULL');
			return $this->mysqli->getQueryResultArray($result);
		}
		/*
		Select g.name, u.username, session.members from gamesession g
		join quiz q on g.quiz_id = q.id
		join user u on q.user_id = u.id
		left join (select gamesession_id, count(user_id) as members from gamemember group by gamesession_id) as session  on g.id = session.gamesession_id
		where g.has_started is NULL;
		*/
		
		/*
		 * User join a game.
		 *  
		 * @return Returns 0 if successful else you will be redirected to the error page because entry already exists //TODO:
		 */
		public function userJoinGame($user_id, $game_id){
			$this->logger->log ( "User joins game ID :".$game_id, Logger::INFO );
			return $this->mysqli->s_insert("INSERT INTO gamemember (gamesession_id, user_id) VALUES (?, ?)",array('i','i'),array($game_id, $user_id));
		}
		
		/*
		* @return Always returns false, because query didn't get any results when delete
		*/
		public function userLeaveGame($user_id, $game_id){
			$this->logger->log ( "User leaves game ID :".$game_id, Logger::INFO );
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