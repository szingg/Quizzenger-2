<?php
/*	@author Simon Zingg
 *	This script uploads attachment to the defined path and checks for extensions, size and if already exists.
*/
class FileUpload{
	var $files;
	function __construct($filesParam){
		$this->files = $filesParam;
	}
	
	public function processFileUpload(){
		require_once("/../../includes/config.php");
		
		//check file uploaded
		if(! isset($this->files["file"]["type"])){
			$this->sendResponse('error', 'Upload fehlgeschlagen.');
			return;
		}
		//check file error
		if($this->files["file"]["error"] > 0){
			$this->sendResponse('error', $this->files["file"]["errorMessage"]);
			return;
		}
		
		$temporary = explode(".", $this->files["file"]["name"]);
		$file_extension = end($temporary);
		
		$extensions = explode(",", ATTACHMENT_ALLOWED_EXTENSIONS);
		$extensions = array_map('trim', $extensions);
		
		$containsExtension = false;
		$validExtension = false;
		foreach ( $extensions as $extension ) {
			//check for valid extension
			if(strpos($extension, $this->files["file"]["type"]) !== false){
				$validExtension = true;
			}
			//check file contains extension
			if(strpos($extension, $file_extension) !== false){
				$containsExtension = true;
			}
		}
	
	if (!$validExtension || !$containsExtension){
			$this->sendResponse('error', 'Das Format der Datei ist ungültig.');
			return;
		}
		
		//check for valid size
		$validSize = ($this->files["file"]["size"] < (MAX_ATTACHMENT_SIZE_KByte*1024));
		if(!$validSize){
			$this->sendResponse('error', 'Die Datei ist zu gross. Maximale Grösse: '.MAX_ATTACHMENT_SIZE_KByte.' KByte');
			return;
		}
		
		$sourcePath = $this->files['file']['tmp_name']; // Storing source path of the file in a variable
		$path = getcwd();
		//$targetDir = $this->join_paths($path, "/../../", ATTACHMENT_PATH, 'temp');
		$targetDir = $this->join_paths($path, ATTACHMENT_PATH, 'temp');
		$targetPath = $this->join_paths($targetDir, $this->files["file"]["name"]); // Target path where file is to be stored
		$fileExists = file_exists($targetPath);
		//check file exists
		if (file_exists($targetPath)) {
			$this->sendResponse('error', 'Die Datei "'.$this->files["file"]["name"].'" existiert bereits.');
			return;
		}
		
		//move uploaded file to temp path	
		if(! file_exists($targetDir)) { mkdir($targetDir, 0777, true); }
		move_uploaded_file($sourcePath,$targetPath);
		
		//send success message
		$this->sendResponse('success', 'Die Datei "'.$this->files["file"]["name"].'" wurde erfolgreich hochgeladen!');
		return;
	}

	private function sendResponse($result, $message){
		header('Content-Type: application/json');
		echo json_encode(array('result' => $result, 'message' => $message));
	}
	
	private function join_paths() {
		$paths = array();

		foreach (func_get_args() as $arg) {
			if ($arg !== '') { $paths[] = $arg; }
		}

		return preg_replace('#/+#','/',join('/', $paths));
	}
}
?>