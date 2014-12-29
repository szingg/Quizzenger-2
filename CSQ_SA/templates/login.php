<?php 	$pageBefore = filter_input(INPUT_GET, 'pageBefore', $filter = FILTER_SANITIZE_SPECIAL_CHARS); ?>
<div class="container">
	<div class="row">
		<div class="col-xs-offset-2 col-xs-8">
			<form class="login_form"  role="form" action="./index.php?view=processLogin<?php if (!is_null($pageBefore)){echo("&amp;pageBefore=".$pageBefore);}?>" method="post" id="login_form" name="login_form">       
		    	<h2 class="form-signin-heading">Bitte melden Sie sich an</h2>            
		        <div class="form-group">
		        	<input type="email" class="form-control" name="login_form_email" id="login_form_email" placeholder="Email Adresse" required autofocus />
		        </div>
				<div class="form-group">		            	
		        	<input type="password" class="form-control"  name="login_form_password"  id="login_form_password"  placeholder="Passwort" required />
		        </div>
				<br>
				<button class="btn btn-lg btn-primary btn-block" type="submit" value="Login" >Anmelden</button>
			</form>
		    <h4>
		    	Sie haben noch keinen Login?<br>Registrieren Sie sich kostenlos und mühelos <a href="./index.php?view=register">hier</a>
		    </h4>
		</div>
	</div>
</div>
     
        
        
