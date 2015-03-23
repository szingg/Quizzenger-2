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
