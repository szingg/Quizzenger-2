<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class ProcessRegistrationController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'blankContent' );

			$username = filter_input ( INPUT_POST, 'register_form_username', FILTER_SANITIZE_STRING );
			$email = filter_input ( INPUT_POST, 'register_form_email', FILTER_SANITIZE_EMAIL );
			$email = filter_var ( $email, FILTER_VALIDATE_EMAIL );
			$password = filter_input ( INPUT_POST, 'register_form_password', FILTER_SANITIZE_STRING );

			ModelCollection::registrationModel()->processRegistration($username,$email,$password);

			return $this->view->loadTemplate();
		}

	} // class ProcessRegistrationController
} // namespace quizzenger\controller\controllers

?>