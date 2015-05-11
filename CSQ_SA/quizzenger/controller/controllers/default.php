<?php

	$newestQuestion=$questionModel->getNewestQuestion();
	$viewInner->assign('newestquestion', $newestQuestion);

	if($GLOBALS['loggedin']){

		$moderatedCategories = $moderationModel->getModeratedCategories($_SESSION['user_id']);

		$moderatedQuestions = array();
		$moderatedRatings = array();

		foreach($moderatedCategories as $category){
			$questionReports = $reportModel->getQuestionReportsByCategory($category['category_id']);
			foreach($questionReports as $qReport){
				$moderatedQuestions[] = $qReport;
			}
			$ratingReports = $reportModel->getRatingReportsByCategory($category['category_id']);
			foreach($ratingReports as $rReport){
				$moderatedRatings[] = $rReport;
			}
		}

		$questionhistoryByUser=$questionModel->getNewestHistoryOfAllUserQuestionsByUserID($_SESSION['user_id']);

		$viewInner->assign('questionhistoryByUser', $questionhistoryByUser);
		$viewInner->assign('username', $userModel->getUsernameByID($_SESSION['user_id']));
		$viewInner->assign('reportedQuestions', $reportModel->getQuestionReportsByUser($_SESSION['user_id']));

		if($_SESSION['superuser']){
			$viewInner->assign('reportedUsers', $reportModel->getReportedUsers());
			$viewInner->assign('subCats', $categoryModel->getAllTrueChildren());
			$viewInner->assign('upperCats', $categoryModel->getChildren ( 0 ));
			$viewInner->assign('middleCats', $categoryModel->getAllMiddle ());

			if(isset($_POST["superusertools_form_new_cat_lower"])){
				$categoryModel->createCategory($_POST["superusertools_form_new_cat_lower"],$_POST["superusertools_form_new_cat_lower_parent"]);
			}

			if(isset($_POST["superusertools_form_new_cat_middle"])){
				$categoryModel->createCategory($_POST["superusertools_form_new_cat_middle"],$_POST["superusertools_form_new_cat_middle_parent"]);
			}

			if(isset($_POST["superusertools_form_new_cat_upper"])){
				$categoryModel->createCategory($_POST["superusertools_form_new_cat_upper"],0);
			}

		}
		$viewInner->assign('reportedRatings', $reportModel->getRatingReportsByUser($_SESSION['user_id']));
		$viewInner->assign('moderatedQuestions', $moderatedQuestions);
		$viewInner->assign('moderatedRatings', $moderatedRatings);

	}
?>