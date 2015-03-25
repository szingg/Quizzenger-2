// Quizzenger JavaScript.
$(document).ready(function(e) {
	var quizzenger = new Quizzenger();
	quizzenger.initialize(e);
});

function Quizzenger() {
	var self = this;

	this.initialize = function(e){
		var opquestion = new OpQuestion();
		opquestion.initialize(e);

		var gamification = new Gamification();
		gamification.initialize(e);

		$(".rank").click(function(){
			self.showTooltip("rank", this);
		});
		$(".rank").mouseenter(function(){
			$(this).addClass("achievement-tooltip");
		});
		$(".rank").mouseleave(function(){
			$(this).removeClass("achievement-tooltip");
		});

		$(".point-achievement").click(function(){
			self.showTooltip("point-achievement", this);
		});
		$(".point-achievement").mouseenter(function(){
			$(this).addClass("achievement-tooltip");
		});
		$(".point-achievement").mouseleave(function(){
			$(this).removeClass("achievement-tooltip");
		});
	};

	this.showTooltip= function(cssClass, showElement){
		//hide all
		$("."+cssClass).removeClass("achievement-tooltip");
		//show this tooltip
		$(showElement).addClass("achievement-tooltip");
	}
}


var quizzenger = {
	question : {
		getAttachmentType : function(url) {
			var regexImage = /^.+\.(?:jpe?g|gif|png|bmp)$/
			var regexYouTube = /^(https?\:\/\/)?((www\.)?youtube\.com|youtu\.?be)\/.+$/;
			var regexVimeo = /^(https?\:\/\/)?((www\.)?vimeo\.com)\/.+$/;

			if(regexImage.test(url))
				return 'image';

			if(regexYouTube.test(url))
				return 'youtube';

			if(regexVimeo.test(url))
				return 'vimeo';

			return 'unknown';
		}
	},

	markdown : {
		getEmbedUrlFromYouTubeUrl : function(url) {
			var regex = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
			var matches = url.match(regex);

			if(!matches || matches[2].length !== 11) {
				return '';
			}

			return 'https://www.youtube.com/embed/' + matches[2];
		},

		getEmbedUrlFromVimeoUrl : function(url) {
			var regex = /(?:https?:\/\/)?(?:www\.)?(?:vimeo\.com)\/(.+)/;
			var matches = url.match(regex);

			if(!matches || matches[1].length === 0) {
				return '';
			}

			return 'https://player.vimeo.com/video/' + matches[1];
		},

		generate : function(text, attachment) {
			if(!attachment) {
				return markdown.toHTML(text, 'Gruber');
			}

			// Recursively walk over the markdown tree and replace all [attachment]
			// elements with the corresponding attachment defined for the question.
			var tree = markdown.parse(text);
			(function attachmentWalker(json) {
				if(json[0] === 'link_ref'
					&& json[1].ref === 'attachment')
				{
					switch(quizzenger.question.getAttachmentType(attachment)) {
						case 'image':
							json[0] = 'img';
							json[1] = { 'href' : attachment, 'alt' : 'attachment' };
							break;

						case 'youtube':
							json[0] = 'div';
							json[1] = { 'class' : 'video-container' };
							json[2] = [	'iframe', {
								'src' : quizzenger.markdown.getEmbedUrlFromYouTubeUrl(attachment),
								'frameborder' : '0',
								'allowfullscreen' : ''
							}];
							break;

						case 'vimeo':
							json[0] = 'div';
							json[1] = { 'class' : 'video-container' };
							json[2] = [ 'iframe', {
								'src' : quizzenger.markdown.getEmbedUrlFromVimeoUrl(attachment),
								'frameborder' : '0',
								'allowfullscreen' : ''
							}];
							break;
					}
				}

				for(var i = 1; i < json.length; i++) {
					if(Array.isArray(json[i]))
						json[i].forEach(attachmentWalker);
				}
			})(tree);

			return markdown.renderJsonML(markdown.toHTMLTree(tree, 'Gruber'));
		}
	}
}