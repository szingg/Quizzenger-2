<?php

class ReportingModel {

	private $mysqli;
	private $logger;

	public function __construct($mysqli, $log) {
		$this->mysqli = $mysqli;
		$this->logger = $log;
	}

	public function getUserList() {
		return $this->mysqli->s_query('SELECT username FROM user ORDER BY username ASC',
			[], [], false);
	}

	public function getQuestionList() {
		return $this->mysqli->s_query('SELECT questiontext FROM question ORDER BY created ASC',
			[], [], false);
	}

	public function getAuthorList() {
		return $this->mysqli->s_query('SELECT username FROM user'
			. ' WHERE id IN (SELECT user_id FROM question)'
			. ' ORDER BY username ASC',
			[], [], false);
	}
}
?>
