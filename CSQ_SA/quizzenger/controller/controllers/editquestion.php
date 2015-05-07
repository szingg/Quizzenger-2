<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	PermissionUtility::checkLogin();

	// only author and mods can edit
	if (! $questionModel->userIDhasPermissionOnQuestionID ( $this->request ['id'], $_SESSION ['user_id'] )) {
		MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized_questionedit');
		NavigationUtility::redirectToErrorPage();
	}

	if (isset ( $this->request ['type'] )) {
		$type = $this->request ['type'];
	} else {
		$type = SINGLECHOICE_TYPE;
	}

	$viewInner->setTemplate ( 'opquestion' );

	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );

	$question = $questionModel->getQuestion ( $this->request ['id'] );
	$tags = $tagModel->getAllTagsByQuestionID ( $this->request ['id'] );
	$answers = $answerModel->getAnswersByQuestionID ( $this->request ['id'] );

	$viewInner->assign ( 'question', $question );
	$viewInner->assign ( 'answers', $answers );
	$viewInner->assign ( 'tags', $tags );
	$viewInner->assign ( 'roots', $roots );
	$viewInner->assign ( 'operation', "edit" );
	$viewInner->assign ( 'chooseOnly', true );
	$viewInner->assign ( 'type', $type );
?>