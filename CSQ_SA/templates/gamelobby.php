<?php ?>
	<script id="dot-openGameRow" type="text/x-dot-template">
		<tr>
			<td>
				<a href="?view=gamestart&gameid={{=it.id}}">
					{{=it.name}}
				</a>
			</td>
			<td class="hidden-xs">
			{{?it.members==null}}0 {{?}}
			{{?it.members!=null}}{{=it.members}} {{?}}Teilnehmer</td>
			<td class="hidden-xs">{{=it.username}}</td>
			<td class="hidden-xs">
				<a href="?view=gamestart&gameid={{=it.id}}">
					<span class="glyphicon glyphicon-ok-sign"></span>
				</a>
			</td>
		</tr>
	</script>
	<div class="panel-group">
		<div class="panel panel-default no-margin">
			<a data-toggle="collapse" data-target="#openGames" href="#openGames">
				<div class="panel-heading bg-info text-info">
					<h4 class="panel-title">Offene Games</h4>
				</div>
			</a>
	    	<div id="openGames" class="panel-collapse collapse in">
				<div class="panel-body">
					<table class="table" id="tableOpenGames">
						<thead>
							<tr>
								<th>
									Name
								</th>
								<th class="hidden-xs">
									Teilnehmer
								</th>
								<th class="hidden-xs">
									Ersteller
								</th>
								<th class="hidden-xs">Beitreten</th>
							</tr>
						</thead>
						<tbody id="tableBodyOpenGames">
						<?php $i=-1; foreach ( $this->_ ['openGames'] as $game ) {
								$i++;  ?>
							<tr>
								<td>
									<a href="<?php echo '?view=gamestart&gameid=' . $game['id']; ?>">
										<?php echo htmlspecialchars($game['name']); ?>
									</a>
								</td>
								<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
								<td class="hidden-xs"><?php echo htmlspecialchars($game['username']); ?></td>
								<td class="hidden-xs">
									<a href="<?php echo '?view=gamestart&gameid=' . $game['id']; ?>" >
										<span class="glyphicon glyphicon-ok-sign"></span>
									</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div> <!-- panel-body -->
	    	</div> <!-- panel-collapse -->
	    </div> <!-- panel -->

	    <div class="panel panel-default no-margin">
			<a data-toggle="collapse" data-target="#newGames" href="#newGames">
				<div class="panel-heading bg-info text-info">
					<h4 class="panel-title">Neues Game erstellen</h4>
				</div>
			</a>
			<div id="newGames" class="panel-collapse collapse in">
				<div class="panel-body">
					<table class="table" id="tableNewGame">
						<thead>
							<tr>
								<th>Quizname</th>
							</tr>
						</thead>
						<tbody>
							<?php
							if (! is_null ( $this->_ ['quizzes'] )) {
								foreach ( $this->_ ['quizzes'] as $quiz ) { ?>
								<tr>
									<td>
									<!--<a role="menuitem" tabindex="-1" data-toggle="modal" data-target="#newQuizDialog" href="javascript:void()">
									Neues Quiz
								</a> -->
										<a data-toggle="modal" data-target="#newGameDialog" data-quiz-id="<?= $quiz['id'] ?>" data-quiz-name="<?= htmlspecialchars($quiz['name']); ?>" href="javascript:void()">
											<?= htmlspecialchars($quiz['name']); ?>
										</a>
									</td>
								</tr>
								<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
		<div class="modal fade" id="newGameDialog" tabindex="-1" role="dialog"
			aria-labelledby="newGameModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">
							<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
						</button>
						<span id="quizIdModal" hidden="true"></span>
						<h4 class="modal-title" id="newGameModalLabel">Neues Game erstellen aus Quiz</h4>
					</div>
					<div class="modal-body">
						<input type="text" autofocus="" required="required"
							placeholder="Game Name" id="gameNameModal"
							class="form-control">
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
						<input id="saveNewGame" type="button" class="btn btn-primary" value="Speichern"></input>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- panel-group -->
<?php ?>