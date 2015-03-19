<?php ?>
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
						<tbody>
						<?php $i=-1; foreach ( $this->_ ['openGames'] as $game ) {
								$i++;  ?>
							<tr>
								<td>
									<a href="?view=todo">
										<?php echo htmlspecialchars($game['name']); ?>
									</a>
								</td>
								<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
								<td class="hidden-xs"><?php echo htmlspecialchars($game['username']); ?></td>
								<td class="hidden-xs">
									<a href="?view=todo" >
										<span class="glyphicon glyphicon-ok-sign"></span>
									</a>
								</td>
							</tr>
						<?php } ?>
							<!--
							<tr>
								<td>
									<a href="?view=todo">
										Game for joy
									</a>
								</td>
								<td class="hidden-xs">0 Teilnehmer </td>
								<td class="hidden-xs">Reläxx </td>
								<td class="hidden-xs">
									<a href="?view=todo">
										<span class="glyphicon glyphicon-ok-sign"></span>
									</a>
								</td>
							</tr>
							<tr>
								<td>
									<a href="?view=todo">
										fourtyfor
									</a>
								</td>
								<td class="hidden-xs">500 Teilnehmer </td>
								<td class="hidden-xs">Halligalli</td>
								<td class="hidden-xs">
									<a href="?view=todo" >
										<span class="glyphicon glyphicon-ok-sign"></span>
									</a>
								</td>
							</tr> -->
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
										<a href="?view=todo">
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
	</div> <!-- panel-group -->
<?php ?>