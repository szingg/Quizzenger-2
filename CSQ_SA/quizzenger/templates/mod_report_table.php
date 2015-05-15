<table class="table" data-link="row" id="tableModerationContents">
		<thead>
			<tr>
				<th>Typ</th>
				<th>Autor</th>
				<th>Text</th>
				<th>Totale Anzahl Meldungen</th>
				<th>Bearbeiten</th>
			</tr>
		</thead>
		<tbody>
		<?php
		if (! is_null ( $this->_ ['moderatedQuestions'] )) {
			foreach ( $this->_ ['moderatedQuestions'] as $moderatedQuestion ) { ?>
			<tr>
				<td>
					Frage
				</td>
				<td>
					<a href="?view=user&amp;id=<?=  $moderatedQuestion['user_id']; ?>"><?= htmlspecialchars($moderatedQuestion['username']); ?></a>
				</td>
				<td>
					<a href="?view=question&amp;id=<?=  $moderatedQuestion['question_id']; ?>"><?= htmlspecialchars($moderatedQuestion['questiontext']); ?></a>
				</td>
				<td>
					<a href="javascript:void()" onclick="getReports(<?=  $moderatedQuestion['question_id']; ?>, 'question')" data-toggle="modal" data-target="#reportList"><?=  $moderatedQuestion['COUNT(*)'];?></a>
				</td>
				<td>
					<a href="?view=editquestion&amp;id=<?=  $moderatedQuestion['question_id']; ?>">
						<span class="glyphicon glyphicon-edit"></span>
					</a>
					<a class="remove-row" href="javascript:void()" data-toggle="tooltip" data-placement="top" title="Frage l&ouml;schen" data-type="question" data-qid="<?=  $moderatedQuestion['question_id']; ?>">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
					<a href="javascript:void()" class="remove-row" data-type="questionreports" data-qid="<?= $moderatedQuestion['question_id']; ?>">
						<span class="glyphicon glyphicon-ok"></span>
					</a>
				</td>
			</tr> <?php
		}
		foreach ( $this->_ ['moderatedRatings'] as $moderatedRating ) { ?>
			<br />
			<tr>
				<td>
					Kommentar
				</td>
				<td>
					<a href="?view=user&amp;id=<?= $moderatedRating['user_id']; ?>"><?= htmlspecialchars($moderatedRating['username']); ?></a>
				</td>
				<td>
					<?= htmlspecialchars($moderatedRating['comment'])?>
				</td>
				<td>
					<a href="javascript:void()" onclick="getReports(<?= $moderatedRating['rating_id']; ?>, 'rating')" data-toggle="modal" data-target="#reportList"><?=  $moderatedRating['COUNT(*)'];?></a>
				</td>
				<td>
					<span class="glyphicon glyphicon-none"></span>&nbsp;
					<a class="remove-row" href="javascript:void()" data-toggle="tooltip" data-placement="top" data-type="rating" data-qid="<?= $moderatedRating['rating_id']; ?>" title="Kommentar l&ouml;schen">
						<span class="glyphicon glyphicon-remove"></span>
					</a>
					<a href="javascript:void()" class="remove-row" data-type="ratingreports" data-qid="<?= $moderatedRating['rating_id']; ?>">
						<span class="glyphicon glyphicon-ok"></span>
					</a>
				</td>
			</tr> <?php
		}
	}?>
	</tbody>
</table>