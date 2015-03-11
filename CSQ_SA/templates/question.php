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
		<div id="question-content" data-attachment="https://hyneman/quizzenger/CSQ_SA/templates/img/header_50.png"><?php echo htmlspecialchars($this->_ ['question']['questiontext']); ?></div>
		<script>
			$("#question-content").html(quizzenger.markdown.generate($("#question-content").text(),
				$("#question-content").attr('data-attachment')));
		</script>
	</div>

	<!-- List group -->
	<ul class="list-group">
	<?php
	foreach ( $this->_ ['answers'] as $answer ) { ?>
		<a href="?view=solution&amp;id=<?= ($this->_['questionID']); ?>&amp;answer=<?php echo $answer['id']; echo $this->_['session_id']; ?>">
			<li class="list-group-item list-group-item-info">
				<?= htmlspecialchars($answer['text']);?>
			</li>
		</a> <?php
	} ?>
	</ul>
</div>
<div class="row">
  <div class="col-md-6">
  	<div class="panel panel-default" id="panelInfo">
  		<div class="panel-heading">
			<h4 class="panel-title">Infos</h4>
		</div>
  	  	<div style="padding-left:10px;">
  	  		<b>Autor:</b> <a target="_blank" href="<?php echo htmlspecialchars(APP_PATH . '/index.php?view=user&id=' . $this->_ ['user_id']); ?>"><?= htmlspecialchars($this->_ ['author'])?></a><br>
			<?php
			echo ("<b>Tags:</b> ");
			foreach ( $this->_ ['tags'] as $tag ) {
				echo ('<span class="badge">' . htmlspecialchars($tag['tag']) . "</span> ");
			}
			?><br>
			<b>Erstellt:</b> <?= $this->_ ['question']['created']; ?><br>
			<b>Geändert:</b> <?= $this->_ ['question']['lastModified']; ?>
		</div>
	</div>
  </div>
  <div class="col-md-6">
  	<div class="panel panel-default" id="panelQuestionHistory" >
		<a data-toggle="collapse" data-target="#collapseQuestionHistory"
			href="#collapseQuestionHistory" class="collapsed">
			<div class="panel-heading" style="background-image: linear-gradient(to bottom, #F5F5F5 0px, #E8E8E8 100%);">
				<h4 class="panel-title">Änderungsgeschichte <span class="caret"></span></h4>
			</div>
		</a>
		<div id="collapseQuestionHistory" class="panel-collapse collapse">
			<div class="panel-body">
				<?php include("questionhistory.php"); ?>
			</div>
		</div>
	</div>
  </div>
</div>	
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