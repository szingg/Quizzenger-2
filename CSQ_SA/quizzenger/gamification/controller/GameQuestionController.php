<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;


	class GameQuestionController{
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
			$this->answerModel = new \AnserModel($this->sqlhelper, log::get());
			$this->gameModel = new GameModel($this->sqlhelper, $this->quizModel);
			$this->request = array_merge ( $_GET, $_POST );
		}
		public function loadView(){
			checkLogin(); 
			
			//check session-fields
			if(! isset($_SESSION['gameid'], $_SESSION['gamequestions'], $_SESSION['gamecounter'])) $this->redirectToErrorPage('err_not_authorized');
			$game_id = $_SESSION['gameid'];

			$gameinfo = $this->gameModel->getGameInfoByGameId($game_id);
			if(count($gameinfo) <= 0) redirectToErrorPage('err_db_query_failed');
			else $gameinfo = $gameinfo[0];
			
			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $game_id);
			
			//checkConditions
			if($isMember==false || $this->isFinished($gameinfo['$is_finished']) || $this->hasStarted($gameinfo['has_started'])==false){
				redirectToErrorPage('err_not_authorized');
			}

			
			
				
			//adminView
			$adminView = "";
			if($this->isGameOwner($gameinfo['owner_id'])){
				$adminView = new \View();
				$adminView->setTemplate ( 'gameadmin' );
				$adminView->assign('gameinfo', $gameinfo);
					
				$adminView = $adminView->loadTemplate();
			}
			$this->view->assign ( 'adminView', $adminView );

			return $this->view;
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}
		private function hasStarted($has_started){
			return isset($has_started);
		}
		private function isFinished($is_finished){
			return isset($is_finished);
		}

		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}

		private function redirectToErrorPage($errorCode = 'err_db_query_failed'){
			header('Location: ./index.php?view=error&err='.$errorCode);
			die ();
		}
	} // class GameQuestionController
} // namespace quizzenger\gamification\controller
	
?>