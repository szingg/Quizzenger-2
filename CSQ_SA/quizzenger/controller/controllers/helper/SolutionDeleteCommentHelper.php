<?php
namespace quizzenger\controller\controllers\helper {
	use \quizzenger\model\ModelCollection as ModelCollection;
	use \quizzenger\logging\Log as Log;

	class SolutionDeleteCommentHelper{
		private $request;

		public function __construct() {
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function process($question){
			if(isset($this->request['ratingRemove']) && $GLOBALS ['loggedin'] ){
				$userIsModHere = ModelCollection::userModel()->userIsModeratorOfCategory($_SESSION['user_id'], $question ['category_id']);
				if($userIsModHere){
					$removalExplanation = (isset($this->request['removalExplanation'])?$this->request['removalExplanation']:'');
					ModelCollection::ratingModel()->removeComment($this->request['ratingRemove'],$_SESSION ['username'],$removalExplanation);
				}else{
					Log::warning('User tried to remove comment but is no mod!');
				}
			}
		}
	}//class SolutionDeleteCommentHelper
} // namespace quizzenger\controller\controllers\helper

?>