<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \Logger as Logger;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameController{
		private $mysqli;
		private $view;
		private $gameModel;
		private $logger;
		private $sqlhelper;
		private $request;
		private $quizModel;
		

		public function __construct($view) {
			$this->view = $view;
			$this->logger = new Logger();
			$this->sqlhelper = new SqlHelper($this->logger);
			$this->quizModel = new \QuizModel($this->sqlhelper, $this->logger); // Backslash means: from global Namespace
			$this->gameModel = new GameModel($this->sqlhelper, $this->quizModel);
			$this->request = array_merge ( $_GET, $_POST );
			
		}
		
		public function loadView(){
			//gamestart
			$this->view->setTemplate ( 'gamestart' );
			
			$game_id = $this->request ['gamesessionid'];
			
			$_SESSION ['quiz_id'. $session_id] = $this->request ['quizid'];
			$_SESSION ['questions'. $session_id] = $this->quizModel->getQuestionArray ( $this->request ['quizid'] );
			$_SESSION ['counter'. $session_id] = 0;
			
			if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
				$firstUrl = "?view=question&id=" . $_SESSION ['questions'. $session_id] [0] . "&gamesession_id=". $game_id;
			} else {
				$firstUrl = "?view=quizend";
			}
			
			$quizinfo = array (
					'quizid' => $this->request ['quizid'],
					'quizname' => $this->quizModel->getQuizName ( $this->request ['quizid'] ),
					'firstUrl' => $firstUrl
			);
			
			$this->view->assign ( 'quizinfo', $quizinfo );
			return $this->view;
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>