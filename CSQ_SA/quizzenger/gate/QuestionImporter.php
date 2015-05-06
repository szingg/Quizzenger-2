<?php

namespace quizzenger\gate {
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	class QuestionImporter {
		private $mysqli;
		private $questionInsertStatement;
		private $answerInsertStatement;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;

			$this->questionInsertStatement = $this->mysqli->prepare('INSERT INTO question'
				. ' (type, questiontext, user_id, created, lastModified, difficulty, category_id)'
				. ' SELECT ?, ?, ?, ?, ?, ?, ct3.id'
				. ' FROM category AS ct1'
				. ' LEFT JOIN category AS ct2 ON ct2.parent_id=ct1.id'
				. ' LEFT JOIN category AS ct3 ON ct3.parent_id=ct2.id'
				. ' WHERE ct1.name=? AND ct2.name=? AND ct3.name=?');

			$this->answerInsertStatement = $this->mysqli->prepare('INSERT INTO answer'
				. ' (correctness, text, explanation, question_id)'
				. ' VALUES (?, ?, ?, ?)');
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

		private function insertQuestion($userId, SimpleXMLElement $question) {
			$type = (string)$question->attributes()->type;
			$difficulty = (string)$question->attributes()->difficulty;
			$author = (string)$question->author;
			$created = (string)$question->created;
			$modified = (string)$question->modified;
			$categoryFirst = (string)$question->category->first;
			$categorySecond = (string)$question->category->second;
			$categoryThird = (string)$question->category->third;
			$text = (string)$question->text;

			// TODO: Implement actual inserts.
			return true;
		}

		public function import($userId, $data) {
			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions/question');
			$this->transaction();
			foreach($questions as $current) {
				if(!$this->insertQuestion($userId, $current)) {
					$this->rollback();
					return false;
				}
			}

			$this->commit();
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
