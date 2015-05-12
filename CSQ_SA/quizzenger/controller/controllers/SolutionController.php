<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\controlling\EventController as EventController;
	use \quizzenger\controller\controllers\helper\SolutionReportHelper as SolutionReportHelper;

	class SolutionController{
		private $view;
		private $request;
		private $questionID;
		private $correct;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			if (! isset ( $this->request ['id'] ) || ! isset ( $this->request ['answer'] )) {
				return;
			}

			$this->questionID = $this->request ['id'];

			$this->loadMainView();
			$this->loadRatingView();
			$this->loadQuizsessionView();

			return $this->view->loadTemplate();
		}

		private function loadMainView(){
			$this->view->setTemplate ( 'solution' );

			$question = ModelCollection::questionModel()->getQuestion ( $this->request ['id'] );

			$author = ModelCollection::userModel()->getUsernameByID ( $question ['user_id'] );

			$categoryName = ModelCollection::categoryModel()->getNameByID ( $question ['category_id'] );

			$answers = ModelCollection::answerModel()->getAnswersByQuestionID ( $this->request ['id'] );
			$order = $_SESSION['questionorder'][$this->request ['id']];
			array_multisort($order, $answers);
			$selectedAnswer = $this->request ['answer'];
			$correctAnswer = ModelCollection::answerModel()->getCorrectAnswer ( $this->request ['id'] );

			$alreadyReported= ModelCollection::reportModel()->checkIfUserAlreadyDoneReport("question", $this->request ['id'] , $_SESSION ['user_id']);
			$this->view->assign ('alreadyreported',$alreadyReported);

			if($GLOBALS['loggedin'] && $correctAnswer == $selectedAnswer){
				if(! ModelCollection::userscoreModel()->hasUserScoredQuestion($this->request ['id'],$_SESSION['user_id'])){ // no multiple scoring for question.
					EventController::fire('question-answered-correct', $_SESSION['user_id'], [
					'category' => $question['category_id']
					]);
					$this->view->assign ('pointsearned', QUESTION_ANSWERED_SCORE);
				}
			}

			$helper = new SolutionReportHelper($this->view);
			$helper->process($question);

			$this->view->assign ( 'answers', $answers );
			$this->view->assign ( 'category', $categoryName );
			$this->view->assign ( 'author', $author );

			$this->view->assign ( 'questionID', $this->request ['id'] );
			$this->view->assign ( 'selectedAnswer', $selectedAnswer );
			$userIsModHere = ModelCollection::userModel()->userIsModeratorOfCategory($_SESSION['user_id'], $question ['category_id']);
			$this->view->assign ( 'userismodhere', $userIsModHere );
			$this->view->assign ( 'question', $question );

			// Implement other Strategies if other question types are desired
			$this->correct = ($correctAnswer == $selectedAnswer ? 100 : 0);

			$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';

		}

		private function loadRatingView(){
			$ratingController = new RatingController($this->view);
			$ratingController->render();
		}

		private function loadQuizsessionView(){
			// Only relevant if question was answered in quiz context
			if (isset ( $this->request ['session_id'] )  ) {
				$session_id = $this->request ['session_id'];
				$inc_counter=0;
				if (ModelCollection::questionModel()->answerExists ( $session_id, $this->request ['id'], $_SESSION['user_id'] ) == 0) { // Normal Quiz
					ModelCollection::questionModel()->InsertQuestionPerformance ( $this->request ['id'], $_SESSION ['user_id'], $this->correct, $session_id, NULL);
					$inc_counter=1;
				}
				$_SESSION ['counter'. $session_id] += $inc_counter;
				$questionCount= count ( $_SESSION ['questions'. $session_id] );
				$currentCounter= $_SESSION ['counter'. $session_id];
				$progress = round ( 100 * ($currentCounter / $questionCount) );
				$this->view->assign ( 'progress', $progress );
				$this->view->assign ( 'questioncount', $questionCount );
				$this->view->assign ( 'currentcounter', $currentCounter );
				$this->view->assign ( 'progress', $progress );

				if (count ( $_SESSION ['questions'. $session_id] ) > $_SESSION ['counter'. $session_id]) {
					$this->view->assign ( 'nextQuestion', "?view=question&id=" . $_SESSION ['questions'. $session_id] [$_SESSION ['counter'. $session_id]] . "&amp;session_id=" . $session_id);
				} else {
					$this->view->assign ( 'nextQuestion', "?view=quizend&session_id=". $session_id);
				}
			}
			else { // not in quiz context
				if(!$pageWasRefreshed){
					ModelCollection::questionModel()->InsertQuestionPerformance ( $this->request ['id'], $_SESSION ['user_id'], $this->correct, NULL, NULL);
				}
			}
		}

	} // class SolutionController
} // namespace quizzenger\controller\controllers

?>
