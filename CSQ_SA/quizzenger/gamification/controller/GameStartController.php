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
		private $gameid;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->gameModel = new GameModel($this->sqlhelper, $this->quizModel);
			$this->request = array_merge ( $_GET, $_POST );
		}
		public function loadView(){
			checkLogin();
			
			$this->loadViewContent();
			
			$this->loadAdminView();
			
			$this->setGameSession();
			
			return $this->view;
		}
		
		private function loadViewContent(){
			$this->view->setTemplate ( 'gamestart' );
			
			$this->gameid = $this->request ['gameid'];
			
			$this->gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			if(count($this->gameinfo) <= 0) redirectToErrorPage('err_db_query_failed');
			else $this->gameinfo = $this->gameinfo[0];
			$this->checkGameStarted($this->gameinfo['has_started']);
			$this->view->assign ( 'gameinfo', $this->gameinfo );
			
			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);
			$this->view->assign ( 'isMember', $isMember );
			
			$members = $this->gameModel->getGameMembersByGameId($this->gameid);
			$this->view->assign ( 'members', $members );
			
			/*
			 if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
			 $firstUrl = "?view=question&id=" . $_SESSION ['questions'. $session_id] [0] . "&gameid=". $this->gameid;
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
		}
		private function setGameSession(){
			$_SESSION ['gameid'] = $this->gameid;
			$_SESSION ['gamequestions'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION ['gamecounter'] = 0;
			
		}
		private function loadAdminView(){
			$adminView = "";
			if($this->isGameOwner($this->gameinfo['owner_id'])){
				$adminView = new \View();
				$adminView->setTemplate ( 'gameadmin' );
				$adminView->assign('gameinfo', $this->gameinfo);
					
				$adminView = $adminView->loadTemplate();
			}
			$this->view->assign ( 'adminView', $adminView );
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}

		/*
		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}	*/
		
		private function checkGameStarted($has_started){
			if ( isset($has_started)) {
				redirectToErrorPage('err_game_has_started');
			}
		}
/*
		private function redirectToErrorPage($errorCode = 'err_db_query_failed'){
			header('Location: ./index.php?view=error&err='.$errorCode);
			die ();
		} */
	} // class GameController
} // namespace quizzenger\gamification\controller

?>