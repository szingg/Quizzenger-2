<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GameDefeatHostAchievement implements IAchievement {

			public function grant(mysqli $database, UserEvent $event) {
				//Setup
				$user = $event->user();
				$sqlhelper = new \sqlhelper ( log::get() );
				$gameModel = new GameModel($sqlhelper);

				$gamereport = $gameModel->getGameReport($event->get('gameid'));
				$host = $gameModel->getGameOwnerByGameId($event->get('gameid'));

				$userScore = null;
				$hostScore = null;
				foreach($gamereport as $report){
					if($report['user_id'] == $host) $hostScore = $report['questionAnsweredCorrect'];
					if($report['user_id'] == $user) $userScore = $report['questionAnsweredCorrect'];
				}

				if(! isset($userScore, $hostScore)) return false;
				else return $userScore > $hostScore;
			}
		} // class GameWinAchievement
	} // namespace quizzenger\plugins\achievements
?>
