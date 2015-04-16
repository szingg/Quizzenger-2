<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameHostingAchievement implements IAchievement {

		public function grant(SqlHelper $database, UserEvent $event) {
				//Setup
				$memberCountCond = $event->get('member-count');
				$user = $event->user();
				$gameModel = new GameModel($database);

				$memberCount = count($gameModel->getGameMembersByGameId($event->get('gameid')) );

				return $memberCount >= $memberCountCond;
			}
		} // class GameHostingAchievement
	} // namespace quizzenger\plugins\achievements
?>
