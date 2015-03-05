function OpQuestion(){
	var self = this;

	this.initialize = function(e){
		uploadAttachment(e);
		$("#btn-attach-file").click(function(){
			$("#modalAttachFile").modal('show');
		});

	};

	var ajax = function(url, type){
		var xmlhttp;
		if (window.XMLHttpRequest) {
			xmlhttp = new XMLHttpRequest();
		}
		xmlhttp.open(type, url, true);		
		xmlhttp.send();
	}

	var ajaxPOST = function(url){
		ajax(url, "POST");
	}
	var ajaxGET = function(url){
		ajax(url, "GET");
	}

	var uploadAttachment = function(e){

		$("#btn-uploadfile").click(function(e) {
			e.preventDefault();
			//var fileSelected = $("#file-seleced");
			//var file = $("#file-selected")[0].attr("files");
			//if(file.length < 1) return;

			//var form = $("#form-uploadFile").first();
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
				complete: function(data, d1, d2, d3){
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
						$("#messageTemp").html(data.responseText);
					}
				}
			});
		});
	};
};