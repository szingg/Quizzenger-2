<?php if($GLOBALS['loggedin']){ ?>
	<div class="jumbotron">
		<h1>Hallo <?php echo htmlspecialchars($this->_['username']); ?>!</h1>
		<p class="lead">Sch&ouml;n, dass du vorbeischaust.</p>
	</div>	
	
	<!--  SUPER USER  -->
	<?php
	if($_SESSION['superuser']){?>
		 <div class="panel panel-default" id="panelSuperuser" >
			<a data-toggle="collapse" data-target="#collapseSuperuser"
				href="#collapseSuperuser" class="collapsed">
				<div class="panel-heading" style="background-image: linear-gradient(to bottom, #F5F5F5 0px, #E8E8E8 100%);">
					<h4 class="panel-title"><img alt="superuser" src="<?= htmlspecialchars(APP_PATH)?>/templates/img/superuser.png">Superuser Tools (<?= count( $this->_ ['reportedUsers']); ?>) <span class="caret"></span></h4>
				</div>
			</a>
			<div id="collapseSuperuser" class="panel-collapse collapse">
				<div class="panel-body">
					<?php include("superusertools.php");?>			
				</div>
			</div>
		</div>
	<?php } ?>
	
	
	<!--  PERSONAL REPORTED ITEMS  -->
	<div class="panel panel-default">
		<div class="panel-heading">Folgende Inhalte von dir wurden gemeldet</div>
		<?php include("personal_report_table.php")?>
	</div>
	
	
	<!--  MODERATOR REPORTS  -->
	<div class="panel panel-default hidden-xs">
		<div class="panel-heading">
			<img alt="moderator" src="<?= htmlspecialchars(APP_PATH)?>/templates/img/moderator.png"> Folgende Inhalte in deinem Moderationsbereich wurden gemeldet
		</div>
		<?php include("mod_report_table.php");?>
	</div>
	
	
	<div class="modal fade" tabindex="-1" role="dialog"
		aria-labelledby="myLargeModalLabel" aria-hidden="true" id="reportList">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">
						<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
					</button>
					<h4 class="modal-title" id="myModalLabel">Meldungen</h4>
				</div>
				<div class="modal-body">
					<table class="table" data-link="row" id="tableListOfReports">
						<thead>
							<tr>
								<th>Melder</th>
								<th>Datum</th>
								<th>Kommentar</th>
							</tr>
						</thead>
						<tbody id="tablebodyreports">
	
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	

	<!--  PERSONAL QUESTION HISTORY  -->
	<div class="panel panel-default" id="panelQuestionHistoryUser" >
		<div class="panel-heading">
			Letzte Änderungen deiner Inhalte
		</div>
		<div class="panel-body">
			<?php	include("questionhistory.php");?>
		</div>
	</div>
	<!-- LOGGED OUT  -->
<?php } else { 
		include("default_loggedout.php");	
} ?>