<?php

namespace quizzenger\logging {
	class LogViewer {
		public function render($logfile) {
			// Ensure that only '*.log' files from within the log directory
			// can be accessed by the Super Users. Permissions have to be
			// checked by the caller.
			$logfile = basename($logfile, '.log') . '.log';
			$filename = LOGPATH . DIRECTORY_SEPARATOR . $logfile;

			if(!file_exists($filename)) {
				return false;
			}

			header('Content-Type: text/plain');
			readfile($filename);
			return true;
		}
	} // class LogViewer
} // namespace quizzenger\logging


?>
