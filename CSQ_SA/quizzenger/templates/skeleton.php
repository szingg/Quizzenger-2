<?php
	use \quizzenger\messages\MessageQueue as MessageQueue;
	use \quizzenger\messages\TextTranslator as TextTranslator;

	function checkActiveTab($openedView){
		$pageBefore = filter_input(INPUT_GET, 'pageBefore', $filter = FILTER_SANITIZE_SPECIAL_CHARS);
		if (!is_null($pageBefore) && $openedView===$pageBefore) {
			return "active";
		}

		if($openedView == $_SESSION['current_view']){
			return "active";
		}
		else{
			return "";
		}
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <meta name="author" content="Oussama Zgheb &amp; Tobias Zahner">
	    <meta name="description" content="Quizzenger ist eine webbasierte Wissensdatenbank mit Lernfragen zu verschiedensten Themen.
		Werden Sie kostenlos Teil unserer Community und erfassen Sie Ihre eigenen Fragen und erlangen Sie dadurch Auszeichnungen.">
		<meta name="keywords" content="Quizzenger, Fragen, Lernen, Community, Prüfungen, Quiz, Quizzes">
		<meta name="robots" content="index, follow">

	    <link rel="shortcut icon" href="content/img/favicon.ico" />
	    <title>Quizzenger</title>

		<link href="content/css/bootstrap.min.css" rel="stylesheet">
	    <link href="content/css/bootstrap-theme.min.css" rel="stylesheet">
	    <link href="content/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet">
	    <link href="content/datatables/extensions/Responsive/css/dataTables.responsive.css" rel="stylesheet">
	    <link href="content/css/custom.css" rel="stylesheet">
	    <script type="text/javascript" src="content/js/ajax.js"></script>
	    <script src="content/js/jquery-1.11.1.min.js"></script>
	    <script type="text/javascript" src="content/js/markdown.min.js"></script>
	    <script type="text/javascript" src="content/js/bootstrap.min.js"></script>
	    <script type="text/javascript" src="content/js/doT.min.js"></script>
	    <script type="text/javascript" src="content/js/custom.js"></script>
	   	<script type="text/javascript" src="content/js/jqueryfunctions.js"></script>
	   	<script type="text/javascript" src="content/js/opquestion.js"></script>
	   	<script type="text/javascript" src="content/js/gamification.js"></script>
	   	<script type="text/javascript" src="content/js/quizzenger.js"></script>
	    <script type="text/javascript" charset="utf-8" src="content/datatables/media/js/jquery.dataTables.min.js"></script>
	    <script type="text/javascript" charset="utf-8" src="content/datatables/extensions/Responsive/js/dataTables.responsive.min.js"></script>
	</head>
	<body>
		<div role="navigation" class="navbar navbar-default navbar-fixed-top">
			<div class="hidden-lg hidden-sm hidden-md">
				<a href="index.php"><img src="<?= htmlspecialchars(APP_PATH) ?>/content/img/header_50.png" alt="Quizzenger Logo"  style="position:absolute;" /></a>
			</div>
			<div class="container">
				<div class="navbar-header">
					<button data-target=".navbar-collapse" data-toggle="collapse"
						class="navbar-toggle collapsed" type="button">
						<span class="sr-only">Toggle navigation</span> <span
							class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>

				</div>
				<div class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="<?=  checkActiveTab("default");?>"><a href="index.php">
							<span class="hidden-lg hidden-sm hidden-md">
								<span class="glyphicon glyphicon-home"></span> Home
							</span>
							<span class="hidden-xs">
								<img src="<?= htmlspecialchars(APP_PATH) ?>/content/img/header_50.png" alt="Quizzenger Logo" style="max-width: 100px; margin-top: -15px;margin-bottom: -15px; " /></a>
							</span>
						</li>
						<li class="<?=  checkActiveTab("questionpool");?>">
							<a href="?view=questionpool">
							<span class="glyphicon glyphicon-list"></span> Fragepool</a>
						</li>
						<li class="<?=  checkActiveTab("learn");?>">
							<a href="?view=learn"><span class="glyphicon glyphicon-random"></span> Quiz/Game</a>
						</li>
						<li class="hidden-xs <?=  checkActiveTab("newquestion");?>">
							<a href="?view=newquestion"><span class="glyphicon glyphicon-plus"></span> Frage</a>
						</li>
						<li class="<?=  checkActiveTab("mycontent");?>">
							<a href="?view=mycontent"><span class="glyphicon glyphicon-briefcase"></span> Meine Inhalte</a>
						</li>
						<li class="<?=  checkActiveTab("user");?>">
							<a href="?view=user"><span class="glyphicon glyphicon-user"></span> Mein Profil</a>
						</li>

						<?php if($this->_['superuser'] || $this->_['anymoderator']): ?>
						<li class="<?= checkActiveTab('reporting'); ?> hidden-xs hidden-sm">
							<a href="?view=reporting"><span class="glyphicon glyphicon-stats"></span> Reporting</a>
						</li>
						<?php endif; ?>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="hidden-sm" style="margin-top:8px;">
							<?php include "searchquestion.php"; ?>
						</li>
						<li <?php
							$pageBefore = filter_input(INPUT_GET, 'pageBefore', $filter = FILTER_SANITIZE_SPECIAL_CHARS);
							if (is_null($pageBefore) && checkActiveTab("login")){echo checkActiveTab("login");} ?>><?php
							if($GLOBALS['loggedin']){
								echo "<a href=\"index.php?view=logout\"><span class=\"glyphicon glyphicon-log-out\"></span> Logout ".htmlspecialchars($this->_['username']);
							}else{
								echo "<a href=\"index.php?view=login\"><span class=\"glyphicon glyphicon-log-in\"></span> Login";
							}
							?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="container">
			<?php
				$messages = MessageQueue::popAll($this->_['userid']);
				$messages = TextTranslator::translate($messages);
				foreach($messages as $current) {
					$alertType = (strpos($current->type,'err_') !== false?'danger':'info');
					echo '<div class="alert alertmessageautoremove alert-dismissible alert-'.$alertType.'" role="alert">';
					echo '<a href="#" class="close" data-dismiss="alert">&times;</a>';
					/*echo 'temporary: ' . htmlspecialchars($current->type) . '(' . (int)$current->static . ') ' . htmlspecialchars(json_encode($current->arguments)); */
					//echo TextTranslator::translate(htmlspecialchars($current->type), json_decode(htmlspecialchars(json_encode($current->arguments))));
					//echo htmlspecialchars($current->text);
					echo $current->text;
					echo '</div>';
				}

				// TODO: Remove the old message system.
				$message = filter_input(INPUT_GET, 'info', $filter = FILTER_SANITIZE_SPECIAL_CHARS);
				if(!is_null($message) && defined($message)){
					$message = constant($message);
					echo('<div class="alert alert-info" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a>'.$message.'</div>');
				}
			?>
			<noscript>
				<div class="alert alert-warning" role="alert">
					Javascript wird benötigt um die Website zu verwenden
				</div>
			</noscript>
			<?=  $this->_['csq_content']; ?>
			<hr />
			<footer>
				<div class="panel panel-default" id="panelQuestionHistoryUser">
					<div class="panel-heading">
						<b>Quizzenger - Die Wissensplattform</b>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-4">
								<ul>
									<li><a href="<?php echo APP_PATH . '?view=about'; ?>">Über Quizzenger</a></li>
									<li><a href="https://github.com/ozzi-/Quizzenger/">Github Repository</a></li>
								</ul>
							</div>
							<div class="col-md-4">
								<ul>
									<li><a href="<?php echo APP_PATH . '?view=about'; ?>">Impressum</a></li>
									<li><a href="<?php echo APP_PATH . '?view=about'; ?>">Rechtliche Hinweise</a></li>
								</ul>
							</div>
							<div class="col-md-4">
								<?php
									if(SHOW_PROCESSING_TIME) {
										$time_end = microtime(true);
										$execution_time = round(($time_end - $GLOBALS['time_start']), 4);
										echo '<div class="pull-right">Erstelldauer: ' . $execution_time . 's</div';
									}
								?>
							</div>
						</div>
					</div>
				</div>
			</footer>
		</div>
	</body>
</html>
