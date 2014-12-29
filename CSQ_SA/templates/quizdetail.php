<div class="panel-group" id="accordion">
	<div class="panel panel-default" id="panel1">
		<a data-toggle="collapse" data-target="#collapseOne"
			href="#collapseOne">
			<div class="panel-heading">
				<h4 class="panel-title">Durchf&uuml;hrungen</h4>
			</div>
		</a>
		<div id="collapseOne" class="panel-collapse collapse in">
			<div class="panel-body">
				<table class="table" id="tableQuizPerformances" data-link="row">
					<thead>
						<tr>
							<th>
								User
							</th>
							<th>
								Score
							</th>
							<th>
								Start
							</th>
							<th>
								Dauer
							</th>
						</tr>
					</thead>
					<tbody>
					<?php
					foreach ( $this->_ ['performances'] as $p ) { ?>
						<tr>
							<td>
								<?=  $p['username']; ?>
							</td>
							<td>
								<?=  $p['score']; ?> / <?=  $p['maxscore']; ?>
							</td>
							<td>
								<?=  $p['start']; ?>
							</td>
							<td>
								<?=  $p['duration']; ?>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
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
				<table class="table" data-link="row" id="tableQuestionPerformances">
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
							<th>
								L&ouml;schen
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $this->_ ['questions'] as $q ) { ?>
							<tr>
								<td>
									<?=  $q['question']; ?>
								</td>
								<td>
									<?=  $q['answered']; ?>
								</td>
								<td>
									<?=  $q['correct']; ?>
								</td>
								<td>
									<?=  $q['wrong']; ?>
								</td>
								<td>
									<div class="dropdown">
										<button class="btn btn-default dropdown-toggle" type="button" id="dropdownWeight<?=  $q['id']; ?>" data-toggle="dropdown">
												<?=  $q['weight']; ?> <span class="caret"></span>
										</button>
										<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
										<?php
											for($i = 1; $i <= DIFFERENT_QUESTION_WEIGHTS; $i ++) { ?>
												<li role="presentation">
													<a	onclick="setWeight(<?=  $i; ?>, <?=  $q['id']; ?>)" role="menuitem" tabindex="-1" href="javascript:void()">
														<?=  $i; ?>
													</a>
												</li>
										<?php } ?>
										</ul>
									</div>
								</td>
								<td>
									<a class="remove-row" href="javascript:void()" onclick="deleteQuestionFromQuiz(<?=  $this->_ ['quizinfo']['quizid']; ?>, <?=  $q['question_id']; ?>)">
										<span class="glyphicon glyphicon-remove"></span>
									</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="panel panel-default" id="panel3">
			<a data-toggle="collapse" data-target="#collapseThree" href="#collapseThree" class="collapsed">
				<div class="panel-heading">
					<h4 class="panel-title">Share Link</h4>
				</div>
			</a>
			<div id="collapseThree" class="panel-collapse collapse">
				<div class="panel-body">
					<div class="row">
						<div class="col-lg-6">
							<div class="input-group">
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-link"></span>
								</span> 
								<input type="text" class="form-control" onClick="this.setSelectionRange(0, this.value.length)" 
										value="<?=  APP_PATH."/index.php?view=quizstart&quizid=". $this->_ ['quizinfo']['quizid']; ?>"
								> 
							</div><br>
							<a href="<?=  APP_PATH."/index.php?view=quizstart&quizid=". $this->_ ['quizinfo']['quizid']; ?>">Zum Quiz</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>