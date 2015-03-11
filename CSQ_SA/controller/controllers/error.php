<?php

	$viewInner->setTemplate ( 'error' );
	if( isset($this->request ['err'])&&is_null($this->request ['err'])){
		$error=err_unkown;
	}else{
		$error = filter_input(INPUT_GET, 'err',	 $filter = FILTER_SANITIZE_SPECIAL_CHARS);
	}
	$viewInner->assign ( 'err', $error);

?>