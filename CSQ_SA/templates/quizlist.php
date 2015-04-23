<div class="panel-body">
	<form id="addToQuizForm" role="form" method="post">
		<table class="table" data-link="row" id="tableQuizList">
			<thead>
				<tr>
					<th>Name</th>
					<th class="hidden-xs">Fragen</th>
					<th class="hidden-xs">Durchf&uuml;hrungen</th>
					<th class="hidden-xs">Bearbeiten</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if (! is_null ( $this->_ ['quizzes'] )) {
					foreach ( $this->_ ['quizzes'] as $quiz ) { ?>
					<tr
						<?php if (isset ( $this->_ ['markQuiz'] ) && $this->_ ['markQuiz'] == $quiz ['id']) {
							echo (' class="success"');
						} ?>
					>
						<td>
							<a href="?view=quizdetail&amp;quizid=<?= $quiz['id']; ?>">
								<?= htmlspecialchars($quiz['name']); ?>
							</a>
						</td>
						<td class="hidden-xs">
							<?= htmlspecialchars($quiz['questions']); ?>
						</td>
						<td class="hidden-xs">
							<?= htmlspecialchars($quiz['performances']); ?>
						</td>
						<td class="hidden-xs">
							<a class="remove-row" href="javascript:void()" data-qid="<?php echo $quiz['id']; ?>" data-type="quiz">
							<span class="glyphicon glyphicon-remove"></span> </a>
							<button type="button" class="btn btn-link btn-xs"
								data-toggle="modal"
								onclick='setEditQuizName(<?=$quiz['id'] ?>,"<?= htmlspecialchars($quiz['name'])?>");'
								data-target="#editQuizName">
								<span class="glyphicon glyphicon-edit"></span>
							</button>
						</td>
					</tr> <?php
					}
				}
				?>
			</tbody>
		</table>
		<button type="button" class="btn btn-success hidden-xs" data-toggle="modal" data-target="#newQuizDialog2">
			Quiz erstellen
		</button>
		<a href="?view=generatequiz" class="btn btn-success hidden-xs">
			Quiz generieren
		</a>
	</form>
</div>

<form role="form" method="post">
	<div class="modal fade" id="newQuizDialog2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">
							&times;
						</span>
						<span class="sr-only">
							Close
						</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">
						Neues Quiz erstellen
					</h4>
				</div>
				<div class="modal-body">
					<input type="text" autofocus="" required="required"
						placeholder="Quiz Name" name="quizname" id="quizname"
						class="form-control">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Abbrechen
					</button>
					<button type="submit" class="btn btn-primary" formaction="./index.php?view=myquizzes">
						Speichern
					</button>
				</div>
			</div>
		</div>
	</div>
</form>

<form role="form" method="post">
	<input type="hidden" id="editQuizNameID" name="editQuizNameID"
		value="-1">
	<div class="modal fade" id="editQuizName" tabindex="-1" role="dialog"
		aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">
						Quizname bearbeiten
					</h4>
				</div>
				<div class="modal-body">
					<input type="text" autofocus="" placeholder="Quizname" name="quizNameField" id="quizNameField" class="form-control">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">
						Abbrechen
					</button>
					<button type="submit" class="btn btn-primary">
						Speichern
					</button>
				</div>
			</div>
		</div>
	</div>
</form>
