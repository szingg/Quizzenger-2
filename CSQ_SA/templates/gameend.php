<div class="jumbotron">
	<h1>Game '<?php echo $this->_['gameinfo']['gamename'] ?>' beendet</h1>
	<p>Du hast <?php echo $this->_['score']; ?> von <?php echo $this->_['maxScore']; ?> Punkten erreicht! </p>

	<p>
		<a href="?view=learn#gamelobby" class="btn btn-primary" role="button">
			Weiter spielen
		</a>
	</p>
</div>
<?php echo $this->_['adminView']; ?>