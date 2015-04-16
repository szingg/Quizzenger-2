<?php
use \quizzenger\controlling\EventController as EventController;
use \quizzenger\utilities\FormatUtility as FormatUtility;
use \quizzenger\logging\Log as Log;

class AjaxController {
	private $request = null;
	private $template = '';
	private $viewOuter = null;
	private $mysqli;
	private $logger;
	private $gameModel;

	public function __construct($request,$pLog) {
		$this->logger = $pLog;
		$this->viewOuter = new View ();
		$this->request = $request;
		$this->template = ! empty ( $request ['view'] ) ? $request ['view'] : 'defaultajax';
		$this->mysqli = new sqlhelper ($this->logger);
		$this->gameModel = new \quizzenger\gamification\model\GameModel($this->mysqli);

		EventController::setup($this->mysqli);
	}

	public function display() {
		$viewInner = new View ();
		$viewInner->setTemplate('defaultajax');

		$ratingModel = new RatingModel($this->mysqli,$this->logger);
		$categoryModel = new CategoryModel($this->mysqli,$this->logger);
		$quizModel = new QuizModel($this->mysqli,$this->logger);
		$questionModel = new QuestionModel($this->mysqli, $this->logger);
		$sessionModel = new SessionModel ( $this->mysqli, $this->logger );
		$reportModel = new ReportModel( $this->mysqli, $this->logger );
		$questionModel = new QuestionModel($this->mysqli, $this->logger);
		$userModel = new UserModel( $this->mysqli, $this->logger );
		$gameModel = $this->gameModel;

		$sessionModel->sec_session_start();

		switch ($this->template) {
			// -------------------------------------------------------
			case 'addrating_ajax':
				//check Permissions
				if(! $this->isLoggedin()) return;
				$parent = $this->request['parent'];
				$alreadyRated= $ratingModel->userHasAlreadyRated($this->request['question_id'] , $_SESSION ['user_id']);
				if((!isset($parent) || !is_numeric($parent)) && $alreadyRated) return;

				//make new rating
				$ret = $ratingModel->newRating($this->request['question_id'], $this->request['stars'],$this->request['comment'],$parent);
				break;
			// -------------------------------------------------------
			case 'categorylist_ajax':
				$viewInner->setTemplate('categorylist_ajax');
				$categories = $categoryModel->getChildren($this->request['id']);
				$categories = $categoryModel->fillCategoryListWithQuestionCount($categories);
				if(isset($this->request['mode'])){
					$viewInner->assign('mode', $this->request['mode']);
				}
				$viewInner->assign('categories', $categories);
				$viewInner->assign('container', $this->request['container']);
				break;
			// -------------------------------------------------------
			case 'remove_quizquestion':
				$result = $quizModel->removeQuestionFromQuiz($this->request['quiz'], $this->request['question']);
				return $this->sendJSONResponse(($result? 'success' : 'error'));
			// -------------------------------------------------------
			case 'remove_question':
				$result = $questionModel->removeQuestion($this->request['question']);
				return $this->sendJSONResponse(($result? 'success' : 'error'));
			// -------------------------------------------------------
			case 'remove_quiz':
				$result = $quizModel->removeQuiz($this->request['quiz']);
				return $this->sendJSONResponse(($result? 'success' : 'error'));
			// -------------------------------------------------------
			case 'remove_sub_cat':
				$cateogryId = $this->request['id'];
				$trueChild = $categoryModel->isTrueChild($cateogryId);
				if($trueChild){
					$result = $categoryModel->removeCategory($cateogryId);
				}
				else{
					$result = false;
				}
				return $this->sendJSONResponse(($result? 'success' : 'error'));
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
				return $this->sendJSONResponse(($result == 0? 'success' : 'error'), '', '');
			case 'leaveGame' :
				$gameModel->userLeaveGame($_SESSION['user_id'], $this->request['gameid']);
				break;
			case 'startGame' :
				$oldValue = $gameModel->startGame($this->request['gameid']);
				if(! isset($oldValue)){ //first time gameend was set
					EventController::fire('game-start', $_SESSION['user_id'], [
					'gameid' => $this->request['gameid']
					]);
				}
				break;
			case 'getGameReport' :
				$gameid = $this->request['gameid'];
				$gameReport = $gameModel->getGameReport($gameid);
				$gameinfo = $gameModel->getGameInfoByGameId($gameid)[0];

				$now = date("Y-m-d H:i:s");
				$durationSec = FormatUtility::timeToSeconds($gameinfo['duration']);
				$timeToEnd = strtotime($gameinfo['calcEndtime']) - strtotime($now);
				$progressCountdown = (int) (100 / $durationSec * $timeToEnd);

				if($gameinfo['endtime']==null && $this->isGameFinished($gameReport, $timeToEnd)){
					$this->setGameend($this->request['gameid']);
				}

				$data = [
						'gameReport' => $gameReport,
						'gameInfo' => $gameinfo,
						'timeToEnd' => $timeToEnd,
						'durationSec' => $durationSec,
						'userId' => $_SESSION['user_id'],
						'progressCountdown' => $progressCountdown
				];
				return $this->sendJSONResponse('', '', $data);
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
			case 'getGameLobbyData' :
				$openGames = $gameModel->getOpenGames();
				$activeGames = $gameModel->getActiveGamesByUser($_SESSION['user_id']);

				$this->checkActiveGamesAreFinished($activeGames);

				$data = [
						'openGames' => $openGames,
						'activeGames' => $activeGames
				];
				return $this->sendJSONResponse('', '', $data);
			case 'getOpenGames' :
				$data = $gameModel->getOpenGames();
				return $this->sendJSONResponse('', '', $data);
			case 'remove_game' :
				$gameid = $this->request['gameid'];
				if($gameModel->userIDhasPermissionOnGameId($_SESSION['user_id'], $gameid)){
					if($gameModel->removeGame($gameid)){
						return $this->sendJSONResponse('success', '', '');
					}
					else{
						return $this->sendJSONResponse('error', 'failed to remove game id: '.$gameid, '');
					}
				}
				return $this->sendJSONResponse('error', 'no permission on game id: '.$gameid, '');

				break;
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
	private function sendJSONResponse($result='', $message='', $data=''){
		header('Content-Type: application/json');
		echo json_encode(array('result' => $result, 'message' => $message, 'data' => $data));
	}

	private function setGameend($gameid){
		$oldValue = $this->gameModel->setGameend($gameid);
		if(! isset($oldValue)){ //first time gameend was set
			EventController::fire('game-end', $_SESSION['user_id'], [
			'gameid' => $gameid
			]);
		}
	}

	/*
	 * Checks if game is finished
	 * @param $gameReport must include columns totalQuestions and questionAnswered
	 * @param $timeToEnd contains the remaining time until the end of the game. Dimension: Seconds
	 * @return Returns true if game is finished, else false
	 */
	private function isGameFinished($gameReport, $timeToEnd){
		if($timeToEnd <= 0 ) return true;

		$allFinished = true;
		foreach($gameReport as $report){
			if($report['totalQuestions'] != $report['questionAnswered']){
				$allFinished = false;
			}
		}
		return $allFinished;
	}
	/*
	 * Checks if active games is finished
	 *
	 * @param $activeGames must include columns calcEndtime and id
	 */
	private function checkActiveGamesAreFinished($activeGames){
		$now = date("Y-m-d H:i:s");
		foreach($activeGames as $game){
			$timeToEnd = strtotime($game['calcEndtime']) - strtotime($now);
			if($timeToEnd <= 0){
				$this->setGameend($game['id']);
			}
		}
	}

	/*
	 * Checks if user is logged in.
	 * @return Returns true if User is logged in, else false;
	 */
	public static function isLoggedin(){
		return $GLOBALS ['loggedin'];
	}
}
?>
