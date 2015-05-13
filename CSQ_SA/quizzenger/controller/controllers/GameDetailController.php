<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\utilities\FormatUtility as FormatUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class GameDetailController{
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
			$this->checkPermission();

			$this->loadGameDetailView();

			return $this->view->loadTemplate();
		}

		private function loadGameDetailView(){
			$this->view->setTemplate ( 'gamedetail' );

			$this->view->assign ( 'gameinfo', $this->gameinfo);
			$gamereport = ModelCollection::gameModel()->getGameReport($this->gameid);
			$this->view->assign ( 'gamereport', $gamereport);
			$questions = ModelCollection::gameModel()->getQuestionDetailsByGame($this->gameid);
			$this->view->assign ( 'questions', $questions);

		}

		/*
		 * Gets the Gameinfo.
		 */
		private function getGameInfo(){
			$gameinfo = ModelCollection::gameModel()->getGameInfoByGameId($this->gameid);
			return $gameinfo;
		}

		/*
		 * Checks if user is logged in and his permission on this game. Dies if not permitted.
		 */
		private function checkPermission(){
			PermissionUtility::checkLogin();
			if(ModelCollection::gameModel()->isGameMember($_SESSION['user_id'], $this->gameid) == false){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}


	} // class GameController
} // namespace quizzenger\controller\controllers

?>