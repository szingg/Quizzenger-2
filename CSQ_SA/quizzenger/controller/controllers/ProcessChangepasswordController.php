<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class ProcessChangepasswordController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			//$this->view->setTemplate ( 'blankContent' );

			if(isset($_POST ['change_password_form_password'])){
				$password = $_POST ['change_password_form_password'];
			}else{
				$this->logger->log ( "Invalid POST request made (processEditQuestion)", Logger::WARNING );
				die ( 'Invalid Request. Please stop this' );
			}

			ModelCollection::userModel()->processChangepassword ($password); // checks if user is logged in too
			//return $this->view->loadTemplate();
		}

	} // class ProcessChangepasswordController
} // namespace quizzenger\controller\controllers

?>