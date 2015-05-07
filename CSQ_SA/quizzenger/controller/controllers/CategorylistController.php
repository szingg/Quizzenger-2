<?php

namespace quizzenger\controller\controllers {
	use quizzenger\model\ModelCollection as ModelCollection;

	class CategorylistController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'categorylist' );
			$roots = ModelCollection::categoryModel()->getChildren ( 0 ); // get all without parent = root "nodes"
			$roots = ModelCollection::categoryModel()->fillCategoryListWithQuestionCount ( $roots );
			$this->view->assign ( 'roots', $roots );
			return $this->view->loadTemplate();
		}

	} // class CategorylistController
} // namespace quizzenger\controller\controllers

?>