<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\data\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class DateAchievement implements IAchievement {
			public function grant(mysqli $database, UserEvent $event) {
				$date = $event->get('date');
				$matches = null;

				if(preg_match('/(\d{4}|\*+)-(\d{2}|\*+)-(\d{2}|\*)/', $date, $matches) !== 1) {
					return false;
				}

				$year = $matches[1];
				$month = $matches[2];
				$day = $matches[3];

				$year = (strpos($year, '*') !== false ? '*' : $year);
				$month = (strpos($month, '*') !== false ? '*' : $month);
				$day = (strpos($day, '*') !== false ? '*' : $day);

				$actualYear = date('Y');
				$actualMonth = date('m');
				$actualDay = date('d');

				if(($year === $actualYear || $year === '*')
					&& ($month === $actualMonth || $month === '*')
					&& ($day === $actualDay || $day === '*'))
				{
					return true;
				}

				return false;
			}
		} // class DateAchievement
	} // namespace quizzenger\plugins\achievements
?>
