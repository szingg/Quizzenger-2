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
define ("QUESTION_ANSWERED_SCORE", "2");
define ("QUESTION_CREATED_SCORE", "5");
define ("ADD_RATING_SCORE", "1");
define ("QUIZ_TAKEN_SCORE", "1");
define ("MODERATION_SCORE", "100");
define ("RATING_MAX_STARS", "5");
//Logical Settings
define ("MIN_DIFFICULTY_COUNT_NEEDED_TO_SHOW",5);
define ("DIFFERENT_QUESTION_WEIGHTS", "5");


// Log Settings
define ("LOGGING_ACTIVE",true);
define ("LOGPATH","log/");


// Security Login / Register
// -------------------------
define ( "SECURE", TRUE ); // FALSE only for debugging
define ( "BRUTE_FORCE_COOLDOWN", "600"); // in seconds (600 = 10 minutes)
define ( "BRUTE_FORCE_MAX_ATTEMPTS","5");
define ( "BRUTE_FORCE_CHECK",TRUE);
define ( "FORCE_HTTPS_CONNECTION",TRUE); // FALSE only for debugging, never in production!



// Configuration PHP to show errors
// DO NOT ACTIVATE IN PRODUCTION
// --------------------------------
ini_set ( "display_errors", 1 );
ini_set ( "display_startup_errors", 1 );
error_reporting ( E_ALL );



// Error Messages
// --------------
define ( "err_register_invalid_mail", "Die eingegebene Email Adresse ist ungültig" );
define ( "err_register_pw", "Ungültige Passwort konfiguration (=/ 128). Haben Sie Javascript aktiviert?" );
define ( "err_register_existing_information", "Die Email Adresse oder der Benutzername wird bereits verwendet" );
define ( "err_register_check", "Verbindung mit der Datenbank fehlgeschlagen" );
define ( "err_register_insert", "Erstellen Ihres Benutzers ist fehlgeschlagen - Insert failed" );
define ( "err_login_bad_credentials", "Email Adresse oder Passwort inkorrekt" );
define ( "err_login_inactive", "Ihr Benutzer ist inaktiv" );
define ( "err_login_tries_exceeded", "<br>Die Anzahl fehlgeschlagene Logins für Ihren Benutzer wurden erreicht<br>Bitte probieren Sie es in ".(BRUTE_FORCE_COOLDOWN/60)." Minuten erneut" );
define ( "err_db_query_failed", "Oops, es wurde eine ungültige Datenbank abfrage getätigt" );
define ( "err_missing_input", "Bitte füllen Sie alle benötigten Felder aus" );
define ( "err_not_authorized_questionedit", "Sie sind nicht berechtigt diese Frage zu bearbeiten" );
define ( "err_not_authorized_quizdetail", "Sie sind nicht berechtigt dieses Quiz anzuschauen" );
define ( "err_unkown", "Oops, es ist ein unbekannter Fehler aufgetreten!" );


// Info Messages
// -------------
define ( "mes_login_success", "Login erfolgreich" );
define ( "mes_register_success", '<b>Registrierung erfolgreich</b> <br> Sie können sich nun <a href="./index.php?view=login">anmelden</a>' );
define ( "mes_logout_success", "Logout erfolgreich" );
define ( "mes_login_already", "Sie sind bereits angemeldet" );
define ( "mes_passwordchange_success", "Ihr Passwort wurde erfolgreich geändert<br>Bitte melden Sie sich erneut an" );
define ( "mes_sent_report", "Die Meldung wurde erfasst und wird von den Moderatoren angeschaut.");
define ( "mes_no_results", "<b>Ihr gesetzter Filter ergab leider keine Ergebnisse</b>" );

?>