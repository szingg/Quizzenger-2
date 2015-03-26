<div class="panel panel-default no-margin">
	<a data-toggle="collapse" data-target="#gameAdmin" href="#gameAdmin">
		<div class="panel-heading bg-info text-info">
			<h4 class="panel-title">Game-Administration</h4>
		</div>
	</a>
	<div id="gameAdmin" class="panel-collapse collapse in">
		<div class="panel-body">
			<h4>TODO:</h4>
			<div class="btn-group">
				<div <?= (isset($this->_['has_started']) ?'hidden="true"':'') ?>>
					<input id="startGame" class="btn btn-primary pull-left" type="button" value="Game starten" />
				</div>
				<div <?= (isset($this->_['has_started']) ?'':'hidden="true"') ?>>
					<input id="stopGame" class="btn btn-primary pull-left" type="button" value="Game beenden" />
				</div>
			</div>
			<h4> view mit status teilnehmer</h4>
		</div>
	</div>
</div>