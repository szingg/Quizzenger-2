<?php
namespace quizzenger\controller\controllers\helper {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class SolutionReportHelper{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function process($question){
			$this->processQuestionReport($question);
			$this->processCommentReport($question);
		}

		private function processQuestionReport($question){
			//Question Report
			if(isset($this->request['questionReport']) && $GLOBALS ['loggedin']){
				$this->view->assign ('message', mes_sent_report);
				if(isset($this->request['questionreportDescription'])){
					ModelCollection::reportModel()->addReport("question", $question['id'], $this->request['questionreportDescription'], $_SESSION['user_id'], $question['category_id']);
				} else {
					ModelCollection::reportModel()->addReport("question", $question['id'], NULL, $_SESSION['user_id'], $question['category_id']);
				}
			}
		}

		private function processCommentReport($question){
			//Comment Report
			if(isset($this->request['ratingReport']) && $GLOBALS ['loggedin']){
				$this->view->assign ('message', mes_sent_report);
				if(isset($this->request['ratingreportDescription'])){
					ModelCollection::reportModel()->addReport("rating", $this->request['ratingReport'], $this->request['ratingreportDescription'], $_SESSION['user_id'], $question['category_id']);
				} else {
					ModelCollection::reportModel()->addReport("rating", $this->request['ratingReport'], NULL, $_SESSION['user_id'], $question['category_id']);
				}
			}
		}

	}//class SolutionReportHelper
} // namespace quizzenger\controller\controllers\helper

?>