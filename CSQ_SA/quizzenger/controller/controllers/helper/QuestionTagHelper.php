<?php

namespace quizzenger\controller\controllers\helper {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class QuestionTagHelper{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function process($questions){
			$tags = array();
			foreach ($questions as $question){
				$tagsPerQuestion="";
				foreach ( ModelCollection::tagModel()->getAllTagsByQuestionID ( $question['id'] ) as $tag ) {
					$tagsPerQuestion=$tagsPerQuestion.'<span class="badge alert-info">' . htmlspecialchars($tag ['tag']) . "</span> ";
				}
				array_push($tags,$tagsPerQuestion);
			}
			$this->view->assign ( 'tags', $tags);
		}
	}//class QuestionTagHelper
} // namespace quizzenger\controller\controllers\helper

?>