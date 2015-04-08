<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameWinAchievement implements IAchievement {
			private function checkConditions($matches) {

			}

			function cmp($a, $b)
			{
				return ($a['totalTimeInSec'] < $b['totalTimeInSec']) ? -1 : (($a['totalTimeInSec'] > $b['totalTimeInSec']) ? 1 : 0);
				//return $a->totalTimeInSec < $b->totalTimeInSec;
			}

			public function grant(mysqli $database, UserEvent $event) {
				//Setup
				try {
					$memberCount = $event->get('member-count');
				} catch (Exception $e) {
					$memberCount = -1;
				}
				try {
					$fastest = $event->get('fastest');
				} catch (Exception $e) {
					$fastest = false;
				}
				if($memberCount = -1 && $fastest == false) return false;

				$user = $event->user();

				$gameid = $event->get('gameid');

				$sqlhelper = new \sqlhelper ( log::get() );
				$gameModel = new GameModel($sqlhelper);

				$gamereport = $gameModel->getGameReport($gameid);

				//getWinners
				$winner = $gamereport[0];

				usort($gamereport, "cmp");
				$timeWinner = $gamereport[0];

				//checkConditions
				if(($winner['user_id'] == $user && $timeWinner['user_id'] == $user)
						|| $fastest == false ){
					$timeCond = true;
				}
				else{
					$timeCond = false;
				}
				if(($winner['user_id'] == $user && count($gamereport) >= $memberCount)
						|| $memberCount = -1){
					$memberCond = true;
				}
				else{
					$memberCond = false;
				}

				return $timeCond && $memberCond;
			}
		} // class DateTimeAchievement
	} // namespace quizzenger\plugins\achievements
?>
