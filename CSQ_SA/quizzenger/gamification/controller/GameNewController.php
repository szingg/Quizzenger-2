<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameNewController{
		private $view;
		private $sqlhelper;
		private $quizModel;
		private $gameModel;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->gameModel = new GameModel($this->sqlhelper, $this->quizModel);
			$this->request = array_merge ( $_GET, $_POST );

		}

		public function loadView(){
			checkLogin();

			$quiz_id = $this->request ['quizid'];
			$gamename = $this->request ['gamename'];
			$gameduration = $this->request ['gameduration'];
			$gameduration < MIN_GAME_DURATION_MINUTES ? $gameduration = MIN_GAME_DURATION_MINUTES : '';
			$gameduration > MAX_GAME_DURATION_MINUTES ? $gameduration = MAX_GAME_DURATION_MINUTES : '';
			$hours = (string)((int) ($gameduration / 60));
			$minutes = (string) ($gameduration % 60);
			$time = (string)$hours.':'.$minutes.':00';
			//$time = strtotime("+1 week 2 days 4 hours 2 seconds");
			$strtotime = strtotime($gameduration.' minutes')-strtotime("now");
			$mysql_time = date('H:i:s',$gameduration*60);
			//$time2 = DateTime::createFromFormat( 'H:i:s', $duration);

			$this->checkPermission($quiz_id);
			$gameid = $this->gameModel->getNewGameSessionId($quiz_id, $gamename, $time);

			$this->redirect($gameid);
		}

		/*
		 * Checks Permission for given quiz id. dies if not permitted
		 */
		private function checkPermission($quiz_id){
			if (! $this->quizModel->userIDhasPermissionOnQuizId($quiz_id,$_SESSION ['user_id'])) {
				log::warning('Unauthorized try to add new Gamesession for Quiz-ID :'.game_id);
				header('Location: ./index.php?view=error&err=err_not_authorized');
				die();
			}
		}

		private function redirect($gameid){
			if($gameid == null){
				redirectToErrorPage('err_db_query_failed');
			}
			else{
				redirect('./index.php?view=GameStart&gameid=' . $gameid);
			}
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>