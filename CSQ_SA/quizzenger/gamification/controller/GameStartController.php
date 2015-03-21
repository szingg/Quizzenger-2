<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;
	

	class GameStartController{
		private $mysqli;
		private $view;
		private $gameModel;
		private $sqlhelper;
		private $request;
		private $quizModel;
		

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->gameModel = new GameModel($this->sqlhelper, $this->quizModel);
			$this->request = array_merge ( $_GET, $_POST );
		}
		
		public function loadView(){
			$this->checkLogin();
			
			//gamestart
			$this->view->setTemplate ( 'gamestart' );
			
			$game_id = $this->request ['gameid'];
			
			$gameinfo = $this->gameModel->getGameInfoByGameId($game_id);
			if(count($gameinfo) <= 0) $this->redirectToErrorPage();
			else $gameinfo = $gameinfo[0]; 
			$this->view->assign ( 'gameinfo', $gameinfo );
			
			$this->view->assign ( 'isOwner', $gameinfo['owner_id'] == $_SESSION['user_id']);
			
			$members = $this->gameModel->getGameMembersByGameId($game_id);
			$this->view->assign ( 'members', $members );
			
			/*$_SESSION ['game_id'. $game_id] = $game_id;
			$_SESSION ['questions'. $session_id] = $this->quizModel->getQuestionArray ( $gameinfo['quiz_id'] );
			$_SESSION ['counter'. $session_id] = 0;
			
			
			if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
				$firstUrl = "?view=question&id=" . $_SESSION ['questions'. $session_id] [0] . "&gameid=". $game_id;
			} else {
				$firstUrl = "?view=quizend";
			}
			
			
			$quizinfo = array (
					'quizid' => $this->request ['quizid'],
					'quizname' => $this->quizModel->getQuizName ( $this->request ['quizid'] ),
					'firstUrl' => $firstUrl
			);
			
			$this->view->assign ( 'quizinfo', $quizinfo );
			*/
			return $this->view;
		}
		
		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}
		
		private function redirectToErrorPage(){
			define ( "err_db_query_failed", "Oops, es wurde eine ungültige Datenbank abfrage getätigt" );
			die ();
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>