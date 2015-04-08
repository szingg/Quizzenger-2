<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameWinAchievement implements IAchievement {

			private function cmp($a, $b)
			{
				return ($a['totalTimeInSec'] < $b['totalTimeInSec']) ? -1 : (($a['totalTimeInSec'] > $b['totalTimeInSec']) ? 1 : 0);
				//return $a->totalTimeInSec < $b->totalTimeInSec;
			}

			public function grant(mysqli $database, UserEvent $event) {
				//Setup
				$memberCount = $event->get('member-count');
				$user = $event->user();
				$sqlhelper = new \sqlhelper ( log::get() );
				$gameModel = new GameModel($sqlhelper);

				$gamereport = $gameModel->getGameReport($event->get('gameid'));

				//getWinners
				$winner = $gamereport[0];

				usort($gamereport, "cmp");
				$timeWinner = $gamereport[0];

				return $winner['user_id'] == $user && count($gamereport) >= $memberCount && $timeWinner['user_id'] == $user;
			}
		} // class GameWinAchievement
	} // namespace quizzenger\plugins\achievements
?>
