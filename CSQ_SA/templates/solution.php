<script language="JavaScript"><!--
javascript:window.history.forward(1);
//--></script>

<?php if (isset($this->_['message'])){
	echo '<div class="alert alert-info" role="alert"><a href="#" class="close" data-dismiss="alert">&times;</a>'.htmlspecialchars($this->_['message']).'</div>';
}

if(isset($this->_['progress'])){ ?>
	<div class="progress">
		<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="<?=  $this->_['progress']; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=  $this->_['progress']; ?>%">
			<b><?=  $this->_['progress'];?>% (<?= $this->_['currentcounter']."/".$this->_['questioncount']?>)</b>
			<span class="sr-only">
				<?=  $this->_['progress']; ?>% beantwortet
			</span>
		</div>
	</div>
<?php } ?>
<div class="panel panel-default">
	<!-- Default panel contents -->
	<div class="panel-heading">
		Kategorie: <?=  htmlspecialchars($this->_ ['category'])?>
		<?php
			if(!$this->_ ['alreadyreported']){?>
			<button type="button" class="btn btn-link btn-xs pull-right" data-toggle="modal" data-target="#newQuestionReportDialog">
				Frage melden
			</button>
		<?php }  ?>
	</div>
	<div class="panel-body">
		<div id="question-content" data-attachment="https://sinv-56014.edu.hsr.ch/auto-deploy/CSQ_SA/templates/img/header_50.png"><?php echo htmlspecialchars($this->_ ['question']['questiontext']); ?></div>
		<script>
			$("#question-content").html(quizzenger.markdown.generate($("#question-content").text(),
				$("#question-content").attr('data-attachment')));
		</script>
	</div>

	<!-- List group -->
	<ul class="list-group">
		<?php
			foreach ($this->_['answers'] as $entry) {?>
				<li class="list-group-item list-group-item-<?php
				if ($entry['correctness']==100){
					echo "success";
				}elseif ($this->_['selectedAnswer'] == $entry['id']){
					echo "danger";
				} else {
					echo "info";
				}?>">
					<strong>
						<?php  echo(htmlspecialchars($entry['text']));?>
					</strong><br>
						<?php  echo(htmlspecialchars($entry['explanation']));?>
					</li>
			<?php }?>
	</ul>
</div>
<div class="panel panel-default" id="discussionpanel">
	<a data-toggle="collapse" data-target="#collapsediscussion" href="#collapsediscussion" class="collapsed">
		<div class="panel-heading">
			<h4 class="panel-title">Diskussion</h4>
		</div>
	</a>
	<div id="collapsediscussion" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="row">
				<div class="col-lg-6">
				<?php
					include('rating.php');
				?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php if(isset($this->_ ['pointsearned'])){?>
	<div class="alert alertautoremove alert-success alert-dismissible centered" id="score-alert" role="alert">
		<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
		<strong><?=htmlspecialchars($this->_ ['pointsearned'])?></strong> Punkte!
	</div>
<?php } ?>

<a href="<?php
	if(isset($this->_ ['nextQuestion'])) {
		echo(htmlspecialchars($this->_ ['nextQuestion']).'" class="btn btn-primary">Nächste Frage</a>');
	} else {
		echo("index.php?view=questionlist&amp;category=".$this->_ ['question']['category_id'].'" class="btn btn-primary">Zurück zur Fragenliste</a>');
	}
	?>
<form role="form" method="post">
<input type="hidden" name="questionReport" value="1">
<div class="modal fade" id="newQuestionReportDialog" tabindex="-1" role="dialog"
	aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">
					<span aria-hidden="true">&times;</span>
					<span class="sr-only">
						Close
					</span>
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
