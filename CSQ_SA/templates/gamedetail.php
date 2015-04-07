<div class="panel panel-default" id="panel1">
		<a data-toggle="collapse" data-target="#collapseOne" href="#collapseOne" class="collapsed">
			<div class="panel-heading">
				<h4 class="panel-title">Report</h4>
			</div>
		</a>
		<div id="collapseTwo" class="panel-collapse collapse">
			<div class="panel-body">
			bla
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
									<div id="questionTextSpan">
										<?= $out = strlen($q['question']) > QUESTIONTEXT_CUTOFF_LENGTH ? htmlspecialchars(substr($q['question'],0,QUESTIONTEXT_CUTOFF_LENGTH))." . . ." : htmlspecialchars($q['question']); ?>
										<?php if(strlen($q['question']) > QUESTIONTEXT_CUTOFF_LENGTH){?>
											<span id="questionTextAddition" class="hidden-xs">
												<?=htmlspecialchars($q['question'])?>
											</span>
										<?php }?>
									</div>
								</td>
								<td>
									<?=  htmlspecialchars($q['answered']); ?>
								</td>
								<td>
									<?=  htmlspecialchars($q['correct']); ?>
								</td>
								<td>
									<?=  htmlspecialchars($q['wrong']); ?>
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
					</div> </div>
				</div>
			</div>
		</div>