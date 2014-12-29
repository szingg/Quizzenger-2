<h3>Logfile Liste:</h3>
<?php
foreach($this->_ ['logfiles'] as $logfile){
	echo('<a href="'.APP_PATH.'/log/'.$logfile.'">'.$logfile.'</a><br>');	
}
?>