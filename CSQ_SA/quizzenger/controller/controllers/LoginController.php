<?php
namespace quizzenger\controller\controllers {
	//use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class LoginController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			// no double logins
			if($GLOBALS['loggedin']){
				MessageQueue::pushPersistent($_SESSION['user_id'], 'mes_login_already');
				NavigationUtility::redirect();
			}

			$this->view->setTemplate ( 'login' );

			return $this->view->loadTemplate();
		}

		} // class LoginController
} // namespace quizzenger\controller\controllers

?>