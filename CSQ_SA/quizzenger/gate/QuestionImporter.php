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

		public function import($data) {
			$xml = simplexml_load_string($data);
			if(!$xml) {
				Log::error('Could not import questions from XML data.');
				return false;
			}

			$questions = $xml->xpath('/quizzenger-question-export/questions');
			foreach($questions as $current) {
				//
			}
		}
	} // class QuestionImporter
} // namespace quizzenger\gate

?>
