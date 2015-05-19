<?php use \quizzenger\utilities\FormatUtility as FormatUtility; ?>
<div class="panel-body">
	<div class="panel-group" id="accordion">
		<div class="panel panel-default" id="panel1">
			<div class="panel-heading clickable">
			<!-- <a data-toggle="collapse" data-target="#collapseOne" href="#collapseOne" class="collapsed">  -->
					<h4 class="panel-title">Gehostet</h4>
			<!-- </a> -->
			</div>
			<div id="collapseOne" class="panel-collapse collapse in">
				<div class="panel-body">
					<div class="table-responsive">
					<table class="table" id="tableHostedGames">
						<thead>
							<tr>
								<th>
									Name
								</th>
								<th class="hidden-xs">
									Teilnehmer
								</th>
								<th class="hidden-xs">
									Start
								</th>
								<th class="hidden-xs">Dauer</th>
								<th class="hidden-xs">Löschen</th>
							</tr>
						</thead>
						<tbody id="tableBodyOpenGames">
							<?php foreach ( $this->_ ['hostedGames'] as $game ) { ?>
							<tr>
								<td>
									<a href="<?php echo '?view=GameDetail&gameid=' . $game['id']; ?>" title="Gamedetails anschauen">
										<?php echo htmlspecialchars($game['name']); ?>
									</a>
								</td>
								<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
								<td class="hidden-xs"><?php echo (isset($game['starttime'])?htmlspecialchars($game['starttime']):'Game nicht gestartet'); ?></td>
								<td class="hidden-xs"><?php echo htmlspecialchars(FormatUtility::formatTime($game['duration'])); ?></td>
								<td class="hidden-xs">
									<a class="remove-row" href="javascript:void()" data-qid="<?php echo $game['id']; ?>" data-type="game" title="Game löschen">
										<span class="glyphicon glyphicon-remove"></span> </a>
									</a>
								</td>
							</tr>
							<?php }  ?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div> <!-- panel -->
		<div class="panel panel-default" id="panel2">
			<div class="panel-heading clickable">
			<!-- <a data-toggle="collapse" data-target="#collapseTwo" href="#collapseTwo" class="collapsed">  -->
					<h4 class="panel-title">Teilgenommen</h4>
			<!-- </a>  -->
			</div>
			<div id="collapseTwo" class="panel-collapse collapse in">
				<div class="panel-body">
				<div class="table-responsive">
					<table class="table" id="tableParticipatedGames">
						<thead>
							<tr>
								<th>
									Name
								</th>
								<th class="hidden-xs">
									Teilnehmer
								</th>
								<th class="hidden-xs">
									Start
								</th>
								<th class="hidden-xs">Dauer</th>
							</tr>
						</thead>
						<tbody id="tableBodyOpenGames">
							<?php foreach ( $this->_ ['participatedGames'] as $game ) { ?>
							<tr>
								<td>
									<a href="<?php echo '?view=GameDetail&gameid=' . $game['id']; ?>">
										<?php echo htmlspecialchars($game['name']); ?>
									</a>
								</td>
								<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
								<td class="hidden-xs"><?php echo (isset($game['starttime'])?htmlspecialchars($game['starttime']):'Game nicht gestartet'); ?></td>
								<td class="hidden-xs"><?php echo htmlspecialchars(FormatUtility::formatTime($game['duration'])); ?></td>
							</tr>
							<?php }  ?>
						</tbody>
					</table>
					</div>
				</div>
			</div>
		</div> <!-- panel -->
	</div> <!-- panel-group -->
</div>