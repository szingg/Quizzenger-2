<?php
	if (! $GLOBALS ['loggedin']) {
		header ( 'Location: ./index.php?view=login&pageBefore=' . $this->template );
		die ();
	}

	include("myquestions.php");
	include("myquizzes.php");


	$viewInner->setTemplate ( 'mycontent' );
?>