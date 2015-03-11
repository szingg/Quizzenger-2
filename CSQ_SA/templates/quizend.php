<div class="jumbotron">
	<h1>Quiz Beendet</h1>
	<p>Du hast <?php echo $this->_['score']; ?> von <?php echo $this->_['maxScore']; ?> Punkten erreicht! </p>

	<form
		<?php
			if($this->_['quiz_id']==-1){
			?>		action="?view=myquizzes&amp;savegeneratedquiz=<?php echo $this->_['session_id']; ?>"<?php
			}else{
			?>		action="?view=myquizzes&amp;copyquiz=<?php echo $this->_['quiz_id']; ?>"<?php
			}
		?> method="post">
		<p>
			<button type="submit" class="btn btn-success">Quiz speichern</button>
			<?php  if($this->_['quiz_id']==-1){ ?>
				<a href="?view=generatequiz" class="btn btn-primary" role="button">
					Weiter lernen
				</a>
			<?php }else{ ?>
				<a href="?view=quizstart&amp;quizid=<?= $this->_['quiz_id']; ?>" class="btn btn-primary" role="button">
					Quiz wiederholen
				</a>
			<?php }?>
		</p>
	</form>
</div>