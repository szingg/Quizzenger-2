<?php
	$user = $this->_['user'];
	$userList = $this->_['userlist'];

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
					<ol>
						<?php
							while($current = $userList->fetch_object()) {
								echo '<li>' . htmlspecialchars($current->username) . '</li>';
							}
						?>
					</ol>
				</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-question-report">
				<div class="panel-body">bar</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-author-report">
				<div class="panel-body">bar</div>
			</div>
			<div role="tabpanel" class="tab-pane" id="tab-system-report">
				<div class="panel-body">bar</div>
			</div>
		</div>
	</div>
</div>
