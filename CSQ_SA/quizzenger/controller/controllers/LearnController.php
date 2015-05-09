<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\view\View as View;

	class LearnController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			PermissionUtility::checkLogin();

			$this->view->setTemplate ( 'learn' ); //learn contains Tabs

			$this->renderQuizView();
			$this->renderGameView();

			return $this->view->loadTemplate();
		}

		private function renderQuizView(){
			//Quiz View
			$viewQuiz = new View();
			$viewQuiz->setTemplate ( 'generatequiz' );

			if (isset ( $this->request ['type'] )) {
				$type = $this->request ['type'];
			} else {
				$type = SINGLECHOICE_TYPE;
			}
			$roots = ModelCollection::categoryModel()->getChildren ( 0 ); // get all without parent = root "nodes"
			$roots = ModelCollection::categoryModel()->fillCategoryListWithQuestionCount ( $roots );
			$totalCount = ModelCollection::categoryModel()->getTotalQuestionCount();

			$viewQuiz->assign ( 'totalCount', $totalCount);
			$viewQuiz->assign ( 'roots', $roots );
			$viewQuiz->assign ( 'mode', 'generator' );
			$viewQuiz->assign ( 'type', $type );
			$this->view->assign( 'quiz_tab' , $viewQuiz->loadTemplate());
		}

		private function renderGameView(){
			//Game View
			$viewGame = new View();
			$viewGame->setTemplate ( 'gamelobby' );

			$quizzes = ModelCollection::quizListModel()->getUserQuizzesByUserID ( $_SESSION ['user_id'] );
			$viewGame->assign ( 'quizzes', $quizzes );

			$this->view->assign( 'game_tab' , $viewGame->loadTemplate());
		}

	} // class LearnController
} // namespace quizzenger\controller\controllers

?>