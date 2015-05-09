<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class ProcessLoginController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'blankContent' );

			if(isset($_POST['login_form_password'])){
				$password = $_POST['login_form_password'];
			}else{
				$password = null;
			}
			if(isset($_POST['login_form_email'])){
				$email = $_POST['login_form_email'];
			}else{
				$email = null;
			}

			ModelCollection::sessionModel()->processLogin ($email,$password);
			return $this->view->loadTemplate();
		}

	} // class ProcessLoginController
} // namespace quizzenger\controller\controllers

?>