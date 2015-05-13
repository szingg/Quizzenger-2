<?php

namespace quizzenger\dispatching {
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\dispatching\UserEvent as UserEvent;
	use \quizzenger\achievements\IAchievement as IAchievement;
	use \quizzenger\messages\MessageQueue as MessageQueue;

	/**
	 * Handles the event dispatching for all defined achievements.
	**/
	class AchievementDispatcher {
		/**
		 * Holds the instance to an existing database connection.
		**/
		private $mysqli;

		/**
		 * Creates the object based on an existing database connection.
		 * @param mysqli $mysqli Existing database connection.
		**/
		public function __construct(SqlHelper $mysqli) {
			$this->mysqli = $mysqli;
		}

		/**
		 * Dynamically creates an instance of an achievement plugin of the specified type.
		 * @param string $type Type of the achievement.
		 * @return IAchievement Returns an instance to the dynamically created plugin.
		**/
		private static function createAchievementInstance($type) {
			require_once 'plugins/achievements/' . $type . 'Achievement.php';
			$qualified = "\\quizzenger\\plugins\\achievements\\" . $type . "Achievement";
			return new $qualified();
		}

		/**
		 * Grants the achievement with the specified ID to the user that caused the event.
		 * @param int $id Achievement ID.
		 * @param UserEvent $event Event that has been fired.
		 * @return boolean Returns 'true' on success, 'false' otherwise.
		**/
		private function grantAchievement($id, UserEvent $event) {
			// Grant the specified achievement to the current user.
			$statement = $this->mysqli->database()->prepare('INSERT INTO `userachievement`'
				. ' (achievement_id, user_id) VALUES (?, ?);');

			$userId = $event->user();
			$statement->bind_param('ii', $id, $userId);
			if(!$statement->execute()) {
				Log::error('Achievement could not be granted.');
				return false;
			}

			return true;
		}

		/**
		 * Grants the bonus score to the specified user.
		 * @param int $userId Receiving user.
		 * @param int $bonusScore Score to be accounted.
		 * @return boolean Returns 'true' on success, 'false' otherwise.
		**/
		public function grantBonusScore($userId, $bonusScore) {
			$statement = $this->mysqli->database()->prepare('UPDATE `user`'
				. ' SET bonus_score=bonus_score+? WHERE id=?;');

			$statement->bind_param('ii', $bonusScore, $userId);
			if(!$statement->execute()) {
				Log::error('Bonus score could not be granted.');
				return false;
			}

			return true;
		}

		/**
		 * Initiates the dispatching of a single event for an achievement type.
		 * @param int $id Achievement ID.
		 * @param string Type of the achievement.
		 * @param $bonusScore Score to be accounted.
		 * @param UserEvent $event Event that has been fired.
		 * @param array $messageInfo with description and image of the achievement
		 * @return boolean Returns 'true' on success, 'false' otherwise.
		**/
		public function dispatchSingle($id, $type, $bonusScore, UserEvent $event, $messageInfo) {
			$achievement = self::createAchievementInstance($type);
			if($achievement->grant($this->mysqli, $event)) {
				$result = $this->grantAchievement($id, $event) && $this->grantBonusScore($event->user(), $bonusScore);
				if($result){
					MessageQueue::pushPersistent($userId, 'q.message.achievement-received', [
					'image' => $messageInfo['image'],
					'achievement' => $messageInfo['description'],
					'score' => $bonusScore
					]);
				}
			}
			return false;
		}

		/**
		 * Automatically dispatches the specified event to all relevant achievement plugins.
		 * @param UserEvent $event Event that has been fired and is now to be dispatched.
		**/
		public function dispatch(UserEvent $event) {
			// Select all non-granted achievements triggered by the current event.
			$statement = $this->mysqli->database()->prepare('SELECT id, type, arguments, bonus_score, description, image FROM `achievement`'
				. ' WHERE achievement.id NOT IN (SELECT userachievement.achievement_id FROM `userachievement` WHERE userachievement.user_id = ?)'
				. ' AND achievement.id IN (SELECT achievementtrigger.achievement_id FROM `achievementtrigger` WHERE achievementtrigger.eventtrigger_name = ?);');

			$userId = $event->user();
			$eventName = $event->name();
			$statement->bind_param('is', $userId, $eventName);

			$statement->execute();
			$result = $statement->get_result();
			if($result) {
				while($current = $result->fetch_object()) {
					$id = $current->id;
					$type = $current->type;
					$bonusScore = $current->bonus_score;
					$args = json_decode($current->arguments, true);
					if($args === null || json_last_error() !== JSON_ERROR_NONE) {
						$args = [];
					}

					$currentEvent = clone $event;
					foreach($args as $name => $value) {
						$currentEvent->set($name, $value);
					}

					$messageInfo = [ 'description' => $current->description, 'image' => $current->image ];
					$this->dispatchSingle($id, $type, $bonusScore, $currentEvent, $messageInfo);
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
