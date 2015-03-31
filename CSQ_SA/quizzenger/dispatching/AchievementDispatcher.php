<?php

namespace quizzenger\dispatching {
	use \mysqli as mysqli;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\dispatching\UserEvent as UserEvent;
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

		private function grantAchievement($id, UserEvent $event) {
			// Grant the specified achievement to the current user.
			$statement = $this->mysqli->prepare('INSERT INTO `userachievement`'
				. ' (achievement_id, user_id) VALUES (?, ?);');

			$userId = $event->user();
			$statement->bind_param('ii', $id, $userId);
			$statement->execute();
		}

		public function dispatchSingle($id, $type, UserEvent $event) {
			$achievement = self::createAchievementInstance($type);
			if($achievement->grant($this->mysqli, $event)) {
				$this->grantAchievement($id, $event);
			}
		}

		public function dispatch(UserEvent $event) {
			// Select all non-granted achievements triggered by the current event.
			$statement = $this->mysqli->prepare('SELECT id, type, arguments FROM `achievement`'
				. ' WHERE id NOT IN (SELECT achievement_id FROM `userachievement` WHERE user_id = ?)'
				. ' AND id IN (SELECT achievement_id FROM `achievementtrigger` WHERE name = ?);');

			$userId = $event->user();
			$eventName = $event->name();
			$statement->bind_param('is', $userId, $eventName);

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

					$currentEvent = clone $event;
					foreach($args as $name => $value) {
						$currentEvent->set($name, $value);
					}

					$this->dispatchSingle($id, $type, $currentEvent);
				}
				$result->close();
			}
			else {
				Log::error('Could not execute DB query.');
			}
		}
	} // class AchievementDispatcher
} // namespace quizzenger\dispatching

?>
