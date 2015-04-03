<script id="dot-gameReportRow" type="text/x-dot-template">
	<div class="row game-report-row">
		<strong class="col-md-1">Rang: {{=it.rank}}</strong><span class="col-md-1">Hollywood</span>
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
			  <div class="progress-bar-togo" style="width:40%">text</div>
			</div>
		</span>
		<span class="col-md-2">Zeit pro Frage: 15s</span><span class="col-md-2">Zeit Total: 1min, 60sek</span>
	</div>
</script>
<div class="panel panel-default no-margin">
	<a data-toggle="collapse" data-target="#gameAdmin" href="#gameAdmin">
		<div class="panel-heading bg-info text-info">
			<h4 class="panel-title">Game-Report</h4>
		</div>
	</a>
	<div id="gameAdmin" class="panel-collapse collapse in">
		<div class="panel-body">
			<br>
			<?php foreach($this->_['gamereport'] as $report){ ?>
			<div class="row game-report-row <?php echo ($report['user_id']==$this->_['gameinfo']['owner_id'] ? 'game-report-active' : ''); ?>">
				<strong class="col-md-1">Rang: <?php echo $report['rank']; ?></strong><span class="col-md-1"><?php echo $report['username']; ?></span>
				<span class="col-md-6" >
					<div class="progress game-report-progress" >
					<?php $correct = 100/$report['totalQuestion']*$report['questionAnsweredCorrect']; ?>
					  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $correct; ?>"
					  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $correct; ?>%">
					    <?php echo $report['questionAnsweredCorrect']; ?>
					  </div>
					  <?php $wrongCount = $report['questionAnswered']-$report['questionAnsweredCorrect'];
					  		$wrong = 100/$report['totalQuestion']*($wrongCount);
					  ?>
					  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $wrong; ?>"
					  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $wrong; ?>%">
					    <?php echo $wrongCount; ?>
					  </div>
					  <?php $goto = 100/$report['totalQuestion']*$report['questionAnswered'];
							$gotoCount = $report['totalQuestion']-$report['questionAnswered']; ?>
					  <div class="progress-bar-togo" style="width:<?php echo $goto; ?>%"><?php echo $gotoCount; ?></div>
					</div>
				</span>
				<span class="col-md-2">Zeit pro Frage: <?php echo $report['timePerQuestion']; ?> Sekunden</span>
				<span class="col-md-2">Zeit Total: <?php echo $report['totalTimeInSec']; ?> Sekunden</span>
			</div>
			<?php } ?> <!--
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
			</div> -->
		</div>
	</div>
</div>