<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\utilities\PermissionUtility as PermissionUtility;
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class EditquestionController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			PermissionUtility::checkLogin();

			// only author and mods can edit
			if (! ModelCollection::questionModel()->userIDhasPermissionOnQuestionID ( $this->request ['id'], $_SESSION ['user_id'] )) {
				MessageQueue::pushPersistent($_SESSION['user_id'], 'err_not_authorized_questionedit');
				NavigationUtility::redirectToErrorPage();
			}

			if (isset ( $this->request ['type'] )) {
				$type = $this->request ['type'];
			} else {
				$type = SINGLECHOICE_TYPE;
			}

			$this->view->setTemplate ( 'opquestion' );

			$roots = ModelCollection::categoryModel()->getChildren ( 0 ); // get all without parent = root "nodes"
			$roots = ModelCollection::categoryModel()->fillCategoryListWithQuestionCount ( $roots );

			$question = ModelCollection::questionModel()->getQuestion ( $this->request ['id'] );
			$tags = ModelCollection::tagModel()->getAllTagsByQuestionID ( $this->request ['id'] );
			$answers = ModelCollection::answerModel()->getAnswersByQuestionID ( $this->request ['id'] );

			$this->view->assign ( 'question', $question );
			$this->view->assign ( 'answers', $answers );
			$this->view->assign ( 'tags', $tags );
			$this->view->assign ( 'roots', $roots );
			$this->view->assign ( 'operation', "edit" );
			$this->view->assign ( 'chooseOnly', true );
			$this->view->assign ( 'type', $type );
			return $this->view->loadTemplate();
		}

	} // class EditquestionController
} // namespace quizzenger\controller\controllers

?>