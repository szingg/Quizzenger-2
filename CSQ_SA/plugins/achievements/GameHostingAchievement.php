<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameHostingAchievement implements IAchievement {

			public function grant(mysqli $database, UserEvent $event) {
				//Setup
				$memberCountCond = $event->get('member-count');
				$user = $event->user();
				$sqlhelper = new \sqlhelper ( log::get() );
				$gameModel = new GameModel($sqlhelper);

				$memberCount = count($gameModel->getGameMembersByGameId($event->get('gameid')) );

				return $memberCount >= $memberCountCond;
			}
		} // class GameHostingAchievement
	} // namespace quizzenger\plugins\achievements
?>
