<?php
 
class QuestionModel{

	var $mysqli;
	var $logger;
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}
	
	function getNewestQuestion(){
		$result = $this->mysqli->s_query("SELECT * FROM question ORDER BY id DESC LIMIT 0,1",array(),array(),true);
		return $this->mysqli->getSingleResult($result);
	}
	

	function answerExists($session, $question_id, $user_id){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM questionperformance WHERE session_id=? AND question_id=? AND user_id=?",array('i','i','i'),array($session,$question_id,$user_id));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}
	
	function setWeight($id, $weight){
		$this->logger->log ( "Editing Weigth(".$weight.") for QuizToQuestion ID: ".$id, Logger::INFO );
		return $this->mysqli->s_query("UPDATE quiztoquestion SET weight=? WHERE id=?",array('i','i'),array($weight, $id));
	}
	
	public function getQuestion($id){
		$result = $this->mysqli->s_query("SELECT * FROM question WHERE id=?",array('i'),array($id),true);
		return $this->mysqli->getSingleResult($result);
	}
	

	

// 	public function getMeanDifficultyByQuestionID($question_id){
// 		$difficultyResult =$this->mysqli->s_query("SELECT difficulty FROM question WHERE id=? ",array('i'),array($question_id));
// 		$difficulty = $this->mysqli->getSingleResult($difficultyResult)['difficulty'];
// 		$difficulty= number_format($rating, 1, ".", "." );
// 		$difficulty = round($difficulty);
// 		return $difficulty;
// 	}
	
	
	function InsertQuestionPerformance($question_id, $user_id, $questionCorrect, $session){	
		$difficultyResult =$this->mysqli->s_query("SELECT difficulty,difficultycount FROM question WHERE id=? ",array('i'),array($question_id));
		$difficultyResult = $this->mysqli->getSingleResult($difficultyResult);
		$difficulty= $difficultyResult['difficulty'];
		$difficulty_count =$difficultyResult['difficultycount'];
		$new_difficulty = ((($difficulty_count)*($difficulty))+($questionCorrect))/(($difficulty_count)+(1));
		$new_difficulty_count = $difficulty_count+1;
		$this->logger->log ( "Updating question difficulty fields for question with id:".$question_id, Logger::INFO );
		$this->mysqli->s_query("UPDATE question SET difficulty=?, difficultycount=? WHERE id=? ",array('d','i','i'),array($new_difficulty,$new_difficulty_count,$question_id));

		$this->logger->log ( "Adding new QuestionPerformance for Question ID:".$question_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO questionperformance (question_id, user_id, questionCorrect, session_id) VALUES (?, ?, ?, ?)",array('i','i','i','i'),array($question_id, $user_id, $questionCorrect, $session));
	}
		
	
	private function checkForMissingParametersOpQwA($chosenCategory,$operation,$categoryModel){
		$missingParam = false;
		for ($i = 1; $i <= SINGLECHOICE_ANSWER_COUNT; $i++){
			if(!isset($_POST ['opquestion_form_answer'.$i]) || ((isset($_POST ['opquestion_form_answer'.$i])) &&  strlen($_POST['opquestion_form_answer'.$i])<=0)){
				$missingParam=true;
			}
		}
		if(!isset($_POST ['opquestion_form_questionText']) || ((isset($_POST ['opquestion_form_questionText'])) &&  strlen($_POST['opquestion_form_questionText'])<=0)){
			$missingParam=true;
		}
		if(! isset ($_POST['opquestion_form_questionType'],  $_POST ['opquestion_form_correctness'])){
			$missingParam=true;
		}
		if(! isset ($_POST ['opquestion_form_attachmentOld'],$_POST ['opquestion_form_attachmentTempFileName'],  $_POST ['opquestion_form_attachment'], $_POST ['opquestion_form_attachmentLocal'])){
			$missingParam=true;
		}
		
		if($operation =="new" && $chosenCategory && sizeof($categoryModel->getChildren($chosenCategory))!=0){
			$missingParam=true; // CHOSEN CAT WASNT A SUB CAT
		}
		if($operation=="edit"){
			if(! isset ( $_POST['opquestion_form_question_id'])){
				$missingParam=true;
			}
		}
		if($missingParam){
			header ( 'Location: ./index.php?view=error&err=err_missing_input' );
			die ();
		}
	}
	public function opQuestionWithAnswers($answerModel,$categoryModel, $tagModel,$operation,$chosenCategory){
		if (!isset ( $GLOBALS ['loggedin'] ) || !$GLOBALS ['loggedin']) {
			header ( 'Location: ./index.php?view=login' );
			die ();
		}
		
		$this->checkForMissingParametersOpQwA($chosenCategory,$operation,$categoryModel);
		
		if ($_POST['opquestion_form_questionType'] == SINGLECHOICE_TYPE){
			$type = $_POST ['opquestion_form_questionType'];
			if($operation=="new"){
				
				$questionID=$this->newQuestion($type,$_POST ['opquestion_form_questionText'],$_SESSION['user_id'],$chosenCategory, $_POST ['opquestion_form_attachment'],$_POST ['opquestion_form_attachmentLocal']);
				//moveTempFile
				if($_POST ['opquestion_form_attachmentLocal'] =='1'){
					$success = $this->moveTempFile($_POST['opquestion_form_attachmentTempFileName'], $questionID.'.'.$_POST['opquestion_form_attachment']);
					if($success == false){
						$this->logger->log ( "Attachment could not be moved", Logger::WARNING );
					}
				}			
				//remove all files in temp dir
				$this->removeAllFilesInTempDir();
				//insert all Answers to Db
				for ($i = 1; $i <= SINGLECHOICE_ANSWER_COUNT; $i++) {
					if($_POST ['opquestion_form_correctness'] == $i){
						$correctnessOfAnswer=100;
					} else {
						$correctnessOfAnswer=0;
					}
					$answerModel->newAnswer($correctnessOfAnswer,$_POST ['opquestion_form_answer'.$i],$_POST['opquestion_form_answerexplanation'.$i], $questionID);
				}
			}elseif ($operation=="edit"){	
				$questionID=$this->editQuestion($type,$_POST['opquestion_form_questionText'],$_SESSION['user_id'],$_POST['opquestion_form_question_id'], $_POST ['opquestion_form_attachment'],$_POST ['opquestion_form_attachmentLocal']);
				//moveTempFile
				if($_POST ['opquestion_form_attachmentLocal'] =='1' && $_POST ['opquestion_form_attachmentTempFileName'] != $_POST ['opquestion_form_attachmentOld']){
					$this->removeAttachment($questionID.'.'.$_POST ['opquestion_form_attachmentOld']);
					$success = $this->moveTempFile($_POST['opquestion_form_attachmentTempFileName'], $questionID.'.'.$_POST['opquestion_form_attachment']);
					if($success == false){
						$this->logger->log ( "Attachment could not be moved", Logger::WARNING );
					}
				}
				//remove all files in temp dir
				$this->removeAllFilesInTempDir();
				//edit answers
				$answers = $answerModel->getAnswersByQuestionID ($_POST['opquestion_form_question_id'] );
				$i=0;
				foreach ( $answers as $answer){
					$i=$i+1;
					if($_POST ['opquestion_form_correctness'] == $i){
						$correctnessOfAnswer=100;
					} else {
						$correctnessOfAnswer=0;
					}
					if($operation=="edit"){
						$answerModel->editAnswer($correctnessOfAnswer,$_POST ['opquestion_form_answer'.$i],$_POST['opquestion_form_answerexplanation'.$i], $answer['id']);
					} else {
						$answerModel->newAnswer($correctnessOfAnswer,$_POST ['opquestion_form_answer'.$i],$_POST['opquestion_form_answerexplanation'.$i], $questionID);
					}					
				}
			}
			if($operation=="edit"){
				$tagModel->removeAllTagsOfQuestionById($_POST['opquestion_form_question_id']); // delete all and readd below. otherwise its way too complicated and not really faster			
			}
			$this->handleNewTagCreation($questionID,$operation,$tagModel);

			if($operation=="new"){
				return $questionID;
			}
			return;
		}
		$this->logger->log ( "Invalid questionType used in questionmodel", Logger::WARNING );
		header ( 'Location: ./index.php?view=error&err=err_db_query_failed' );
		die();
	}
	
	/**
	 * Moves a file from the temporary attachment directory to the attachment directory
	 * @param $oldFilename filename used in the temp dir
	 * @param $newFilename filename used in the attachment dir
	 * @return bool Returns true on success or false on failure.
	 */
	private function moveTempFile($oldFilename, $newFilename){
		$path = getcwd();
		$targetDir = $this->join_paths($path, ATTACHMENT_PATH);
		$sourceDir = $this->join_paths($targetDir, 'temp');
		$targetFile = $this->join_paths($targetDir, $newFilename);
		$sourceFile = realpath($this->join_paths($sourceDir, $oldFilename));
		//check if sourceFile is in tempDir. This check prevents xss 
		if($sourceDir==false || dirname($sourceFile) !== realpath($sourceDir)) return false;
		return rename($sourceFile, $targetFile);
	}
	
	/**
	 * Removes a specific files in the attachment directory
	 * @param $file attachment/file to delete
	 * @return bool Returns true on success or false on failure. 
	 */
	private function removeAttachment($file){
		$path = getcwd();
		$targetPath = $this->join_paths($path, ATTACHMENT_PATH, $file);
		return unlink($targetPath);
	}
	
	/**
	 * Removes all files in the temporary attachment directory
	 * @return no return value
	 */
	private function removeAllFilesInTempDir(){
		$path = getcwd();
		$targetDir = $this->join_paths($path, ATTACHMENT_PATH);
		$sourceDir = $this->join_paths($targetDir, 'temp', '*');
		array_map('unlink', glob($sourceDir));
	}
	/**
	 * Joins multiple path-fragments to a single path
	 * @param  Comma-separated list of directory- and file-fragments 
	 * @return Returns the joined path
	*/
	private function join_paths() {
		$paths = array();
	
		foreach (func_get_args() as $arg) {
			if ($arg !== '') { $paths[] = $arg; }
		}
		return preg_replace('#/+#','/',join('/', $paths));
	}
	
	private function handleNewTagCreation($questionID,$operation,$tagModel){
		if(isset($_POST['opquestion_form_tags'])){
			if(!empty($_POST['opquestion_form_tags'])){
				$tags = explode(",", $_POST['opquestion_form_tags']);
				foreach ($tags as $tag){
					if($operation=="new"){
						$tagModel->newTag($tag,$questionID);							
					}elseif($operation=="edit"){
						$tagModel->newTag($tag,$_POST['opquestion_form_question_id']);
					}
				}
			}
		}		
	}
	
	public function userIsAuthorOfQuestion($user_id, $question_id){
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM question WHERE id=? AND user_id=?",array('i','i'),array($question_id,$user_id));
		return ($this->mysqli->getSingleResult($result)['COUNT(*)']) > 0;
	}
	
	public function userIsModeratorOfQuestion($user_id, $question_id){
		$category = $this->getCategoryOfQuestion($question_id);
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM moderation WHERE user_id=? AND category_id=?",array('i', 'i'),array($user_id, $category));
		return ($this->mysqli->getSingleResult($result)['COUNT(*)']) > 0;
	}
	
	public function getCategoryOfQuestion($question_id){
		$result = $this->mysqli->s_query("SELECT category_id FROM question WHERE id=?", array('i'), array($question_id));
		return $this->mysqli->getSingleResult($result)['category_id'];
	}
	 
	public function userIDhasPermissionOnQuestionID($question_id,$user_id){
		$isModerator = $this->userIsModeratorOfQuestion($user_id, $question_id);
		$isAuthor = $this->userIsAuthorOfQuestion($user_id, $question_id);
		$isSuperuser = $this->isSuperuser($user_id);
		return ($isAuthor || $isModerator || $isSuperuser);
	}
	
	
	function isSuperuser($user_id){
		$result = $this->mysqli->s_query("SELECT superuser FROM user WHERE id=?",array('i'),array($user_id));
		return $this->mysqli->getSingleResult($result)['superuser']  ? true : false;
	}
	

	public function newQuestionHistory($question_id,$user_id,$action){
		$this->logger->log ( "Creating new Questionhistory for Question ID: ".$question_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO questionhistory (question_id,user_id,action) VALUES (?,?,?)",array('i','i','s'),array($question_id,$user_id,$action));
	}
	
	public function getHistoryForQuestionByID($question_id){
		$result = $this->mysqli->s_query("SELECT * FROM questionhistory LEFT JOIN user ON user.id=questionhistory.user_id WHERE question_id=? ORDER BY questionhistory.timestamp DESC",array('i'),array($question_id));
		return $this->mysqli->getQueryResultArray($result);
	}
	
	public function getNewestHistoryOfAllUserQuestionsByUserID($user_id){
		$result = $this->mysqli->s_query("SELECT * FROM questionhistory LEFT JOIN question ON questionhistory.question_id = question.id LEFT JOIN user ON user.id=questionhistory.user_id WHERE question.user_id=? ORDER BY questionhistory.timestamp DESC LIMIT ".QUESTIONHISTORY_NEWEST_SHOWNCOUNT,array('i'),array($user_id));
		return $this->mysqli->getQueryResultArray($result);
	}
	
	public function newQuestion($type, $questiontext, $userID, $categoryID, $attachment, $attachment_local){
		$this->logger->log ( "Creating Question with ID", Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO question (type, questiontext, user_id, category_id,created,attachment,attachment_local) VALUES (?, ?, ?, ?, ?, ?, ?)",array('s', 's','i','i','s','s','i'),array($type,$questiontext,$userID,$categoryID,null,$attachment,$attachment_local));		
	}

	public function removeQuestion($question_id){
		if($this->userIDhasPermissionOnQuestionID($question_id,$_SESSION ['user_id'])){ 
			$this->logger->log ( "Removing Question with ID :".$question_id, Logger::INFO );
			$question = $this->getQuestion($question_id);
			$this->logger->log ( "Decrement userscore (5) in category ". $question['category_id'] ." and user_id ". $question['user_id'] , Logger::INFO );
			$this->mysqli->s_query("UPDATE userscore SET score=score-". QUESTION_CREATED_SCORE ." WHERE user_id=? AND category_id=?", array('i', 'i'), array($question['user_id'], $question['category_id']));
			return $this->mysqli->s_query("DELETE FROM question WHERE id=?",array('i'),array($question_id));
		} else {
			$this->logger->log ( "Unauthorized try to remove of Question with ID :".$question_id, Logger::WARNING );
		}
	}

	public function editQuestion($type, $questiontext, $userID, $question_id, $attachment, $attachment_local){
		if($this->userIDhasPermissionOnQuestionID($question_id,$_SESSION ['user_id'])){
			$this->logger->log ( "Editing Question with ID :".$question_id, Logger::INFO );
			return $this->mysqli->s_insert("UPDATE question SET type=?, questiontext=?, user_id=?, attachment=?, attachment_local=? WHERE id=?",array('s', 's','i','s','i','i'),array($type,$questiontext,$userID,$attachment,$attachment_local,$question_id));
		}else{
			$this->logger->log ( "Unauthorized try to edit of Question with ID :".$question_id, Logger::WARNING );
		}
	}
	
}
?>