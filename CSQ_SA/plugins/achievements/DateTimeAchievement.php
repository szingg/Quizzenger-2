<?php
	namespace quizzenger\plugins\achievements {
		use \mysqli as mysqli;
		use \quizzenger\data\UserEvent as UserEvent;
		use \quizzenger\achievements\IAchievement as IAchievement;

		class DateTimeAchievement implements IAchievement {
			private static function cast($input) {
				if(strpos($input, '*') !== false)
					return '*';

				return (int)$input;
			}

			private static function match($dateTime) {
				$regex = '/(\d{4}|\*+)-(\d{2}|\*+)-(\d{2}|\*+)-(\d{2}|\*+)-(\d{2}|\*+)-(\d{2}|\*+)/';
				$matches = null;

				if(preg_match($regex, $dateTime, $matches) !== 1) {
					return false;
				}

				return [
					'year' => self::cast($matches[1]),
					'month' => self::cast($matches[2]),
					'day' => self::cast($matches[3]),
					'hour' => self::cast($matches[4]),
					'minute' => self::cast($matches[5]),
					'second' => self::cast($matches[6])
				];
			}

			private static function checkConditions($matches) {
				$actualYear = (int)date('Y');
				$actualMonth = (int)date('m');
				$actualDay = (int)date('d');
				$actualHour = (int)date('H');
				$actualMinute = (int)date('i');
				$actualSecond = (int)date('s');

				if(($matches['year'] === '*' || $matches['year'] === $actualYear)
					&& ($matches['month'] === '*' || $matches['month'] === $actualMonth)
					&& ($matches['day'] === '*' || $matches['day'] === $actualDay)
					&& ($matches['hour'] === '*' || $matches['hour'] === $actualHour)
					&& ($matches['minute'] === '*' || $matches['minute'] === $actualMinute)
					&& ($matches['second'] === '*' || $matches['second'] === $actualSecond))
				{
					return true;
				}

				return false;
			}

			public function grant(mysqli $database, UserEvent $event) {
				$dateTime = $event->get('date-time');
				$matches = self::match($dateTime);

				if($matches === false)
					return false;

				return self::checkConditions($matches);
			}
		} // class DateTimeAchievement
	} // namespace quizzenger\plugins\achievements
?>
