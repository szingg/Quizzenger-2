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
	$userList = $reportingModel->getUserList();
	$questionList = $reportingModel->getQuestionList();
	$authorList = $reportingModel->getAuthorList();

	$viewInner->assign('user', $user);
	$viewInner->assign('userlist', $userList);
	$viewInner->assign('questionlist', $questionList);
	$viewInner->assign('authorlist', $authorList);
?>
