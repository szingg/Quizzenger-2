<script language="JavaScript"><!--
javascript:window.history.forward(1);
//--></script>
<div class="jumbotron">
	<?php /*if(is_null($this->_ ['gameinfo']['gameid'])){
		header('Location: index.php');
		die();
	} */ ?>
	<h1>Willkommen zum Game XY</h1>

	<p>
  		<!-- wenn nicht beigetreten-->
  		<a href="<?php /* echo $this->_ ['quizinfo']['firstUrl']; */ ?>" class="btn btn-primary btn-lg" role="button">
	  		Beitreten
		</a>
		<!-- else-->
		<a href="<?php /* echo $this->_ ['quizinfo']['firstUrl']; */ ?>" class="btn btn-primary btn-lg" role="button">
	  		Austreten
		</a>
		<p>
			Warten, bis das Game gestartet wird...
		</p>

	</p>

	<a data-toggle="collapse" data-target="#participants" href="#participants">
		<h4 class="panel-title">Teilnehmer</h4>
	</a>
	<div id="participants" class="panel-collapse collapse in">
		<ul>
			<li>Teilnehmer1 </li>
			<li>Teilnehmer2 </li>
			<li>Teilnehmer3 </li>
			<li>Teilnehmer1 </li>
			<li>Teilnehmer1 </li>
			<li>Teilnehmer1 </li>
		</ul>
	</div>
</div>
<div class="panel panel-default no-margin">
	<a data-toggle="collapse" data-target="#gameAdmin" href="#gameAdmin">
		<div class="panel-heading bg-info text-info">
			<h4 class="panel-title">Game-Administration</h4>
		</div>
	</a>
	<div id="gameAdmin" class="panel-collapse collapse in">
		<div class="panel-body">
			<div class="btn-group">
				<input id="btn-uploadfile" class="btn btn-primary pull-left" type="button" value="Game starten" />
				<input id="btn-uploadfile" class="btn btn-primary pull-left" type="button" value="Game beenden" />
			</div>
			<h4> view mit status teilnehmer</h4>
		</div>
	</div>
</div>