<table class="table" data-link="row" id="tableReportedContents">
		<thead>
			<tr>
				<th>Typ</th>
				<th>Text</th>
				<th class="hidden-xs">Totale Anzahl Meldungen</th>
				<th class="hidden-xs">Bearbeiten</th>
			</tr>
		</thead>
		<tbody>
	<?php
	if (! is_null ( $this->_ ['reportedQuestions'] )) {
		foreach ( $this->_ ['reportedQuestions'] as $reportedQuestion ) {
			?>
			<tr>
				<td>
					Frage
				</td>
				<td>
					<a href="?view=question&amp;id=<?=  $reportedQuestion['question_id']; ?>">
						<?= htmlspecialchars($reportedQuestion['questiontext']); ?>
					</a>
				</td>
				<td class="hidden-xs">
					<a href="javascript:void()" onclick="getReports(<?=  $reportedQuestion['question_id']; ?>, 'question')" 
					   data-toggle="modal" data-target="#reportList"><?=  $reportedQuestion['COUNT(*)'];?>
					 </a>
				</td>
				<td class="hidden-xs">
					<a href="?view=editquestion&amp;id=<?=  $reportedQuestion['question_id']; ?>">
						<span class="glyphicon glyphicon-edit"></span>
					</a> 
					<a class="remove-row" href="javascript:void()"
						data-toggle="tooltip" data-placement="top" title="Frage l&ouml;schen"
						data-qid="<?=  $reportedQuestion['id']; ?>" data-type="question">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
				</td>
			</tr> <?php }
			foreach ( $this->_ ['reportedRatings'] as $reportedRating ) { ?>
			<tr>
				<td>
					Kommentar
				</td>
				<td>
					<a href="#"><?= htmlspecialchars($reportedRating['comment']); ?></a>
				</td>
				<td class="hidden-xs">
					<a href="javascript:void()" onclick="getReports(<?=  $reportedRating['rating_id']; ?>, 'rating')"
						data-toggle="modal" data-target="#reportList"><?=  $reportedRating['COUNT(*)'];?>
					</a>
				</td>
				<td class="hidden-xs">
				</td>
			</tr> <?php }
	}?>			
	</tbody>
</table>