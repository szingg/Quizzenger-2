SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Table structure for table `answer`
--

CREATE TABLE IF NOT EXISTS `answer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `correctness` tinyint(1) NOT NULL,
  `text` varchar(200) NOT NULL,
  `explanation` varchar(1000) DEFAULT NULL,
  `question_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_questionanswer` (`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moderation`
--

CREATE TABLE IF NOT EXISTS `moderation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `inactive` tinyint(1) NOT NULL DEFAULT '0',
  `startdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_usermoderation` (`user_id`),
  KEY `fk_categorymoderation` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `questiontext` varchar(320) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastModified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `difficulty` double DEFAULT NULL,
  `difficultycount` int(11) DEFAULT NULL,
  `rating` double DEFAULT NULL,
  `ratingcount` int(11) DEFAULT NULL,
  `attachment` varchar(300) DEFAULT NULL,
  `attachment_local` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userquestion` (`user_id`),
  KEY `fk_categoryquestion` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `questionhistory`
--

CREATE TABLE IF NOT EXISTS `questionhistory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `action` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `questionperformance`
--

CREATE TABLE IF NOT EXISTS `questionperformance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `questionCorrect` int(11) NOT NULL,
  `session_id` int(11) DEFAULT NULL,
  `gamesession_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_userquestionperformance` (`user_id`),
  KEY `fk_questionquestionperformance` (`question_id`),
  KEY `fk_sessionquestionperformance` (`session_id`),
  KEY `fk_gamesessionquestionperformance` (`gamesession_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE IF NOT EXISTS `quiz` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_userquiz` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `quiz` (`id`, `name`, `user_id`, `created`) VALUES (-1, 'Temporary Quiz', 1, '1999-01-01 12:23:34');

-- --------------------------------------------------------

--
-- Table structure for table `quizsession`
--

CREATE TABLE IF NOT EXISTS `quizsession` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quiz_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_quizsession` (`quiz_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `quiztoquestion`
--

CREATE TABLE IF NOT EXISTS `quiztoquestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `weight` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_questionquiztoquestion` (`question_id`),
  KEY `fk_quizquiztoquestion` (`quiz_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `stars` int(11) DEFAULT NULL,
  `comment` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_userrating` (`user_id`),
  KEY `fk_questionrating` (`question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE IF NOT EXISTS `report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) DEFAULT NULL,
  `rating_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `by_user_id` int(11) NOT NULL,
  `message` varchar(500) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `doneon` timestamp NULL DEFAULT NULL,
  `doneby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_question` (`question_id`),
  KEY `fk_category` (`category_id`),
  KEY `fk_user` (`user_id`),
  KEY `fk_doneby` (`doneby`),
  KEY `fk_rating` (`rating_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE IF NOT EXISTS `tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `tagtoquestion`
--

CREATE TABLE IF NOT EXISTS `tagtoquestion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_questiontagtoquestion` (`question_id`),
  KEY `fk_tagtagtoquestion` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(35) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` char(128) NOT NULL,
  `salt` char(128) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `inactive` tinyint(1) DEFAULT NULL,
  `superuser` tinyint(1) NOT NULL DEFAULT '0',
  `bonus_score` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

INSERT INTO `user` (`id`, `username`, `email`, `password`, `salt`, `inactive`, `superuser`) VALUES (1, 'Gast', 'gast@gast.ch', '41f72c210785dc5d8839286e25a1af21f7a302b24fb0147ea20a7d6324427eba888211ae8006d50729913cecdf67cbb7234c437d38244bf14667ec80b34ed186', '2ba3e92e20ed389bb34094ef871b9ddba2a79c1670ec2ddd0b74fdb97fc4ee3b136287023a6c6e7121003924b0a69b6bbf79247f07b21894f15295441bb4d61b', 1, 0);
INSERT INTO `user` (`id`, `username`, `email`, `password`, `salt`, `inactive`, `superuser`) VALUES (2, 'Superuser', 'superuser@quizzenger.ch', '751b5c13d0a17effcae78bb17f01f59932727f701ffc9c1109ef428501fbc0c542daebdc1b6e78250507e364e4e0ba746aa2c58f457743c9dc994d74ac2bc429', '52947e10919640a98af1d55cce08f9da6c39fcb5d99cb4cffd5dafba3f9470b1f8a82615d7f9e4605ed54dc8a16b845d86d0cbfbaea5bcd810185b725782a654', NULL, 01);

-- --------------------------------------------------------

--
-- Table structure for table `userscore`
--

CREATE TABLE IF NOT EXISTS `userscore` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `producer_score` int(11) DEFAULT 0 NOT NULL,
  `consumer_score` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`user_id`,`category_id`),
  KEY `fk_useruserscore` (`user_id`),
  KEY `fk_categoryuserscore` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `userachievement`
--

CREATE TABLE IF NOT EXISTS `userachievement` (
  `achievement_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `achieved_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY(achievement_id, user_id),
  KEY `fk_user_userachievement` (`user_id`),
  KEY `fk_achievement_userachievement` (`achievement_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `achievement`
--

CREATE TABLE IF NOT EXISTS `achievement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `sort_order` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `image` varchar(64) NOT NULL,
  `arguments` text NULL,
  `bonus_score` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `achievementtrigger`
--
CREATE TABLE IF NOT EXISTS `achievementtrigger` (
  `achievement_id` int(11) NOT NULL,
  `eventtrigger_name` varchar(50) NULL,
  PRIMARY KEY(`achievement_id`, `eventtrigger_name`),
  KEY `fk_achievement_achievementtrigger` (`achievement_id`),
  KEY `fk_eventtrigger_achievementtrigger` (`eventtrigger_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `eventtrigger`
--
CREATE TABLE IF NOT EXISTS `eventtrigger` (
  `name` varchar(64) NOT NULL,
  `producer_score` int(11) DEFAULT 0 NOT NULL,
  `consumer_score` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY(`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--
CREATE TABLE IF NOT EXISTS `rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `threshold` int(11) NOT NULL,
  `image` varchar(64) DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------


--
-- Table structure for table `gamesession`
--

CREATE TABLE IF NOT EXISTS `gamesession` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `starttime` timestamp NULL DEFAULT NULL,
  `endtime` timestamp NULL DEFAULT NULL,
  `duration` time NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_quizgamesession` (`quiz_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

-- --------------------------------------------------------

--
-- Table structure for table `gamemember`
--
CREATE TABLE IF NOT EXISTS `gamemember` (
  `gamesession_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY(`gamesession_id`, `user_id`),
  KEY `fk_gamesession_gamemember` (`gamesession_id`),
  KEY `fk_user_gamemember` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `type` varchar(128) NOT NULL,
  `arguments` text,
  PRIMARY KEY (`id`),
  KEY `fk_message_user` (`user_id`),
  KEY `fk_message_translation` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `translation`
--

CREATE TABLE IF NOT EXISTS `translation` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`type` varchar(128) NOT NULL,
	`text` text NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `fk_questionanswer` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `moderation`
--
ALTER TABLE `moderation`
  ADD CONSTRAINT `fk_categorymoderation` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usermoderation` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `fk_categoryquestion` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userquestion` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `questionperformance`
--
 ALTER TABLE `questionperformance`
  ADD CONSTRAINT `fk_sessionquestionperformance` FOREIGN KEY (`session_id`) REFERENCES `quizsession` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gamesessionquestionperformance` FOREIGN KEY (`gamesession_id`) REFERENCES `gamesession` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_questionquestionperformance` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userquestionperformance` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `fk_userquiz` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quizsession`
--
ALTER TABLE `quizsession`
  ADD CONSTRAINT `fk_quizsession` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `quiztoquestion`
--
ALTER TABLE `quiztoquestion`
  ADD CONSTRAINT `fk_questionquiztoquestion` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_quizquiztoquestion` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rating`
--
ALTER TABLE `rating`
  ADD CONSTRAINT `fk_questionrating` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_userrating` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doneby` FOREIGN KEY (`doneby`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_question` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rating` FOREIGN KEY (`rating_id`) REFERENCES `rating` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tagtoquestion`
--
ALTER TABLE `tagtoquestion`
  ADD CONSTRAINT `fk_questiontagtoquestion` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tagtagtoquestion` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userscore`
--
ALTER TABLE `userscore`
  ADD CONSTRAINT `fk_categoryuserscore` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_useruserscore` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE;


--
-- Constraints for table `achievementtrigger`
--
ALTER TABLE `achievementtrigger`
  ADD CONSTRAINT `fk_achievement_achievementtrigger` FOREIGN KEY (`achievement_id`) REFERENCES `achievement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_eventtrigger_achievementtrigger` FOREIGN KEY (`eventtrigger_name`) REFERENCES `eventtrigger` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userachievement`
--
ALTER TABLE `userachievement`
  ADD CONSTRAINT `fk_user_userachievement` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_achievement_userachievement` FOREIGN KEY (`achievement_id`) REFERENCES `achievement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gamesession`
--
ALTER TABLE `gamesession`
  ADD CONSTRAINT `fk_quizgamesession` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gamemember`
--
ALTER TABLE `gamemember`
  ADD CONSTRAINT `fk_gamesession_gamemember` FOREIGN KEY (`gamesession_id`) REFERENCES `gamesession` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_gamemember` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `fk_message_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_message_translation` FOREIGN KEY (`type`) REFERENCES `translation` (`type`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Views
--
CREATE OR REPLACE VIEW userscoreaggregateview AS
	SELECT user.id, user.username, user.created_on,
		SUM(userscore.producer_score) AS producer_score,
		SUM(userscore.consumer_score) AS consumer_score,
		user.bonus_score,
		(SELECT value FROM settings WHERE name="q.scoring.producer-multiplier") AS producer_multiplier
		FROM user
		LEFT JOIN userscore ON user.id=userscore.user_id
		WHERE user.id NOT IN (0, 1, 2)
		GROUP BY user.id
		ORDER BY user.id ASC;

CREATE OR REPLACE VIEW userscoreview AS
	SELECT userscoreaggregateview.id AS id, username, created_on,
		producer_score, consumer_score, bonus_score,
		(FLOOR(producer_score*producer_multiplier+consumer_score+bonus_score)) AS total_score,
		MAX(rank.threshold) AS rank_threshold,
		rank.name AS rank_name,
		rank.image AS rank_image
		FROM userscoreaggregateview
		LEFT JOIN rank
			ON (rank.threshold<=(FLOOR(producer_score*producer_multiplier+consumer_score+bonus_score))
				OR rank.threshold=0)
		GROUP BY id;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
