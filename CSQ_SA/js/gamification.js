function Gamification(){
	var self = this;
	var gameLobbyTimer;

	this.initialize = function(){
		//GameLobby
		//self.showModalNewGameEvent();
		self.initTableNewGames();
		self.gameLobbyTimer();
		//GameStart
		self.joinGameEvent();
		self.leaveGameEvent();
		self.startGameEvent();
		self.gameStartTimer();
		//GameReport
		self.gameReportTimer();
	};
/*
	this.showModalNewGameEvent = function(){
		$("#newGameDialog").on("show.bs.modal", function(e) {
			//quizName
		    var quizName = $(e.relatedTarget).data("quiz-name");
		    $("#newGameModalLabel").text("Neues Game aus Quiz '" + quizName + "' erstellen");

		    //quizId
		    var quizId = $(e.relatedTarget).data("quiz-id");
		    $("#quizIdModal").val(quizId);
		});
	} */

	this.saveNewGameEvent = function(){
		/*$('submitNewGame').submit(function() {
  			return false;
		}); */
		/*$("#saveNewGame").submit(function(e){
			var quizId = $("#quizIdModal").text();
			var gameName = $("#gameNameModal").val();
			if(gameName == false){
				alert("Bitte Feld einfüllen.");
				return;
			}
			window.location.href = "index.php?view=gamenew&quizid="+quizId+"&gamename="+gameName;
		}); */
	}

	this.joinGameEvent = function(){
		$("#joinGame").click(function(){
			var gameId = self.getUrlParameter('gameid');

			$.ajax({
				url: "index.php?view=joinGame&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					if(data.responseJSON !== undefined && data.responseJSON.result == "success"){
						$("#leaveGame").parent().removeAttr('hidden');
						$("#joinGame").parent().attr('hidden', 'true');
					}
					else{
						alert("Something went wrong");
					}
				}
			});
		});
	}

	this.leaveGameEvent = function(){
		$("#leaveGame").click(function(){
			var gameId = self.getUrlParameter('gameid');

			$.ajax({
				url: "index.php?view=leaveGame&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					$("#joinGame").parent().removeAttr('hidden');
					$("#leaveGame").parent().attr('hidden', 'true');
				}
			});
		});
	}

	this.startGameEvent = function(){
		$("#startGame").click(function(){
			var gameId = self.getUrlParameter('gameid');

			$.ajax({
				url: "index.php?view=startGame&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					$("#startGame").val("Game gestartet");
				}
			});
		});
	}

	this.initTableNewGames = function(){
		$('#tableNewGame tbody').on( 'click', 'tr', function () {
			var x = $(this).find('input[type=radio]');
        	$(this).find('input[type=radio]').prop('checked', true);
    		$('#tableNewGame tbody > tr').removeClass('success');
        	$(this).addClass('success');
    	} );
	}

	this.gameStartTimer = function(){
		var gameId = self.getUrlParameter('gameid');
		if(! self.contains(document.URL, 'view=GameStart&gameid='+gameId)) return;

		window.setInterval(function(){
			$.ajax({
				url: "index.php?view=getGameStartInfo&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					if(data.responseJSON === undefined) return;
					var resp = data.responseJSON.data;

					//startGame
					if(resp.gameinfo.has_started != null){
						window.location.href = "index.php?view=GameQuestion&gameid=" + resp.gameinfo.game_id;
					}

					//updateMembers
					$('#participantCount').text(resp.members.length);
					$('#participantList').html('');
					$(resp.members).each(function(id, member){
						$('#participantList').append('<li>' + member.member +'</li>');
					});
				}
			});
		}, 2000);
	}

	this.gameLobbyTimer = function(){
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e){
			if(e.currentTarget.hash == "#generateQuiz"){
				//removeTimer
				clearInterval(gameLobbyTimer);
			}
			if(e.currentTarget.hash == "#gameLobby"){
				//setTimer
				gameLobbyTimer = window.setInterval(function(){
					self.updateOpenGames();
				}, 2000);
			}
		});
	}

	this.updateOpenGames = function(){

		$.ajax({
				url: "index.php?view=getOpenGames&type=ajax",
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					if(data.responseJSON === undefined) return;
					//$("#tableBodyOpenGames").html("");
					var table = $('#tableOpenGames').DataTable();
					//table.destroy();
					table.rows().remove();
					var template = '#dot-openGameRow';
					$(data.responseJSON.data).each(function(id, game){
						var tempHtml = (doT.template($(template).text()))(game);
						table.row.add($(tempHtml));
						//self.appendTemplateToContainer("dot-openGameRow", game, "tableBodyOpenGames");
					});
				/*	$('#tableOpenGames').DataTable( {
        				responsive: true
    				} ); */
					table.draw();
				}
			});

		/*
		var table = $('#tableOpenGames');
		var x = table.data();
		var i = 1;
		//table.ajax.url('index.php?view=getOpenGames&type=ajax');
		/*table.ajax.reload( function(data, dt){
			var x = 1;
		}, false ); */
	}

	this.gameReportTimer = function(){
		var gameId = self.getUrlParameter('gameid');
		if(! (self.contains(document.URL, 'view=GameQuestion&gameid='+gameId)
			|| self.contains(document.URL, 'view=GameSolution&gameid='+gameId)
			|| self.contains(document.URL, 'view=GameEnd&gameid='+gameId))) return;

		window.setInterval(function(){
			self.updateGameReport();
		}, 1000);
	}

	this.updateGameReport = function(){
		var gameId = self.getUrlParameter('gameid');

		$.ajax({
				url: "index.php?view=getGameReport&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(resp){
					if(resp.responseJSON === undefined || resp.responseJSON.data == undefined) return;
					var data = resp.responseJSON.data;
					/*
						'gameReport' => $gameReport,
						'gameInfo' => $gameinfo,
						'timeToEnd' => $timeToEnd,
						'userId'
						'durationSec' => $durationSec,
						'progressCountdown' => $progressCountdown
					*/
					//set Countdown
					if(data.timeToEnd > 0){
						var formatTimeToEnd = self.formatSeconds(data.timeToEnd);
						self.applyTemplate("dot-gameReportCountdown", {
							'progressCountdown' : data.progressCountdown,
							'formatTimeToEnd' : formatTimeToEnd
						}, "gameCountdown");
					}
					else{
						//redirect to GameEnd view if not already on this view
						if(! self.contains(document.URL, 'view=GameEnd')){
							window.location.href = "index.php?view=GameEnd&gameid=" + data.gameInfo.game_id;
						}
					}

					$('#gameReport').html('');
					//set GameReport
					$(data.gameReport).each(function(id, report){
						var isCurrentUser = report.user_id == data.userId;

						var correct = 100/report.totalQuestions * report.questionAnsweredCorrect;
						var wrongCount = report.questionAnswered - report.questionAnsweredCorrect;
						var wrong = 100 / report.totalQuestions * wrongCount;
						var togo = 100 / report.totalQuestions * (report.totalQuestions - report.questionAnswered);
						var togoCount = report.totalQuestions - report.questionAnswered;

						var formatTimePerQuestion = self.formatSeconds(report.timePerQuestion);
						var formatTotalTimeInSec = self.formatSeconds(report.totalTimeInSec);
						self.appendTemplateToContainer("dot-gameReportRow", {
							'report' : report,
							'isCurrentUser' : isCurrentUser,
							'correct' : correct,
							'wrongCount' : wrongCount,
							'wrong' : wrong,
							'togo' : togo,
							'togoCount' : togoCount,
							'formatTimePerQuestion' : formatTimePerQuestion,
							'formatTotalTimeInSec' : formatTotalTimeInSec
						}, "gameReport");
					});
				}
			});
	}

	/*
	 * Returns a string like '1 Std 6 Min 5 Sek'
	* @param sec total seconds
	*/
	this.formatSeconds = function(sec){
		var hours = parseInt(sec / 3600);
		sec = sec % 3600;
		var minutes = parseInt(sec / 60);
		var seconds = sec % 60;
		return (hours > 0?hours+' Std ':'')+(minutes > 0?minutes+' Min ':'')+(seconds > 0?seconds+' Sek':'');
	}

	this.applyTemplate = function(template, parameters, container) {
		container = "#" + container;
		template = "#" + template;
		$(container).html((doT.template($(template).text()))(parameters));
	};

	this.appendTemplateToContainer = function(template, parameters, container) {
		container = "#" + container;
		template = "#" + template;
		$(container).html($(container).html() + (doT.template($(template).text()))(parameters));
	};

	/*
	* Checks if obj1 contains obj2
	* @return Returns true if contains, else false
	*/
	this.contains = function(obj1, obj2){
		return obj1.indexOf(obj2) > -1;
	};

	this.getUrlParameter = function(sParam){
	    var sPageURL = window.location.search.substring(1);
	    var sURLVariables = sPageURL.split('&');
	    for (var i = 0; i < sURLVariables.length; i++)
	    {
	        var sParameterName = sURLVariables[i].split('=');
	        if (sParameterName[0] == sParam)
	        {
	            return sParameterName[1];
	        }
	    }
	}


}