<?php use \quizzenger\utilities\FormatUtility as FormatUtility; ?>
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
					Start
				</th>
				<th class="hidden-xs">Dauer</th>
				<th class="hidden-xs">Löschen</th>
			</tr>
		</thead>
		<tbody id="tableBodyOpenGames">
			<?php foreach ( $this->_ ['games'] as $game ) { ?>
			<tr>
				<td>
					<a href="<?php echo '?view=GameDetail&gameid=' . $game['id']; ?>">
						<?php echo htmlspecialchars($game['name']); ?>
					</a>
				</td>
				<td class="hidden-xs"><?php echo (isset($game['members'])?$game['members']:'0').' Teilnehmer'; ?> </td>
				<td class="hidden-xs"><?php echo (isset($game['starttime'])?htmlspecialchars($game['starttime']):'Game nicht gestartet'); ?></td>
				<td class="hidden-xs"><?php echo htmlspecialchars(FormatUtility::formatTime($game['duration'])); ?></td>
				<td class="hidden-xs">
					<a class="remove-row" href="javascript:void()" data-qid="<?php echo $game['id']; ?>" data-type="game">
						<span class="glyphicon glyphicon-remove"></span> </a>
					</a>
				</td>
			</tr>
			<?php }  ?>
		</tbody>
	</table>
</div>