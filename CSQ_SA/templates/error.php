<?php
$error = $this->_['err'];
if (!is_null($error) && defined($error)){
	$errorMessage= constant($error);
}else{ // no 'self' defined error messages
    $errorMessage = 'Oops, es ist ein unbekannter Fehler aufgetreten!';
}
?>
<div class="alert alert-danger" role="error">
        <strong>Es ist ein Problem aufgetreten:</strong> <?= htmlspecialchars($errorMessage); ?><br>
</div>