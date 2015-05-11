<?php

namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class DefaultController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			PermissionUtility::checkLogin();

			$this->view->setTemplate ( 'default' );

			$newestQuestion= ModelCollection::questionModel()->getNewestQuestion();
			$this->view->assign('newestquestion', $newestQuestion);

			$moderatedCategories = ModelCollection::moderationModel()->getModeratedCategories($_SESSION['user_id']);

			$moderatedQuestions = array();
			$moderatedRatings = array();

			foreach($moderatedCategories as $category){
				$questionReports = ModelCollection::reportModel()->getQuestionReportsByCategory($category['category_id']);
				foreach($questionReports as $qReport){
					$moderatedQuestions[] = $qReport;
				}
				$ratingReports = ModelCollection::reportModel()->getRatingReportsByCategory($category['category_id']);
				foreach($ratingReports as $rReport){
					$moderatedRatings[] = $rReport;
				}
			}

			$questionhistoryByUser= ModelCollection::questionModel()->getNewestHistoryOfAllUserQuestionsByUserID($_SESSION['user_id']);

			$this->view->assign('questionhistoryByUser', $questionhistoryByUser);
			$this->view->assign('username', ModelCollection::userModel()->getUsernameByID($_SESSION['user_id']));
			$this->view->assign('reportedQuestions', ModelCollection::reportModel()->getQuestionReportsByUser($_SESSION['user_id']));

			if($_SESSION['superuser']){
				$this->view->assign('reportedUsers', ModelCollection::reportModel()->getReportedUsers());
				$this->view->assign('subCats', ModelCollection::categoryModel()->getAllTrueChildren());
				$this->view->assign('upperCats', ModelCollection::categoryModel()->getChildren ( 0 ));
				$this->view->assign('middleCats', ModelCollection::categoryModel()->getAllMiddle ());

				if(isset($_POST["superusertools_form_new_cat_lower"])){
					ModelCollection::categoryModel()->createCategory($_POST["superusertools_form_new_cat_lower"],$_POST["superusertools_form_new_cat_lower_parent"]);
				}

				if(isset($_POST["superusertools_form_new_cat_middle"])){
					ModelCollection::categoryModel()->createCategory($_POST["superusertools_form_new_cat_middle"],$_POST["superusertools_form_new_cat_middle_parent"]);
				}

				if(isset($_POST["superusertools_form_new_cat_upper"])){
					ModelCollection::categoryModel()->createCategory($_POST["superusertools_form_new_cat_upper"],0);
				}

			}
			$this->view->assign('reportedRatings', ModelCollection::reportModel()->getRatingReportsByUser($_SESSION['user_id']));
			$this->view->assign('moderatedQuestions', $moderatedQuestions);
			$this->view->assign('moderatedRatings', $moderatedRatings);

			return $this->view->loadTemplate();
		}

	} // class DefaultController
} // namespace quizzenger\controller\controllers

?>