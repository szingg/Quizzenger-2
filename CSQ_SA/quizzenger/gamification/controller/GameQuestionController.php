<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameQuestionController{
		private $view;
		private $sqlhelper;
		private $request;

		private $gameModel;
		private $questionModel;
		private $quizModel;
		private $answerModel;
		private $reportModel;
		private $categoryModel;

		private $gameid;
		private $gamequestions;
		private $gamecounter;
		private $gameinfo;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->request = array_merge ( $_GET, $_POST );

			$this->gameModel = new GameModel($this->sqlhelper);
			$this->questionModel = new \QuestionModel($this->sqlhelper, log::get());
			$this->quizModel = new \QuizModel($this->sqlhelper, log::get()); // Backslash means: from global Namespace
			$this->answerModel = new \AnswerModel($this->sqlhelper, log::get());
			$this->reportModel = new \ReportModel($this->sqlhelper, log::get());
			$this->categoryModel = new \CategoryModel($this->sqlhelper, log::get());

			$this->checkGameSessionParams();
			$this->gameid = $this->request ['gameid'];

			$this->gamequestions = $_SESSION ['game'][$this->gameid]['gamequestions'];
			$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			$this->gameinfo = $this->getGameInfo();
		}
		public function loadView(){
			$this->checkPreconditions();

			$this->view->setTemplate( 'gamequestion' );
			$this->loadQuestionView();

			$this->loadReportView();

			return $this->view;
		}

		private function LoadQuestionView(){
			$questionView = new \View();
			$questionView->setTemplate ( 'question' );

			$questionView->assign ( 'session_id', '' );

			$questionID= $this->gamequestions[$this->gamecounter];
			$questionView->assign ( 'questionID', $questionID );
			$question = $this->questionModel->getQuestion ( $questionID );
			$questionView->assign ( 'question', $question );
			$categoryName = $this->categoryModel->getNameByID ( $question ['category_id'] );
			$questionView->assign ( 'category', $categoryName );

			$answers = $this->answerModel->getAnswersByQuestionID ( $questionID );
			$questionView->assign ( 'answers', $answers );
			$linkToSolution = '?view=GameSolution&gameid='.$this->gameid;
			$questionView->assign ( 'linkToSolution', $linkToSolution );

			$alreadyReported= $this->reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
			$questionView->assign ('alreadyreported',$alreadyReported);

			//assign GameSession
			$questionCount= count ( $this->gamequestions );
			$questionView->assign ( 'questioncount', $questionCount );
			$currentCounter= $this->gamecounter;
			$questionView->assign ( 'currentcounter', $currentCounter );
			$progress = round ( 100 * ($currentCounter / $questionCount) );
			$questionView->assign ( 'progress', $progress );
			$weight= $this->quizModel->getWeightOfQuestionInQuiz($questionID, $this->gameinfo['quiz_id']);
			$questionView->assign ( 'weight', $weight);

			$this->view->assign ( 'questionView', $questionView->loadTemplate() );
		}

		private function loadReportView(){
			$reportView = new \View();
			$reportView->setTemplate ( 'gamereport' );
			$reportView->assign('gameinfo', $this->gameinfo);
			$gameReport = $this->gameModel->getGameReport($this->gameid);
			$reportView->assign('gamereport', $gameReport);

			$this->view->assign ( 'reportView', $reportView->loadTemplate() );
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
		 * Redirects if at leaste one condition fails
		 * @Precondition User is logged in
		 * @Precondition User is game member
		 * @Precondition Game has started
		 * @Precondition Game is not finished
		 */
		private function checkPreconditions(){
			checkLogin();

			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);

			if($isMember && ( $this->isFinished($this->gameinfo['is_finished']) || $this->gamecounter >= count($this->gamequestions)) ){
				redirect('./index.php?view=GameEnd&gameid='.$this->gameid);
			}

			if($isMember==false && $this->hasStarted($this->gameinfo['has_started'])){
				redirectToErrorPage('err_game_has_started');
			}

			//checkConditions
			if($isMember==false || $this->isFinished($this->gameinfo['is_finished'])
					|| $this->hasStarted($this->gameinfo['has_started'])==false ){
				redirectToErrorPage('err_not_authorized');
			}
		}
		private function checkGameSessionParams(){
			if(! isset($this->request ['gameid'],
					$_SESSION ['game'][$this->request ['gameid']]['gamequestions'],
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter'])
					){
						redirectToErrorPage('err_not_authorized');
			}
		}

		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}
		private function hasStarted($has_started){
			return isset($has_started);
		}
		private function isFinished($is_finished){
			return isset($is_finished);
		}

	} // class GameQuestionController
} // namespace quizzenger\gamification\controller

?>