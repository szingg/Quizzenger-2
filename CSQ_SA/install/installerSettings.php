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

function throwSqlInsertException($link, $statement){
	echo("<p style=\"color:red; \">Insert Failed! Error : (". $link->errno .") ". $link->error ."</p><p>".$statement."</p><br>");
	die();
}

include("../includes/config.php");
if(isset($_POST['install'])){

	echo("Connecting to DB...<br>");
	$link = mysqli_connect(dbhost.":".dbport, dbuser, dbpassword) ;
	if (!$link || !$link->set_charset("utf8")) {
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
	if(! isset($xml->eventtriggers, $xml->eventtriggers[0])){
		throwMalformedXMLException();
	}

	foreach($xml->eventtriggers[0] as $trigger){
		if(! isset($trigger['name'], $trigger->producer_score, $trigger->consumer_score)){
			throwMalformedXMLException();
		}
		$name = '"'.$link->real_escape_string($trigger['name']).'"';
		$producer_score = '"'.$link->real_escape_string($trigger->producer_score).'"';
		$consumer_score = '"'.$link->real_escape_string($trigger->consumer_score).'"';

		$stmt = "INSERT INTO eventtrigger (name, producer_score, consumer_score) VALUES ($name, $producer_score, $consumer_score)"
			." ON DUPLICATE KEY UPDATE producer_score=$producer_score, consumer_score=$consumer_score";
		$result = $link->query($stmt);
		if($result){}
		else{
			throwSqlInsertException($link, $stmt);
		}
	}

	echo("<p style=\"color:green;\">Eventtriggers successfully installed!</p><br>");

	if(! isset($xml->achievements, $xml->achievements[0])){
		throwMalformedXMLException();
	}

	foreach ($xml->achievements[0]->achievement as $ach){
		if(! isset($ach['type'], $ach->name, $ach->description, $ach->sort_order, $ach->image, $ach->arguments, $ach->bonus_score, $ach->eventtriggers)){
			throwMalformedXMLException();
		}
		$type = '"'.$link->real_escape_string($ach['type']).'"';
		$name = '"'.$link->real_escape_string($ach->name).'"';
		$description = '"'.$link->real_escape_string($ach->description).'"';
		$sort_order = '"'.$link->real_escape_string($ach->sort_order).'"';
		$image = '"'.$link->real_escape_string($ach->image).'"';

		//name, description, sort_order, type, image, argument, bonus_score
		$arguments = array();
		foreach($ach->arguments->argument as $arg){
			if(! isset($arg["name"], $arg["value"])){
				throwMalformedXMLException();
			}
			$arguments[$link->real_escape_string($arg["name"])] = $link->real_escape_string($arg["value"]);
		}
		$arguments = (count($arguments)==0?'""':'"'.$link->real_escape_string(json_encode($arguments)).'"'); //decode with json_decode($arguments, true);
		$bonus_score = '"'.$link->real_escape_string($ach->bonus_score).'"';
		$triggers = $ach->eventtriggers;

		$stmt = "INSERT INTO achievement (name, description, sort_order, type, image, arguments, bonus_score) VALUES ($name, $description, $sort_order, $type, $image, $arguments, $bonus_score)";
		$result = $link->query($stmt);
		if($result){
			$achievementId = $link->insert_id;
			foreach($triggers->eventtrigger as $trigger){
				if(! isset($trigger['name'], $achievementId)){
					throwMalformedXMLException();
				}
				$triggerName = '"'.$link->real_escape_string($trigger['name']).'"';
				$stmt = "INSERT INTO achievementtrigger (achievement_id, eventtrigger_name) VALUES ($achievementId, $triggerName)";
				$resultTrigger = $link->query($stmt);
				if($resultTrigger){}
				else{
					throwSqlInsertException($link, $stmt);
				}
			}
		}
		else{
			throwSqlInsertException($link, $stmt);
		}
	}
	echo("<p style=\"color:green;\">Achievements successfully installed!</p><br>");

	if(! isset($xml->ranks, $xml->ranks[0])){
		throwMalformedXMLException();
	}

	foreach ($xml->ranks[0]->rank as $rank){
		if(! isset($rank['name'], $rank['threshold'],$rank['image'])){
			throwMalformedXMLException();
		}
		$name = '"'.$link->real_escape_string($rank['name']).'"';
		$threshold = '"'.$link->real_escape_string($rank['threshold']).'"';
		$image = '"'.$link->real_escape_string($rank['image']).'"';

		$stmt = "INSERT INTO rank (name, threshold, image) VALUES ($name, $threshold, $image)";
		$result = $link->query($stmt);
		if($result){}
		else{
			throwSqlInsertException($link, $stmt);
		}
	}
	echo("<p style=\"color:green;\">Ranks successfully installed!</p><br>");

	if(! isset($xml->messages, $xml->messages[0])){
		throwMalformedXMLException();
	}

	foreach ($xml->messages[0]->message as $message){
		if(! isset($message->type, $message->text)){
			throwMalformedXMLException();
		}
		$type = '"'.$link->real_escape_string($message->type).'"';
		$text = '"'.$link->real_escape_string($message->text).'"';

		$stmt = "INSERT INTO translation (type, text) VALUES ($type, $text)"
			." ON DUPLICATE KEY UPDATE text=$text";
		$result = $link->query($stmt);
		if($result){}
		else{
			throwSqlInsertException($link, $stmt);
		}
	}
	echo("<p style=\"color:green;\">Messages successfully installed!</p><br>");

	if(! isset($xml->settings, $xml->settings[0])){
		throwMalformedXMLException();
	}

	foreach ($xml->settings[0]->setting as $setting){
		if(! isset($setting['name'], $setting['value'])){
			throwMalformedXMLException();
		}
		$name = '"'.$link->real_escape_string($setting['name']).'"';
		$value = '"'.$link->real_escape_string($setting['value']).'"';

		$stmt = "INSERT INTO settings (name, value) VALUES ($name, $value)"
			." ON DUPLICATE KEY UPDATE value=$value";
		$result = $link->query($stmt);
		if($result){}
		else{
			throwSqlInsertException($link, $stmt);
		}
	}
	echo("<p style=\"color:green;\">Settings successfully installed!</p><br>");

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