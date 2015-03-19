<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\data\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class QuestionAnsweredCorrectAchievement implements IAchievement {
			public function grant(mysqli $database, UserEvent $event) {
				$userId = $event->user();
				$questionCount = $event->get('question-count');

				$statement = $database->prepare('SELECT COUNT(*) as count FROM `questionperformance`'
					. ' WHERE user_id = ? AND questionCorrect > 0');

				$statement->bind_param('i', $userId);

				if($statement->execute() === false) {
					Log::error('Database Query failed in QuestionAnsweredCorrectAchievement.');
					return false;
				}

				if($result = $statement->get_result()) {
					if($result->fetch_object()->count >= $questionCount)
						return true;
				}

				return false;
			}
		} // class QuestionAnsweredCorrectAchievement
	} // namespace quizzenger\plugins\achievements
?>
