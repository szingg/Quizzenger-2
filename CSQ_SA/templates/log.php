<h3>Logfile Liste:</h3>
<?php
foreach($this->_ ['logfiles'] as $logfile){
	echo('<a href="'.htmlspecialchars(APP_PATH).'/log/'.htmlspecialchars($logfile).'">'.htmlspecialchars($logfile).'</a><br>');
}
?>