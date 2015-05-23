<?php
	use \quizzenger\utilities\FormatUtility as FormatUtility;

	$user = $this->_['user'];
	$userList = $this->_['userlist'];
	$questionList = $this->_['questionlist'];
	$authorList = $this->_['authorlist'];
	$categoryId = $this->_['categoryid'];
	$categoryList = $this->_['categorylist'];
	$systemStatus = $this->_['systemstatus'];

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
			<?php if($user['superuser']): ?>
			<li role="presentation"><a href="#tab-system-report" role="tab" data-toggle="tab"><b>System</b></a></li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="tab-user-report">
				<div class="panel-body">
					<div class="table-responsive">
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
										. ($current->rank_image != "" ? "<img src=\"{$rankImagePath}\" />" : "") . ' ' . htmlspecialchars($current->rank_name), true);
									$outputRow((int)$current->producer_score);
									$outputRow((int)$current->consumer_score);
									echo "</tr>";
								}
							?>
							<?php if($categoryId != 0): ?>
								<script>
									(function() {
										$('#tableReportUserList').DataTable().column(3).visible(false);
									})();
								</script>
							<?php endif; ?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-question-report">
				<div class="panel-body">
					<div class="table-responsive">
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
								<th>Aktionen</th>
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
										$outputRow(FormatUtility::formatNumber((float)$current->rating / (float)$current->ratingcount, 1));
									else
										$outputRow('');

									$outputRow(FormatUtility::formatNumber($current->difficulty, 2));
									$outputRow($current->solved_count);
									if($current->autorized_on_question > 0){
										$outputRow('<a class="remove-row" href="#" data-qid="'.$current->id.'" data-type="question" title="Löschen">'
														. '<span class="glyphicon glyphicon-remove"></span> '
													. '</a>&nbsp;'
													.' <a href="?view=editquestion&amp;id='.$current->id.'" title="Bearbeiten" >'
														.'<span class="glyphicon glyphicon-edit"></span>'
													.'</a>',true);
									}else{
										$outputRow('');
									}
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-author-report">
				<div class="panel-body">
					<div class="table-responsive">
					<table id="tableReportAuthorList" class="table">
						<thead>
							<tr>
								<th>ID</th>
								<th>Name</th>
								<th>Anzahl</th>
								<th>&#216;&nbsp;Bewertung</th>
								<th>&#216;&nbsp;Schwierigkeit</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($current = $authorList->fetch_object()) {
									echo '<tr>';
									$outputRow($current->author_id);
									$outputRow("<a href=\"" . APP_PATH . "/?view=user&amp;id={$current->author_id}\">"
										. htmlspecialchars($current->author) . "</a>", true);

									$outputRow($current->question_count);
									$outputRow($current->rating_average == '' ? ''
										: FormatUtility::formatNumber($current->rating_average, 2));
									$outputRow(FormatUtility::formatNumber($current->difficulty_average, 2, '.'));
									echo '</tr>';
								}
							?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
			<?php if($user['superuser']): ?>
			<div role="tabpanel" class="tab-pane" id="tab-system-report">
				<div class="panel-body">
					<p><b>System Status</b></p>
					<pre><code><?php
						echo date('Y-m-d H:i:s') . "\n"
							. "Attachment Memory Usage : " . FormatUtility::formatNumber($systemStatus->attachment_usage / 1000000.0, 2) . "M\n"
							. "Database Memory Usage   : " . FormatUtility::formatNumber($systemStatus->database_usage / 1000000.0, 2) . "M\n"
							. "Login Attempts (24h)    : " . $systemStatus->login_attempts . "\n"
					?></code></pre>
					<p><b>Log Files</b></p>
					<ul>
						<?php
							foreach($systemStatus->log_files as $log) {
								$log = htmlspecialchars($log);
								$filename = htmlspecialchars(APP_PATH . '/index.php?view=syslog&logfile=' . $log);
								echo "<li><a href=\"$filename\">$log</a></li>";
							}
						?>
					</ul>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</div>
</div>
