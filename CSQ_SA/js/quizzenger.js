// Quizzenger JavaScript.
var quizzenger = {
	embed : {
		embedYouTube : function(url) {
			var regex = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
			var matches = url.match(regex);

			if(!matches || matches[2].length != 11) {
				return "";
			}

			return $('<div>', { 'class' :'video-container' })
				.append($('<iframe>', {
					'src' : 'https://www.youtube.com/embed/' + matches[2],
					'frameborder' : '0',
					'allowfullscreen' : ''
				}));
		}
	},

	markdown : {
		generate : function(markdown) {

		}
	}
};
