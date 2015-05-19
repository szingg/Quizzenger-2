<?php use \quizzenger\utilities\FormatUtility as FormatUtility; ?>
<div class="panel panel-default">
	<div class="panel-heading">
		<b>Gamedetail für <?php echo $this->_['gameinfo']['gamename']; ?></b>
	</div>
	<div class="panel-body">
		<div class="panel-group" id="accordion">
			<div class="panel panel-default" id="panel1">
				<a data-toggle="collapse" data-target="#collapseOne" href="#collapseOne" class="collapsed">
					<div class="panel-heading">
						<h4 class="panel-title">Report</h4>
					</div>
				</a>
				<div id="collapseOne" class="panel-collapse collapse in">
					<div class="panel-body">
						<div class="table-responsive">
						<table class="table" id="tableGameDetailReport" data-link="row" >
							<thead>
								<tr>
									<th>
										Rang
									</th>
									<th>
										Username
									</th>
									<th>
										Punkte
									</th>
									<th>
										Zeit / Frage
									</th>
									<th>
										Zeit Total
									</th>
								</tr>
							</thead>
							<tbody>
							<?php  foreach($this->_['gamereport'] as $report){ ?>
								<tr>
									<td><?php echo $report['rank']; ?></td>
									<td><?php echo $report['username']; ?></td>
									<td>
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
									</td>
									<td><?php echo FormatUtility::formatSeconds($report['timePerQuestion']); ?></td>
									<td><?php echo FormatUtility::formatSeconds($report['totalTimeInSec']); ?></td>
								</tr>
							<?php } ?>
							</tbody>
						</table>
						</div>
					</div>
				</div>
			</div>


			<div class="panel panel-default" id="panel2">
				<a data-toggle="collapse" data-target="#collapseTwo" href="#collapseTwo" class="collapsed">
					<div class="panel-heading">
						<h4 class="panel-title">Fragen</h4>
					</div>
				</a>
				<div id="collapseTwo" class="panel-collapse collapse">
					<div class="panel-body">

						<!-- <div class="table-responsive">  -->
						<!-- <table class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%" id="tableQuestionPerformances" data-link="row" >
						<table id="tableQuestionPerformances" class="table table-striped table-hover dt-responsive display nowrap" cellspacing="0"> -->
						<!-- <table class="table" id="tableQuestionPerformances" data-link="row" > -->
						<!--  <div style="width: 100%; padding-left: -10px; border: 1px solid red;">  -->
		    			<div class="table-responsive">
		    			<table class="table" id="tableQuestionPerformances" data-link="row" >

							<thead>
								<tr>
									<th>
										Frage
									</th>
									<th>
										Beantwortet
									</th>
									<th>
										Richtig
									</th>
									<th>
										Falsch
									</th>
									<th>
										Gewicht
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								foreach ( $this->_ ['questions'] as $q ) { ?>
									<tr>
										<td>
											<div id="questionTextSpan">
												<?= $out = strlen($q['questiontext']) > QUESTIONTEXT_CUTOFF_LENGTH ? htmlspecialchars(substr($q['questiontext'],0,QUESTIONTEXT_CUTOFF_LENGTH))." . . ." : htmlspecialchars($q['questiontext']); ?>
												<?php if(strlen($q['questiontext']) > QUESTIONTEXT_CUTOFF_LENGTH){?>
													<span id="questionTextAddition" class="hidden-xs">
														<?=htmlspecialchars($q['questiontext'])?>
													</span>
												<?php }?>
											</div>
										</td>
										<td>
											<?=  htmlspecialchars($q['answeredTotal']); ?>
										</td>
										<td>
											<?=  htmlspecialchars($q['answeredCorrect']); ?>
										</td>
										<td>
											<?=  htmlspecialchars($q['answeredWrong']); ?>
										</td>
										<td>
											<?= htmlspecialchars($q['weight']); ?>
										</td>
									</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
						<br>
						<a class="text-primary" href="?view=quizdetail&quizid=<?php echo $this->_['gameinfo']['quiz_id']; ?>" >zum Quiz...</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
