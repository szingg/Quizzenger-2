<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class ErrorController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			$this->view->setTemplate ( 'error' );
			if( isset($this->request ['err'])&&is_null($this->request ['err'])){
				$error=err_unkown;
			}else{
				$error = filter_input(INPUT_GET, 'err',	 $filter = FILTER_SANITIZE_SPECIAL_CHARS);
			}
			$this->view->assign ( 'err', $error);
			return $this->view->loadTemplate();
		}

	} // class ErrorController
} // namespace quizzenger\controller\controllers

?>