<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\gate\QuestionImporter as QuestionImporter;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	class QuestionImportController {
		private $view;
		private $importer;

		public function __construct($view) {
			$this->view = $view;
			$this->importer = new QuestionImporter(ModelCollection::database());
		}

		public function importUploadedFile() {
			if(!isset($GLOBALS['loggedin']) || !$GLOBALS['loggedin']) {
				NavigationUtility::redirect();
				return false;
			}

			if(!isset($_FILES['import'])
				|| !isset($_FILES['import']['tmp_name']))
			{
				return false;
			}

			$content = file_get_contents($_FILES['import']['tmp_name']);
			if($content === false)
				return false;

			$xml = gzdecode($content);
			if($xml === false)
				return false;

			return $this->importer->import($_SESSION['user_id'], $xml);
		}

		public function render() {
			$this->view->setTemplate('questionimport');
			$result = $this->importUploadedFile();

			$this->view->assign('messages', $this->importer->messages());
			$this->view->assign('successful', $result);
			return $this->view->loadTemplate();
		}
	} // class QuestionImportController
} // namespace quizzenger\controller\controllers

?>
