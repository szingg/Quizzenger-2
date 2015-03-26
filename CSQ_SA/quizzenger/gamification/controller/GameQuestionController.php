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

			
			$this->gameid = $_SESSION['gameid'];
			$this->gamequestions = $_SESSION['gamequestions'];
			$this->gamecounter = $_SESSION['gamecounter'];
			$this->gameinfo = $this->getGameInfo();
		}
		public function loadView(){
			$this->checkPreconditions();

			$this->view->setTemplate( 'gamequestion' );
			$this->loadQuestionView();
			
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
		 * @Precondition Setted SESSION params
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