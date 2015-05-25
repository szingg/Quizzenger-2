<?php

// Load config override if defined.
if(file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'config.dev.php')) {
	include(__DIR__ . DIRECTORY_SEPARATOR . 'config.dev.php');
	return;
}

// DB connection
define ( "dbuser", "ENTER ME" );
define ( "dbpassword", "ENTER ME" );
define ( "db", "csq" );
define ( "dbhost", "ENTER ME" );
define ( "dbport", "3306" );

// Quizzenger Settings
define ("APP_PATH","https://quizzenger.ch");
//Attachment Settings
define ("ATTACHMENT_PATH","content/attachments");
define ("MAX_ATTACHMENT_SIZE_KByte",1024);
define ("ATTACHMENT_ALLOWED_EXTENSIONS","image/jpg, image/jpeg, image/png");
//Achievement Settings
define ("ACHIEVEMENT_PATH","content/achievements");
define ("ACHIEVEMENT_IMAGE_EXTENSION","png");
//Rank Settings
define ("RANK_PATH","content/ranks");
define ("RANK_IMAGE_EXTENSION","png");
//Question Settings
define ("SINGLECHOICE_ANSWER_COUNT","4");
define ("SINGLECHOICE_TYPE","SingleChoice");
//Game Settings
define ('MIN_GAME_DURATION_MINUTES', '1');
define ('MAX_GAME_DURATION_MINUTES', '10');
//Layout Settings
define ("QUESTION_INPUTFIELD_MAX_LENGTH","320");
define ("QUESTION_INPUTFIELD_MAX_ROWCOUNT","3");
define ("ANSWER_INPUTFIELD_MAX_LENGTH","160");
define ("ANSWER_INPUTFIELD_ROWCOUNT","2");
define ("ANSWER_EXPLANATION_INPUTFIELD_MAX_LENGTH","320");
define ("ANSWER_EXPLANATION_INPUTFIELD_ROWCOUNT","1");
define ("USER_INACTIVE_NAME_ADDITION","(inaktiv)");
define ("QUESTIONHISTORY_NEWEST_SHOWNCOUNT",10);
define ("SHOW_PROCESSING_TIME",true);
define ("QUESTIONTEXT_CUTOFF_LENGTH",75);
//SCORE
//define ("QUESTION_ANSWERED_SCORE", "2");
//define ("QUESTION_CREATED_SCORE", "5");
//define ("ADD_RATING_SCORE", "1");
//define ("QUIZ_TAKEN_SCORE", "1");
//define ("MODERATION_SCORE", "100");
define ("RATING_MAX_STARS", "5");
//Logical Settings
define ("MIN_DIFFICULTY_COUNT_NEEDED_TO_SHOW",5);
define ("DIFFERENT_QUESTION_WEIGHTS", "5");


// Log Settings
define ("LOGGING_ACTIVE",true);
define ("LOGPATH","/var/log/quizzenger");
define ("MAX_LOG_DAYS", 365);

// Security Login / Register
// -------------------------
define ( "SECURE", TRUE ); // FALSE only for debugging
define ( "BRUTE_FORCE_COOLDOWN", "600"); // in seconds (600 = 10 minutes)
define ( "BRUTE_FORCE_MAX_ATTEMPTS","5");
define ( "BRUTE_FORCE_CHECK",TRUE);
define ( "FORCE_HTTPS_CONNECTION",TRUE); // FALSE only for debugging, never in production!
define ( "FORCE_RECAPTCHA_FOR_NEW_QUESTIONS", TRUE);



// Configuration PHP to show errors
// DO NOT ACTIVATE IN PRODUCTION
// --------------------------------
ini_set ( "display_errors", 1 );
ini_set ( "display_startup_errors", 1 );
error_reporting ( E_ALL );
?>
