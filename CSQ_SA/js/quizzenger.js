// Quizzenger JavaScript.
$(document).ready(function(e) {
	var quizzenger = new Quizzenger();

	quizzenger.initialize(e);
});

function Quizzenger(){
	var self = this;

	this.initialize = function(e){
		var opquestion = new OpQuestion();
		opquestion.initialize(e);
		
	};
};