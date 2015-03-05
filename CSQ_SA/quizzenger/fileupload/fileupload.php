<?php

require_once("/../../includes/config.php");

$test = "1";
if(isset($_FILES["file"]["type"]))
{
	//check file error
	if ($_FILES["file"]["error"] > 0)
	{
		header('Content-Type: application/json');
		echo json_encode(array('result' => 'error', 'message' => $_FILES["file"]["errorMessage"]));
		return;
	}
	
	$temporary = explode(".", $_FILES["file"]["name"]);
	$file_extension = end($temporary);

	$extensions = explode(",", ATTACHMENT_ALLOWED_EXTENSIONS);
	$extensions = array_map('trim', $extensions);
	
	$containsExtension = false;
	$validExtension = false;
	foreach ( $extensions as $extension ) {
		//check for valid extension
		if(strpos($extension, $_FILES["file"]["type"]) !== false){
			$validExtension = true;
		}
		//check file contains extension
		if(strpos($extension, $file_extension) !== false){
			$containsExtension = true;
		}
	}
	
	if (!$validExtension || !$containsExtension){
		header('Content-Type: application/json');
		echo json_encode(array('result' => 'error', 'message' => 'Das Format der Datei ist ungültig.'));
		return;
	}

	//check for valid size
	$validSize = ($_FILES["file"]["size"] < (MAX_ATTACHMENT_SIZE_KByte*1024));
	if(!$validSize){
		header('Content-Type: application/json');
		echo json_encode(array('result' => 'error', 'message' => 'Die Datei ist zu gross. Maximale Grösse: '.MAX_ATTACHMENT_SIZE_KByte.' KByte'));
		return;
	}
	
	$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
	$targetPath = join_paths("../../", ATTACHMENT_PATH, $_FILES["file"]["name"]); // Target path where file is to be stored
	$fileExists = file_exists($targetPath);
	if (file_exists($targetPath)) {
		header('Content-Type: application/json');
		echo json_encode(array('result' => 'error', 'message' => 'Die Datei "'.$_FILES["file"]["name"].'" existiert bereits.'));
		return;
	}
	
	//move uploaded file
	move_uploaded_file($sourcePath,$targetPath) ; 
	
	//send success message
	header('Content-Type: application/json');
	echo json_encode(array('result' => 'success', 'message' => 'Die Datei "'.$_FILES["file"]["name"].'" wurde erfolgreich hochgeladen!'));
	return;
}

function join_paths() {
	$paths = array();

	foreach (func_get_args() as $arg) {
		if ($arg !== '') { $paths[] = $arg; }
	}

	return preg_replace('#/+#','/',join('/', $paths));
}
?>