<div role="tabpanel">
	<!-- Nav tabs -->
	<ul class="nav nav-tabs" role="tablist">
		<li role="presentation" class="active">
			<a href="#myquestions" aria-controls="myuqestions" role="tab" data-toggle="tab">
				<b>Meine Fragen</b>
			</a>
		</li>
		<li role="presentation">
			<a href="#myquizzes" aria-controls="myquizzes" role="tab" data-toggle="tab">
				<b>Meine Quizzes</b>
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
	</div>
</div>

