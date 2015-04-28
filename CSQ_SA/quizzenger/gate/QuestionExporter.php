<?php

namespace quizzenger\gate {
	use \stdClass as stdClass;
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	class QuestionExporter {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		private function queryQuestions($userId) {
			$statement = $this->mysqli->prepare('SELECT id, user_id, type, questiontext,'
				. ' created, lastModified, difficulty, difficultycount, attachment, attachment_local,'
				. ' (SELECT username FROM user WHERE id=user_id) AS username'
				. ' FROM question'
				. ' WHERE user_id=?'
				. ' ORDER BY id');


			$statement->bind_param('i', $userId);
			if(!$statement->execute())
				return false;

			return $statement->get_result();
		}

		private function queryAnswers($userId) {
			$statement = $this->mysqli->prepare('SELECT question_id, correctness, text, explanation'
				. ' FROM answer'
				. ' LEFT JOIN question ON question.id=answer.question_id'
				. ' WHERE user_id=?'
				. ' ORDER BY question_id');

			$statement->bind_param('i', $userId);
			if(!$statement->execute())
				return false;

			return $statement->get_result();
		}

		private function output($export) {
			$document = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?>'
				. '<quizzenger-question-export version="1.0"></quizzenger-question-export>');

			$meta = $document->addChild('meta');
			$meta->addChild('system')->{0} = APP_PATH;
			$meta->addChild('date')->{0} = date('Y-m-d H:i:s');

			// TODO: Add attachment, category.
			$questions = $document->addChild('questions');
			foreach($export as $current) {
				$questionElement = $questions->addChild('question');
				$questionElement->addAttribute('type', $current->type);
				$questionElement->addAttribute('difficulty', $current->difficulty);

				$questionElement->addChild('author')->{0} = $current->username;
				$questionElement->addChild('created')->{0} = $current->created;
				$questionElement->addChild('modified')->{0} = $current->lastModified;
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
						// TODO: Add Base64 encoding here.
					}
					else {
						$attachmentElement->addAttribute('type', 'url');
						$attachmentElement->{0} = $current->attachment;
					}
				}
			}

			header('Content-Type: text/xml');
			echo utf8_encode($document->asXML());
		}

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
		}
	} // class QuestionExporter
} // namespace quizzenger\gate

?>
