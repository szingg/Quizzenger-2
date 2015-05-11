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