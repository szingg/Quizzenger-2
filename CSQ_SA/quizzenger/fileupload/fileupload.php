<?php
/*	@author Simon Zingg
 *	this script uploads attachment to the defined path and checks for extensions, size and if already exists.
*/

	require_once("/../../includes/config.php");

	//check file uploaded
	if(! isset($_FILES["file"]["type"])){
		sendResponse('error', 'Upload fehlgeschlagen.');
		return;
	}
	//check file error
	if($_FILES["file"]["error"] > 0){
		sendResponse('error', $_FILES["file"]["errorMessage"]);
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
		sendResponse('error', 'Das Format der Datei ist ung�ltig.');
		return;
	}

	//check for valid size
	$validSize = ($_FILES["file"]["size"] < (MAX_ATTACHMENT_SIZE_KByte*1024));
	if(!$validSize){
		sendResponse('error', 'Die Datei ist zu gross. Maximale Gr�sse: '.MAX_ATTACHMENT_SIZE_KByte.' KByte');
		return;
	}

	$sourcePath = $_FILES['file']['tmp_name']; // Storing source path of the file in a variable
	$path = getcwd();
	$targetDir = join_paths($path, "/../../", ATTACHMENT_PATH, 'temp');
	$targetPath = join_paths($targetDir, $_FILES["file"]["name"]); // Target path where file is to be stored
	$fileExists = file_exists($targetPath);
	//check file exists
	if (file_exists($targetPath)) {
		sendResponse('error', 'Die Datei "'.$_FILES["file"]["name"].'" existiert bereits.');
		return;
	}

	//move uploaded file to temp path
	if(! file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
	move_uploaded_file($sourcePath,$targetPath);

	//send success message
	sendResponse('success', 'Die Datei "'.$_FILES["file"]["name"].'" wurde erfolgreich hochgeladen!');
	return;

	function sendResponse($result, $message){
		header('Content-Type: application/json');
		echo json_encode(array('result' => $result, 'message' => $message));
	}

	function join_paths() {
		$paths = array();

		foreach (func_get_args() as $arg) {
			if ($arg !== '') { $paths[] = $arg; }
		}

		return preg_replace('#/+#','/',join('/', $paths));
	}
?>