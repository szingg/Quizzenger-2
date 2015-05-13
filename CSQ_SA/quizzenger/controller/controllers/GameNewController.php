<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class GameNewController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			PermissionUtility::checkLogin();

			$quiz_id = $this->request ['quizid'];
			$gamename = $this->request ['gamename'];
			$gameduration = $this->request ['gameduration'];
			$gameduration < MIN_GAME_DURATION_MINUTES ? $gameduration = MIN_GAME_DURATION_MINUTES : '';
			$gameduration > MAX_GAME_DURATION_MINUTES ? $gameduration = MAX_GAME_DURATION_MINUTES : '';
			$hours = (string)((int) ($gameduration / 60));
			$minutes = (string) ($gameduration % 60);
			$time = (string)$hours.':'.$minutes.':00';

			$this->checkPermission($quiz_id);
			$gameid = ModelCollection::gameModel()->getNewGameSessionId($quiz_id, $gamename, $time);

			$this->redirect($gameid);
		}

		/*
		 * Checks Permission for given quiz id. dies if not permitted
		 */
		private function checkPermission($quiz_id){
			if (! ModelCollection::quizModel()->userIDhasPermissionOnQuizId($quiz_id,$_SESSION ['user_id'])) {
				log::warning('Unauthorized try to add new Gamesession for Quiz-ID :'.game_id);
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}

		private function redirect($gameid){
			if($gameid == null){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_db_query_failed');
				NavigationUtility::redirectToErrorPage();
			}
			else{
				NavigationUtility::redirect('./index.php?view=GameStart&gameid=' . $gameid);
			}
		}
	} // class GameController
} // namespace quizzenger\controller\controllers

?>