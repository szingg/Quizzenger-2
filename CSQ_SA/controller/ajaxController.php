<?php
class AjaxController {	
	private $request = null;
	private $template = '';
	private $viewOuter = null;
	private $mysqli;
	private $logger;

	public function __construct($request,$pLog) {
		$this->logger = $pLog;
		$this->viewOuter = new View ();
		$this->request = $request;
		$this->template = ! empty ( $request ['view'] ) ? $request ['view'] : 'defaultajax';
		$this->mysqli = new sqlhelper ($this->logger);
	}

	public function display() {
		$viewInner = new View ();
		$viewInner->setTemplate('defaultajax');

		$ratingModel = new RatingModel($this->mysqli,$this->logger);
		$categoryListModel = new CategoryModel($this->mysqli,$this->logger);
		$quizModel = new QuizModel($this->mysqli,$this->logger);
		$questionModel = new QuestionModel($this->mysqli, $this->logger);
		$sessionModel = new SessionModel ( $this->mysqli, $this->logger );
		$reportModel = new ReportModel( $this->mysqli, $this->logger );
		$questionModel = new QuestionModel($this->mysqli, $this->logger);
		$userModel = new UserModel( $this->mysqli, $this->logger );
		$gameModel = new \quizzenger\gamification\model\GameModel($this->mysqli, $quizModel);

		$sessionModel->sec_session_start();

		switch ($this->template) {
			// -------------------------------------------------------
			case 'addrating_ajax':
				$ret = $ratingModel->newRating($this->request['question_id'],$this->request['stars'],$this->request['comment'],$this->request['parent']);
				break;
			// -------------------------------------------------------
			case 'categorylist_ajax':
				$viewInner->setTemplate('categorylist_ajax');
				$categories = $categoryListModel->getChildren($this->request['id']);
				$categories = $categoryListModel->fillCategoryListWithQuestionCount($categories);
				if(isset($this->request['mode'])){
					$viewInner->assign('mode', $this->request['mode']);
				}
				$viewInner->assign('categories', $categories);
				$viewInner->assign('container', $this->request['container']);
				break;
			// -------------------------------------------------------
			case 'remove_quizquestion':
				$quizModel->removeQuestionFromQuiz($this->request['quiz'], $this->request['question']);
				break;
			// -------------------------------------------------------
			case 'remove_question':
				$questionModel->removeQuestion($this->request['question']);
				break;
			// -------------------------------------------------------
			case 'remove_quiz':
				$quizModel->removeQuiz($this->request['quiz']);
				break;
			// -------------------------------------------------------
			case 'remove_sub_cat':
				$quizModel->removeQuiz($this->request['id']);
				break;
			// -------------------------------------------------------
			case 'inactive_user':
				$retVal=$userModel->setUserInactiveByID($this->request['id']);
				if($retVal!=1){ // if not authorized -> dont remove reports
					$reportModel->removeReportsByObject($this->request['id'], 'user', $_SESSION['user_id']);
				}
				break;
			// -------------------------------------------------------
			case 'remove_reports':
				if(isset($this->request['id'], $this->request['reporttype'])){
					$reportModel->removeReportsByObject($this->request['id'], $this->request['reporttype'], $_SESSION['user_id']);
				}
				break;
			// -------------------------------------------------------
			case 'set_weight':
				$questionModel->setWeight($this->request['id'], $this->request['weight']);
				break;
			// -------------------------------------------------------
			case 'report_list':
				if(isset($this->request['id'], $this->request['reporttype'])){
					$viewInner->setTemplate('reportList');
					$list = $reportModel->getReportsByObject($this->request['id'], $this->request['reporttype']);
					$viewInner->assign('reports', $list);
				}
				break;
			case 'fileupload':
				require_once("/../quizzenger/fileupload/fileupload.php");
				$fileupload = new FileUpload($_FILES);
				return $fileupload->processFileUpload();
			case 'joinGame' :
				$result = $gameModel->userJoinGame($_SESSION['user_id'], $this->request['gameid']);
				$result == 0? $result = 'success' : 'error';
				return $this->sendJSONResponse($result, '', '');
			case 'leaveGame' :
				$gameModel->userLeaveGame($_SESSION['user_id'], $this->request['gameid']);
				break;
			case 'startGame' :
				$result = $gameModel->startGame($this->request['gameid']);
				break;
			case 'stopGame' :
				$result = $gameModel->stopGame($this->request['gameid']);
				break;
			case 'getGameMembers': 
				$data = $gameModel->getGameMembersByGameId($this->request['gameid']);
				return $this->sendJSONResponse('', '', $data);
			case 'getGameStartInfo' : 
				$game_id = $this->request['gameid'];
				$gameinfo = $gameModel->getGameInfoByGameId($game_id)[0];
				$isMember = $gameModel->isGameMember($_SESSION['user_id'], $game_id);
				$members = $gameModel->getGameMembersByGameId($game_id);
				
				$data = [
						'gameinfo' => $gameinfo,
						'isMember' => $isMember,
						'members' => $members
				];
				return $this->sendJSONResponse('', '', $data);
			case 'getOpenGames' :
				$data = $gameModel->getOpenGames();
				return $this->sendJSONResponse('', '', $data);
			case 'default' :
			default :
				break;
		}

		$this->viewOuter->setTemplate ( 'blankContent' );
		$this->viewOuter->assign ( 'content', $viewInner->loadTemplate () );
		return $this->viewOuter->loadTemplate ();
	}
	
	/*
	 * Sends a json response.
	 * @param $result e.g. success or error
	 * @param $message send an optional message
	 * @param $data optional data
	 */
	private function sendJSONResponse($result, $message, $data) {
		header('Content-Type: application/json');
		echo json_encode(array('result' => $result, 'message' => $message, 'data' => $data));
	}
}
?>
