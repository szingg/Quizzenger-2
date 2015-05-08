<?php

namespace quizzenger\gate {
	use \stdClass as stdClass;
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	class QuestionImporter {
		private $mysqli;
		private $firstCategoryInsertStatement;
		private $secondCategoryInsertStatement;
		private $thirdCategoryInsertStatement;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;

			$this->firstCategoryInsertStatement = $this->mysqli->prepare('INSERT INTO category (name, parent_id)'
				. ' SELECT DISTINCT ?, 0 FROM category AS ct0'
				. ' WHERE ? NOT IN (SELECT ct1.name FROM category AS ct1 WHERE ct1.parent_id=0)');

			$this->secondCategoryInsertStatement = $this->mysqli->prepare('INSERT INTO category (name, parent_id)'
				. ' SELECT DISTINCT ?, (SELECT DISTINCT xct1.id FROM category AS xct1'
				. '     WHERE xct1.name=? AND xct1.parent_id=0)'
				. ' FROM category AS ct0 WHERE ? NOT IN (SELECT ct2.name FROM category AS ct1'
				. '     JOIN category AS ct2 ON ct2.parent_id=ct1.id'
				. '     WHERE ct1.name=? AND ct1.parent_id=0)');

			$this->thirdCategoryInsertStatement = $this->mysqli->prepare('INSERT INTO category (name, parent_id)'
				. ' SELECT DISTINCT ?, (SELECT DISTINCT xct2.id FROM category AS xct1'
				. '     JOIN category AS xct2 ON xct2.parent_id=xct1.id'
				. '        WHERE xct2.name=? AND xct2.parent_id=xct1.id AND xct1.name=?)'
				. ' FROM category AS ct0 WHERE ? NOT IN (SELECT ct3.name FROM category AS ct1'
				. '     JOIN category AS ct2 ON ct2.parent_id=ct1.id'
				. '     JOIN category AS ct3 ON ct3.parent_id=ct2.id'
				. '     WHERE ct1.parent_id=0 AND ct1.name=? AND ct2.name=? AND ct3.name=?)');
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

		private function insertCategories($first, $second, $third) {
			$this->firstCategoryInsertStatement->bind_param('ss', $first, $first);
			$this->secondCategoryInsertStatement->bind_param('ssss', $second, $first, $second, $first);
			$this->thirdCategoryInsertStatement->bind_param('sssssss', $third, $second, $first, $third, $first, $second, $third);

			return $this->firstCategoryInsertStatement->execute()
				&& $this->secondCategoryInsertStatement->execute()
				&& $this->thirdCategoryInsertStatement->execute();
		}

		private function importSingleQuestionForUser($userId, SimpleXMLElement $question) {
			$uuid = $question->attributes()->uuid;
			$firstCategory = $question->category->attributes()->first;
			$secondCategory = $question->category->attributes()->second;
			$thirdCategory = $question->category->attributes()->third;

			$this->transaction();
			if(!$this->insertCategories($firstCategory, $secondCategory, $thirdCategory)) {
				$this->rollback();
				Log::error("Categories for question $uuid could not be created.");
				return false;
			}

			$this->commit();
			return true;
		}

		private function importQuestionsForUser($userId, array $questions) {
			foreach($questions as $current) {
				$uuid = $current->attributes()->uuid;
				if(!$this->importSingleQuestionForUser($userId, $current)) {
					Log::error("Import of question $uuid failed.");
				}
			}
		}

		public function import($userId, $data) {
			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions/question');
			return $this->importQuestionsForUser($userId, $questions);
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
