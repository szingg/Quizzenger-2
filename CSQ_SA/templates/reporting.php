<?php
	$user = $this->_['user'];
	$userList = $this->_['userlist'];
	$questionList = $this->_['questionlist'];
	$authorList = $this->_['authorlist'];
	$categoryId = $this->_['categoryid'];
	$categoryList = $this->_['categorylist'];

	if (isset($this->_['message'])){
		echo '<div class="alert alert-info" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a>'.htmlspecialchars($this->_['message']).'</div>';
	}

	$outputRow = function($text, $raw = false) {
		if($raw)
			echo "<td>$text</td>";
		else
			echo '<td>' . htmlspecialchars($text) . '</td>';
	};
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong>Reporting</strong>
	</div>
	<div role="tabpanel">
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#tab-user-report" role="tab" data-toggle="tab"><b>Benutzer</b></a></li>
			<li role="presentation"><a href="#tab-question-report" role="tab" data-toggle="tab"><b>Fragen</b></a></li>
			<li role="presentation"><a href="#tab-author-report" role="tab" data-toggle="tab"><b>Autoren</b></a></li>
			<li role="presentation"><a href="#tab-system-report" role="tab" data-toggle="tab"><b>System</b></a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="tab-user-report">
				<div class="panel-body">
					<table id="tableReportUserList" class="table">
						<thead>
							<form id="tableReportUserList-category" method="GET">
								<label>Kategorie&nbsp;</label>
								<select form="tableReportUserList-category" name="category" onchange="this.form.submit()">
									<option value="0">Alle Kategorien</option>
									<?php
										while($current = $categoryList->fetch_object()) {
											if($current->id == $categoryId)
												echo "<option value=\"{$current->id}\" selected>" . htmlspecialchars($current->name) . "</option>";
											else
												echo "<option value=\"{$current->id}\">" . htmlspecialchars($current->name) . "</option>";
										}
									?>
								</select>
								<input type="hidden" name="view" value="reporting" />
							<form>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Erstellt</th>
								<th>Rang</th>
								<th>Producer</th>
								<th>Consumer</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($current = $userList->fetch_object()) {
									$rankImagePath = RANK_PATH . "/{$current->rank_image}." . RANK_IMAGE_EXTENSION;

									echo "<tr>";
									$outputRow($current->id);
									$outputRow("<a href=\"" . APP_PATH . "/?view=user&amp;id={$current->id}\">"
										. htmlspecialchars($current->username) . "</a>", true);
									$outputRow($current->created_on);
									$outputRow("<span style=\"display:none\">{$current->rank_threshold}</span>"
										. ($current->rank_image != "" ? "<img src=\"{$rankImagePath}\" />" : "") . ' ' . htmlspecialchars($current->rank), true);
									$outputRow((int)$current->producer_score);
									$outputRow((int)$current->consumer_score);
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-question-report">
				<div class="panel-body">
					<table id="tableReportQuestionList" class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Frage</th>
								<th>Kategorie</th>
								<th>Autor</th>
								<th>Datum</th>
								<th>Bewertung</th>
								<th>Schwierigkeit</th>
								<th>Gelöst</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($current = $questionList->fetch_object()) {
									echo '<tr>';
									$outputRow($current->id);
									$outputRow("<a href=\"" . APP_PATH . "/?view=question&amp;id={$current->id}\">"
										. htmlspecialchars($current->questiontext) . "</a>", true);

									$outputRow("<a href=\"" . APP_PATH . "/?view=questionlist&amp;category={$current->category_id}\">"
										. htmlspecialchars($current->category) . "</a>", true);

									$outputRow("<a href=\"" . APP_PATH . "/?view=user&amp;id={$current->author_id}\">"
										. htmlspecialchars($current->author) . "</a>", true);
									$outputRow("{$current->created} ({$current->last_modified})");

									if($current->ratingcount != 0)
										$outputRow(number_format((float)$current->rating / (float)$current->ratingcount, 1, '.', ''));
									else
										$outputRow('');

									$outputRow(number_format($current->difficulty, 2, '.', ''));
									$outputRow($current->solved_count);
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-author-report">
				<div class="panel-body">
					<table id="tableReportAuthorList" class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Anzahl</th>
								<th>&#216; Bewertung</th>
								<th>&#216; Schwierigkeit</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($current = $authorList->fetch_object()) {
									echo '<tr>';
									$outputRow($current->id);
									$outputRow($current->author);
									$outputRow($current->question_count);
									$outputRow($current->rating_average == ''
										? '' : number_format($current->rating_average, 2, '.', ''));
									$outputRow(number_format($current->difficulty_average, 2, '.', ''));
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-system-report">
				<div class="panel-body">
					<table class="table quizzenger-report-table">
						<thead>
							<tr>
								<th>Frage</th>
								<th>Bewertung</th>
								<th>Schwierigkeit</th>
								<th>Durchführungen</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>jjj</td><td>jjj</td><td>jjj</td><td>jjj</td>
							</tr>
							<tr>
								<td>kkk</td><td>kkk</td><td>kkk</td><td>kkk</td>
							</tr>
							<tr>
								<td>lll</td><td>lll</td><td>lll</td><td>lll</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
