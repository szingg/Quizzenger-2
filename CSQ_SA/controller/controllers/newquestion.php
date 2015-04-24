<?php
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;

	PermissionUtility::checkLogin();

	$viewInner->setTemplate ( 'opquestion' );
	if (isset ( $this->request ['type'] )) {
		$type = $this->request ['type'];
	} else {
		$type = SINGLECHOICE_TYPE;
	}

	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );

	$viewInner->assign ( 'roots', $roots );
	$viewInner->assign ( 'operation', "new" );
	$viewInner->assign ( 'mode', 'add_question' );
	$viewInner->assign ( 'type', $type );
?>