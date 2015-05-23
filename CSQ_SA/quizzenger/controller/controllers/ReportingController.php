<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	use \quizzenger\model\ModelCollection as ModelCollection;

	class ReportingController{
		private $view;
		private $request;

		public function __construct($view) {
			$this->view = $view;
			$this->request = array_merge ( $_GET, $_POST );
		}

		public function render(){
			$this->view->setTemplate('reporting');

			if(isset($this->request['id'])) {
				$userId = $this->request['id'];
			} elseif($GLOBALS['loggedin']) {
				$userId = $_SESSION['user_id'];
			} else {
				NavigationUtility::redirect('./index.php?view=login&pageBefore=' . $this->template);
			}

			$user = ModelCollection::userModel()->getUserByID($userId);

			if(!$user['superuser'] && ! ModelCollection::reportingModel()->isAnyModerator($userId)) {
				NavigationUtility::redirect('./index.php?view=login&pageBefore=' . $this->template);
			}

			$categoryId = (isset($_GET['category']) ? ((int)$_GET['category']) : 0);
			$questionList = ModelCollection::reportingModel()->getQuestionList($_SESSION['user_id']);
			$authorList = ModelCollection::reportingModel()->getAuthorList();
			$categoryList = ModelCollection::reportingModel()->getCategoryList($userId, $user['superuser']);

			// Check whether the user is allowed to view that category.
			$allowed = false;
			while($current = $categoryList->fetch_object()) {
				if($current->id == $categoryId) {
					$allowed = true;
					break;
				}
			}
			$categoryId = ($allowed ? $categoryId : '0');
			$categoryList->data_seek(0);

			$userList = ModelCollection::reportingModel()->getUserList($categoryId);

			$systemStatus = new \stdClass();
			$systemStatus->attachment_usage = ModelCollection::reportingModel()->getAttachmentMemoryUsage();
			$systemStatus->database_usage = ModelCollection::reportingModel()->getDatabaseMemoryUsage();
			$systemStatus->login_attempts = ModelCollection::reportingModel()->getRecentLoginAttempts();
			$systemStatus->log_files = ModelCollection::reportingModel()->getLogFiles();

			$this->view->assign('user', $user);
			$this->view->assign('categoryid', $categoryId);
			$this->view->assign('userlist', $userList);
			$this->view->assign('questionlist', $questionList);
			$this->view->assign('authorlist', $authorList);
			$this->view->assign('categorylist', $categoryList);
			$this->view->assign('systemstatus', $systemStatus);
			return $this->view->loadTemplate();
		}

	} // class ReportingController
} // namespace quizzenger\controller\controllers

?>
