<?php
namespace quizzenger\controller\controllers {
	use quizzenger\controller\controllers\CategorylistController as CategorylistController;
	use \quizzenger\view\View as View;

	class QuestionpoolController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->loadCategorylist();
			$this->view->setTemplate ( 'questionpool' );
			return $this->view->loadTemplate();
		}

		private function loadCategorylist(){
			$categoryView = new View();
			$categorylist = new CategorylistController($categoryView);
			$this->view->assign('categorylist', $categorylist->render());
		}

	} // class QuestionpoolController
} // namespace controller\controllers
?>