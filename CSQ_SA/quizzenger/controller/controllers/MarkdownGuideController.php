<?php

namespace quizzenger\controller\controllers {

	class MarkdownGuideController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'markdownguide' );
			return $this->view->loadTemplate();
		}

	} // class MarkdownGuideController
} // namespace quizzenger\controller\controllers

?>