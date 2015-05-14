<?php
namespace quizzenger\controller {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\messages\MessageFormatter as MessageFormatter;
	use \quizzenger\messages\TextTranslator as TextTranslator;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\controlling\EventController as EventController;
	use \quizzenger\gamification\model\GameModel as GameModel;
	use \quizzenger\logging\LogViewer as LogViewer;
	use \quizzenger\gate\QuestionExporter as QuestionExporter;
	use \quizzenger\view\View as View;

	class Controller {
		private $request = null;
		private $template = '';
		private $viewOuter = null;
		private $mysqli;
		private $logger;

		public function __construct($request, $pLog) {
			$this->logger = $pLog;
			$this->viewOuter = new View ();
			$this->request = $request;
			$this->template = ! empty ( $request ['view'] ) ? $request ['view'] : 'default';
			$this->mysqli = new \sqlhelper ( $this->logger );

			ModelCollection::setup($this->mysqli);
			MessageQueue::setup($this->mysqli->database());
			TextTranslator::setup($this->mysqli->database(), new MessageFormatter());
			EventController::setup($this->mysqli);
		}

		public function render() { /*
			$questionListModel = new QuestionListModel ( $this->mysqli, $this->logger );
			$questionModel = new QuestionModel ( $this->mysqli, $this->logger );
			$answerModel = new AnswerModel ( $this->mysqli, $this->logger );
			$categoryModel = new CategoryModel ( $this->mysqli, $this->logger );
			$userModel = new UserModel ( $this->mysqli, $this->logger );
			$quizModel = new QuizModel ( $this->mysqli, $this->logger );
			$ratingModel = new RatingModel ( $this->mysqli, $this->logger );
			$sessionModel = new SessionModel ( $this->mysqli, $this->logger );
			$quizListModel = new QuizListModel ( $this->mysqli, $this->logger );
			$tagModel = new TagModel ( $this->mysqli, $this->logger );
			$registrationModel = new RegistrationModel ( $this->mysqli, $this->logger );
			$userscoreModel = new UserScoreModel ( $this->mysqli, $this->logger );
			$moderationModel = new ModerationModel( $this->mysqli, $this->logger );
			$reportModel = new ReportModel( $this->mysqli, $this->logger );
			$reportingModel = new ReportingModel($this->mysqli, $this->logger);
	*/

			ModelCollection::sessionModel()->sec_session_start();

			$_SESSION ['current_view'] = $this->template;

			$viewInner = new View ();

			switch ($this->template) {
				case 'about' : case 'learn' :
				case 'question' :case 'login' : case 'user' : case 'solution' : case 'questionlist' : case 'myquestions' :
				case 'mycontent' : case 'myquizzes' : case 'quizdetail' : case 'quizstart' : case 'quizend' : case 'categorylist' :
				case 'logout' : case 'error' : case 'register' : case 'processLogin' : case 'processChangepassword' : case 'processRegistration' :
				case 'newquestion' : case 'editquestion' : case 'processNewQuestion' : case 'processGenerateQuiz':
				case 'log': case 'questionpool': case 'processEditQuestion': case 'reporting':
				case 'default':
					/*include("controllers/".$this->template.".php");
					$viewInner = $viewInner->loadTemplate();
					break; */
					$tmplt = strtoupper($this->template[0]).substr($this->template, 1);
					$className = '\\quizzenger\\controller\\controllers\\'.$tmplt.'Controller';
					$controller = new $className($viewInner);
					$viewInner = $controller->render();
					break;
				case 'GameNew' : case 'GameStart' : case 'GameEnd' :
				case 'GameQuestion' : case 'GameSolution' : case 'GameDetail' :
					$className = '\\quizzenger\\gamification\\controller\\'.$this->template.'Controller';
					$controller = new $className($viewInner);
					$viewInner = $controller->render();
					break;

				case 'syslog':
					if(!$_SESSION['superuser'] || !isset($_GET['logfile'])) {
						NavigationUtility::redirect();
					}
					else {
						(new LogViewer())->render($_GET['logfile']);
						die();
					}
					break;

				case 'questionexport':
					$exportUserId = (isset($_GET['id']) ? $_GET['id'] : null);
					if($exportUserId === null && $_SESSION['superuser']) {
						(new QuestionExporter($this->mysqli->database()))->export(null);
						die();
					}
					else if($exportUserId == $_SESSION['user_id']) {
						(new QuestionExporter($this->mysqli->database()))->export($_SESSION['user_id']);
						die();
					}
					else {
						NavigationUtility::redirect();
					}
					break;

				case 'questionimport':
					$controller = new \quizzenger\controller\controllers\QuestionImportController($viewInner);
					$viewInner = $controller->render();
					break;

				default:
					$className = '\\quizzenger\\controller\\controllers\\DefaultController';
					$controller = new $className($viewInner);
					$viewInner = $controller->render();
					break;
			}

			// loads the head, css etc.
			$this->viewOuter->setTemplate ( 'skeleton' );
			$this->viewOuter->assign('userid', $_SESSION['user_id']);
			$this->viewOuter->assign('username', $_SESSION ['username']);
			$this->viewOuter->assign('superuser', $_SESSION['superuser']);
			$this->viewOuter->assign('anymoderator', ModelCollection::reportingModel()->isAnyModerator($_SESSION['user_id']));
			$this->viewOuter->assign( 'csq_content', $viewInner);
			// Return the whole page now
			return $this->viewOuter->loadTemplate ();
		}
	}
} //namespace
?>
