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
			$this->gameModel = new GameModel($this->sqlhelper);
			$this->request = array_merge ( $_GET, $_POST );

			$this->gameid = $this->request ['gameid'];
			$this->gameinfo = $this->getGameInfo();

		}
		public function loadView(){
			checkLogin();

			$this->loadGameStartView();

			$this->setGameSession();

			return $this->view;
		}

		private function loadGameStartView(){
			$this->view->setTemplate ( 'gamestart' );

			$this->checkGameStarted($this->gameinfo['starttime']);
			$this->view->assign ( 'gameinfo', $this->gameinfo );

			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);
			$this->view->assign ( 'isMember', $isMember );

			$isOwner = $this->isGameOwner($this->gameinfo['owner_id']);
			$this->view->assign ( 'isOwner', $isOwner );

			$members = $this->gameModel->getGameMembersByGameId($this->gameid);
			$this->view->assign ( 'members', $members );

		}
		private function setGameSession(){
			$_SESSION [$this->gameid.'gamequestions'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION [$this->gameid.'gamecounter'] = 0;
			//$_SESSION ['game'][$this->gameid]['gamequestions'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			//$_SESSION ['game'][$this->gameid]['gamecounter'] = 0;
			$_SESSION ['game'][$this->gameid]['gamequestions'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION ['game'][$this->gameid]['gamecounter'] = 0;
			$_SESSION ['game'][0]['gamecounter'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION ['test']['test']['gamequestions'] = $this->quizModel->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION ['game'][$this->gameid]['gamecounter'] = 0;

		}

		/*
		 * Gets the Gameinfo. Redirects to errorpage when no result returned.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			if(count($gameinfo) <= 0) redirectToErrorPage('err_db_query_failed');
			else return $gameinfo[0];
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}

		private function checkGameStarted($has_started){
			if ( isset($has_started)) {
				redirectToErrorPage('err_game_has_started');
			}
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>