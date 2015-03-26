<?php

namespace quizzenger\gamification\controller {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
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

			
			$this->gameid = $_SESSION['gameid'];
			$this->gamequestions = $_SESSION['gamequestions'];
			$this->gamecounter = $_SESSION['gamecounter'];
			$this->gameinfo = $this->getGameInfo();
		}
		public function loadView(){
			$this->checkPreconditions();

			$this->view->setTemplate( 'gamesolution' );
			
			$this->loadSolutionView();
			//$this->loadQuestionView();
			
			$this->loadAdminView();

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
			$linkToSolution = '?view=gamesolution&id='.$questionID;
			$questionView->assign ( 'linkToSolution', $linkToSolution );
				
			$alreadyReported= $this->reportModel->checkIfUserAlreadyDoneReport("question", $questionID , $_SESSION ['user_id']);
			$questionView->assign ('alreadyreported',$alreadyReported);
				
			//setGameSession
			$questionCount= count ( $_SESSION ['gamequestions'] );
			$questionView->assign ( 'questioncount', $questionCount );
			$currentCounter= $_SESSION ['gamecounter'];
			$questionView->assign ( 'currentcounter', $currentCounter );
			$progress = round ( 100 * ($currentCounter / $questionCount) );
			$questionView->assign ( 'progress', $progress );
			$weight= $this->quizModel->getWeightOfQuestionInQuiz($questionID, $this->gameinfo['quiz_id']);
			$questionView->assign ( 'weight', $weight);
				
			$this->view->assign ( 'questionView', $questionView->loadTemplate() );
		}
		
		private function loadSolutionView(){
			$solutionView = new \View();
			$solutionView->setTemplate ( 'solution' );
			
			if (isset ( $this->request ['id'] ) && isset ( $this->request ['answer'] )) {
				$viewInner->setTemplate ( 'solution' );
			
				$question = $questionModel->getQuestion ( $this->request ['id'] );
				$author = $userModel->getUsernameByID ( $question ['user_id'] );
				$userIsModHere =$userModel-> userIsModeratorOfCategory($_SESSION['user_id'], $question ['category_id']);
			
				$categoryName = $categoryModel->getNameByID ( $question ['category_id'] );
			
				$answers = $answerModel->getAnswersByQuestionID ( $this->request ['id'] );
				$selectedAnswer = $this->request ['answer'];
				$correctAnswer = $answerModel->getCorrectAnswer ( $this->request ['id'] );
			
				$alreadyReported= $reportModel->checkIfUserAlreadyDoneReport("question", $this->request ['id'] , $_SESSION ['user_id']);
				$viewInner->assign ('alreadyreported',$alreadyReported);
			
				include("rating.php");
			
			
				if($GLOBALS['loggedin'] && $correctAnswer == $selectedAnswer){
					if(!$userscoreModel->hasUserScoredQuestion( $this->request ['id'],$_SESSION['user_id'])){ // no multiple scoring for question
						$userscoreModel->addScoreToCategory($_SESSION['user_id'], $question ['category_id'],QUESTION_ANSWERED_SCORE, $moderationModel);
						$viewInner->assign ( 'pointsearned', QUESTION_ANSWERED_SCORE );
					}
				}
			
				include("helper/solution_report.php");
			
			
				$viewInner->assign ( 'answers', $answers );
				$viewInner->assign ( 'category', $categoryName );
				$viewInner->assign ( 'author', $author );
			
				$viewInner->assign ( 'questionID', $this->request ['id'] );
				$viewInner->assign ( 'selectedAnswer', $selectedAnswer );
				$viewInner->assign ( 'userismodhere', $userIsModHere );
				$viewInner->assign ( 'question', $question );
			
				// Implement other Strategies if other question types are desired
				$correct = ($correctAnswer == $selectedAnswer ? 100 : 0);
			
				$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
			
			
				// Only relevant if question was answered in quiz context
				if (isset ( $this->request ['session_id'] )  ) {
					$session_id = $this->request ['session_id'];
					$inc_counter=0;
					if ($questionModel->answerExists ( $session_id, $this->request ['id'], $_SESSION['user_id'] ) == 0) { // Normal Quiz
						$questionModel->InsertQuestionPerformance ( $this->request ['id'], $_SESSION ['user_id'], $correct, $session_id );
						$inc_counter=1;
					}
					$_SESSION ['counter'. $session_id] += $inc_counter;
					$questionCount= count ( $_SESSION ['questions'. $session_id] );
					$currentCounter= $_SESSION ['counter'. $session_id];
					$progress = round ( 100 * ($currentCounter / $questionCount) );
					$viewInner->assign ( 'progress', $progress );
					$viewInner->assign ( 'questioncount', $questionCount );
					$viewInner->assign ( 'currentcounter', $currentCounter );
					$viewInner->assign ( 'progress', $progress );
			
					if (count ( $_SESSION ['questions'. $session_id] ) > $_SESSION ['counter'. $session_id]) {
						$viewInner->assign ( 'nextQuestion', "?view=question&id=" . $_SESSION ['questions'. $session_id] [$_SESSION ['counter'. $session_id]] . "&amp;session_id=" . $session_id);
					} else {
						$viewInner->assign ( 'nextQuestion', "?view=quizend&session_id=". $session_id);
					}
				}
				else { // not in quiz context
					if(!$pageWasRefreshed){
						$questionModel->InsertQuestionPerformance ( $this->request ['id'], $_SESSION ['user_id'], $correct, NULL);
					}
				}
			}
			
			$this->view->assign ( 'solutionView', $solutionView->loadTemplate() );
		}
		
		private function loadAdminView(){
			$adminView = "";
			if($this->isGameOwner($this->gameinfo['owner_id'])){
				$adminView = new \View();
				$adminView->setTemplate ( 'gameadmin' );
				$adminView->assign('gameinfo', $this->gameinfo);
					
				$adminView = $adminView->loadTemplate();
			}
			$this->view->assign ( 'adminView', $adminView );
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
		 * @Precondition Setted cookie params
		 * @Precondition User is game member
		 * @Precondition Game has started
		 * @Precondition Game is not finished 
		 */
		private function checkPreconditions(){
			checkLogin();
			
			//check session-fields
			if(! isset($_SESSION['gameid'], $_SESSION['gamequestions'], $_SESSION['gamecounter'])) redirectToErrorPage('err_not_authorized');
				
			$isMember = $this->gameModel->isGameMember($_SESSION['user_id'], $this->gameid);
				
			if($isMember && $this->isFinished($this->gameinfo['is_finished'])){
				//TODO: redirect('gameend');
			}
			
			//checkConditions
			if($isMember==false || $this->isFinished($this->gameinfo['is_finished']) || $this->hasStarted($this->gameinfo['has_started'])==false){
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

		/*
		private function checkLogin(){
			if (! $GLOBALS ['loggedin']) {
				header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
				die ();
			}
		}

		private function redirectToErrorPage($errorCode = 'err_db_query_failed'){
			header('Location: ./index.php?view=error&err='.$errorCode);
			die ();
		} */
	} // class GameQuestionController
} // namespace quizzenger\gamification\controller
	
?>