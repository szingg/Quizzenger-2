<?php

namespace quizzenger\controller\controllers {

	class AboutController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'about' );
			return $this->view->loadTemplate();
		}

	} // class Aboutcontroller
} // namespace quizzenger\controller\controllers

?>