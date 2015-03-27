<?php
	$viewInner->setTemplate('reporting');

	if(isset($this->request['id'])) {
		$userId = $this->request['id'];
	} elseif($GLOBALS['loggedin']) {
		$userId = $_SESSION['user_id'];
	} else {
		header('Location: ./index.php?view=login&pageBefore=' . $this->template);
		die();
	}

	$user = $userModel->getUserByID($userId);
	$categoryId = (isset($_GET['category']) ? ((int)$_GET['category']) : 0);
	$userList = $reportingModel->getUserList($categoryId);
	$questionList = $reportingModel->getQuestionList();
	$authorList = $reportingModel->getAuthorList();
	$categoryList = $reportingModel->getCategoryList();

	$systemStatus = new \stdClass();
	$systemStatus->attachment_usage = $reportingModel->getAttachmentMemoryUsage();
	$systemStatus->database_usage = $reportingModel->getDatabaseMemoryUsage();

	$viewInner->assign('user', $user);
	$viewInner->assign('categoryid', $categoryId);
	$viewInner->assign('userlist', $userList);
	$viewInner->assign('questionlist', $questionList);
	$viewInner->assign('authorlist', $authorList);
	$viewInner->assign('categorylist', $categoryList);
	$viewInner->assign('systemstatus', $systemStatus);
?>
