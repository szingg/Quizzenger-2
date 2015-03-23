<?php

class ReportingModel {

	private $mysqli;
	private $logger;

	public function __construct($mysqli, $log) {
		$this->mysqli = $mysqli;
		$this->logger = $log;
	}

	public function getUserList($userId) {
		return $this->mysqli->s_query('SELECT username FROM user',
			[], [], false);
	}
}
?>
