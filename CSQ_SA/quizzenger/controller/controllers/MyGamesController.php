<?php
namespace quizzenger\controller\controllers {
	use \quizzenger\model\ModelCollection as ModelCollection;

	class MyGamesController{
		private $view;

		public function __construct($view) {
			$this->view = $view;
		}

		public function render(){
			$this->view->setTemplate ( 'gamelist' );

			$hostedGames = ModelCollection::gameModel()->getHostedGamesByUser($_SESSION['user_id']);
			$this->view->assign( 'hostedGames', $hostedGames);

			$participatedGames = ModelCollection::gameModel()->getParticipatedGamesByUser($_SESSION['user_id']);
			$this->view->assign( 'participatedGames', $participatedGames);

			return $this->view->loadTemplate();
		}

	} // class MyGamesController
} // namespace quizzenger\controller\controllers

?>