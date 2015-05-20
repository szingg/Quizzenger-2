<?php
$roots = $this->_ ['roots'];
?>
<h3>Quiz generieren</h3>
<h4>Wähle Themen und stelle dir so ein individuelles Quiz zur Lernkontrolle zusammen</h4>
<br>
<form role="form" id="quiz_generator_form" method="post" action="?view=processGenerateQuiz">

	<div class="panel-group" id="accordion">
		<div class="panel panel-default" id="panel1">
			<a data-toggle="collapse" data-target="#collapseCategory"
				href="#collapseCategory">
				<div class="panel-heading">
					<h4 class="panel-title">Kategorien</h4>
				</div>
			</a>
			<div id="collapseCategory" class="panel-collapse collapse in">
				<div class="panel-body">
					<?php include "categorylist.php"; ?>
					<div id="generatorSelectedCategories">
					<!-- GETS FILLED WITH BADGES -->
					</div>
				</div>
			</div>
		</div>

		<div class="panel panel-default" id="accordion">
			<a data-toggle="collapse" data-target="#collapseOptions"
				href="#collapseOptions">
				<div class="panel-heading">
					<h4 class="panel-title">Optionen</h4>
				</div>
			</a>
			<div id="collapseOptions" class="panel-collapse collapse in">
				<div class="panel-body">
					<div class="row">
						<!-- DIFFICULTY -->
				  		<div class="col-md-4">
				  			<h4>Schwierigkeit</h4><hr>
								<div>
									<label> <input type="checkbox"
										id="quiz_generator_form_difficulty_1"
										name="quiz_generator_form_difficulty[]" value="0"> Einfach
									</label>
								</div>
								<div>
									<label> <input type="checkbox"
										id="quiz_generator_form_difficulty_2"
										name="quiz_generator_form_difficulty[]" value="1"> Normal
									</label>
								</div>
								<div>
									<label> <input type="checkbox"
										id="quiz_generator_form_difficulty_3"
										name="quiz_generator_form_difficulty[]" value="2"> Moderat
									</label>
								</div>
								<div>
									<label> <input type="checkbox"
										id="quiz_generator_form_difficulty_4"
										name="quiz_generator_form_difficulty[]" value="3"> Schwer
									</label>
								</div>
						</div>
						<p class="hidden-md hidden-lg">&nbsp;</p>
						<!-- MAX COUNT -->
				  		<div class="col-md-4">
				  			<h4>Maximale Anzahl Fragen</h4><hr>
			  				<div class="row">
			  					<div class="col-xs-8">
									<input type="range" class="form-control"
										id="quiz_generator_form_count" min="1" max="50" name="quiz_generator_form_count"
										value="10">
								</div>
								<div class="col-xs-4">
									<input id="quiz_generator_form_count_text" type="text" disabled="true" size="2" value="10">
								</div>
							</div>
						</div>
						<p class="hidden-md hidden-lg">&nbsp;</p>
						<!-- SEARCH MODE -->
						<div class="col-md-4">
							<h4>Such Modus</h4><hr>
							<select class="form-control" id="quiz_generator_form_mode"
									name="quiz_generator_form_mode">
								<option value="random">Zuf&auml;llig</option>
								<option value="best">Am besten bewertete Fragen bevorzugen</option>
								<option value="most">Am meisten beantwortete Fragen bevorzugen</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<button type="submit" class="btn btn-primary">Quiz Generieren</button>
</form>