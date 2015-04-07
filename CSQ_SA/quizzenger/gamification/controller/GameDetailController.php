<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;


	class GameDetailController{
		private $view;
		private $gameModel;
		private $sqlhelper;
		private $request;
		private $gameid;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->gameModel = new GameModel($this->sqlhelper);
			$this->request = array_merge ( $_GET, $_POST );

			$this->gameid = $this->request ['gameid'];
			$this->gameinfo = $this->getGameInfo();

		}
		public function loadView(){
			checkLogin();
			$this->checkPermission();

			$this->loadGameDetailView();

			return $this->view;
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
		 * Gets the Gameinfo. Redirects to errorpage when no result returned.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			if(count($gameinfo) <= 0) redirectToErrorPage('err_db_query_failed');
			else return $gameinfo[0];
		}

		/*
		 * Checks permission for the given game. Dies if not permitted.
		 */
		private function checkPermission(){
			if($this->isGameOwner($this->gameinfo['owner_id']) == false){
				redirectToErrorPage('err_not_authorized');
			}
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}


	} // class GameController
} // namespace quizzenger\gamification\controller

?>