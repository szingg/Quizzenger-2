<?php

namespace quizzenger\model {
	use \SqlHelper as SqlHelper;
	use \quizzenger\logging\Log as Log;
	use \quizzenger\model\GameModel as GameModel;
	/**
	 *
	 **/
	class ModelCollection{
		private static $questionListModel;
		public static function questionListModel(){ return self::$questionListModel; }
		private static $questionModel;
		public static function questionModel(){ return self::$questionModel; }
		private static $answerModel;
		public static function answerModel(){ return self::$answerModel; }
		private static $categoryModel;
		public static function categoryModel(){ return self::$categoryModel; }
		private static $userModel;
		public static function userModel(){ return self::$userModel; }
		private static $quizModel;
		public static function quizModel(){ return self::$quizModel; }
		private static $ratingModel;
		public static function ratingModel(){ return self::$ratingModel; }
		private static $sessionModel;
		public static function sessionModel(){ return self::$sessionModel; }
		private static $quizListModel;
		public static function quizListModel(){ return self::$quizListModel; }
		private static $tagModel;
		public static function tagModel(){ return self::$tagModel; }
		private static $registrationModel;
		public static function registrationModel(){ return self::$registrationModel; }
		private static $userscoreModel;
		public static function userscoreModel(){ return self::$userscoreModel; }
		private static $moderationModel;
		public static function moderationModel(){ return self::$moderationModel; }
		private static $reportModel;
		public static function reportModel(){ return self::$reportModel; }
		private static $reportingModel;
		public static function reportingModel(){ return self::$reportingModel; }
		private static $gameModel;
		public static function gameModel(){ return self::$gameModel; }

		/**
		 * Prevents any objects from being created.
		**/
		private function __construct() {
			//
		}

		/**
		 * Creates instances of all accessable models
		 **/
		public static function setup(SqlHelper $mysqli) {
			self::loadIncludes();

			self::$questionListModel = new \QuestionListModel ( $mysqli, log::get() );
			self::$questionModel = new \QuestionModel ( $mysqli, log::get() );
			self::$answerModel = new \AnswerModel ( $mysqli, log::get() );
			self::$categoryModel = new \CategoryModel ( $mysqli, log::get() );
			self::$userModel = new \UserModel ( $mysqli, log::get() );
			self::$quizModel = new \QuizModel ( $mysqli, log::get() );
			self::$ratingModel = new \RatingModel ( $mysqli, log::get() );
			self::$sessionModel = new \SessionModel ( $mysqli, log::get() );
			self::$quizListModel = new \QuizListModel ( $mysqli, log::get() );
			self::$tagModel = new \TagModel ( $mysqli, log::get() );
			self::$registrationModel = new \RegistrationModel ( $mysqli, log::get() );
			self::$userscoreModel = new \UserScoreModel ( $mysqli, log::get() );
			self::$moderationModel = new \ModerationModel( $mysqli, log::get() );
			self::$reportModel = new \ReportModel( $mysqli, log::get() );
			self::$reportingModel = new \ReportingModel($mysqli, log::get());
			self::$gameModel = new GameModel($mysqli);
		}

		private static function loadIncludes(){
			include('sessionmodel.php');
			include('ratingmodel.php');
			include('tagmodel.php');
			include('registrationmodel.php');
			include('questionlistmodel.php');
			include('questionmodel.php');
			include('categorymodel.php');
			include('usermodel.php');
			include('answermodel.php');
			include('quizlistmodel.php');
			include('quizmodel.php');
			include('userscoremodel.php');
			include('moderationmodel.php');
			include('reportmodel.php');
			include('reportingmodel.php');
		}

	} // class
} // namespace controller\controllers

?>