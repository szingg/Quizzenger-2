<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameWinAchievement implements IAchievement {

			public function grant(SqlHelper $database, UserEvent $event) {
				//Setup
				$memberCount = $event->get('member-count');
				$user = $event->user();
				$gameModel = new GameModel($database);

				$gamereport = $gameModel->getGameReport($event->get('gameid'));

				//getWinner
				$winner = $gamereport[0]['user_id'];

				return $winner == $user && count($gamereport) >= $memberCount;
			}
		} // class GameWinAchievement
	} // namespace quizzenger\plugins\achievements
?>
