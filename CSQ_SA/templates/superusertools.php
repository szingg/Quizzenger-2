<?php  if($_SESSION['superuser']){?>
	<a href="index.php?view=log"><h4>Log Viewer</h4></a><br>
	<div class="panel panel-default">
		<div class="panel panel-default">
			<div class="panel-heading">
				Gemeldete Benutzer
			</div>
			<table class="table" data-link="row" id="tableReportedContents">
				<thead>
					<tr>
						<th>Name</th>
						<th>Totale Anzahl Meldungen</th>
						<th>Bearbeiten</th>
					</tr>
				</thead>
				<tbody>
			<?php
			if (! is_null ( $this->_ ['reportedUsers'] )) {
				foreach ( $this->_ ['reportedUsers'] as $reportedUser ) {
					?>
					<tr>
						<td><a href="index.php?view=user&amp;id=<?=$reportedUser['user_id']?>"><?= htmlspecialchars($reportedUser['username']) ?></a></td>
						<td>
							<a href="javascript:void()" onclick="getReports(<?php echo $reportedUser['user_id']; ?>, 'user')"
						   		data-toggle="modal" data-target="#reportList"><?=$reportedUser['COUNT(*)']?>
						 	</a>
					 	</td>
						<td>
							<a class="remove-row" href="javascript:void()"
								data-toggle="tooltip" data-placement="top" title="Benutzer sperren"
								data-qid="<?= $reportedUser['user_id']; ?>" data-type="user">
								<span class="glyphicon glyphicon-remove"></span>
							</a>
							<a href="javascript:void()" class="remove-row" data-type="userreports" data-qid="<?= $reportedUser['user_id'] ?>">
								<span class="glyphicon glyphicon-ok"></span>
							</a>
						</td>
					</tr> <?php }
				}?>
				</tbody>
			</table>
		</div>
		<div class="panel-heading">
			Thema löschen
		</div>
		<table class="table" data-link="row" id="tableSubCats">
			<thead>
				<tr>
					<th>Kategorie</th>
					<th>Bearbeiten</th>
				</tr>
			</thead>
			<tbody>
		<?php
		if (! is_null ( $this->_ ['subCats'] )) {
			foreach ( $this->_['subCats']  as $subCat) {
				?>
				<tr>
					<td><a href="index.php?view=questionlist&amp;category=<?=$subCat['id']?>"><?=htmlspecialchars($subCat['name'])?></a></td>
					<td>
						<a
							class="remove-row" href="javascript:void()"
							data-toggle="tooltip" data-placement="top" title="Kategorie und Fragen darin löschen"
							data-qid="<?php echo $subCat['id']; ?>" data-type="subcat">
							<span class="glyphicon glyphicon-remove"></span>
						</a>
					</td>
				</tr> <?php
			}
			}?>
			</tbody>
		</table>
		<br>
		<div class="panel-heading">
			Neue Kategorie erstellen
		</div>
		<div class="panel-body">
			<form method="post">
				Kategorie:<br>
				<input name="superusertools_form_new_cat_upper" type="text">
				<input type="submit" value="Kategorie erstellen">
			</form>
			<br>
			<form method="post">
				Kategoriebereich:<br>
				<input name="superusertools_form_new_cat_middle" type="text">
				Parent:
				<select name="superusertools_form_new_cat_middle_parent">
				<?php
					foreach($this->_['upperCats'] as $cat){
						echo('<option value="'.$cat['id'].'">'.htmlspecialchars($cat['name']).'</option>');
					}
				?>
				</select>
				<input type="submit" value="Kategoriebereich erstellen">
			</form>
			<br>
			<form method="post">
				Thema:<br>
				<input name="superusertools_form_new_cat_lower" type="text">
				Parent:
				<select name="superusertools_form_new_cat_lower_parent">
				<?php
					foreach($this->_['middleCats'] as $cat){
						echo('<option value="'.$cat['id'].'">'.htmlspecialchars($cat['name']).'</option>');
					}
				?>
				</select>
				<input type="submit" value="Thema erstellen">
			</form>
			<br>
		</div>
	</div>

<?php } ?>