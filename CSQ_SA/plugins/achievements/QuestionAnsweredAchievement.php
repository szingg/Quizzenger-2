<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\data\ArgumentCollection as ArgumentCollection;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class QuestionAnsweredAchievement implements IAchievement {
			public function grant(mysqli $database, ArgumentCollection $collection, $id, $event, $type, $arguments) {
				$userId = $collection->get('user-id');
				$questionCount = $arguments['question-count'];

				$statement = $database->prepare('SELECT COUNT(*) as count FROM `questionperformance`'
					. ' WHERE user_id = ? AND questionCorrect > 0');

				$statement->bind_param('i', $userId);

				if($statement->execute() === false)
					return false;

				if($result = $statement->get_result()) {
					if($result->fetch_object()->count >= $questionCount)
						return true;
				}

				return false;
			}
		} // class QuestionAnsweredAchievement
	} // namespace quizzenger\plugins\achievements
?>
