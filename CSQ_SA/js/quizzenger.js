// Quizzenger JavaScript.
var quizzenger = {
	question : {
		getAttachmentType : function(url) {
			var regexImage = /^https?:\/\/(?:[a-z\-]+\.)+[a-z]{2,6}(?:\/[^\/#?]+)+\.(?:jpe?g|gif|png)$/
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
				else if(Array.isArray(json[1])) {
					json[1].forEach(attachmentWalker);
				}
				else if(Array.isArray(json[2])) {
					json[2].forEach(attachmentWalker);
				}
			})(tree);

			return markdown.renderJsonML(markdown.toHTMLTree(tree, 'Gruber'));
		}
	}
};