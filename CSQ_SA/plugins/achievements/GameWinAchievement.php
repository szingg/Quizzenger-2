<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\model\ModelCollection as ModelCollection;

		class GameWinAchievement implements IAchievement {

			public function grant(SqlHelper $database, UserEvent $event) {
				//Setup
				$memberCount = $event->get('member-count');
				$user = $event->user();

				$gamereport = ModelCollection::gameModel()->getGameReport($event->get('gameid'));

				//getWinner
				$winner = $gamereport[0]['user_id'];

				return $winner == $user && count($gamereport) >= $memberCount;
			}
		} // class GameWinAchievement
	} // namespace quizzenger\plugins\achievements
?>
