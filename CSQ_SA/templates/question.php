<script language="JavaScript"><!--
javascript:window.history.forward(1);
//--></script>

<?php if (isset($this->_['message'])){
	echo '<div class="alert alert-info" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a>'.htmlspecialchars($this->_['message']).'</div>';
}
if(isset($this->_['progress'])){ ?>
<div class="progress">
	<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?= $this->_['progress']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=  $this->_['progress']; ?>%">
		<b><?=  $this->_['progress'];?>% (<?= $this->_['currentcounter']."/".$this->_['questioncount']?>)</b>
		<span class="sr-only"><?=  $this->_['progress']; ?>% beantwortet</span>
	</div>
</div><?php
}
?>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<?php
		$question = $this->_ ['question'];
	?>
	<div class="panel-heading">Kategorie: <?=  htmlspecialchars($this->_ ['category'])?>
		<?php
			if(!$this->_ ['alreadyreported']){?>
			<button type="button" class="btn btn-link btn-xs pull-right" data-toggle="modal" data-target="#newQuestionReportDialog">
				Frage melden
			</button>
		<?php }  ?>
	</div>
	<div class="panel-body">
		<div id="question-content" data-attachment="<?php 
			$link = "";
			$question = $this->_ ['question'];
			switch($question['attachment_local']){
				case '0':
					$link = $question['attachment'];
					break;
				case '1':
					$paths = array();
					$paths[] = ATTACHMENT_PATH;
					$paths[] = $question['id'].'.'.$question['attachment'];

					$link = preg_replace('#/+#','/',join('/', $paths));
					break;
			}
			echo $link
			?>"><?php echo htmlspecialchars($this->_ ['question']['questiontext']); ?></div>
		<script>
			$("#question-content").html(quizzenger.markdown.generate($("#question-content").text(),
				$("#question-content").attr('data-attachment')));
		</script>
	</div>

	<!-- List group -->
	<ul class="list-group">
	<?php
	//shuffle($this->_ ['answers']); //randomize answers
	foreach ( $this->_ ['answers'] as $answer ) { ?>
		<!-- <a href="?view=solution&amp;id=<?= ($this->_['questionID']); ?>&amp;answer=<?php echo $answer['id']; echo $this->_['session_id']; ?>">  -->
		<a href="<?php echo $this->_['linkToSolution'] ;?>&amp;answer=<?php echo $answer['id'];?>">
			<li class="list-group-item list-group-item-info">
				<?= htmlspecialchars($answer['text']);?>
			</li>
		</a> <?php
	} ?>
	</ul>
</div>
<?php if(isset($this->_['questioninfo'])){
	echo $this->_['questioninfo'];
}?>
<form role="form" method="post">
	<input type="hidden" name="questionReport" value="1">
	<div class="modal fade" id="newQuestionReportDialog" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Frage melden</h4>
				</div>
				<div class="modal-body">
					<input type="text" autofocus="" placeholder="Begr&uuml;ndung der Meldung" name="questionreportDescription" id="questionreport" class="form-control">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Abbrechen
					</button>
					<button type="submit" class="btn btn-primary">
						Senden
					</button>
				</div>
			</div>
		</div>
	</div>
</form>