<?php

namespace quizzenger\gate {
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	class QuestionImporter {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		private function transaction() {
			Log::info('Starting transaction for question import.');
			$this->mysqli->autocommit(false);
		}

		private function rollback() {
			Log::info('An error occured during question import, executing rollback.');
			$this->mysqli->rollback();
			$this->mysqli->autocommit(true);
		}

		private function commit() {
			Log::info('Committing transaction for question import.');
			$this->mysqli->commit();
		}

		private function insertQuestion(SimpleXMLElement $question) {
			// TODO: Implement insert statements.
			return true;
		}

		public function import($data) {
			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions/question');
			$this->transaction();
			foreach($questions as $current) {
				if(!$this->insertQuestion($current)) {
					$this->rollback();
					return false;
				}
			}
			$this->commit();
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
