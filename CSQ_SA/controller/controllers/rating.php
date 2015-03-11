<?php
	$ratings = $ratingModel->getAllRatingsByQuestionID ( $this->request ['id'] );
	$ratings = $ratingModel->enrichRatingsWithAuthorName ( $ratings, $userModel,$moderationModel,$questionModel,$reportModel );
	$meanRating = $question['rating'];
	$userAlreadyRated=$ratingModel->userHasAlreadyRated($question['id'],$_SESSION['user_id']);


	$comments = $ratingModel->getAllCommentsByQuestionID ( $this->request ['id'] );
	$comments = $ratingModel->enrichRatingsWithAuthorName ( $comments, $userModel ,$moderationModel,$questionModel,$reportModel);
	include("helper/solution_deletecomment.php");
	$viewInner->assign ( 'useralreadyrated',$userAlreadyRated);
	$viewInner->assign ( 'ratings', $ratings );
	$viewInner->assign ( 'comments', $comments );
	$viewInner->assign ( 'meanRating', $meanRating );
?>