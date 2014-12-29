<?php
	$viewInner->setTemplate ( 'categorylist' );
	$roots = $categoryModel->getChildren ( 0 ); // get all without parent = root "nodes"
	$roots = $categoryModel->fillCategoryListWithQuestionCount ( $roots );
	$viewInner->assign ( 'roots', $roots );
?>