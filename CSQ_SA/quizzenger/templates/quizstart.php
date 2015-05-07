<script language="JavaScript"><!--
javascript:window.history.forward(1);
//--></script>
<div class="jumbotron">
	<?php
	use \quizzenger\utilities\NavigationUtility as NavigationUtility;
	if(is_null($this->_ ['quizinfo']['quizid'])){
		NavigationUtility::redirect();
	}
	?>
	<h1>Willkommen bei Quizzenger</h1>
	<p>
		Du wurdest eingeladen am Quiz "<?php echo htmlspecialchars($this->_ ['quizinfo']['quizname']); ?>" teilzunehmen.
	</p>
  	<p>
  		<a href="<?php echo $this->_ ['quizinfo']['firstUrl']; ?>" class="btn btn-primary btn-lg" role="button">
	  		Quiz starten!
		</a>
	</p>
</div>