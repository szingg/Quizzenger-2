<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\model\ModelCollection as ModelCollection;

		class GameHostingAchievement implements IAchievement {

		public function grant(SqlHelper $database, UserEvent $event) {
				//Setup
				$memberCountCond = $event->get('member-count');
				$user = $event->user();

				$memberCount = count(ModelCollection::gameModel()->getGameMembersByGameId($event->get('gameid')) );

				return $memberCount >= $memberCountCond;
			}
		} // class GameHostingAchievement
	} // namespace quizzenger\plugins\achievements
?>
