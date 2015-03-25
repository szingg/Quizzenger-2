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
			$this->checkLogin();
			
			$quiz_id = $this->request ['quizid'];
			$gamename = $this->request ['gamename'];
			
			$this->checkPermission($quiz_id);
			$gameid = $this->gameModel->getNewGameSessionId($quiz_id, $gamename);

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
		
		/*
		 * Checks login. dies if not logged in
		 */
		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}

		private function redirect($gameid){
			if($gameid == null){
				header('Location: ./index.php?view=error&err=err_db_query_failed');
			}
			else{
				header ( 'Location: ./index.php?view=gamestart&gameid=' . $gameid );
			}
			die ();
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>