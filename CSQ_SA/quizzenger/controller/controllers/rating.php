<?php
	$userIsModHere =$userModel-> userIsModeratorOfCategory($_SESSION['user_id'], $question ['category_id']);
	$ratings = $ratingModel->getAllRatingsByQuestionID ( $this->request ['id'] );
	$ratings = $ratingModel->enrichRatingsWithAuthorName ( $ratings, $userModel,$moderationModel,$questionModel,$reportModel );
	$meanRating = $question['rating'];
	$userAlreadyRated=$ratingModel->userHasAlreadyRated($question['id'],$_SESSION['user_id']);


	$comments = $ratingModel->getAllCommentsByQuestionID ( $this->request ['id'] );
	$comments = $ratingModel->enrichRatingsWithAuthorName ( $comments, $userModel ,$moderationModel,$questionModel,$reportModel);
	include("helper/solution_deletecomment.php");

	$ratingView = new \View();
	$ratingView->setTemplate ( 'solutionRating' );

	$ratingView->assign ( 'useralreadyrated',$userAlreadyRated);
	$ratingView->assign ( 'ratings', $ratings );
	$ratingView->assign ( 'comments', $comments );
	$ratingView->assign ( 'meanRating', $meanRating );
	$ratingView->assign ( 'questionID', $this->request ['id'] );
	$ratingView->assign ( 'userismodhere', $userIsModHere );

	$viewInner->assign ( 'ratingView', $ratingView->loadTemplate() );
?>