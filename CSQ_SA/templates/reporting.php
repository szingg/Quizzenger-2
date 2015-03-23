<?php
	$user = $this->_['user'];
	$userList = $this->_['userlist'];
	$questionList = $this->_['questionlist'];
	$authorList = $this->_['authorlist'];

	if (isset($this->_['message'])){
		echo '<div class="alert alert-info" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a>'.htmlspecialchars($this->_['message']).'</div>';
	}
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
					<table class="table quizzenger-report-table">
						<thead>
							<tr>
								<th>Name</th>
								<th>Rang</th>
								<th>Score</th>
							</tr>
						</thead>
						<tbody>
							<?php
								while($current = $userList->fetch_object()) {
									echo "<tr>";
									echo "<td>{$current->username}</td><td>n/a</td><td>n/a</td>";
									echo "</tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-author-report">
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
								<td>ddd</td><td>ddd</td><td>ddd</td><td>ddd</td>
							</tr>
							<tr>
								<td>eee</td><td>eee</td><td>eee</td><td>eee</td>
							</tr>
							<tr>
								<td>fff</td><td>fff</td><td>fff</td><td>fff</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-question-report">
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
								<td>ggg</td><td>ggg</td><td>ggg</td><td>ggg</td>
							</tr>
							<tr>
								<td>hhh</td><td>hhh</td><td>hhh</td><td>hhh</td>
							</tr>
							<tr>
								<td>iii</td><td>iii</td><td>iii</td><td>iii</td>
							</tr>
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
