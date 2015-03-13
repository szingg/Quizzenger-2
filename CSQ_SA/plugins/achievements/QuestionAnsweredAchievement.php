<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class QuestionAnsweredAchievement implements IAchievement {
			public function grant(mysqli $database, $id, $event, $type, $arguments) {
				return true;
			}
		} // class QuestionAnsweredAchievement
	} // namespace quizzenger\plugins\achievements
?>
