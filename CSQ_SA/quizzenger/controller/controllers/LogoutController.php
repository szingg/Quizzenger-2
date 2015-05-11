<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class LogoutController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			ModelCollection::sessionModel()->logout();
		}

	} // class LogoutController
} // namespace quizzenger\controller\controllers
?>