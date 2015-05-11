<?php

namespace quizzenger\gate {
	use \stdClass as stdClass;
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	/**
	 * Provides the required functionality for importing questions
	 * via the Quizzenger XML format.
	**/
	class QuestionImporter {
		/**
		 * Holds the connection to the database.
		**/
		private $mysqli;

		/**
		 * Represents the query for first level category inserts.
		**/
		private $firstCategoryInsertStatement;

		/**
		 * Represents the query for second level category inserts.
		**/
		private $secondCategoryInsertStatement;

		/**
		 * Represents the query for third level category inserts.
		**/
		private $thirdCategoryInsertStatement;

		/**
		 * Represents the query for inserting questions into the database.
		**/
		private $questionInsertStatement;

		/**
		 * Represents the query for inserting answers into the database.
		**/
		private $answerInsertStatement;

		/**
		 * Creates the object based on an active MySQL database connection.
		 * @param mysqli $mysqli Active database connection.
		**/
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

			$this->questionInsertStatement = $this->mysqli->prepare('INSERT IGNORE INTO question'
				. ' (uuid, type, questiontext, user_id, category_id, created, lastModified,'
				. '     difficulty, difficultycount, attachment, attachment_local, imported)'
				. ' SELECT DISTINCT ?, ?, ?, ?, ct3.id, ?, ?, ?, ?, ?, ?, 1'
				. ' FROM category AS ct1'
				. ' LEFT JOIN category AS ct2 ON ct2.parent_id=ct1.id'
				. ' LEFT JOIN category AS ct3 ON ct3.parent_id=ct2.id'
				. ' WHERE ct1.name=? AND ct2.name=? AND ct3.name=?');

			$this->answerInsertStatement = $this->mysqli->prepare('INSERT INTO answer (correctness, text,'
				. ' explanation, question_id) VALUES (?, ?, ?, ?)');
		}

		/**
		 * Starts a new transaction.
		**/
		private function transaction() {
			Log::info('Starting transaction for question import.');
			$this->mysqli->autocommit(false);
		}

		/**
		 * Performs a rollback of the current transaction.
		**/
		private function rollback() {
			Log::info('An error occured during question import, executing rollback.');
			$this->mysqli->rollback();
			$this->mysqli->autocommit(true);
		}

		/**
		 * Commits the current transaction.
		**/
		private function commit() {
			Log::info('Committing transaction for question import.');
			$this->mysqli->commit();
		}

		/**
		 * Inserts the specified categories into the database.
		 * @param string $first First level category name.
		 * @param string $second Second level category name.
		 * @param string $third Third level category name.
		 * @return boolean Returns true on success, false otherwise.
		**/
		private function insertCategories($first, $second, $third) {
			$this->firstCategoryInsertStatement->bind_param('ss', $first, $first);
			$this->secondCategoryInsertStatement->bind_param('ssss', $second, $first, $second, $first);
			$this->thirdCategoryInsertStatement->bind_param('sssssss', $third, $second, $first, $third, $first, $second, $third);

			return $this->firstCategoryInsertStatement->execute()
				&& $this->secondCategoryInsertStatement->execute()
				&& $this->thirdCategoryInsertStatement->execute();
		}

		/**
		 * Inserts the specified answers into the database.
		 * @param integer $questionId The question that the answers belong to.
		 * @param array $answers An array of answers to be inserted.
		 * @return boolean Returns true on success, false otherwise.
		**/
		private function insertAnswers($questionId, array $answers) {
			foreach($answers as $current) {
				$correctness = (integer)$current->attributes()->correctness;
				$text = (string)$current->text;
				$explanation = (string)$current->explanation;

				$this->answerInsertStatement->bind_param('issi', $correctness, $text, $explanation, $questionId);
				if(!$this->answerInsertStatement->execute()) {
					Log:error("Could not insert answer for question $questionId.");
					return false;
				}
			}

			return true;
		}

		/**
		 * Inserts a question into the database.
		 * @param integer $userId The user ID that the question belongs to.
		 * @param SimpleXMLElement $question The XML node of a question.
		 * @return boolean Returns true on success, false otherwise.
		**/
		private function insertQuestion($userId, SimpleXMLElement $question) {
			$uuid = (string)$question->attributes()->uuid;
			$type = (string)$question->attributes()->type;
			$difficulty = (double)$question->attributes()->difficulty;
			$difficultyCount = (integer)$question->attributes()->{'difficulty-count'};
			$author = (string)$question->author;
			$created = (string)$question->created;
			$modified = (string)$question->modified;
			$firstCategory = (string)$question->category->attributes()->first;
			$secondCategory = (string)$question->category->attributes()->second;
			$thirdCategory = (string)$question->category->attributes()->third;
			$text = (string)$question->text;
			$attachment = (string)$question->attachment;
			$attachmentLocal = '';

			if($attachment)
				$attachmentLocal = (string)$question->attachment->attributes()->type;

			if($attachmentLocal !== "url")
				$attachment = base64_decode($attachment);

			$this->questionInsertStatement->bind_param('ssssssdisssss', $uuid, $type, $text, $userId, $created, $modified,
				$difficulty, $difficultyCount, $attachment, $attachmentLocal, $firstCategory, $secondCategory, $thirdCategory);

			if(!$this->questionInsertStatement->execute()) {
				Log::error("Insert of question $uuid failed.");
				return false;
			}

			// Question UUID already existed, so no insert has been performed.
			if($this->questionInsertStatement->insert_id === 0) {
				Log::info("Question $uuid already exists.");
				return true;
			}

			return $this->insertAnswers($this->questionInsertStatement->insert_id,
				$question->xpath('./answers/answer'));
		}

		/**
		 * Processes the import of one single question.
		 * @param integer $userId The user ID that the question belongs to.
		 * @param SimpleXMLElement $question The XML node of a question.
		 * @return boolean Returns true on success, false otherwise.
		**/
		private function importSingleQuestionForUser($userId, SimpleXMLElement $question) {
			$uuid = $question->attributes()->uuid;
			$firstCategory = $question->category->attributes()->first;
			$secondCategory = $question->category->attributes()->second;
			$thirdCategory = $question->category->attributes()->third;

			if(!$this->insertCategories($firstCategory, $secondCategory, $thirdCategory)) {
				$this->rollback();
				Log::error("Categories for question $uuid could not be inserted.");
				return false;
			}

			if(!$this->insertQuestion($userId, $question)) {
				$this->rollback();
				Log::error("Question $uuid could not be inserted.");
				return false;
			}

			return true;
		}

		/**
		 * Imports all questions for the specified user.
		 * @param integer $userId The user ID that the question belongs to.
		 * @param arrayt $question An array of SimpleXMLElement nodes, one for each question.
		**/
		private function importQuestionsForUser($userId, array $questions) {
			foreach($questions as $current) {
				$uuid = $current->attributes()->uuid;

				$this->transaction();
				if(!$this->importSingleQuestionForUser($userId, $current)) {
					Log::error("Import of question $uuid failed.");
					$this->rollback();
					continue;
				}
				$this->commit();
			}

			return true;
		}

		/**
		 * Imports the questions defined in the specified XML data and assigns them to the user.
		 * @param integer $userId The user ID that the questions belong to.
		 * @param string $data XML data to be imported.
		**/
		public function import($userId, $data) {
			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				return false;
			}

			$version = $xml->xpath('/quizzenger-question-export/@version');
			if(empty($version) || ((string)$version[0]) !== '1.0') {
				Log::error('The specified version for the import is not supported.');
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions/question');
			return $this->importQuestionsForUser($userId, $questions);
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
