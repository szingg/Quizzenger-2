function OpQuestion(){
	var self = this;

	this.initialize = function(){
		uploadAttachmentEvent();
		showModalAttachFileEvent();
		togglePanelsEvent();
		checkLinkEvent();
	};

	var showModalAttachFileEvent = function(){
		$("#btn-attach-file").click(function(){
			$("#modalAttachFile").modal('show');
		});
	}

	var togglePanelsEvent = function(){
		$(".panel-heading.clickable-toggle").click(function(){
			$(".panel-collapse.collapse").collapse("toggle");
		});
	}

	var checkLinkEvent = function(){
		$("#btn-checkLink").click(function(){
			if(validateUrl($("#inputLink").val())){
				$("#msg-attach").text("Video erfolgreich eingebettet!");
				$("#msg-link").text("");
				$("#modalAttachFile").modal('hide');
				//write values to opquestion form
				$("#opquestion_form_attachment").val($("#inputLink").val());
				$("#opquestion_form_attachmentLocal").val("0"); //false
			}
			else{
				$("#msg-link").text("Link ist ungültig.");
			}
		});
	}

	var validateUrl = function(value){
    	var regex = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.‌​\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[‌​6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1‌​,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00‌​a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u‌​00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
		return regex.test(value);
	}

	//sends the attachment to the server and displays the response
	var uploadAttachmentEvent = function(){

		$("#btn-uploadfile").click(function(e) {
			e.preventDefault();
			var form = $("#form-uploadFile")[0];
			$.ajax({
				//url: "quizzenger/fileupload/fileupload.php", // Url to which the request is send
				url: "index.php?view=fileupload&type=ajax",
				type: "POST",             // Type of request to be send, called as method
				data: new FormData(form), // Data sent to server, a set of key/value pairs (i.e. form fields and values)
				//data: file[0],
				contentType: false,       // The content type used when sending data to the server.
				cache: false,             // To unable request pages to be cached
				processData:false,        // To send DOMDocument or non processed data file it is set to false
				success: function(data)   // A function to be called if request succeeds
				{
				},
				complete: function(data){
					if($(data).has("responseJSON") && data.responseJSON !== undefined ){
						if(data.responseJSON.result == "success"){
							$("#msg-attach").text(data.responseJSON.message);
							$("#msg-upload").text("");
							$("#modalAttachFile").modal('hide');
							//write values to opquestion form
							$("#opquestion_form_attachmentTempFileName").val($("#selectedFile").val()); //full filename
							$("#opquestion_form_attachment").val($("#selectedFile").val().split('.').pop()); //file-extension
							$("#opquestion_form_attachmentLocal").val("1"); //true
							return;
						}
						else{
							$("#msg-upload").text(data.responseJSON.message);
							return;
						}
					}
					else{
						$("#msg-upload").text("Upload failed.");
						//$("#messageTemp").html(data.responseText);
					}
				}
			});
		});
	};
};