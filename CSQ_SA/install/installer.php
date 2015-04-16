<?php
include("../includes/config.php");
if(isset($_POST['install'])){

	echo("Connecting to DB<br>");
	$link = mysqli_connect(dbhost.":".dbport, dbuser, dbpassword) ;
	if (!$link) {
		die ("MySQL Connection error");
	}
	echo("<p style=\"color:green;\">Connected to DB</p><br>");

	$result = mysqli_query($link,"CREATE DATABASE IF NOT EXISTS ".db);
	if(!mysqli_select_db($link ,db)) {
		die ("Couldn't connect to Database ".db);
	}

	echo("<p style=\"color:green;\">Created DB or DB existed already</p><br>");

	$result = mysqli_query($link,"USE DATABASE ".db);

	$sqlErrorCode=0;
	$sqlFileToExecute='./install.sql';
	// read the sql file
	$f = fopen($sqlFileToExecute,"r");
	$sqlFile = fread($f, filesize($sqlFileToExecute));
	fclose($f);
	$sqlArray = explode(';',$sqlFile);
	foreach ($sqlArray as $stmt) {
		if (strlen($stmt)>3 && substr(ltrim($stmt),0,2)!='/*') {
			$result = mysqli_query($link,$stmt);
			echo '<p>' . mysqli_error($link) . $stmt . '</p>';
			if (!$result) {
				$sqlErrorCode = mysql_errno();
				$sqlErrorText = mysql_error();
				$sqlStmt = $stmt;
				break;
			}
		}
	}
	if ($sqlErrorCode == 0) {
		echo("<p style=\"color:green;\">Finished successfully!</p><br>");
	} else {
		echo("<p style=\"color:red\">");
			echo "An error occured during installation!<br/>";
			echo "Error code: " . htmlspecialchars($sqlErrorCode) . "<br/>";
			echo "Error text: " . htmlspecialchars($sqlErrorText) . "<br/>";
			echo "Statement:<br/> " . htmlspecialchars($sqlStmt) . "<br/>";
		echo("</p>");
	}
	echo("<hr>");
	echo"<b>Remove following files: installer.php, install.sql!</b>";

}else{?>
	<h3>Welcome to the Quizzenger Installer</h3>
	<b>0.</b> Requirements: PHP 5, Mysql 5, a Webserver, PHP Mysql Native Driver<br>
	<i>Tested with PHP 5.5 , Mysql 5.6</i><br><br>
	<b>1.</b> Unzip and copy the CSQ folder to your /var/www/ directory or wherever your webserver is based<br><br>
	<b>2.</b> Make sure the permissions are set correctly on your filesystem (R/W). Especially for the user running PHP.<br>
	If you don't want the logfiles to be (technically) public, set according permissions to the log folder.<br><br>
	<b>3.</b> Add your connection settings for the database in includes/config.php under 'DB connection'<br>
			Momentarily they are: db = <?= htmlspecialchars(db)?> , dbuser = <?= htmlspecialchars(dbuser)?> , db= ******* , dbhost= <?= htmlspecialchars(dbhost)?>, dbport = <?= htmlspecialchars(dbport)?><br>
			* F5 to refresh if you edited the file<br><br>
	<b>4.</b> Add the FQDN (https://www.yourdomain.com/optionalfolder) in said config file under 'APP_PATH'<br>
		Momentarily it is set to: APP_PATH= <?= htmlspecialchars(APP_PATH)?><br><br>
	<b>5.</b> REMOVE this file and install.sql after installation!<br><br>
	<b>6.</b> Congratulations! The Superuser's login data is : superuser@quizzenger.ch , password: changeme (change the password asap!)
	<b>7. </b> Create the desired categories in the db<br>
	Note: There are three "levels" of categories, such without parents (general field), such with parents and having chidlren (category) and such with parents and no children (subcategory)<br><br>
	<br>
	<br>
	<form action="installer.php" method="post">
		<input type="hidden" name="install" value="go">
		<input type="submit" value="Install">
	</form>

	<br><hr>
	Prequisites installation "Script" under Debian based systems:<br>
	<i>sudo apt-get install apache2<br>
	sudo apt-get install php5<br>
	sudo apt-get install libapache2-mod-php5<br>
	sudo apt-get install php5-mysqlnd<br>
	sudo /etc/init.d/apache2 restart</i>

<?php } ?>