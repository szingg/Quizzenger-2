<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameWinAchievement implements IAchievement {

			public function grant(mysqli $database, UserEvent $event) {
				//Setup
				$memberCount = $event->get('member-count');
				$user = $event->user();
				$sqlhelper = new \sqlhelper ( log::get() );
				$gameModel = new GameModel($sqlhelper);

				$gamereport = $gameModel->getGameReport($event->get('gameid'));

				//getWinner
				$winner = $gamereport[0];

				return $winner['user_id'] == $user && count($gamereport) >= $memberCount;
			}
		} // class GameWinAchievement
	} // namespace quizzenger\plugins\achievements
?>
