<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class InvolvedCategoriesAchievement implements IAchievement {
			public function grant(SqlHelper $database, UserEvent $event) {
				$database = $database->database();
				$userId = $event->user();
				$categoryCount = $event->get('category-count');

				$statement = $database->prepare('SELECT COUNT(DISTINCT question.category_id) as count'
					. ' FROM `questionperformance` JOIN (`question`, `category`)'
					. ' ON (questionperformance.question_id = question.id AND question.category_id = category.id)'
					. ' WHERE questionperformance.user_id = ? AND questionperformance.questionCorrect > 0');

				$statement->bind_param('i', $userId);

				if($statement->execute() === false) {
					Log::error('Database Query failed in InvolvedCategoriesAchievement.');
					return false;
				}

				if($result = $statement->get_result()) {
					if($result->fetch_object()->count >= $categoryCount) {
						return true;
					}
				}

				return false;
			}
		} // class InvolvedCategoriesAchievement
	} // namespace quizzenger\plugins\achievements
?>
