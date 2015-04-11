<?php
	namespace quizzenger\plugins\achievements {
		use \SqlHelper as SqlHelper;
		use \quizzenger\logging\Log as Log;
		use \quizzenger\dispatching\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;
		use \quizzenger\gamification\model\GameModel as GameModel;

		class QuizPerfectAchievement implements IAchievement {

			public function grant(SqlHelper $database, UserEvent $event) {
				$database = $database->database();
				$userid = $event->user();
				$quizid = $event->get('quizid');

				$result = $database->s_query('SELECT SUM(CASE WHEN questionCorrect = 100 THEN 1 ELSE 0 END) AS correct,'
						.' tot.total FROM questionperformance q'
						.' JOIN ('
							.' SELECT quizsession.id, COUNT(weight) AS total '
							.' FROM quizsession, quiztoquestion'
							.' WHERE quizsession.quiz_id = quiztoquestion.quiz_id AND quizsession.id = ?'
						.' ) AS tot ON tot.id = q.session_id'
						.' WHERE q.session_id = ? AND q.user_id = ?', ['i','i','i'], [$quizid, $quizid, $userid]);


				if($result) {
					$resultObject = $database->getSingleResult($result);
					if($resultObject['correct'] == $resultObject['total']) {
						return true;
					}
				}
				return false;

			}
		} // class QuizPerfectAchievement
	} // namespace quizzenger\plugins\achievements
?>
