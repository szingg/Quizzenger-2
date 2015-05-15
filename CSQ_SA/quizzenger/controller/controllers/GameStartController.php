<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class GameStartController{
		private $view;
		private $request;

		private $gameid;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );

			$this->gameid = $this->request ['gameid'];
			$this->gameinfo = $this->getGameInfo();

		}
		public function render(){
			PermissionUtility::checkLogin();

			$this->loadGameStartView();

			$this->setGameSession();

			return $this->view->loadTemplate();
		}

		private function loadGameStartView(){
			$this->view->setTemplate ( 'gamestart' );

			$this->checkGameStarted($this->gameinfo['starttime']);
			$this->view->assign ( 'gameinfo', $this->gameinfo );

			$isMember = ModelCollection::gameModel()->isGameMember($_SESSION['user_id'], $this->gameid);
			$this->view->assign ( 'isMember', $isMember );

			$isOwner = $this->isGameOwner($this->gameinfo['owner_id']);
			$this->view->assign ( 'isOwner', $isOwner );

			$members = ModelCollection::gameModel()->getGameMembersByGameId($this->gameid);
			$this->view->assign ( 'members', $members );

		}
		private function setGameSession(){
			$_SESSION [$this->gameid.'gamequestions'] = ModelCollection::quizModel()->getQuestionArray ( $this->gameinfo['quiz_id'] );
			$_SESSION [$this->gameid.'gamecounter'] = 0;
			$sessionData = ModelCollection::gameModel()->getSessionData($_SESSION['user_id'], $this->gameid);
			$_SESSION ['game'][$this->gameid]['gamequestions'] = $sessionData['gamequestions'];
			$_SESSION ['game'][$this->gameid]['gamecounter'] = $sessionData['gamecounter'];
		}

		/*
		 * Gets the Gameinfo.
		 */
		private function getGameInfo(){
			$gameinfo = ModelCollection::gameModel()->getGameInfoByGameId($this->gameid);
			return $gameinfo;
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}

		private function checkGameStarted($has_started){
			if ( isset($has_started)) {
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_game_has_started');
				NavigationUtility::redirectToErrorPage();
			}
		}
	} // class GameController
} // namespace quizzenger\controller\controllers

?>