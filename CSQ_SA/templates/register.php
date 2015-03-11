<?php
	// no double logins
    if($GLOBALS['loggedin'] ){
       	header('Location: index.php?info=mes_login_already');
       	die();
    }
?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-md-6 col-md-offset-3">
		<h2>Registration</h2>
		<p>
			Die Registration ist kostenlos, sicher, mühelos und ermöglicht Ihnen Zugriff auf viele zusätzliche Funktionen.
		        	</p>
			        <hr>
			        <form class="register_form" action="./index.php?view=processRegistration"  method="post" id="register_form" name="register_form">
						<div class="form-group">
							<input class="form-control" type='text' placeholder="Username" name='register_form_username' id='register_form_username' />
						</div>
						<div class="form-group">
			            	<input class="form-control" type="text" placeholder="Email Adresse" name="register_form_email" id="register_form_email" />
			            </div>
			            <div class="form-group">
			          		<input class="form-control" type="password" placeholder="Passwort" name="register_form_password" id="register_form_password"/>
			          	</div>
			          	<div class="form-group">
			          		<input class="form-control" placeholder="Passwort wiederholen" type="password" name="register_form_password_confirm" id="register_form_password_confirm" />
			          	</div>
			          	<br>
			           	<button class="btn btn-lg btn-primary btn-block" type="submit" value="Register" />
			           		Registrieren
			           	</button>
			        </form>
			    </div>
			</div>
		</div>