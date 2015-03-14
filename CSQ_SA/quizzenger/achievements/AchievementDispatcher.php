<?php

namespace quizzenger\achievements {
	use \mysqli as mysqli;
	use \quizzenger\data\ArgumentCollection as ArgumentCollection;
	use \quizzenger\achievements\IAchievement as IAchievement;

	class AchievementDispatcher {
		private $mysqli;

		public function __construct(mysqli $mysqli) {
			$this->mysqli = $mysqli;
		}

		private static function createAchievementInstance($type) {
			require_once 'plugins/achievements/' . $type . 'Achievement.php';
			$qualified = "\\quizzenger\\plugins\\achievements\\" . $type . "Achievement";
			return new $qualified();
		}

		private function grantAchievement(ArgumentCollection $collection, $id) {
			// Grant the specified achievement to the current user.
			$statement = $this->mysqli->prepare('INSERT INTO `userachievement`'
				. ' (achievement_id, user_id) VALUES (?, ?);');

			$userId = $collection->get('user-id');
			$statement->bind_param('ii', $id, $userId);
			$statement->execute();
		}

		public function dispatchSingle($collection, $id, $event, $type, $arguments) {
			$achievement = self::createAchievementInstance($type);
			if($achievement->grant($this->mysqli, $collection, $id, $event, $type, $arguments)) {
				$this->grantAchievement($collection, $id);
			}
		}

		public function dispatch($event, ArgumentCollection $collection) {
			// Select all non-granted achievements triggered by $event.
			$statement = $this->mysqli->prepare('SELECT id, type, arguments FROM `achievement` WHERE id'
				. ' NOT IN (SELECT achievement_id FROM `userachievement` WHERE user_id = ?)'
				. ' AND id IN (SELECT achievement_id FROM `achievementtrigger` WHERE name = ?);');

			$userId = $collection->get('user-id');
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

					$this->dispatchSingle($collection, $id, $event, $type, $args);
				}
				$result->close();
			}
		}
	} // class AchievementDispatcher
} // namespace quizzenger\achievements

?>
