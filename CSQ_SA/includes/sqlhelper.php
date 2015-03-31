<?php

class SqlHelper {
	private $mysqli;
	private $logger;

	public function __construct($logP) {
		$this->logger = $logP;

		$this->mysqli = new mysqli ( dbhost, dbuser, dbpassword, db, dbport );
		if (mysqli_connect_errno ()) {
			printf ( "Connect failed: %s\n", mysqli_connect_error () );
			exit ();
		}

		if (!$this->mysqli->set_charset("utf8")) {
			$this->logger->log("sqlhelper failed to set utf8 charset, error:".$this->mysqli->error , FileLogger::FATAL);
			header('Location: ./index.php?view=error&err=err_db_query_failed');
			die();

		}

	}

	public function database() {
		return $this->mysqli;
	}

	public function error(){
		return $this->mysqli->error;
	}

	public function prepare($query){
		return $this->mysqli->prepare($query);
	}

	public function __destruct(){
		mysqli_close($this->mysqli);
	}

	// Example:
	// statement = "INSERT INTO question (type, questiontext, user_id, category_id) VALUES (?, ?, ?, ?)"
	// a_param_type "ssii"
	// a_bind_params {$datatypes, $type, $questiontext,$userID,$categoryID}

	public function s_query($statement,$a_param_type, $a_bind_params,$rowCheck=false){
		return $this->doStatement($statement,$a_param_type, $a_bind_params,$rowCheck,"query");
	}

	public function s_insert($statement,$a_param_type, $a_bind_params){
		return $this->doStatement($statement,$a_param_type, $a_bind_params,false,"insert");
	}

	public function doStatement($statement,$a_param_type, $a_bind_params,$rowCheck=false,$type){

		if ($stmt = $this->mysqli->prepare ($statement)) {

			// Following is needed, as bind_params doesn't take an array only varargs
			// Source: http://www.pontikis.net/blog/dynamically-bind_param-array-mysqli , slightly modified for our needs
			// --- start ---
			$a_params = array();
			$param_type = '';
			$n = (count($a_param_type)==null)?0:count($a_param_type);
			for($i = 0; $i < $n; $i++) {
				$param_type .= $a_param_type[$i];
			}
			$a_params[] = & $param_type;
			for($i = 0; $i < $n; $i++) {
				$a_params[] = & $a_bind_params[$i];
			}
			if($n!=0){ // only if there are parameters to bind
				call_user_func_array(array($stmt, 'bind_param'), $a_params);
			}
			// --- done ---
			$resultExecute = $stmt->execute ();
			if (!$resultExecute ) {
				$this->logger->log ( "Error trying to execute statement. Caller:".$this->getCaller(3)." -  SQL Error: " . $this->mysqli->error, Logger::ERROR );
				header ( 'Location: ./index.php?view=error&err=err_db_query_failed' );
				die ();
			} else {
				if($type=="query"){
					$result = $stmt->get_result(); // needs MySQLND (native driver)

					if( ($rowCheck && $result->num_rows == 0)){
						if($this->mysqli->error==""){
							$error = "Query didn't return any results";
						}else{
							$error = $this->mysqli->error;
						}
						$this->logger->log($this->getCaller(3)." failed to get result. SQL Error: ".$error ,Logger::ERROR);
						header('Location: ./index.php?view=error&err=err_db_query_failed');
						die();
					}
					$stmt -> close();
					return $result;
				}elseif($type="insert"){
					return $stmt->insert_id;
				}
			}
		} else {
			$this->logger->log ( "Error trying to prepare statement. Caller:".$this->getCaller(3)." -  SQL Error: " . $this->mysqli->error, Logger::ERROR );
			header ( 'Location: ./index.php?view=error&err=err_db_query_failed' );
			die ();
		}

	}

	public function query($query,$rowCheck=false) {
		$queryResult = $this->mysqli->query ( $query );
		if(!$queryResult || ($rowCheck && $queryResult->num_rows == 0)){
			$callers=debug_backtrace();
			$firstCallerInfo= $callers[1]['class']."/".$callers[1]['function']." on line ".$callers[1]['line'];
			if($this->mysqli->error==""){
				$error = "Query didn't return any results";
			}else{
				$error = $this->mysqli->error;
			}
			$this->logger->log($this->getCaller(2)." failed to get result. SQL Error: ".$error ,Logger::ERROR);
			header('Location: ./index.php?view=error&err=err_db_query_failed');
			die();
		}

		return $queryResult;
	}

	public function getCaller($depth){
		$callers=debug_backtrace();
		return $callers[$depth]['class']."/".$callers[$depth]['function']." on line ".$callers[$depth]['line'];
	}

	public function getSingleResult($result){
		return $result->fetch_assoc();
	}

	public function getQueryResultArray($result) {
		$resultArray = array ();
		while ( $row = $result->fetch_assoc () ) {
			array_push ( $resultArray, $row );
		}
		return $resultArray;
	}

	public function sqlValueBuilder() {
		$returnString = "(`";
		$returnString = implode ( "`,`", func_get_args () );
		$returnString = "`)";
	}
}

?>
