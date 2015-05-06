<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\controlling\EventController as EventController;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\gamification\model\GameModel as GameModel;

	class GameSolutionController{
		private $view;
		private $sqlhelper;
		private $request;

		private $gameModel;
		private $questionModel;
		private $quizModel;
		private $answerModel;
		private $reportModel;
		private $categoryModel;
		private $userscoreModel;
		//private $userModel;

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
			$this->userscoreModel = new \UserScoreModel($this->sqlhelper, log::get());
			//$this->userModel = new \UserModel($this->sqlhelper, log::get());

			$this->checkGameSessionParams();
			$this->gameid = $this->request ['gameid'];
			$this->gamequestions = $_SESSION ['game'][$this->gameid]['gamequestions'];
			$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			$this->gameinfo = $this->getGameInfo();
		}
		public function loadView(){
			$this->checkPreconditions();

			$this->view->setTemplate( 'gamesolution' );

			$this->loadSolutionView();

			$this->loadReportView();

			return $this->view;
		}

		private function loadSolutionView(){
			$solutionView = new \View();
			$solutionView->setTemplate ( 'solution' );

			$questionID = $this->gamequestions[$this->gamecounter];
			$solutionView->assign ( 'questionID', $questionID );
			$question = $this->questionModel->getQuestion ( $questionID );
			$solutionView->assign ( 'question', $question );

			$categoryName = $this->categoryModel->getNameByID ( $question ['category_id'] );
			$solutionView->assign ( 'category', $categoryName );

			$answers = $this->answerModel->getAnswersByQuestionID ( $questionID );
			$order = $_SESSION['questionorder'][$questionID];
			array_multisort($order, $answers);
			$solutionView->assign ( 'answers', $answers );
			$selectedAnswer = $this->request ['answer'];
			$solutionView->assign ( 'selectedAnswer', $selectedAnswer );

			$alreadyReported= $this->reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
			$solutionView->assign ('alreadyreported',$alreadyReported);

			$correctAnswer = $this->answerModel->getCorrectAnswer ( $questionID );

			//Score
			if($GLOBALS['loggedin'] && $correctAnswer == $selectedAnswer){
				if(!$this->userscoreModel->hasUserScoredQuestion( $questionID, $_SESSION['user_id'])){ // no multiple scoring for question
					EventController::fire('game-question-answered-correct', $_SESSION['user_id'], [
						'gameid' => $this->gameid,
						'category' => $question['category_id']
					]);
				}
			}
			//$session_id = $this->request ['session_id'];
			//$inc_counter=0;

			if (! $this->questionModel->gameAnswerExists ( $this->gameid, $questionID, $_SESSION['user_id'] )) {
				// Implement other Strategies if other question types are desired
				$correct = ($correctAnswer == $selectedAnswer ? 100 : 0);
				$this->questionModel->InsertQuestionPerformance ( $questionID, $_SESSION ['user_id'], $correct, null, $this->gameid );
				$_SESSION ['game'][$this->gameid]['gamecounter'] =  $this->gamecounter + 1;
				$this->gamecounter = $_SESSION ['game'][$this->gameid]['gamecounter'];
			}
			//$_SESSION['gamecounter'] += $inc_counter;

			$questionCount = count ( $this->gamequestions );
			$solutionView->assign ( 'questioncount', $questionCount );
			$solutionView->assign ( 'currentcounter', $this->gamecounter );
			$progress = round ( 100 * ($this->gamecounter / $questionCount) );
			$solutionView->assign ( 'progress', $progress );

			if ($questionCount > $this->gamecounter) {
				$solutionView->assign ( 'nextQuestion', '?view=GameQuestion&gameid='.$this->gameid);
			} else {
				$solutionView->assign ( 'nextQuestion', '?view=GameEnd&gameid='.$this->gameid);
			}

			$solutionView->assign ( 'ratingView', '');
			/* DELETE
			//not in quiz context
			$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
			if(!$pageWasRefreshed){
				$questionModel->InsertQuestionPerformance ( $this->request ['id'], $_SESSION ['user_id'], $correct, NULL);
			}*/

			$this->view->assign ( 'solutionView', $solutionView->loadTemplate() );
		}

		private function loadReportView(){
			$reportView = new \View();
			$reportView->setTemplate ( 'gamereport' );
			/*
			$reportView->assign('gameinfo', $this->gameinfo);
			$gameReport = $this->gameModel->getGameReport($this->gameid);
			$reportView->assign('gamereport', $gameReport);

			$now = date("Y-m-d H:i:s");
			$durationSec = FormatUtility::timeToSeconds($this->gameinfo['duration']);
			$timeToEnd = strtotime($this->gameinfo['calcEndtime']) - strtotime($now);
			$progressCountdown = (int) (100 / $durationSec * $timeToEnd);
			$reportView->assign( 'timeToEnd', $timeToEnd);
			$reportView->assign( 'progressCountdown', $progressCountdown);
			*/

			$this->view->assign ( 'reportView', $reportView->loadTemplate() );
		}

		/*
		 * Gets the Gameinfo.
		 */
		private function getGameInfo(){
			$gameinfo = $this->gameModel->getGameInfoByGameId($this->gameid);
			return $gameinfo;
		}

		/*
		 * Redirects if at leaste one condition fails
		 * @Precondition User is logged in
		 * @Precondition User is game member
		 * @Precondition Game has started
		 * @Precondition Game is not finished
		 */
		private function checkPreconditions(){
			PermissionUtility::checkLogin();

			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);

			$now = date("Y-m-d H:i:s");
			$timeToEnd = strtotime($this->gameinfo['calcEndtime']) - strtotime($now);
			$finished = $timeToEnd <= 0 || isset($this->gameinfo['endtime']);

			if($isMember && ( $finished || $this->gamecounter >= count($this->gamequestions)) ){
				NavigationUtility::redirect('./index.php?view=GameEnd&gameid='.$this->gameid);
			}

			//checkConditions
			if(isset($this->request ['answer'])==false
					|| $isMember==false
					|| $finished
					|| $this->hasStarted($this->gameinfo['starttime'])==false){

				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}
		private function checkGameSessionParams(){
			if(! isset($this->request ['gameid'],
					$_SESSION ['game'][$this->request ['gameid']]['gamequestions'],
					$_SESSION ['game'][$this->request ['gameid']]['gamecounter'],
					$this->request ['answer'] )
			){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized');
				NavigationUtility::redirectToErrorPage();
			}
		}


		private function isGameOwner($owner_id){
			return $owner_id == $_SESSION['user_id'];
		}
		private function hasStarted($has_started){
			return isset($has_started);
		}
	} // class GameQuestionController
} // namespace quizzenger\gamification\controller

?>
