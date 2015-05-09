<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\view\View as View;

	class MycontentController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			PermissionUtility::checkLogin();
			$this->view->setTemplate ( 'mycontent' );
			//$this->view->assign('template', $this->template);

			$this->loadQuestionView();
			$this->loadQuizView();
			$this->loadGameView();

			return $this->view->loadTemplate();
		}

		function loadQuestionView(){
			$myquestionscontroller = new MyquestionsController(new View());
			$this->view->assign ( 'questionlist', $myquestionscontroller->render() );
		}

		function loadQuizView(){
			$myquizzescontroller = new MyquizzesController(new View());
			$this->view->assign ( 'quizlist', $myquizzescontroller->render() );
		}

		function loadGameView(){
			$gameView = new View();
			$gameView->setTemplate ( 'gamelist' );

			$games = ModelCollection::gameModel()->getGamesByUser($_SESSION['user_id']);
			$gameView->assign( 'games', $games);

			$this->view->assign ( 'gamelist', $gameView->loadTemplate() );
		}

	} // class MycontentController
} // namespace quizzenger\controller\controllers


?>