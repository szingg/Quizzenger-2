<?php
class QuizModel {
	var $entries;
	var $mysqli;
	var $logger;
	var $questionCorrectValue;
	
	function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
		$this->questionCorrectValue = 100;
	}
	
	function getNewSessionId($quiz_id){
		$this->logger->log ( "Getting New Quiz Session for ID :".$quiz_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO quizsession (quiz_id) VALUES (?)",array('i'),array($quiz_id));
	} 
	
	function copyQuiz($user_id, $quiz_id){
		$this->logger->log ("User with ID ". $user_id ." copies Quiz with ID ". $quiz_id);
		
		$questions = $this->getQuestionArray($quiz_id);
		$quizName = $this->getQuizName($quiz_id);
		$newQuizId = $this->mysqli->s_insert("INSERT INTO quiz (name, user_id) VALUES ( ?, ?)",array('s','i'),array($quizName,$user_id));
		foreach($questions as $question){
			$weight = $this->getWeightOfQuestionInQuiz($question,$quiz_id);
			$this->mysqli->s_insert("INSERT INTO quiztoquestion (question_id, quiz_id, weight) VALUES ( ?, ?, ?)",array('i','i','i'),array($question, $newQuizId, $weight));
		}
		return $newQuizId;
	}
	
	function saveGeneratedQuiz($user_id, $questions){
		$this->logger->log ( "User saving generated Quiz", Logger::INFO );
		$quiz_id=$this->createQuiz("Generiertes Quiz - ".date('Y-m-d H:i:s'), $_SESSION ['user_id']);
		foreach ($questions as $question){
			$this->addQuestionToQuiz($quiz_id, $question);
		}
		return $quiz_id;
	}
	
	function generateQuiz($categoryModel, $limit,$searchMode,$categories_id,$difficultyArr){
		if($searchMode=="best"){
			$orderBy = "ORDER BY rating DESC";
		}elseif($searchMode=="most"){
			$orderBy = "ORDER BY difficultycount DESC";
		}elseif($searchMode=="random"){
			$orderBy = "ORDER BY RAND()";
		}else{
			$orderBy ="";
		}
	
			
		$categories="";
		if(!is_null($categories_id)){
			if(!is_array($categories_id)){
				die("INVALID PARAMETERS (TYPE)");
			}
			$counter=0;
			foreach ($categories_id as $category_id){
				if(!is_numeric($category_id)){
					die("INVALID PARAMETERS (NOT NUMERIC)");
				}
				$categories = $categories.(($counter!=0)?" OR  ":" ")."category_id = ".$category_id;
				$counter++;
			}	
		}	

		if(is_null($difficultyArr)){
			$difficulty="";
		}else{
			if(!$categories==""){
				$difficulty=" AND (";
			}
			$counter = 0; 
			foreach ($difficultyArr as $diff){
				if($counter!=0){
					$difficulty=$difficulty." OR ";
				}
				$counter++;
				$difficulty= $difficulty." (difficulty >=".$this->difficultyLookup($diff,"from")."  AND difficulty <=".$this->difficultyLookup($diff,"to").") ";
			}
			if(!$categories==""){
				$difficulty=$difficulty.")";
			}
		}
		
		if($difficulty=="" && $categories==""){
			$where = "";
		}else{
			$where =" WHERE "; 
		}
				
		$this->logger->log ( "User creating temporary Quiz", Logger::INFO );
		
		
		$result = $this->mysqli->s_query("SELECT * FROM question ".$where.$categories.$difficulty." ".$orderBy ." LIMIT ?",array('i'),array($limit));
		$questions = $this->mysqli->getQueryResultArray ( $result );
		$questionArray = array();
		foreach($questions as $q){
			$questionArray[] = $q['id'];
		}
		return $questionArray;
	}
	

	function difficultyLookup($difficulty,$op){
		if($difficulty==0){
			return ($op=="from"?0:25);
		}elseif($difficulty==1){
			return ($op=="from"?26:50);
		}elseif($difficulty==2){
			return ($op=="from"?51:75);
		}elseif($difficulty==3){
			return ($op=="from"?76:100);
		}
		return null;
	}
	
	function getWeightOfQuestionInQuiz($question_id,$quiz_id){
		$weightRes = $this->mysqli->s_query("SELECT weight FROM quiztoquestion WHERE question_id = ? AND quiz_id=?",array('i','i'),array($question_id,$quiz_id));
		return $this->mysqli->getSingleResult($weightRes)['weight'];
	}
	function removeQuiz($quiz_id){
		$this->logger->log ( "Removing Quiz with ID :".$quiz_id, Logger::INFO );
		$result = $this->mysqli->s_query("DELETE FROM quiz WHERE id = ?",array('i'),array($quiz_id));
		return $this->mysqli->getSingleResult($result);
	}
	function getQuizName($quiz_id){
		$result = $this->mysqli->s_query("SELECT name FROM quiz WHERE `id` = ?",array('i'),array($quiz_id));
		return $this->mysqli->getSingleResult($result)['name'];
	}
	function getNumberOfPerformances($quiz_id){
		$result = $this->mysqli->s_query("SELECT COUNT( DISTINCT questionperformance.session_id ) FROM questionperformance INNER JOIN quizsession ON questionperformance.session_id=quizsession.id WHERE quizsession.quiz_id =?",array('i'),array($quiz_id));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT( DISTINCT questionperformance.session_id )"];
	}
	function getNumberOfQuestions($quiz_id) {
		$result = $this->mysqli->s_query("SELECT COUNT(*) FROM quiztoquestion WHERE `quiz_id` = ?",array('i'),array($quiz_id));
		$result=  $this->mysqli->getSingleResult($result);
		return $result ["COUNT(*)"];
	}

	function getQuizSessions($quiz_id) {
		$result = $this->mysqli->s_query("SELECT questionperformance.session_id, questionperformance.user_id FROM `questionperformance` INNER JOIN quizsession ON questionperformance.session_id=quizsession.id WHERE quizsession.quiz_id =  ? GROUP BY questionperformance.session_id, questionperformance.user_id",array('i'),array($quiz_id));		
		return $this->mysqli->getQueryResultArray ( $result );
	}
	function getSingleChoiceScore($session, $quiz_id) {
		$correctQuestions = $this->mysqli->s_query("SELECT question_id from questionperformance WHERE `session_id` =  ?  AND `questionCorrect` = ". $this->questionCorrectValue,array('i'),array($session));		
		$score = 0;
		foreach ( $correctQuestions as $question ) {
			$w = $this->mysqli->s_query("SELECT weight FROM quiztoquestion WHERE `question_id` =  ?  AND `quiz_id` = ?",array('i','i'),array($question['question_id'],$quiz_id));
			$weight = $this->mysqli->getSingleResult($w)['weight'];
			if($weight == null || $weight < 1){
				$weight = 1;
			}
			$score += $weight;
		}
		return $score;
	}
	function getMaxSingleChoiceScore($quiz_id){
		$result  = $this->mysqli->s_query("SELECT SUM(weight) AS maxScore FROM quiztoquestion WHERE `quiz_id` = ?",array('i'),array($quiz_id));
		return $this->mysqli->getSingleResult($result)['maxScore'];
	}
	function getQuizStart($session) {
		$result  = $this->mysqli->s_query("SELECT timestamp, id FROM questionperformance WHERE session_id = ?   AND timestamp = (SELECT MIN(timestamp) FROM questionperformance WHERE session_id = ?)",array('i','i'),array($session,$session));
		return $this->mysqli->getSingleResult ( $result )['timestamp'];
	}
	
	function getQuizEnd($session) {
		$result  = $this->mysqli->s_query("SELECT timestamp, id FROM questionperformance WHERE session_id = ?   AND timestamp = (SELECT MAX(timestamp) FROM questionperformance WHERE session_id = ?) ",array('i','i'),array($session,$session));
		return $this->mysqli->getSingleResult ( $result )['timestamp'];
	}

	function getAnsweredQuestions($quiz_id, $question_id){
		$result  = $this->mysqli->s_query("SELECT questionperformance.id, questionperformance.questionCorrect, quizsession.quiz_id FROM questionperformance INNER JOIN quizsession ON questionperformance.session_id=quizsession.id WHERE quizsession.quiz_id = ? AND questionperformance.question_id = ?",array('i','i'),array($quiz_id,$question_id));
		return $result->num_rows;
	}
	function getCorrectAnsweredQuestions($quiz_id, $question_id){
		$result  = $this->mysqli->s_query("SELECT questionperformance.id, questionperformance.questionCorrect, quizsession.quiz_id FROM questionperformance INNER JOIN quizsession ON questionperformance.session_id=quizsession.id WHERE quizsession.quiz_id = ? AND questionperformance.question_id = ? AND questionCorrect = ". $this->questionCorrectValue,array('i','i'),array($quiz_id,$question_id));
		return $result->num_rows;
	}
	function removeQuestionFromQuiz($quiz_id, $question_id){
		$this->logger->log ( "Removing quiztoquestion links for Quiz ID:".$quiz_id." and Question ID:".$question_id, Logger::INFO );
		return $this->mysqli->s_query("DELETE FROM quiztoquestion WHERE quiz_id =  ? AND question_id = ? ",array('i','i'),array($quiz_id,$question_id));
	}
	function getQuestionArray($quiz_id){
		$result = $this->mysqli->s_query("SELECT id, question_id FROM quiztoquestion WHERE `quiz_id` = ? ",array('i'),array($quiz_id));
		$questions = $this->mysqli->getQueryResultArray ( $result );
		$questionArray = array();
		foreach($questions as $q){
			$questionArray[] = $q['question_id'];
		}
		return $questionArray;
	}
	function getPerformances($quiz_id,$userModel) {
		$sessions = $this->getQuizSessions ( $quiz_id );
		$entries = array ();
		foreach ( $sessions as $key => $session ) {
			$entries [$key] ['username'] = $userModel->getUsernameById($session ['user_id']);
			$entries [$key] ['score'] = $this->getSingleChoiceScore ( $session ['session_id'], $quiz_id );
			$entries [$key] ['maxscore'] = $this->getMaxSingleChoiceScore($quiz_id);
			$entries [$key] ['start'] = $this->getQuizStart ( $session ['session_id'] );
			$end = $this->getQuizEnd ( $session ['session_id'] );
			$diff = date_diff(new Datetime($entries [$key] ['start']), new Datetime($end));
			$h = intval($diff->format('%a')) * 24 + intval($diff->format('%h'));
			$duration= $diff->format($h .':%I:%S');
			$duration=($duration=="0:00:00")? "Quiz nicht beendet" :$duration ;
			$entries [$key] ['duration'] =$duration;
		}
		return $entries;
	}
	function checkIfQuizIDExists($quiz_id){
		$result = $this->mysqli->s_query("SELECT * FROM quiz WHERE `id` = ? ",array('i'),array($quiz_id),true);
	}
	function getQuestionsByQuizID($quiz_id) {
		$result = $this->mysqli->s_query("SELECT * FROM quiztoquestion WHERE `quiz_id` = ? ",array('i'),array($quiz_id));
		$questions = $this->mysqli->getQueryResultArray ( $result );
		
		$entries = array();
		foreach($questions as $key => $question){
			$entries [$key] ['id'] = $question['id'];
			$entries [$key] ['question_id'] = $question['question_id'];
			$entries [$key] ['question'] = $this->getQuestionText($question['question_id']);
			$entries [$key] ['answered'] = $this->getAnsweredQuestions($quiz_id, $question['question_id']);
			$entries [$key] ['correct'] = $this->getCorrectAnsweredQuestions($quiz_id, $question['question_id']);
			$entries [$key] ['wrong'] = $entries [$key] ['answered'] - $entries [$key] ['correct'];
			$entries [$key] ['weight'] = $question['weight'];
		}
		return $entries;
	}
	
	function getQuestionText($question_id){
		$result = $this->mysqli->s_query("SELECT questiontext FROM question WHERE `id` = ?",array('i'),array($question_id));
		return $this->mysqli->getSingleResult($result)['questiontext'];
	}

	function createQuiz($quizname, $user){
		$this->logger->log ( "Creating Quiz with name: ".$quizname, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO quiz (name, user_id) VALUES ( ?, ?)",array('s','i'),array($quizname,$user));
	}
	
	function setQuizName($quiz_id,$name){
		$this->logger->log ( "Changing Quiz ID Namem, ".$quiz_id." - ".$name, Logger::INFO );
		return $this->mysqli->s_query("UPDATE quiz SET name = ? WHERE id= ?",array('s','i'),array($name,$quiz_id));
	}
	
	
	function addQuestionToQuiz($quiz_id, $question_id){
		$weigth =1;
		$this->logger->log ( "Adding Question to Quiz, Quiz ID: ".$quiz_id." and Question ID: ".$question_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO quiztoquestion (question_id, quiz_id, weight) VALUES ( ?, ?, ?)",array('i','i','i'),array($question_id,$quiz_id,$weigth));
	}
	
	public function userIDhasPermissionOnQuizID($quiz_id,$user_id){
		$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM quiz WHERE id=? AND user_id=?)",array('i','i'),array($quiz_id,$user_id));
		$result= array_values($this->mysqli->getSingleResult($result));
		if($result[0]=="1"){
			return true;
		}else{
			return false;
		}
	}
	
	
}
?>
