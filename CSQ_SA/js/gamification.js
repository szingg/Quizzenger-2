function Gamification(){
	var self = this;
	var gameLobbyTimer;

	this.initialize = function(){
		self.showModalNewGameEvent();
		self.saveNewGameEvent();
		self.joinGameEvent();
		self.leaveGameEvent();
		self.gameLobbyTimer();
	};

	this.showModalNewGameEvent = function(){
		$("#newGameDialog").on("show.bs.modal", function(e) {
			//quizName
		    var quizName = $(e.relatedTarget).data("quiz-name");
		    $("#newGameModalLabel").text("Neues Game aus Quiz '" + quizName + "' erstellen");

		    //quizId
		    var quizId = $(e.relatedTarget).data("quiz-id");
		    $("#quizIdModal").text(quizId);
		});
	}

	this.saveNewGameEvent = function(){
		$("#saveNewGame").click(function(e){
			var quizId = $("#quizIdModal").text();
			var gameName = $("#gameNameModal").val();
			if(gameName == false){
				alert("Bitte Feld einfüllen.");
				return;
			}
			window.location.href = "index.php?view=gamenew&quizid="+quizId+"&gamename="+gameName;
		});
	}

	this.joinGameEvent = function(){
		$("#joinGame").click(function(){
			var gameId = $("#gameId").text();

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
			var gameId = $("#gameId").text();

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

	this.gameLobbyTimer = function(){
		$("#gameLobby").focus(function(e){
			alert("focus");
		});
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
					$("#tableBodyOpenGames").html("");
					$(data.responseJSON.data).each(function(id, game){
						self.appendTemplateToContainer("dot-openGameRow", game, "tableBodyOpenGames"));
					});
					$("#tableOpenGames").DataTable().draw();
				}
			});
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


}