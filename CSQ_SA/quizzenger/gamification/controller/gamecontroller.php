<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameController{
		private $mysqli;
		private $view;
		private $gameModel;

		public function __construct($view) {
			$this->view = view;
			$this->gameModel = new GameModel();
		}
		
		public function loadView(){

			$view->setTemplate ( 'gamestart' );
			
			$session_id = $quizModel->getNewSessionId ($this->request ['quizid']);
			
			$_SESSION ['quiz_id'. $session_id] = $this->request ['quizid'];
			$_SESSION ['questions'. $session_id] = $quizModel->getQuestionArray ( $this->request ['quizid'] );
			$_SESSION ['counter'. $session_id] = 0;
			
			if (count ( $_SESSION ['questions'. $session_id] ) > 0) {
				$firstUrl = "?view=question&id=" . $_SESSION ['questions'. $session_id] [0] . "&session_id=". $session_id;
			} else {
				$firstUrl = "?view=quizend";
			}
			
			$quizinfo = array (
					'quizid' => $this->request ['quizid'],
					'quizname' => $quizModel->getQuizName ( $this->request ['quizid'] ),
					'firstUrl' => $firstUrl
			);
			
			$viewInner->assign ( 'quizinfo', $quizinfo );
			return $this->view;
		}
	} // class GameController
} // namespace quizzenger\gamification\controller

?>

