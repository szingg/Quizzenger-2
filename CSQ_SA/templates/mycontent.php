<div class="panel panel-default">
	<div class="panel-heading">
		<b>Meine Inhalte</b>
	</div>
	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#myquestions" aria-controls="myuqestions" role="tab" data-toggle="tab">
					<b>Meine Fragen</b>
				</a>
			</li>
			<li role="presentation">
				<a href="#myquizzes" id="myQuizzesEvent" aria-controls="myquizzes" role="tab" data-toggle="tab">
					<b>Meine Quizzes</b>
				</a>
			</li>
			<li role="presentation">
				<a href="#mygames" id="myGamesEvent" aria-controls="mygames" role="tab" data-toggle="tab">
					<b>Meine Games</b>
				</a>
			</li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="myquestions">
				<?php include("questionlist.php"); ?>
			</div>
			<div role="tabpanel" class="tab-pane" id="myquizzes">
				<?php include("quizlist.php"); ?>
			</div>
			<div role="tabpanel" class="tab-pane" id="mygames">
				<?php echo $this->_['gamelist']; ?>
			</div>
		</div>
	</div>
</div>