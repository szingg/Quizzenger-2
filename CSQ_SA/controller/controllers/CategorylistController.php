<?php

namespace controller\controllers {
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use model\ModelCollection;

	class CategorylistController{
		private $view;
		private $sqlhelper;
		private $categoryModel;

		public function __construct($view) {
			$this->view = $view;
			$this->sqlhelper = new SqlHelper(log::get());
			$this->categoryModel = ModelCollection::categoryModel();
		}

		public function render(){
			$this->view->setTemplate ( 'categorylist' );
			$roots = ModelCollection::categoryModel()->getChildren ( 0 ); // get all without parent = root "nodes"
			$roots = $this->categoryModel->fillCategoryListWithQuestionCount ( $roots );
			$this->view->assign ( 'roots', $roots );
		}

	} // class CategorylistController
} // namespace controller\controllers

?>