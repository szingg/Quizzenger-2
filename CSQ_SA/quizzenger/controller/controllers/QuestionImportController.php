<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\gate\QuestionImporter as QuestionImporter;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	class QuestionImportController {
		private $view;

		public function __construct($view) {
			$this->view = $view;
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

			$importer = new QuestionImporter(ModelCollection::database());
			return $importer->import($_SESSION['user_id'], $xml);
		}

		public function render() {
			$this->view->setTemplate('questionimport');
			$this->importUploadedFile();
			return $this->view->loadTemplate();
		}
	} // class QuestionImportController
} // namespace quizzenger\controller\controllers

?>
