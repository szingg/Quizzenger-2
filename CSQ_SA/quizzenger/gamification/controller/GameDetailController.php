<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\utilities\FormatUtility as FormatUtility;
	use \quizzenger\gamification\model\GameModel as GameModel;
use quizzenger\model\ModelCollection;


	class GameDetailController{
		private $view;
		private $gameModel;
		private $sqlhelper;
		private $request;
		private $gameid;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(Log::get());
			$this->gameModel = new GameModel($this->sqlhelper);
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
			$gamereport = $this->gameModel->getGameReport($this->gameid);
			$this->view->assign ( 'gamereport', $gamereport);
			$questions = $this->gameModel->getQuestionDetailsByGame($this->gameid);
			$this->view->assign ( 'questions', $questions);

		}

		/*
		 * Gets the Gameinfo.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
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
} // namespace quizzenger\gamification\controller

?>