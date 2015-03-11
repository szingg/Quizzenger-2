<?php
	if (! $GLOBALS ['loggedin']) {
		header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
		die ();
	}
	$viewInner->setTemplate ( 'generatequiz' );
	if (isset ( $this->request ['type'] )) {
		$type = $this->request ['type'];
	} else {
		$type = SINGLECHOICE_TYPE;
	}
	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );
	$totalCount = $categoryModel->getTotalQuestionCount();
	$viewInner->assign ( 'totalCount', $totalCount);
	$viewInner->assign ( 'roots', $roots );
	$viewInner->assign ( 'mode', 'generator' );
	$viewInner->assign ( 'type', $type );

?>