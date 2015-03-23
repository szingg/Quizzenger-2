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

			$gameid = $this->gameModel->getNewGameSessionId($this->request ['quizid'], $this->request ['gamename']);

			$this->redirect($gameid);
		}

		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}

		private function redirect($gameid){
			if($gameid == null){
				define ( "err_db_query_failed", "Oops, es wurde eine ungültige Datenbank abfrage getätigt" );
			}
			else{
				header ( 'Location: ./index.php?view=gamestart&gameid=' . $gameid );
			}
			die ();
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>