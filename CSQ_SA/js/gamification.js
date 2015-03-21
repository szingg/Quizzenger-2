function Gamification(){
	var self = this;

	this.initialize = function(){
		self.showModalNewGameEvent();
		self.saveNewGameEvent();
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
}