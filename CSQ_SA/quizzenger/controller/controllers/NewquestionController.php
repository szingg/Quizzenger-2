<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class NewquestionController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			PermissionUtility::checkLogin();

			$this->view->setTemplate ( 'opquestion' );
			if (isset ( $this->request ['type'] )) {
				$type = $this->request ['type'];
			} else {
				$type = SINGLECHOICE_TYPE;
			}

			$roots = ModelCollection::categoryModel()->getChildren ( 0 ); // get all without parent = root "nodes"
			$roots = ModelCollection::categoryModel()->fillCategoryListWithQuestionCount ( $roots );

			$this->view->assign ( 'roots', $roots );
			$this->view->assign ( 'operation', "new" );
			$this->view->assign ( 'mode', 'add_question' );
			$this->view->assign ( 'type', $type );
			return $this->view->loadTemplate();
		}

	} // class NewquestionController
} // namespace quizzenger\controller\controllers

?>