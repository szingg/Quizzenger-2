<?php

namespace quizzenger\gate {
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	/**
	 * Provides the required functionality to export questions using Quizzenger's XML format.
	**/
	class QuestionExporter {
		/**
		 * Holds a reference to an active database connection.
		**/
		private $mysqli;

		/**
		 * Creates the object based on an active database connection.
		 * @param mysqli $mysqli Active database connection.
		**/
		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		/**
		 * Executes a query to retrieve all questions assigned with the specified user.
		 * @param integer $userId The user ID that indicates what questions to retrieve.
		 * @return mysqli_result Returns the MySQLi result set.
		**/
		private function queryQuestions($userId) {
			$statement = $this->mysqli->prepare('SELECT question.id, question.uuid, question.user_id, question.type, question.questiontext,'
				. ' question.created, question.lastModified, question.difficulty, question.difficultycount,'
				. ' question.attachment, question.attachment_local,'
				. ' category.first_category, category.second_category, category.third_category,'
				. ' (SELECT username FROM user WHERE id=user_id) AS username'
				. ' FROM question'
				. ' LEFT OUTER JOIN ('
				. '     SELECT ct3.id, ct1.name AS first_category, ct2.name AS second_category, ct3.name AS third_category'
				. '     FROM category AS ct1'
				. '     LEFT JOIN category AS ct2 ON ct2.parent_id = ct1.id'
				. '     LEFT JOIN category AS ct3 ON ct3.parent_id = ct2.id'
				. ' ) AS category ON category.id=question.category_id'
				. ' WHERE user_id=? OR ?'
				. ' ORDER BY id');

			$everything = ($userId === null) ? 1 : 0;
			$statement->bind_param('ii', $userId, $everything);
			if(!$statement->execute())
				return false;

			return $statement->get_result();
		}

		/**
		 * Executes a query to retrieve all answers associated with questions from the specified user.
		 * @param integer $userId The user ID that indicates what answers to retrieve.
		 * @return mysqli_result Returns the MySQLi result set.
		**/
		private function queryAnswers($userId) {
			$statement = $this->mysqli->prepare('SELECT question_id, correctness, text, explanation'
				. ' FROM answer'
				. ' LEFT JOIN question ON question.id=answer.question_id'
				. ' WHERE user_id=? OR ?'
				. ' ORDER BY question_id');

			$everything = ($userId === null) ? 1 : 0;
			$statement->bind_param('ii', $userId, $everything);
			if(!$statement->execute())
				return false;

			return $statement->get_result();
		}

		/**
		 * Encodes the specified attachment with Base64.
		 * @param string $filename The filename of the attachment to be encoded.
		 * @return string Returns the Base64 encoded content, or false on failure.
		**/
		private function encodeAttachment($filename) {
			$absolutePath = QUIZZENGER_ROOT . DIRECTORY_SEPARATOR
				. ATTACHMENT_PATH . DIRECTORY_SEPARATOR . $filename;

			$content = file_get_contents($absolutePath);
			if($content === false) {
				return false;
			}

			return base64_encode($content);
		}

		/**
		 * Generates the XML file based on the specified questions and writes it onto the output stream.
		 * @param array $export Array of questions to be exported.
		 * @return boolean Returns true on success, false otherwise. The generated XML file will be
		 *         sent back to the client automatically.
		**/
		private function output($export) {
			$document = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>'
				. '<quizzenger-question-export version="1.0"></quizzenger-question-export>');

			$meta = $document->addChild('meta');
			$meta->addChild('system')->{0} = APP_PATH;
			$meta->addChild('date')->{0} = date('Y-m-d H:i:s');

			$questions = $document->addChild('questions');
			foreach($export as $current) {
				$questionElement = $questions->addChild('question');
				$questionElement->addAttribute('uuid', $current->uuid);
				$questionElement->addAttribute('type', $current->type);
				$questionElement->addAttribute('difficulty', $current->difficulty);

				$questionElement->addChild('author')->{0} = $current->username;
				$questionElement->addChild('created')->{0} = $current->created;
				$questionElement->addChild('modified')->{0} = $current->lastModified;

				$categoryElement = $questionElement->addChild('category');
				$categoryElement->addAttribute('first', $current->first_category);
				$categoryElement->addAttribute('second', $current->second_category);
				$categoryElement->addAttribute('third', $current->third_category);

				$questionElement->addChild('text')->{0} = $current->questiontext;
				$answersElement = $questionElement->addChild('answers');
				foreach($current->answers as $answer) {
					$answerElement = $answersElement->addChild('answer');
					$answerElement->addAttribute('correctness', $answer->correctness);
					$answerElement->addChild('text')->{0} = $answer->text;
					if(!empty($answer->explanation))
						$answerElement->addChild('explanation')->{0} = $answer->explanation;
				}

				if($current->attachment) {
					$attachmentElement = $questionElement->addChild('attachment');
					if($current->attachment_local) {
						$attachmentElement->addAttribute('type', 'local');
						$attachmentElement->addAttribute('extension', $current->attachment);

						$encodedAttachment = $this->encodeAttachment($current->id . '.' . $current->attachment);
						if($encodedAttachment === false)
							return false;

						$attachmentElement->{0} = $encodedAttachment;
					}
					else {
						$attachmentElement->addAttribute('type', 'url');
						$attachmentElement->{0} = $current->attachment;
					}
				}
			}

			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: Binary');
			header('Content-Disposition: attachment; filename="' . date('YmdHis') . '.quizzenger"');

			echo gzencode($document->asXML());
			return true;
		}

		/**
		 * Exports all questions associated with the specified user and creates the XML file.
		 * @param integer $userId The user ID that indicates what questions to retrieve.
		 * @return boolean Returns true on success, false otherwise. The generated XML file will
		 *         be rendered and sent back to the client.
		**/
		public function export($userId) {
			$export = [];
			$questions = null;
			$answers = null;

			if(!($questions = $this->queryQuestions($userId))) {
				Log::error("Could not query questions for user $userId.");
				return false;
			}

			if(!($answers = $this->queryAnswers($userId))) {
				Log::error("Could not query answers for user $userId.");
				return false;
			}

			// First create a list of questions...
			while($current = $questions->fetch_object()) {
				$export[$current->id] = $current;
			}

			// ...and then assign all answers to each question.
			while($current = $answers->fetch_object()) {
				$currentExport = &$export[$current->question_id];
				if(!isset($currentExport->answers))
					$currentExport->answers = [];

				$currentExport->answers[] = $current;
			}

			$this->output($export);
			return true;
		}
	} // class QuestionExporter
} // namespace quizzenger\gate

?>
