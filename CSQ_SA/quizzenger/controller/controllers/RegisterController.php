<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	class RegisterController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			// no double logins
		    if($GLOBALS['loggedin'] ){
		    	MessageQueue::pushPersistent($_SESSION['user_id'], 'mes_login_already');
				NavigationUtility::redirect();
		    }
			$this->view->setTemplate ( 'register' );
			return $this->view->loadTemplate();
		}

	} // class RegisterController
} // namespace quizzenger\controller\controllers

?>