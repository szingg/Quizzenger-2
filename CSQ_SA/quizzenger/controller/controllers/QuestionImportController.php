<?php

namespace quizzenger\controller\controllers {
	class QuestionImportController {
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render() {
			$this->view->setTemplate('questionimport');
			return $this->view->loadTemplate();
		}
	} // class QuestionImportController
} // namespace quizzenger\controller\controllers

?>
