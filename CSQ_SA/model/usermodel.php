<?php
use quizzenger\messages\MessageQueue as MessageQueue;

class UserModel {

	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function getUserByID($id){
		$result = $this->mysqli->s_query("SELECT * FROM user WHERE id=?",array('i'),array($id));
		return $this->mysqli->getSingleResult($result);
	}

	public function isSuperuser($user_id){
		$result = $this->mysqli->s_query("SELECT superuser FROM user WHERE id=?",array('i'),array($user_id));
		return $this->mysqli->getSingleResult($result)['superuser']  ? true : false;
	}

	public function getQuestionAbsolvedCount($user_id){
		$result = $this->mysqli->s_query("SELECT COUNT(DISTINCT question_id) FROM questionperformance WHERE user_id=?",array('i'),array($user_id));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(DISTINCT question_id)"];
	}

	public function userIsModeratorOfCategory($user_id, $category_id){
		if($this->isSuperuser($user_id)){
			return true;
		}
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM moderation WHERE user_id=? AND category_id=?",array('i', 'i'),array($user_id, $category_id));
		return ($this->mysqli->getSingleResult($result)['COUNT(*)']) > 0;
	}

	public function processChangepassword($password) {
		if (!isset ($GLOBALS ['loggedin'] ) || ! $GLOBALS ['loggedin']) { // only logged in users
			header ( 'Location: ./index.php' );
			die();
		}

		if (!is_null($password)) {
			$password = hash ( 'sha512', $password );
			$changepasswordResult = $this->changePassword( $password, $this->mysqli );
			if($changepasswordResult){
				$this->logger->log ( "User changed password sucessfully ", Logger::INFO );
				header ( 'Location: ./index.php?info=mes_passwordchange_success' );
				die();
			}else{
				$this->logger->log ( "Something went wrong when user tried to change password ", Logger::WARNING );
				header ( 'Location: ./index.php?view=error&err=err_db_query_failed' );
				die();
			}
		}
	}

	public function getUserInactiveStateByID($id){
		return $this->mysqli->s_query("SELECT EXISTS (SELECT inactive FROM user WHERE id=?",array('i'),array($id),true);
	}

	public function setUserInactiveByID($id){
		if($_SESSION['superuser']){
			$this->logger->log ( "Superuser setting user inactive with id=".$id, Logger::INFO );
			return $this->mysqli->s_query("UPDATE user SET inactive='1', username=?  WHERE id=?",array('s','i'),array($this->getUserByID($id)['username'].USER_INACTIVE_NAME_ADDITION,$id));
		}else{
			$this->logger->log ( "Non superuser tried to set user inactive!", Logger::WARNING );
			return -1;
		}
	}

	public function getUsernameByID($id){
		$user= $this->getUserByID($id);
		$username = $user['username'];
		return $username;
	}

	public function changePassword($pwHash, $mysqli){
		$random_salt = hash ( 'sha512', uniqid ( mt_rand (), true ) );
		$passwordForDB = hash ( 'sha512', $pwHash . $random_salt );

		if ($stmt = $mysqli->prepare ( "UPDATE user SET password = ? , salt = ? WHERE id = ?" )) {
			$stmt->bind_param('ssi',$passwordForDB,$random_salt,$_SESSION ['user_id']);
			if($stmt->execute ()){
				$stmt->close();
				return true;
			}else{
				$stmt->close();
				return false;
			}
		} else {
			return false;
		}
	}

	public function getAchievementList($userId) {
		return $this->mysqli->s_query('SELECT name, sort_order, description, image, bonus_score,'
			. ' (SELECT CASE WHEN EXISTS'
				. ' (SELECT * FROM userachievement WHERE userachievement.achievement_id=id AND userachievement.user_id=?)'
				. ' THEN 1 ELSE 0 END) as achieved'
			. ' FROM achievement'
			. ' ORDER BY type ASC, sort_order ASC, name ASC', ['i'], [$userId], false);
	}

	public function getRankList($userId) {
		return $this->mysqli->s_query('SELECT name, threshold, image'
			. ' FROM rank'
			. ' ORDER BY threshold ASC',
			[], [], false);
	}
}
?>
