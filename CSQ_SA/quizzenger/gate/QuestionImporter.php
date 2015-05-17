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
		 * Indicates that the question already exists.
		**/
		const MESSAGE_ALREADY_EXISTS = 0;

		/**
		 * Indicates that the question could not be imported due to an error.
		**/
		const MESSAGE_IMPORT_FAILED = 1;

		/**
		 * Indicates that the provided XML was invalid.
		**/
		const MESSAGE_INVALID_XML = 2;

		/**
		 * Indicates that the version of the import file is not supported.
		**/
		const MESSAGE_UNSUPPORTED_VERSION = 3;

		/**
		 * Holds the connection to the database.
		**/
		private $mysqli;

		/**
		 * Represents an array indexed by message type that holds question IDs.
		**/
		private $messageQueue;

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
			$this->messageQueue = [];

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
			$this->mysqli->commit();
		}

		/**
		 * Pushes a message into the message queue.
		 * @param int $type Message type.
		 * @param string $uuid The UUID of the message.
		**/
		private function pushMessage($type, $uuid = 0) {
			if(!isset($this->messageQueue[$type]))
				$this->messageQueue[$type] = [];

			$this->messageQueue[$type][] = $uuid;
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
		 * Decodes the XML-Base64-encoded attachment and copies it into the correct directory.
		 * @param integer $questionId The question ID that defines the filename.
		 * @param SimpleXMLElement $question The question to which the achievement belongs.
		 * @return boolean Returns true on success, false otherwise.
		**/
		private function transferAttachment($questionId, SimpleXMLElement $question) {
			$uuid = (string)$question->attributes()->uuid;
			$content = (string)$question->attachment;
			$extension = (string)$question->attachment->attributes()->extension;

			$size = strlen($content);
			$size = $size - ($size / 3); // Subtract 33% to account for wasted space due to encoding.

			if($size > MAX_ATTACHMENT_SIZE_KByte * 1024) {
				Log::error("Attachment of question $uuid is too large ($size).");
				return false;
			}

			if(file_put_contents(ATTACHMENT_PATH . DIRECTORY_SEPARATOR . $questionId . '.' . $extension,
				base64_decode($content)) === false)
			{
				Log::error("Could not write contents of attachment for question $uuid.");
				return false;
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

			if($attachment) {
				$attachmentType = (string)$question->attachment->attributes()->type;

				if($attachmentType == 'local') {
					$attachmentLocal = true;
					$attachment = (string)$question->attachment->attributes()->extension;
				}
				else {
					$attachmentLocal = false;
				}
			}
			else {
				$attachmentType = '';
				$attachmentLocal = false;
			}

			$this->questionInsertStatement->bind_param('ssssssdisssss', $uuid, $type, $text, $userId, $created, $modified,
				$difficulty, $difficultyCount, $attachment, $attachmentLocal, $firstCategory, $secondCategory, $thirdCategory);

			if(!$this->questionInsertStatement->execute()) {
				Log::error("Insert of question $uuid failed.");
				return false;
			}

			// Question UUID already existed, so no insert has been performed.
			if($this->questionInsertStatement->insert_id === 0) {
				Log::info("Question $uuid already exists.");
				$this->pushMessage(self::MESSAGE_ALREADY_EXISTS, $uuid);
				return true;
			}

			if($attachmentType == "local") {
				if(!$this->transferAttachment($this->questionInsertStatement->insert_id, $question)) {
					Log::error("Transfer of attachment from question $uuid failed.");
					return false;
				}
			}

			if(!$this->insertAnswers($this->questionInsertStatement->insert_id,
				$question->xpath('./answers/answer')))
			{
				Log::error("Could not insert answers for question $uuid.");
				return false;
			}

			return true;
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
				Log::error("Categories for question $uuid could not be inserted.");
				return false;
			}

			if(!$this->insertQuestion($userId, $question)) {
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
					$this->pushMessage(self::MESSAGE_IMPORT_FAILED, $uuid);
					$this->rollback();
					continue;
				}
				$this->commit();
			}

			return true;
		}

		/**
		 * Gets an array of question IDs indexed by message type.
		 * @return Returns an array with elements of type [message] => [id].
		**/
		public function messages() {
			return $this->messageQueue;
		}

		/**
		 * Imports the questions defined in the specified XML data and assigns them to the user.
		 * @param integer $userId The user ID that the questions belong to.
		 * @param string $data XML data to be imported.
		**/
		public function import($userId, $data) {
			$this->messageQueue = [];

			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				$this->pushMessage(self::MESSAGE_INVALID_XML);
				return false;
			}

			$version = $xml->xpath('/quizzenger-question-export/@version');
			if(empty($version) || ((string)$version[0]) !== '1.0') {
				Log::error('The specified version for the import is not supported.');
				$this->pushMessage(self::MESSAGE_UNSUPPORTED_VERSION, $uuid);
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions/question');
			return $this->importQuestionsForUser($userId, $questions);
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
