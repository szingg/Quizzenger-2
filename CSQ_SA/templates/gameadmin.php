<div class="panel panel-default no-margin">
	<a data-toggle="collapse" data-target="#gameAdmin" href="#gameAdmin">
		<div class="panel-heading bg-info text-info">
			<h4 class="panel-title">Game-Report</h4>
		</div>
	</a>
	<div id="gameAdmin" class="panel-collapse collapse in">
		<div class="panel-body">
			<br>
			<div class="row game-report-row">
				<strong class="col-md-1">Rang: 1</strong><span class="col-md-1">Hollywood</span>
				<span class="col-md-6" >
					<div class="progress game-report-progress" >
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"
					  aria-valuemin="0" aria-valuemax="100" style="width:40%">
					    40% Complete (success)
					  </div>
					  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="20"
					  aria-valuemin="0" aria-valuemax="100" style="width:20%">
					    20% Complete (danger)
					  </div>
					</div>
				</span>
				<span class="col-md-2">Zeit pro Frage: 15s</span><span class="col-md-2">Zeit Total: 1min, 60sek</span>
			</div>
			<div class="row game-report-row game-report-active">
				<strong class="col-md-1">Rang: 1</strong><span class="col-md-1">Hollywood</span>
				<span class="col-md-6" >
					<div class="progress game-report-progress" >
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"
					  aria-valuemin="0" aria-valuemax="100" style="width:40%">
					    40% Complete (success)
					  </div>
					  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="20"
					  aria-valuemin="0" aria-valuemax="100" style="width:20%">
					    20% Complete (danger)
					  </div>
					</div>
				</span>
				<span class="col-md-2">Zeit pro Frage: 15s</span><span class="col-md-2">Zeit Total: 1min, 60sek</span>
			</div>
			<div class="row game-report-row">
				<strong class="col-md-1">Rang: 1</strong><span class="col-md-1">Hollywood</span>
				<span class="col-md-6" >
					<div class="progress game-report-progress" >
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40"
					  aria-valuemin="0" aria-valuemax="100" style="width:40%">
					    40% Complete (success)
					  </div>
					  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="20"
					  aria-valuemin="0" aria-valuemax="100" style="width:20%">
					    20% Complete (danger)
					  </div>
					</div>
				</span>
				<span class="col-md-2">Zeit pro Frage: 15s</span><span class="col-md-2">Zeit Total: 1min, 60sek</span>
			</div>
		</div>
			<h4>TODO:</h4>
			<div class="btn-group">
				<?php $hasStarted = isset($this->_['gameinfo']['has_started']) ?>
				<div <?php echo ($hasStarted ? 'hidden="true"' : '' ); ?>>
					<input id="startGame" class="btn btn-primary pull-left" type="button" value="Game starten" />
				</div>
				<div <?php echo (! $hasStarted ? 'hidden="true"' : '' ); ?>>
					<input id="stopGame" class="btn btn-primary pull-left" type="button" value="Game beenden" />
				</div>
			</div>
			<h4> view mit status teilnehmer</h4>
	</div>
</div>