<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class GamePerfectAchievement implements IAchievement {

			public function grant(SqlHelper $database, UserEvent $event) {
				$userid = $event->user();
				$gameid = $event->get('gameid');

				$result = $database->s_query('SELECT SUM(CASE WHEN questionCorrect = 100 THEN 1 ELSE 0 END) AS correct,'
						.' tot.total FROM questionperformance q'
						.' JOIN ('
							.' SELECT gamesession.id, COUNT(weight) AS total '
							.' FROM gamesession, quiztoquestion'
							.' WHERE gamesession.quiz_id = quiztoquestion.quiz_id AND gamesession.id = ?'
						.' ) AS tot ON tot.id = q.gamesession_id'
						.' WHERE q.gamesession_id = ? AND q.user_id = ?', ['i','i','i'], [$gameid, $gameid, $userid]);


				if($result) {
					$resultObject = $database->getSingleResult($result);
					if($resultObject['correct'] == $resultObject['total']) {
						return true;
					}
				}
				return false;
			}
		} // class GamePerfectAchievement
	} // namespace quizzenger\plugins\achievements
?>
