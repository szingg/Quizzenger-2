<?php

namespace controller\controllers {
	use \stdClass as stdClass;
	use \SplEnum as SplEnum;
	use \mysqli as mysqli;
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\utilities\FormatUtility as FormatUtility;

	class AboutController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$viewInner->setTemplate ( 'about' );
			return $this->view->loadTemplate();
		}

	} // class
} // namespace controller\controllers

?>