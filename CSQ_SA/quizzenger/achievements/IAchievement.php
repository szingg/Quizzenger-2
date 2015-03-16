<?php

namespace quizzenger\achievements {
	use \mysqli as mysqli;
	use \quizzenger\data\UserEvent as UserEvent;

	/**
	 * Defines the plugin interface for individual achievements.
	**/
	interface IAchievement {
		/**
		 * This function determines whether the conditions have been met to
		 * grant the specific achievement that implements this interface.
		 * @param mysqli $database Connection to the database.
		 * @param UserEvent $event The event that has been fired, containing relevant information specific to the achievement.
		 * @return boolean Returns true if the specific achievement is to be granted, false otherwise.
		**/
		public function grant(mysqli $database, UserEvent $event);
	} // interface IAchievement
} // namespace quizzenger\achievements

?>