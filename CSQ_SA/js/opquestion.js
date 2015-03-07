function OpQuestion(){
	var self = this;

	this.initialize = function(){
		uploadAttachment();
		$("#btn-attach-file").click(function(){
			$("#modalAttachFile").modal('show');
		});

	};

	//sends the attachment to the server and displays the response
	var uploadAttachment = function(){

		$("#btn-uploadfile").click(function(e) {
			e.preventDefault();
			var form = $("#form-uploadFile")[0];
			$.ajax({
				url: "quizzenger/fileupload/fileupload.php", // Url to which the request is send
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
					$("#modalAttachFile").modal('hide');
					$("#msg-upload").removeClass("text-success");
					$("#msg-upload").removeClass("text-danger");
					
					if($(data).has("responseJSON") && data.responseJSON !== undefined ){
						if(data.responseJSON.result == "success"){

							$("#msg-upload").addClass("text-success");
						}
						else{
							$("#msg-upload").addClass("text-danger");
						}
						$("#msg-upload").text(data.responseJSON.message);
					}
					else{
						$("#msg-upload").addClass("text-danger");
						$("#msg-upload").text("Upload failed.");
						//$("#messageTemp").html(data.responseText);
					}
				}
			});
		});
	};
};