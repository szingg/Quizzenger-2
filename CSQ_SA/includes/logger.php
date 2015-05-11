<?php
class Logger {
	protected $fileHandle;
	protected $timeFormat = 'd.m.Y - H:i:s';
	protected $today;

	const INFO = '[INFO]';
	const WARNING = '[WARNING]';
	const ERROR = '[ERROR]';
	const FATAL = '[FATAL]';

	public function __construct() {
		$today = date("Y-m-d");
		if(! file_exists(LOGPATH)) { mkdir(LOGPATH, 0777, true); }
		$filePath= LOGPATH . DIRECTORY_SEPARATOR . $today . '.log';
		if (is_null ( $this->fileHandle )) {
			$this->openLogFile ($filePath);
		}
	}

	public function log($message, $messageLevel = Logger::INFO) {
		if(!LOGGING_ACTIVE){
			return;
		}
		if ($this->fileHandle == NULL) {
			die ( "[logger.php] Couldn't open logfile $logfile ( handle = null ). Do you have permission / does it exist?" );
		}
		if (! is_string ( $message )) {
			$this->writeToLogFile("[logger.php] Something else than a string was passed to log message! This shouldn't happen . . .");
			return;
		}
		if ($messageLevel != Logger::INFO && $messageLevel != Logger::WARNING && $messageLevel != Logger::ERROR && $messageLevel != Logger::FATAL) {
			$this->writeToLogFile("[logger.php] Unkown or no message type given" );
			return;
		}
		$userName = (isset($_SESSION ['username']))? $_SESSION ['username'] : "not logged in";
		$this->writeToLogFile ( "[" . date ( $this->timeFormat ) . "]" . $messageLevel . " - " . $message. " [IP: ".$_SERVER['REMOTE_ADDR']." - User: ".$userName."]");
	}

	private function writeToLogFile($message) {
		flock ( $this->fileHandle, LOCK_EX );
		fwrite ( $this->fileHandle, $message . PHP_EOL );
		flock ( $this->fileHandle, LOCK_UN );
	}

	protected function closeLogFile() {
		if ($this->fileHandle != NULL) {
			fclose ( $this->fileHandle );
			$this->fileHandle = NULL;
		}
	}

	private function deleteLogFiles() {
		// Unlimitted number of log files.
		if(MAX_LOG_DAYS <= 0)
			return;

		$iterator = new DirectoryIterator(LOGPATH);
		foreach($iterator as $file) {
			if($file->isDir() || $file->isDot() || $file->getExtension() != 'log')
				continue;

			if($file->getMTime() < (time() - (24 * 60 * 60 * MAX_LOG_DAYS))) {
				unlink($file->getPathname());
			}
		}
	}

	public function openLogFile($logFile) {
		$this->closeLogFile (); // we don't want it opened multiple times

		// If the file does not exist, it suggests that it is a new day,
		// so use this chance to delete old log files.
		if(!file_exists($logFile)) {
			$this->deleteLogFiles();
		}

		if (! $this->fileHandle = fopen ( $logFile, 'a+' )) {
			die ( '[logger.php] Failed to get file handle! ('.$logFile.')' );
		}
	}
	public function __destruct() {
		$this->closeLogFile ();
	}
}

?>
