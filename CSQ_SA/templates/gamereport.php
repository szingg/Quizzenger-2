<script id="dot-gameReportRow" type="text/x-dot-template">
				<div class="row game-report-row {{?it.isCurrentUser==null}}game-report-active{{?}}">
					<strong class="col-md-1">Rang: {{=it.report.rank}}</strong><span class="col-md-1">{{=it.report.username}}</span>
					<span class="col-md-6" >
						<div class="progress game-report-progress" >
					{{?it.report.questionAnsweredCorrect > 0}}
						  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="{{=it.correct}}"
						  aria-valuemin="0" aria-valuemax="100" style="width:{{=it.correct}}%">
							{{=it.report.questionAnsweredCorrect}}
						  </div>
					{{?}}
					{{?it.wrongCount > 0}}
						  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="{{=it.wrong}}"
						  aria-valuemin="0" aria-valuemax="100" style="width:{{=it.wrong}}%">
							{{=it.wrongCount}}
						  </div>
					{{?}}
					{{?it.togoCount > 0}}
						  <div class="progress-bar-togo" style="width:{{=it.togo}}%">{{=it.togoCount}}</div>
					{{?}}
						</div>
					</span>
					<span class="col-md-2">{{?it.formatTimePerQuestion}}
						{{=it.formatTimePerQuestion}}/Frage
					{{?}}</span>
					<span class="col-md-2">{{?it.formatTotalTimeInSec}}
						{{=it.formatTotalTimeInSec}} Total
					{{?}}</span>
				</div>
</script>
<script id="dot-gameReportCountdown" type="text/x-dot-template">
	<div class="pull-left countdown-title">Countdown:</div>
	<div class="progress game-report-progress pos-relative" >
		<div class="progress-bar progress-bar-info pos-absolute" role="progressbar" aria-valuenow="{{=it.progressCountdown}}"
		  aria-valuemin="0" aria-valuemax="100" style="width:{{=it.progressCountdown}}%">
	  	</div>
		<div class="progress-bar-title pos-absolute">{{=it.formatTimeToEnd}}</div>
	</div>
</script>
<div class="panel panel-default no-margin">
	<a data-toggle="collapse" data-target="#gameAdmin" href="#gameAdmin">
		<div class="panel-heading bg-info text-info">
			<div class="text-right">
				<h4 class="panel-title pull-left">Game-Report</h4>
				<div id="gameCountdown" class="display-inline-block width-3">
					<?php /*if($this->_['timeToEnd'] > 0){ ?>
						<div class="pull-left countdown-title">Countdown:</div>
						<div class="progress game-report-progress pos-relative" >
						  <div class="progress-bar progress-bar-info pos-absolute" role="progressbar" aria-valuenow="<?php echo $this->_['progressCountdown']; ?>"
						  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $this->_['progressCountdown']; ?>%">
						  </div>
						  <div class="progress-bar-title pos-absolute"><?php echo formatSeconds($this->_['timeToEnd']); ?></div>
						</div>
					<?php } */ ?>
				</div>
			</div>
		</div>
	</a>
	<div id="gameAdmin" class="panel-collapse collapse in">
		<div class="panel-body">
			<br>
			<div id="gameReport">
				<?php /* foreach($this->_['gamereport'] as $report){ ?>
				<div class="row game-report-row <?php echo ($report['user_id']==$this->_['gameinfo']['owner_id'] ? 'game-report-active' : ''); ?>">
					<strong class="col-md-1">Rang: <?php echo $report['rank']; ?></strong><span class="col-md-1"><?php echo $report['username']; ?></span>
					<span class="col-md-6" >
						<div class="progress game-report-progress" >
						<?php $correct = 100/$report['totalQuestions']*$report['questionAnsweredCorrect'];
							if($report['questionAnsweredCorrect']>0){ ?>
						  <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $correct; ?>"
						  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $correct; ?>%">
						    <?php echo $report['questionAnsweredCorrect']; ?>
						  </div>
						  <?php }
						  		$wrongCount = $report['questionAnswered']-$report['questionAnsweredCorrect'];
						  		$wrong = 100/$report['totalQuestions']*($wrongCount);
						  		if($wrongCount>0){
						  ?>
						  <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="<?php echo $wrong; ?>"
						  aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $wrong; ?>%">
						    <?php echo $wrongCount; ?>
						  </div>
						  <?php }
						  		$goto = 100/$report['totalQuestions']*($report['totalQuestions']-$report['questionAnswered']);
								$gotoCount = $report['totalQuestions']-$report['questionAnswered'];
								if($gotoCount>0){
							?>
						  <div class="progress-bar-togo" style="width:<?php echo $goto; ?>%"><?php echo $gotoCount; ?></div>
						  <?php } ?>
						</div>
					</span>
					<span class="col-md-2"><?php echo formatSeconds($report['timePerQuestion']); ?>/Frage</span>
					<span class="col-md-2"><?php echo formatSeconds($report['totalTimeInSec']); ?> Total</span>
				</div>
				<?php }  */ ?>
			</div>
				 <!--
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