<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\view\View as View;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\model\ModelCollection as ModelCollection;


	class GameEndController{
		private $view;
		private $request;

		private $gameid;
		private $gamequestions;
		private $gamecounter;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );

			$this->checkGameSessionParams();
			$this->gameid = $this->request ['gameid'];
			$this->gamequestions = $_SESSION ['game'][$this->gameid]['gamequestions'];
			$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			$this->gameinfo = $this->getGameInfo();

		}
		public function render(){
			$this->checkPreconditions();

			$this->loadGameEndView();

			$this->loadReportView();

			return $this->view->loadTemplate();
		}

		private function loadGameEndView(){
			$this->view->setTemplate ( 'gameend' );

			$score = ModelCollection::quizModel()->getSingleChoiceScoreByGameId ( $this->gameinfo['game_id'], $this->gameinfo['quiz_id'] );
			$maxScore = ModelCollection::quizModel()->getMaxSingleChoiceScore ( $this->gameinfo['quiz_id'] );

			$this->view->assign ( 'score', $score );
			$this->view->assign ( 'maxScore', $maxScore );

			$this->view->assign ( 'gameinfo', $this->gameinfo );

		}

		private function loadReportView(){
			$reportView = new View();
			$reportView->setTemplate ( 'gamereport' );

			$this->view->assign ( 'reportView', $reportView->loadTemplate());
		}

		/*
		 * Redirects if at leaste one condition fails
		 * @Precondition User is logged in
		 * @Precondition Setted SESSION and request params
		 * @Precondition User is game member
		 * @Precondition Game has started
		 */
		private function checkPreconditions(){
			PermissionUtility::checkLogin();

			$isMember = ModelCollection::gameModel()->isGameMember($_SESSION['user_id'], $this->gameid);

			//checkConditions
			if(($isMember==false && $this->isGameOwner($this->gameinfo['owner_id'])==false) || $this->hasStarted($this->gameinfo['starttime'])==false){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}

		private function checkGameSessionParams(){
			if(! isset($this->request ['gameid'])){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
			/*
			if(! isset($_SESSION ['game'][$this->request ['gameid']]['gamequestions'],
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter']))
			{
				if($this->gameModel->isGameMember($_SESSION['user_id'], $this->request ['gameid'])){
					//restore GameSession
					$sessionData = $this->gameModel->getSessionData($_SESSION['user_id'], $this->request ['gameid']);
					$_SESSION ['game'][$this->request ['gameid']]['gamequestions'] = $sessionData['gamequestions'];
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter'] = $sessionData['gamecounter'];
				}
				else{
					MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
					NavigationUtility::redirectToErrorPage();
				}
			}*/
			//restore GameSession
			$sessionData = ModelCollection::gameModel()->getSessionData($_SESSION['user_id'], $this->request ['gameid']);
			$_SESSION ['game'][$this->request ['gameid']]['gamequestions'] = $sessionData['gamequestions'];
			$_SESSION ['game'][$this->request ['gameid']]['gamecounter'] = $sessionData['gamecounter'];
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
		private function hasStarted($has_started){
			return isset($has_started);
		}

	} // class GameController
} // namespace quizzenger\controller\controllers

?>