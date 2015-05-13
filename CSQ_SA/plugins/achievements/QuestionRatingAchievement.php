<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class QuestionDifficultyAchievement implements IAchievement {
			public function grant(SqlHelper $database, UserEvent $event) {
				$database = $database->database();
				$userId = $event->user();
				$questionCount = $event->get('question-count');
				$minDifficulty = $event->get('min-difficulty');
				$minQuestionperformanceCount = $event->get('min-questionperformance-count');

				$statement = $database->prepare('SELECT COUNT(*) as count FROM question '
						.' WHERE user_id=? AND difficulty >= ? AND difficultyCount >= ?');

				$statement->bind_param('iii', $userId, $minDifficulty, $minQuestionperformanceCount);

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
		} // class QuestionDifficultyAchievement
	} // namespace quizzenger\plugins\achievements
?>
