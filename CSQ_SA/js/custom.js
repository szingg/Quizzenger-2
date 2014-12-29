function addChosenCategory(id, name){
	existingBadge = document.getElementById("selectedCategory" + id);
	if(existingBadge == null){
		element = document.getElementById("generatorSelectedCategories");
		
		var nametext = document.createTextNode(name);
		var closetext = document.createTextNode("Close");
		var hiddentext = document.createTextNode("×");
		
		var input = document.createElement("input");
		input.setAttribute("type", "hidden");
		input.setAttribute("name", "quiz_generator_form_category[]");
		input.setAttribute("value", id);
		input.setAttribute("id", "selectedCategory" + id)
		
		var badge = document.createElement("div");
		badge.setAttribute("class", "alert alert-info alert-dismissible category-badge");
		badge.setAttribute("role", "alert");
		
		var closeButton = document.createElement("button");
		closeButton.setAttribute("type", "button");
		closeButton.setAttribute("class", "close");
		closeButton.setAttribute("data-dismiss", "alert");
		closeButton.onclick = function() {
			element.removeChild(input);
		}
		
		var span1 = document.createElement("span");
		span1.setAttribute("aria-hidden", "true");
		
		var span2 = document.createElement("span");
		span2.setAttribute("class", "sr-only");
		
		span1.appendChild(hiddentext);
		span2.appendChild(closetext);
		closeButton.appendChild(span1);
		closeButton.appendChild(span2);
		badge.appendChild(closeButton);
		badge.appendChild(nametext);
		element.appendChild(badge);
		
		element.appendChild(input);
	}
}

function submitAddQuestionToQuiz(quizId){
	document.getElementById('inputquizid').value = quizId;
	document.getElementById('addToQuizForm').submit();
}

function setReportRating(id){
	document.getElementById('ratingToReport').value=id;
}

function setEditQuizName(id,currentName){ 
	document.getElementById('editQuizNameID').value=id;
	document.getElementById('quizNameField').value=currentName;
}

function setRemoveRating(id){
	document.getElementById('ratingToRemove').value=id;
}

