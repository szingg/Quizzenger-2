<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	class UserController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			$this->view->setTemplate ( 'user' );

			if (isset ( $this->request ['id'] )) {
				$userID = $this->request ['id'];
			} elseif ($GLOBALS['loggedin']) {
				$userID = $_SESSION ['user_id'];
			} else {
				NavigationUtility::redirect('./index.php?view=login&pageBefore=user');
			}

			$user = ModelCollection::userModel()->getUserByID ( $userID );
			$quizCount = ModelCollection::quizListModel()->getUserQuizzesByUserIDCount ( $userID );
			$questionCount = ModelCollection::questionListModel()->getQuestionsByUserIDCount ( $userID );
			$userscore = ModelCollection::userscoreModel()->getUserScore($userID);
			$categoryscores = ModelCollection::userscoreModel()->getUserScoreAllCategories($userID);
			$leadingTrailingUsers = ModelCollection::userscoreModel()->getLeadingTrailingUsers($userID);
			$moderatedCategories = ModelCollection::moderationModel()->getModeratedCategoryNames($userID);
			$absolvedCount= ModelCollection::userModel()->getQuestionAbsolvedCount($userID);

			if(isset($this->request['userReport']) && $GLOBALS ['loggedin']){
				$this->view->assign ('message', mes_sent_report);
				if(isset($this->request['userreportDescription'])){
					ModelCollection::reportModel()->addReport("user", $this->request ['id'], $this->request['userreportDescription'], $_SESSION['user_id'],NULL);
				} else {
					ModelCollection::reportModel()->addReport("user", $this->request ['id'], NULL, $_SESSION['user_id'], NULL);
				}
			}
			$alreadyReported = ModelCollection::reportModel()->checkIfUserAlreadyDoneReport("user", $userID, $_SESSION ['user_id']);

			$achievementList = ModelCollection::userModel()->getAchievementList($userID);
			$rankList = ModelCollection::userModel()->getRankList($userID);
			$rankListByCategory = ModelCollection::userscoreModel()->getRankinglistAllCategories($userID);

			$this->view->assign('alreadyreported',$alreadyReported);
			$this->view->assign('user', $user);
			$this->view->assign('quizcount', $quizCount);
			$this->view->assign('questioncount', $questionCount);
			$this->view->assign('userscore', $userscore);
			$this->view->assign('categoryscores', $categoryscores);
			$this->view->assign('leadingtrailingusers', $leadingTrailingUsers);
			$this->view->assign('moderatedcategories', $moderatedCategories);
			$this->view->assign('absolvedcount', $absolvedCount);
			$this->view->assign('achievementlist', $achievementList);
			$this->view->assign('ranklist', $rankList);
			$this->view->assign('rankListByCategory', $rankListByCategory);
			return $this->view->loadTemplate();
		}

	} // class UserController
} // namespace quizzenger\controller\controllers

?>
