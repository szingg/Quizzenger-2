<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;

	$viewInner->setTemplate('reporting');

	if(isset($this->request['id'])) {
		$userId = $this->request['id'];
	} elseif($GLOBALS['loggedin']) {
		$userId = $_SESSION['user_id'];
	} else {
		NavigationUtility::redirect('./index.php?view=login&pageBefore=' . $this->template);
	}

	$user = $userModel->getUserByID($userId);

	if(!$user['superuser'] && !$reportingModel->isAnyModerator($userId)) {
		NavigationUtility::redirect('./index.php?view=login&pageBefore=' . $this->template);
	}

	$categoryId = (isset($_GET['category']) ? ((int)$_GET['category']) : 0);
	$questionList = $reportingModel->getQuestionList();
	$authorList = $reportingModel->getAuthorList();
	$categoryList = $reportingModel->getCategoryList($userId, $user['superuser']);

	// Check whether the user is allowed to view that category.
	$allowed = false;
	while($current = $categoryList->fetch_object()) {
		if($current->id == $categoryId) {
			$allowed = true;
			break;
		}
	}
	$categoryId = ($allowed ? $categoryId : '0');
	$categoryList->data_seek(0);

	$userList = $reportingModel->getUserList($categoryId);

	$systemStatus = new \stdClass();
	$systemStatus->attachment_usage = $reportingModel->getAttachmentMemoryUsage();
	$systemStatus->database_usage = $reportingModel->getDatabaseMemoryUsage();
	$systemStatus->login_attempts = $reportingModel->getRecentLoginAttempts();
	$systemStatus->log_files = $reportingModel->getLogFiles();

	$viewInner->assign('user', $user);
	$viewInner->assign('categoryid', $categoryId);
	$viewInner->assign('userlist', $userList);
	$viewInner->assign('questionlist', $questionList);
	$viewInner->assign('authorlist', $authorList);
	$viewInner->assign('categorylist', $categoryList);
	$viewInner->assign('systemstatus', $systemStatus);
?>
