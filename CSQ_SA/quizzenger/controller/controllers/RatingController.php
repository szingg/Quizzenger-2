<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\controller\controllers\helper\SolutionDeleteCommentHelper as SolutionDeleteCommentHelper;
	use \quizzenger\view\View as View;

	class RatingController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			$ratingView = new View();
			$ratingView->setTemplate ( 'solutionRating' );

			$question = ModelCollection::questionModel()->getQuestion ( $this->request ['id'] );
			$userIsModHere = ModelCollection::userModel()-> userIsModeratorOfCategory($_SESSION['user_id'], $question ['category_id']);
			$ratings = ModelCollection::ratingModel()->getAllRatingsByQuestionID( $this->request ['id'] );
			$ratings = ModelCollection::ratingModel()->enrichRatingsWithAuthorName( $ratings);
			$meanRating = $question['rating'];
			$userAlreadyRated = ModelCollection::ratingModel()->userHasAlreadyRated($question['id'],$_SESSION['user_id']);

			$comments = ModelCollection::ratingModel()->getAllCommentsByQuestionID( $this->request ['id'] );
			$comments = ModelCollection::ratingModel()->enrichRatingsWithAuthorName($comments);

			$helper = new SolutionDeleteCommentHelper();
			$helper->process($question);

			$ratingView->assign ( 'useralreadyrated',$userAlreadyRated);
			$ratingView->assign ( 'ratings', $ratings );
			$ratingView->assign ( 'comments', $comments );
			$ratingView->assign ( 'meanRating', $meanRating );
			$ratingView->assign ( 'questionID', $this->request ['id'] );
			$ratingView->assign ( 'userismodhere', $userIsModHere );

			$this->view->assign ( 'ratingView', $ratingView->loadTemplate() );

		}

	} // class RatingController
} // namespace quizzenger\controller\controllers

?>