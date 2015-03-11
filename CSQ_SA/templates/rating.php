
<?php
	$ratings = $this->_ ['ratings'];
	$comments = $this->_ ['comments'];
	$meanRating = $this->_ ['meanRating'];
	$question_id = $this->_ ['questionID'];
	$userIsModHere= $this->_ ['userismodhere'];
	$userHasAlreadyRated= $this->_ ['useralreadyrated'];

	if(empty($ratings)){
		echo("Es wurden noch keine Kommentare oder Bewertungen abgegeben<br><br>");
	} else {
		echo("Durchschnittliche Bewertung: [".number_format(($meanRating), 1, ".", "." )."] ".createStarsString($meanRating)."<br><hr>");
		?><ul class="list-group"><?php
		foreach ($ratings as $rating){
			?>
			<li class="list-group-item">
				<?php
				echo(createRatingString($rating,$userIsModHere));
				foreach($comments as $comment){
					echo("<ul>");
					if($comment['parent']==$rating['id']){
						echo('<li class="list-group-item">'.createRatingString($comment,$userIsModHere)."</li>");
					}
					echo("</ul>");
				}
				if($GLOBALS ['loggedin']){?>
					<ul><div class="panel panel-default" id="discussioncomment<?= $rating['id']?>">
						<a data-toggle="collapse" data-target="#collapsediscussioncomment<?= $rating['id']?>" <?= $rating['id']?>" class="collapsed">
							<div class="panel-heading">
								<p class="panel-title"><span class="glyphicon glyphicon-comment"></span> Kommentieren</p>
							</div>
						</a>
						<div id="collapsediscussioncomment<?= $rating['id']?>" class="panel-collapse collapse">
							<div class="panel-body">
								<?= (createCommentForm($rating['id'],$question_id)) ?>
							</div>
						</div>
					</div></ul>
				<?php }?>
			</li><?php
		}
		?></ul><?php
	}

	if($GLOBALS ['loggedin']){
		echo(createRatingForm($question_id,$userHasAlreadyRated));
	}else{
		echo('Um an der Community teilzunehmen müssen Sie sich zuerst <a href="index.php?view=login">einloggen</a>.');
	}



	function createRatingForm($question_id,$userHasAlreadyRated){
		$fieldState=($userHasAlreadyRated)?"disabled":"";
		$buttonText=($userHasAlreadyRated)?"Sie haben bereits Bewertet":"Bewertung abschicken";
		return('
			<input type="text" class="form-control" id="rating" name="rating" placeholder="Bewertung (optional)" '.$fieldState.'/>
			<br>
			<div class="rating">
				Ihre Bewertung der Frage: &nbsp;&nbsp;
			    <input type="radio" name="rating" value="0" '.$fieldState.'/><span id="hide"></span>
			    <input type="radio" name="rating" value="1" '.$fieldState.'/><span></span>
			    <input type="radio" name="rating" value="2" '.$fieldState.'/><span></span>
			    <input type="radio" name="rating" value="3" checked '.$fieldState.'/><span></span>
			    <input type="radio" name="rating" value="4" '.$fieldState.'/><span></span>
			    <input type="radio" name="rating" value="5" '.$fieldState.'/><span></span>
			</div>
			<br>
			<div id="ratingdiv"></div>
			<button type="button" class="btn btn-primary btn-block" onclick="newRating('.$question_id.');" id="ratingFormButton" value="commentButton" '.$fieldState.'>'.$buttonText.'</button>

	');
	}

	function createCommentForm($id,$question_id){
	return('
			<input type="text" class="form-control" id="comment'.$id.'" name="comment'.$id.'" placeholder="Kommentar"  />
			<br>
			<div id="commentdiv'.$id.'"></div>
			<button type="button" class="btn btn-primary btn-block" onclick="newComment('.$question_id.','.$id.');" id="commentFormButton'.$id.'" value="commentButton" >Kommentar absenden</button>

	');
	}

	function createRatingString($rating,$userIsModHere){
		$strng='<a target="_blank" href="index.php?view=user&amp;id='.$rating['user_id'].'">';
		$modString= ($rating['ismod'])?'<img alt="Moderator" src="'.htmlspecialchars(APP_PATH).'/templates/img/moderator.png">':"" ;
		$suString= ($rating['issuperuser'])?'<img alt="Sueruser" src="'.htmlspecialchars(APP_PATH).'/templates/img/superuser.png">':"" ;


		$strng2=$rating['author'].'</a>'.$modString.$suString.' am '.$rating['created'];
		if($userIsModHere){
			$strng2=$strng2.'<button type="button" class="btn btn-link btn-xs" data-toggle="modal" data-target="#removeRatingDialog" onClick="setRemoveRating('.$rating['id'].')"><span class="glyphicon glyphicon-remove"></span></button>';
		}
		if(!$rating['alreadyreported']){
			$strng2=$strng2.'<button id="btnReportRating" type="button" class="btn btn-link btn-xs pull-right" data-toggle="modal" data-target="#newRatingReportDialog" onClick="setReportRating('.$rating['id'].')">Kommentar melden</button>';
		}
		$strng2=$strng2.'<br>';
		if($rating['stars']!=0){
				$strng2=$strng2.(createStarsString($rating['stars'])."<br>");
		}
		if($rating['comment']!=null){
			$strng2=$strng2.$rating['comment']."<br>";
		}
		return $strng.$strng2."<br>";
	}
	//<span class="glyphicon glyphicon-remove"></span>

	function createStarsString($stars){
		$stars=round($stars);
		$maxStars=RATING_MAX_STARS;
		return str_repeat('<span class="glyphicon glyphicon-star"></span>',$stars).
			   str_repeat('<span class="glyphicon glyphicon-star-empty"></span>',($maxStars-$stars));
	}
?>
	<form role="form" method="post">
		<input id="ratingToRemove" type="hidden" name="ratingRemove" value="0">
		<div class="modal fade" id="removeRatingDialog" tabindex="-1" role="dialog"
			aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
						</button>
						<h4 class="modal-title" id="myModalLabel">Bewertungskommentar löschen</h4>
					</div>
					<div class="modal-body">
						<input type="text" autofocus="" placeholder="Begr&uuml;ndung der Löschung des Kommentars" name="removalExplanation" id="1t"
							class="form-control">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
						<button type="submit" class="btn btn-primary">Löschen</button>
					</div>
				</div>
			</div>
		</div>
	</form>

<form role="form" method="post">
<input id="ratingToReport" type="hidden" name="ratingReport" value="0">
<div class="modal fade" id="newRatingReportDialog" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
				</button>
				<h4 class="modal-title" id="myModalLabel">Kommentar melden</h4>
			</div>
			<div class="modal-body">
				<input type="text" autofocus="" placeholder="Begr&uuml;ndung der Meldung" name="ratingreportDescription" id="1t"
					class="form-control">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
				<button type="submit" class="btn btn-primary">Senden</button>
			</div>
		</div>
	</div>
</div>
</form>

