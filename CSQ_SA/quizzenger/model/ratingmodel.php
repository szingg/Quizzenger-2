<?php
use \quizzenger\model\ModelCollection as ModelCollection;

class RatingModel {
	private $mysqli;
	private $logger;

	public function __construct($mysqliP, $logP) {
		$this->mysqli = $mysqliP;
		$this->logger = $logP;
	}

	public function removeComment($id,$moderator_name,$explanation){
		$this->logger->log ( "[MOD] Removing Comment ".$id, Logger::INFO );
		return $this->mysqli->s_query("UPDATE rating SET comment='Kommentar entfernt durch Moderator ".$moderator_name.". Begründung: ".$explanation."' WHERE id=?",array('i'),array($id));
	}
	public function getAllRatingsByQuestionID($id){
		$result = $this->mysqli->s_query("SELECT * FROM rating WHERE question_id=? AND parent IS NULL",array('i'),array($id));
		return $result;
	}
	public function getAllCommentsByQuestionID($id){
		$result = $this->mysqli->s_query("SELECT * FROM rating WHERE question_id=? AND parent IS NOT NULL",array('i'),array($id));
		return $result;
	}

	/**
	 * @return Returns id of inserted rating.
	 */
	public function newRating($question_id,$stars,$comment,$parent){

		$stars = ($stars=="null")? null : $stars ;
		$comment = ($comment=="null")? null : $comment ;
		$this->logger->log ( "PARENT".$parent, Logger::INFO );
		$parent = ($parent=="null" || $parent=="[object Window]")? null : $parent ;
		$user_id=$_SESSION['user_id'];

		if($stars!=null){
			$ratingResult =$this->mysqli->s_query("SELECT rating,ratingcount FROM question WHERE id=? ",array('i'),array($question_id));
			$ratingResult = $this->mysqli->getSingleResult($ratingResult);
			$rating= $ratingResult['rating'];
			$rating_count =$ratingResult['ratingcount'];
			$new_rating = ((($rating_count)*($rating))+($stars))/(($rating_count)+(1));
			$new_rating_count = $rating_count+1;
			$this->logger->log ( "Updating question rating fields for question with id:".$question_id, Logger::INFO );
			$this->mysqli->s_query("UPDATE question SET rating=?, ratingcount=? WHERE id=? ",array('d','i','i'),array($new_rating,$new_rating_count,$question_id));
		}
		$this->logger->log ( "Creating Rating for Question with ID :".$question_id." by ".$user_id, Logger::INFO );
		return $this->mysqli->s_insert("INSERT INTO rating (user_id,question_id,stars,comment,parent) VALUES (?,?,?,?,?)",array('i','i','i','s','i'),array($user_id,$question_id,$stars,$comment,$parent));
	}

	public function userHasAlreadyRated($question_id,$user_id){
		$result = $this->mysqli->s_query("SELECT EXISTS ( SELECT 1 FROM rating WHERE question_id=? AND user_id=? AND parent IS NULL)",array('i','i'),array($question_id,$user_id));
		$result= array_values($this->mysqli->getSingleResult($result));
		return ($result[0]=="1");
	}

	public function enrichRatingsWithAuthorName($ratings){
		$userModel = ModelCollection::userModel(); $moderationModel = ModelCollection::moderationModel();
		$questionModel = ModelCollection::questionModel(); $reportModel = ModelCollection::reportModel();
		$entries = array ();
		foreach ( $ratings as $key => $rating ) {
			$question = $questionModel->getQuestion( $rating ['question_id'] );
			break;
		}
		foreach ( $ratings as $key => $rating ) {
			$entries [$key] ['id'] = $rating ['id'];
			$entries [$key] ['user_id'] = $rating ['user_id'];
			$entries [$key] ['question_id'] = $rating ['question_id'];
			$entries [$key] ['stars'] = $rating ['stars'];
			$entries [$key] ['comment'] = $rating ['comment'];
			$entries [$key] ['author'] = $userModel->getUsernameByID( $rating['user_id'] );
			$entries [$key] ['ismod'] = $moderationModel->isModerator( $rating['user_id'],$question['category_id'] );
			$entries [$key] ['issuperuser'] = $userModel->isSuperuser( $rating['user_id']);
			$entries [$key] ['alreadyreported'] = $reportModel -> checkIfUserAlreadyDoneReport("rating", $rating['id'], $_SESSION['user_id']) ;
			$entries [$key] ['created'] = $rating ['created'];
			$entries [$key] ['parent'] = $rating ['parent'];
		}
		return $entries;
	}
}

?>