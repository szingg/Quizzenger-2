<div class="jumbotron">
	<h1>Willkommen bei Quizzenger</h1>
	<p class="lead">
		<img style="float: right" alt="Quizzenger Logo"
			src="<?= htmlspecialchars(APP_PATH)?>/templates/img/logo_s.png"> Quizzenger bietet jedem
		eine webbasierte Wissensdatenbank für verschiendste Themenbereiche.
		Die zur verfügungenstehenden Fragen werden durch die Community selbst
		erstellt und unterhalten.<br> <br> Quizzenger verfolgt die Idee einer
		offenen Community an der jeder Teilnehmen darf. Deswegen wurde das
		Projekt unter der GPLv3 Version lizenziert.
	</p>
	<p>
		<b>Die neuste hinzugefügte Frage</b> (<?=$this->_ ['newestquestion']['created']?>)<br> <a href="index.php?view=question&amp;id=<?= $this->_ ['newestquestion']['id']?>"><?= $this->_ ['newestquestion']['questiontext']?></a>			
	</p>
	<br>
	<p>
		<a href="./index.php?view=register" class="btn btn-lg btn-success"
			role="button">Registrieren</a>
	</p>
	<p>
		<a href="./index.php?view=about" class="btn btn-lg btn-primary"
			role="button">Über Quizzenger</a>
	</p>
</div>

<div class="row marketing">
	<div class="col-lg-6">
		<h3>Fragen</h3>
		<p>
			Erstellen Sie Wissensfragen für sich und die Community und
			profitieren Sie vom Wissen anderer.<br>
		</p>

		<h3>Quizzes</h3>
		<p>Erstellen Sie Quizzes, bestehend aus verschiedenen Fragen und laden
			Sie Leute einfach per Link ein. Für jedes Quiz erhalten Sie
			detailierte Informationen zu den Quizteilnehmern und deren Leistung.
		</p>

		<h3>Responsive unterwegs</h3>
		<p>
			Dank neusten Technologien auch auf Smartphones unterwegs benutzbar.<br>
			<a
				href="http://validator.w3.org/check?uri=<?php echo htmlspecialchars(APP_PATH); ?>"><img
				src="<?= htmlspecialchars(APP_PATH) ?>/templates/img/html5-badge-h-solo.png" width="63"
				height="64" alt="HTML5 Powered" title="HTML5 Powered"></a>
		</p>
	</div>

	<div class="col-lg-6">
		<h3>Community</h3>
		<p>
			Nehmen Sie teil an einer tollen Community!<br> Durch Benutzung
			erhalten Sie Punkte mit denen Sie auf Ihrem Profil Badges
			freischalten können und sogar Moderationsrechte erhalten.
		</p>

		<h3>Statistiken</h3>
		<p>Morbi leo risus, porta ac consectetur ac, vestibulum at eros. Cras
			mattis consectetur purus sit amet fermentum.</p>

		<h3>Libre Open Source Software</h3>
		<img src="<?= htmlspecialchars(APP_PATH) ?>/templates/img/GPLv3_Logo.png" width="144"
			height="72" alt="GPLv3" title="GPLv3">
	</div>
</div>