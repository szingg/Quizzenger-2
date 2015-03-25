<?php

function throwXmlParseException(){
	echo("<p style=\"color:red; \">achievements.xml could not successfully be parsed.</p><br>");
	die();
}

function throwVersionException($version){
	echo("<p style=\"color:red; \">Version conflict. Expected Version is '".$version."' </p><br>");
	die();
}

function throwMalformedXMLException(){
	echo("<p style=\"color:red; \">Parse Error: Malformed XML</p><br>");
	die();
}

function throwSqlInsertException($link){
	echo("<p style=\"color:red; \">Insert Failed! Error : (". $link->errno .") ". $link->error ."</p><br>");
	die();
}

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

	$xml = simplexml_load_file('settings.xml');
	//check version
	if(!isset($xml['version'])){
		throwXmlParseException();
	}
	$version = '2.0';
	if($xml['version'] != $version){
		throwVersionException($version);
	}
	foreach ($xml->achievements[0]->achievement as $ach){
		if(! isset($ach['type'], $ach->name, $ach->order, $ach->description, $ach->image, $ach->arguments, $ach->bonusscore, $ach->triggers)){
			throwMalformedXMLException();
		}
		$type = '"'.$link->real_escape_string($ach['type']).'"';
		$name = '"'.$link->real_escape_string($ach->name).'"';
		$order = '"'.$link->real_escape_string($ach->order).'"';
		$description = '"'.$link->real_escape_string($ach->description).'"';
		$image = '"'.$link->real_escape_string($ach->image).'"';

		$arguments = array();
		foreach($ach->arguments->argument as $arg){
			if(! isset($arg["name"], $arg["value"])){
				throwMalformedXMLException();
			}
			$arguments[$link->real_escape_string($arg["name"])] = $link->real_escape_string($arg["value"]);
		}
		$arguments = '"'.$link->real_escape_string(json_encode($arguments)).'"'; //decode with json_decode($arguments, true);
		$bonusscore = '"'.$link->real_escape_string($ach->bonusscore).'"';
		$triggers = $ach->triggers;

		$result = $link->query("INSERT INTO achievement (name, description, type, image, arguments, bonus_score) VALUES ($name, $description, $type, $image, $arguments, $bonusscore)");
		if($result){
			$achievementId = $link->insert_id;
			foreach($triggers->trigger as $trigger){
				if(! isset($trigger['name'], $achievementId)){
					echo('<p style=\"color:red;\">Parse Error: Malformed XML</p><br>');
					die();
				}
				$triggerName = '"'.$link->real_escape_string($trigger['name']).'"';
				$resultTrigger = $link->query("INSERT INTO achievementtrigger (achievement_id, name) VALUES ($achievementId, $triggerName)");
				if($resultTrigger){}
				else{
					throwSqlInsertException($link);
				}
			}
		}
		else{
			throwSqlInsertException($link);
		}
	}
	echo("<p style=\"color:green;\">Achievements successfully installed!</p><br>");

	foreach ($xml->ranks[0]->rank as $rank){
		if(! isset($rank['name'], $rank['threshold'],$rank['image'])){
			throwMalformedXMLException();
		}
		$name = '"'.$link->real_escape_string($rank['name']).'"';
		$threshold = '"'.$link->real_escape_string($rank['threshold']).'"';
		$image = '"'.$link->real_escape_string($rank['image']).'"';

		$result = $link->query("INSERT INTO rank (name, threshold, image) VALUES ($name, $threshold, $image)");
		if($result){}
		else{
			throwSqlInsertException($link);
		}
	}
	echo("<p style=\"color:green;\">Ranks successfully installed!</p><br>");

	echo("<hr>");
	echo"<b>Remove following files: settings.xml, installerSettings.php!</b>";

}else{?>
	<h3>Welcome to the Quizzenger Settings Installer</h3>
	<b>1.</b> Specify your required Achievements, Ranks and Triggers in the settings.xml file.
	 Please just use the presetted Achievement Types and Triggers.
	 Otherwise you have to write your own Achievement or Rank plugin.
	 Consider that the attributes of each Achievement depend on its type.<br><br>
	<b>2.</b>Save the images which correspond to the Achievements in the content/achievements folger or a public folder of your choice.<br><br>
	<b>3.</b>Open the config.php file and set the 'ACHIEVEMENT_PATH', 'ACHIEVEMENT_IMAGE_EXTENSION',
	'RANK_PATH' and the 'RANK_IMAGE_EXTENSION' constants.<br><br>
	<br><br>
	<b>4.</b>Press the Install Button<br><br>


	<form action="installerSettings.php" method="post">
		<input type="hidden" name="install" value="go">
		<input type="submit" value="Install Settings">
	</form>

	<br><hr>

<?php } ?>