function Gamification(){
	var self = this;
	var gameLobbyTimer;

	this.initialize = function(){
		self.showModalNewGameEvent();
		//self.saveNewGameEvent();
		self.joinGameEvent();
		self.leaveGameEvent();
		self.startGameEvent();
		self.stopGameEvent();
		self.gameStartTimer();
		self.gameLobbyTimer();
		self.initTableNewGames();
	};

	this.showModalNewGameEvent = function(){
		$("#newGameDialog").on("show.bs.modal", function(e) {
			//quizName
		    var quizName = $(e.relatedTarget).data("quiz-name");
		    $("#newGameModalLabel").text("Neues Game aus Quiz '" + quizName + "' erstellen");

		    //quizId
		    var quizId = $(e.relatedTarget).data("quiz-id");
		    $("#quizIdModal").val(quizId);
		});
	}

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

	this.startGameEvent = function(){
		$("#startGame").click(function(){
			var gameId = $("#gameId").text();

			$.ajax({
				url: "index.php?view=startGame&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					//nothing, because you will be redirected
				}
			});
		});
	}

	this.stopGameEvent = function(){
		$("#stopGame").click(function(){
			var gameId = $("#gameId").text();

			$.ajax({
				url: "index.php?view=stopGame&type=ajax&gameid="+gameId,
				type: "GET",
				contentType: false,
				cache: false,
				processData:false,
				complete: function(data){
					//nothing, because you will be redirected
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
		if(! self.contains(document.URL, 'view=gamestart')) return;
		var gameId = $("#gameId").text();

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
						window.location.href = "index.php?view=gamequestion&gameid=" + resp.gameinfo.game_id;
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


}