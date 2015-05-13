<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\model\ModelCollection as ModelCollection;

		class GameDefeatHostAchievement implements IAchievement {

			public function grant(SqlHelper $database, UserEvent $event) {
				//Setup
				$user = $event->user();

				$gamereport = ModelCollection::gameModel()->getGameReport($event->get('gameid'));
				$host = ModelCollection::gameModel()->getGameOwnerByGameId($event->get('gameid'));

				$userScore = null;
				$hostScore = null;
				foreach($gamereport as $report){
					if($report['user_id'] == $host) $hostScore = $report['questionAnsweredCorrect'];
					if($report['user_id'] == $user) $userScore = $report['questionAnsweredCorrect'];
				}

				if(! isset($userScore, $hostScore)) return false;
				else return $userScore > $hostScore;
			}
		} // class GameDefeatAchievement
	} // namespace quizzenger\plugins\achievements
?>
