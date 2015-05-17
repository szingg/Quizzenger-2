<?php
	use \quizzenger\gate\QuestionImporter as QuestionImporter;

	$messages = $this->_['messages'];
	$successful = $this->_['successful'];
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<b>Fragen-Import</b>
	</div>
	<div class="panel-body">
		<?php if($successful): ?>
			<h2>Der Import war erfolgreich.</h2>
		<?php else: ?>
			<h2>Der Import ist fehlgeschlagen.</h2>
			<?php if(isset($messages[QuestionImporter::MESSAGE_INVALID_XML])): ?>
				Die hochgeladene Datei ist ungültig und konnte nicht gelesen werden.
			<?php elseif(isset($messages[QuestionImporter::MESSAGE_UNSUPPORTED_VERSION])): ?>
				Die Version der Import-Datei wird von diesem System nicht unterstützt.
			<?php endif; ?>
		<?php endif; ?>

		<?php if(isset($messages[QuestionImporter::MESSAGE_IMPORT_FAILED])): ?>
			<strong>Fehlgeschlagene Imports:</strong><br />
<pre><code><?php
				foreach($messages[QuestionImporter::MESSAGE_IMPORT_FAILED] as $current) {
					echo "$current\n";
				}
?></code></pre>
		<?php endif; ?>
		<?php if(isset($messages[QuestionImporter::MESSAGE_ALREADY_EXISTS])): ?>
			<strong>Bereits vorhandene Fragen:</strong><br />
<pre><code><?php
				foreach($messages[QuestionImporter::MESSAGE_ALREADY_EXISTS] as $current) {
					echo "$current\n";
				}
?></code></pre>
		<?php endif; ?>
	</div>
</div>
