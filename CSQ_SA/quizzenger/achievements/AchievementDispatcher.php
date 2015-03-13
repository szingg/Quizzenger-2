<?php

namespace quizzenger\achievements {
	use \mysqli as mysqli;
	use \quizzenger\data\ArgumentCollection as ArgumentCollection;

	class AchievementDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		public function dispatchSingle($id, $event, $type, $arguments) {
			//
		}

		public function dispatch($event, ArgumentCollection $arguments) {
			// Select all non-granted achievements triggered by $event.
			$statement = $this->mysqli->prepare('SELECT id, type, arguments FROM `achievement` WHERE id'
				. ' NOT IN (SELECT achievement_id FROM `userachievement` WHERE user_id = ?)'
				. ' AND id IN (SELECT achievement_id FROM `achievementtrigger` WHERE name = ?);');

			$userId = $arguments->get('user-id');
			$statement->bind_param('is', $userId, $event);

			$statement->execute();
			$result = $statement->get_result();
			if($result) {
				while($current = $result->fetch_object()) {
					$id = $current->id;
					$type = $current->type;
					$args = json_decode($current->arguments, true);
					if($args === null || json_last_error() !== JSON_ERROR_NONE) {
						$args = [];
					}

					$this->dispatchSingle($id, $event, $type, $args);
				}
				$result->close();
			}
		}
	} // class AchievementDispatcher
} // namespace quizzenger\achievements

?>
