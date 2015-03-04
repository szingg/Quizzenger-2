<?php
	$tags = array();
	foreach ($questions as $question){
		$tagsPerQuestion="";
		foreach ( $tagModel->getAllTagsByQuestionID ( $question['id'] ) as $tag ) {
			$tagsPerQuestion=$tagsPerQuestion.'<span class="badge alert-info">' . htmlspecialchars($tag ['tag']) . "</span> ";
		}
		array_push($tags,$tagsPerQuestion);
	}
	$viewInner->assign ( 'tags', $tags);
?>