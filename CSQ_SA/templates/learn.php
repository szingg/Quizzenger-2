<div class="panel panel-default">
	<div class="panel-heading">
		<b>Lernen</b>
	</div>
	<div role="tabpanel">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active">
				<a href="#generateQuiz" aria-controls="generateQuiz" role="tab" data-toggle="tab">
					<b>Quiz generieren</b>
				</a>
			</li>
			<li role="presentation" class="">
				<a href="#gameLobby" id="gameLobbyEvent" aria-controls="gameLobby" role="tab" data-toggle="tab">
					<b>Game-Lobby</b>
				</a>
			</li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="generateQuiz">
				<div class="panel-body">
					<?=  $this->_['quiz_tab']; ?>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="gameLobby">
				<div class="panel-body">
					<?=  $this->_['game_tab']; ?>
				</div>
			</div>
		</div>
	</div>
</div>