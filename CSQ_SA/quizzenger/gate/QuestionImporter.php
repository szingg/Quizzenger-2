<?php

namespace quizzenger\gate {
	use \stdClass as stdClass;
	use \mysqli as mysqli;
	use \SimpleXMLElement as SimpleXMLElement;
	use \quizzenger\logging\Log as Log;

	class QuestionImporter {
		private $mysqli;
		private $categoryCache;
		private $categoryCacheStatement;
		private $questionInsertStatement;
		private $answerInsertStatement;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
			$this->categoryCache = [];

			$this->categoryCacheStatement = $this->mysqli->prepare('SELECT ct1.name AS first_name, ct1.id AS first_id, ct1.parent_id AS first_parent,'
				. ' ct2.name AS second_name, ct2.id AS second_id, ct2.parent_id AS second_parent,'
				. ' ct3.name AS third_name, ct3.id AS third_id, ct3.parent_id AS third_parent'
				. ' FROM category AS ct1'
				. ' LEFT JOIN category AS ct2 ON ct2.parent_id=ct1.id'
				. ' LEFT JOIN category AS ct3 ON ct3.parent_id=ct2.id'
				. ' WHERE ct1.parent_id=0'
				. ' ORDER BY ct1.parent_id, ct2.parent_id, ct3.parent_id');

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

		private function createCacheEntry($name, $id, $parent, $existing = true) {
			return [
				'name' => $name,
				'id' => $id,
				'parent' => $parent,
				'existing' => $existing,
				'children' => []
			];
		}

		private function &cacheCategoryLevel(array &$cache, $name, $id, $parent) {
			$null = null;

			if($name === null || $name === "")
				return $null;

			if(!isset($cache[$name]))
				$cache[$name] = $this->createCacheEntry($name, $id, $parent);

			return $cache[$name];
		}

		private function cacheCategory(stdClass &$category) {
			$firstLevel = &$this->cacheCategoryLevel($this->categoryCache, $category->first_name, $category->first_id, $category->first_parent);
			if($firstLevel === null)
				return;

			$secondLevel = &$this->cacheCategoryLevel($firstLevel['children'], $category->second_name, $category->second_id, $category->second_parent);
			if($secondLevel === null)
				return;

			$thirdLevel = &$this->cacheCategoryLevel($secondLevel['children'], $category->third_name, $category->third_id, $category->third_parent);
		}

		private function rebuildCategoryCache() {
			if(!$this->categoryCacheStatement->execute())
				return false;

			$this->categoryCache = [];
			$result = $this->categoryCacheStatement->get_result();
			while($current = $result->fetch_object()) {
				$this->cacheCategory($current);
			}
		}

		private function &getCachedCategory($first, $second, $third) {
			$null = null;
			if(!(isset($this->categoryCache[$first])
				&& isset($this->categoryCache[$first]['children'][$second])
				&& isset($this->categoryCache[$first]['children'][$second]['children'][$third])))
			{
				return $null;
			}

			return $this->categoryCache[$first]['children'][$second]['children'][$third];
		}

		private function importQuestionsForUser($userId, array $questions) {
			$this->rebuildCategoryCache();
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
