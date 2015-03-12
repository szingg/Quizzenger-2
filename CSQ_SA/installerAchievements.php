<?php
include("includes/config.php");
if(isset($_POST['install'])){

	echo("Connecting to DB...<br>");
	$link = mysqli_connect(dbhost.":".dbport, dbuser, dbpassword) ;
	if (!$link) {
		die ("MySQL Connection error");
	}
	echo("<p style=\"color:green;\">Connected to DB</p><br>");

	//$result = mysqli_query($link,"CREATE DATABASE IF NOT EXISTS ".db);
	if(!mysqli_select_db($link ,db)) {
		die ("Couldn't connect to Database ".db);
	}

	$result = mysqli_query($link,"USE DATABASE ".db);
	echo("<p style=\"color:green;\">Connected to DB ".db."</p><br>");

	
	$sqlErrorCode=0;
	/*
	$sqlFileToExecute='./install.sql';
	// read the sql file
	$f = fopen($sqlFileToExecute,"r");
	$sqlFile = fread($f, filesize($sqlFileToExecute));
	fclose($f);
	$sqlArray = explode(';',$sqlFile);
	foreach ($sqlArray as $stmt) {
		if (strlen($stmt)>3 && substr(ltrim($stmt),0,2)!='/*') {
			$result = mysqli_query($link,$stmt);
			if (!$result) {
				$sqlErrorCode = mysql_errno();
				$sqlErrorText = mysql_error();
				$sqlStmt = $stmt;
				break;
			}
		}
	}
	*/
	$xml = simplexml_load_file('achievements.xml');
	//check version
	if(!isset($xml['version'])){
		echo("<p style=\"color:red;\">achievements.xml could not successfully be parsed.</p><br>");
		die();
	} 
	$version = '2.0';
	if($xml['version'] != $version){
		echo("<p style=\"color:red;\">Version conflict. Expected Version is '".$version."' </p><br>");
		die();
	}
	foreach ($xml->achievements[0]->achievement as $ach){
		$type = '"'.$link->real_escape_string($ach['type']).'"';
		$name = '"'.$link->real_escape_string($ach->name).'"';
		$order = '"'.$link->real_escape_string($ach->order).'"';
		$description = '"'.$link->real_escape_string($ach->description).'"';
		$image = '"'.$link->real_escape_string($ach->image).'"';
		$arguments = '"'.$link->real_escape_string($ach->arguments->asXML()).'"';
		$args1 = $ach->arguments->asXML();
		$bonusscore = '"'.$link->real_escape_string($ach->bonusscore).'"';
		
		$result = $link->query("INSERT INTO achievement (name, description, type, image, attributes, bonus_score) VALUES ($name, $description, $type, $image, $arguments, $bonusscore)");
		if($result){
			$achievementId = $link->insert_id;
			foreach($ach->triggers->trigger as $trigger){
				$triggerName = '"'.$link->real_escape_string($trigger['name']).'"';
				$resultTrigger = $link->query("INSERT INTO achievementtrigger (achievement_id, type) VALUES ($achievementId, $triggerName)");
				if($resultTrigger){}
				else{
					echo('<p style=\"color:red;\">Insert Failed! Error : ('. $link->errno .') '. $link->error .'</p><br>');
					die();
				}
			}	
		}
		else{
			echo('<p style=\"color:red;\">Insert Failed! Error : ('. $link->errno .') '. $link->error .'</p><br>');
			die();
		}
		//$achId = mysqli->s_insert("INSERT INTO question (type, questiontext, user_id, category_id,created,attachment,attachment_local) VALUES (?, ?, ?, ?, ?, ?, ?)",array('s', 's','i','i','s','s','i'),array($type,$questiontext,$userID,$categoryID,null,$attachment,$attachment_local));
		
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
	echo"<b>Remove following files: achievements.xml, installerAchievements.php!</b>";

}else{?>
	<h3>Welcome to the Quizzenger Achievement Installer</h3>
	<b>1.</b> Specify your required Achievements in the achievements.xml file.
	 Please just use the presetted Achievement Types and Triggers.
	 Consider that the attributes of each Achievement depend on its type.<br><br>
	<b>2.</b>Save the images which correspond to the Achievements in the content/achievements folger or a public folder of your choice.<br><br>
	<b>3.</b>Open the config.php file and set the 'ACHIEVEMENT_PATH' and the 'ACHIEVEMENT_IMAGE_EXTENSION' constants.<br><br>
	<b>4.</b>Press the Install Button<br><br>


	<form action="installerAchievements.php" method="post">
		<input type="hidden" name="install" value="go">
		<input type="submit" value="Install Achievements">
	</form>

	<br><hr>

<?php } ?>