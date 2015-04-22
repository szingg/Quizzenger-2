<?php
	$type = $this->_ ['type'];
	$operation = $this->_ ['operation'];
	$answers = array("-"); // index starts with 1, array with 0 -> fill first element
	$answersSolution = array("-");
	$answersExplanation = array("-");

	if($operation=="edit"){
		$submitButtonName="submit_opquestion_edit_btn";
		$question = $this->_ ['question'];
		$questiontext = $question['questiontext'];
		$tags="";
		foreach ( $this->_ ['tags'] as $tag ) {
			$tags=$tags.$tag['tag'].",";
		}
		foreach ( $this->_ ['answers'] as $answer ){
			array_push($answers,$answer['text']);
			array_push($answersSolution,$answer['correctness']);
			array_push($answersExplanation,$answer['explanation']);
		}
	}else{
		$submitButtonName="submit_opquestion_btn";
		for ($i = 1; $i <= SINGLECHOICE_ANSWER_COUNT; $i++) {
			array_push($answers,"");
			array_push($answersSolution,0);
			array_push($answersExplanation,"");
		}
		$tags="";
		$questiontext="";
	}
	if($type==SINGLECHOICE_TYPE){ ?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<b>Frage hinzufügen</b>
		</div>
		<div class="panel-body">
		<form class="opquestion_form" role="form" action="./index.php?view=<?=($operation=="new")?"processNewQuestion":"processEditQuestion"; ?>" method="post" id="newquestion_<?= (SINGLECHOICE_TYPE);?>" name="newquestion_<?= (SINGLECHOICE_TYPE);?>">
			<?php if($operation=="new"){?>
				<h4>Erfasse eine neue Frage und teile diese mit allen Quizzenger Mitgliedern</h4><br>
				<h3>Thema wählen:</h3>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">Gewähltes Thema</span>
						<input style="width:245px;" type="text" class="form-control" data-bv-excluded="false" id="opquestion_form_chosenCategoryName" name="opquestion_form_chosenCategoryName"  value=""  readonly >
						<input style="visibility: hidden; width:45px;" type="text" class="form-control" data-bv-excluded="false" id="opquestion_form_chosenCategory" name="opquestion_form_chosenCategory"  value="" readonly >
						<input style="visibility: hidden; width:45px;" type="text" class="form-control" data-bv-excluded="true" id="opquestion_form_chosenCategory_parent_id" name="opquestion_form_chosenCategory_parent_id"  value="" readonly >
					</div>
				</div>
				<?php
				include('categorylist.php');
			} ?>

			<h3>Frage:</h3>
				<div style="display: none;">
				<?php if($operation=="edit"){ ?>
					<div class="form-group">
						<input type="text" class="form-control" id="opquestion_form_question_id" name="opquestion_form_question_id"  value="<?= $question['id'] ?>" >
					</div>
					<div class="form-group">
						<input type="text" class="form-control" data-bv-excluded="false" id="opquestion_form_chosenCategory" name="opquestion_form_chosenCategory"  value="123" >
					</div>
					<?php }?>
					<div class="form-group">
						<input type="text" class="form-control" id="opquestion_form_questionType" name="opquestion_form_questionType"  value="<?= (SINGLECHOICE_TYPE);?>" >
					</div>
				</div>
				<br>
				<div class="form-group">
					<input type="text" class="form-control" data-bv-excluded="true" id="opquestion_form_tags" maxlength="155" name="opquestion_form_tags"  value="<?= $tags;?>" placeholder="Tags getrennt mit ," >
				</div>
				<div class="form-group">
					 <textarea id="opquestion_form_questionText" name="opquestion_form_questionText" rows="<?=QUESTION_INPUTFIELD_MAX_ROWCOUNT?>" placeholder="Frage Text" maxlength="<?=QUESTION_INPUTFIELD_MAX_LENGTH?>"  class="form-control" ><?= htmlspecialchars($questiontext)?></textarea>
				</div>
				<div class="form-group">
					<div class="btn-group"><input id="btn-attach-file" type="button" class="btn btn-primary" value="Datei anh&auml;ngen"> </div>
					<div class="btn-group text-success" id="msg-attach">
						<?php
							$link = "";
							if($operation=="edit"){
								switch($question['attachment_local']){
									case '0':
										$link = htmlspecialchars($question['attachment']);
										echo '<b><a class="text-success" href="'.$link.'">Anhang</a></b> eingebettet.';
										break;
									case '1':
										$paths = array();
										$paths[] = ATTACHMENT_PATH;
										$paths[] = $question['id'].'.'.htmlspecialchars($question['attachment']);

										$link = preg_replace('#/+#','/',join('/', $paths));
										echo '<b><a class="text-success" href="'.$link.'">Anhang</a></b> eingebettet.';
										break;
								}
							}
						?>
					</div>
					<div hidden="true">
						<input name="opquestion_form_attachment" id="opquestion_form_attachment" type="text" value="<?= htmlspecialchars($question['attachment']) ?>"/>
						<input name="opquestion_form_attachmentTempFileName" id="opquestion_form_attachmentTempFileName" type="text" value="<?= htmlspecialchars($question['attachment']) ?>"/>
						<input name="opquestion_form_attachmentLocal" id="opquestion_form_attachmentLocal" type="text" value="<?= htmlspecialchars($question['attachment_local']) ?>"/>
						<input name="opquestion_form_attachmentOld" id="opquestion_form_attachmentOld" type="text" value="<?= htmlspecialchars($question['attachment']) ?>" />
					</div>
				</div>
				<h3>Antworten:</h3>
				<div class="form-group">
					<div class="input-group">
						<span class="input-group-addon">Gewählte richtige Antwort</span>
						<?php if($operation=="edit"){
							for ($i = 1; $i <= SINGLECHOICE_ANSWER_COUNT; $i++) {
								if($answersSolution[$i]==100){
									$correctAnswer=$i;
								}
							}
						}elseif($operation=="new"){
							$correctAnswer="";
						}
						?>
						<input style="width:45px;" type="text" maxlength="1" size="1" class="form-control" data-bv-excluded="false" id="opquestion_form_chosenCorrectAnswer" name="opquestion_form_chosenCorrectAnswer"  value="<?=$correctAnswer?>" readonly>
					</div>
				</div>
				<?php
					$rows=ceil(SINGLECHOICE_ANSWER_COUNT/2);
					for ($i = 1; $i <= $rows ; $i++) {
					  ?><br><div class="row">
						<?php
							$questionRowCount=2;
							if($i==$rows && SINGLECHOICE_ANSWER_COUNT% 2 != 0){
								$questionRowCount=1;
							}
							for ($ir = 1; $ir <= $questionRowCount; $ir++) {
								$index=$ir+(($i-1)*2);
								?>
								 <div class="col-lg-6">
									<div class="form-group">
								    	<div class="input-group">
									      <span class="input-group-addon">
									        <input class="css-checkbox" type="radio" onclick="radioButtonSelected(<?= $index ?>);" name="opquestion_form_correctness" id="<?= $index ?>" value="<?= $index ?>" <?= ($answersSolution[$index]==100)?"checked":"";?>>
									      	<label for="<?= $index ?>" class="css-label"></label>
									      </span>
								      	<textarea id="opquestion_form_answer<?= $index ?>" maxlength="<?= ANSWER_INPUTFIELD_MAX_LENGTH ?>" placeholder="Antwort # <?= $index ?>" name="opquestion_form_answer<?= $index ?>" rows="<?= ANSWER_INPUTFIELD_ROWCOUNT?>" class="form-control" data-minlength="6"  ><?= htmlspecialchars($answers[$index])?></textarea>
								      </div>
								    </div><!-- /input-group -->
									<div class="form-group">
										<textarea id="opquestion_form_answerexplanation<?= $index ?>" maxlength="<?= ANSWER_EXPLANATION_INPUTFIELD_MAX_LENGTH ?>" placeholder="Optionale Erklärung" name="opquestion_form_answerexplanation<?= $index ?>" rows="<?= ANSWER_EXPLANATION_INPUTFIELD_ROWCOUNT?>" class="form-control"><?= htmlspecialchars($answersExplanation[$index])?></textarea>
									</div>
								 </div><!-- /.col-lg-6 -->
								<?php
							}
							if($questionRowCount==1){
								?><br><div class="col-lg-6">
									<button class="btn btn-lg btn-primary btn-block" type="submit" id="opquestion_form_<?=$submitButtonName?>"><?= ($operation=="new")?"Frage erfassen":"Änderungen speichern";?></button>
								</div><!-- /.col-lg-6 --><?php
							}
						?>
						</div><br>
					<?php
						if($questionRowCount==2 && $i==$rows){
							?><br><div class="row"><div class="col-lg-12">
								<button class="btn btn-lg btn-primary btn-block" type="submit" id="opquestion_form_<?=$submitButtonName?>"><?= ($operation=="new")?"Frage erfassen":"Änderungen speichern";?></button>
							</div><!-- /.col-lg-6 --></div><!-- /.row --><?php
						}
					}?>

				<div id="messageTemp"></div>
			</form>
			<div id="modalAttachFile" class="modal fade">
			    <div class="modal-dialog">
			        <div class="modal-content">
			            <div class="modal-header bg-primary">
			                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			                <h4 class="modal-title">Datei anhängen</h4>
			            </div>
			           <!-- <div class="modal-body">  -->
			           <div class="">
			            	<div class="panel panel-default no-margin">
				            <!-- <a data-toggle="collapse" data-target="#collapseEmbed" href="#collapseOne"> -->
									<div class="panel-heading clickable">
										<h4 class="panel-title">Video einbetten</h4>
									</div>
								 <!-- </a> -->
				            	<div class="panel-collapse collapse in">
									<div class="panel-body">
										<div class="btn-group">
											<input id="inputLink" class="pull-left" type="text" size="60" placeholder="Bitte geben Sei ihren Link ein" value="<?php if($operation=="edit" && $question['attachment_local']=='0'){ echo htmlspecialchars($question['attachment']); }?>"/>
										</div>
										<div class="btn-group">
											<input id="btn-checkLink" class="btn btn-primary pull left" type="button" value="Pr&uuml;fen" />
										</div>
										<div class="text-danger" id="msg-link"></div>
									</div>
				            	</div>
				            </div>
				            <div class="panel panel-default no-margin">
			               		<div class="panel-heading clickable">
			            			<h4 class="panel-title">Bild hochladen</h4>
			            		</div>
				            	<div class="panel-collapse collapse">
				            		<div class="panel-body">
										<form id="form-uploadFile" enctype="multipart/form-data">
											<div class="btn-group">
							            		<input id="selectedFile" class="btn btn-default pull-left" type="file" name="file" id="file" required accept="<?= ATTACHMENT_ALLOWED_EXTENSIONS ;?>" />
							            	</div>
							            	<div class="btn-group">
												<input id="btn-uploadfile" class="btn btn-primary pull-left" type="button" value="Upload" />
											</div>
											<div class="text-danger" id="msg-upload"></div>
										</form>
									</div>
								</div>
							</div>
			            </div> <!-- modal-body -->
			        </div> <!-- modal-content -->
			    </div> <!-- modal-dialog -->
			</div>
		</div>

<?php }
// Add different question types here
?>
