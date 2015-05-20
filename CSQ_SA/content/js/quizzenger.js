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

		self.initAchievements();
		self.initRanks();
		self.initRanklist();
		self.loadTabPage();

	};

	this.initRanklist = function(){
		$("#ranklist tr").click(function(){
			//hide all
			$('#ranklist tr').find('#ranklistTooltip').removeClass("display-inline");
			$('#ranklist tr').find('#ranklistTooltip').addClass("hide");
			//show single tooptip
			$(this).find('#ranklistTooltip').addClass("display-inline");
			$(this).find('#ranklistTooltip').removeClass("hide");
		});
		$("#ranklist tr").mouseenter(function(){
			$(this).find('#ranklistTooltip').removeClass("hide");
			$(this).find('#ranklistTooltip').addClass("display-inline");
		});
		$("#ranklist tr").mouseleave(function(){
			$(this).find('#ranklistTooltip').removeClass("display-inline");
			$(this).find('#ranklistTooltip').addClass("hide");
		});
	}


	this.initAchievements = function(){
		$(".point-achievement").click(function(){
			self.showTooltip("point-achievement", this);
		});
		$(".point-achievement").mouseenter(function(){
			$(this).addClass("achievement-tooltip");
		});
		$(".point-achievement").mouseleave(function(){
			$(this).removeClass("achievement-tooltip");
		});
	}

	this.initRanks = function(){
		$(".rank").click(function(){
			self.showTooltip("rank", this);
		});
		$(".rank").mouseenter(function(){
			$(this).addClass("achievement-tooltip");
		});
		$(".rank").mouseleave(function(){
			$(this).removeClass("achievement-tooltip");
		});
	}

	this.showTooltip= function(cssClass, showElement){
		//hide all
		$("."+cssClass).removeClass("achievement-tooltip");
		//show this tooltip
		$(showElement).addClass("achievement-tooltip");
	}

	this.loadTabPage = function(){
		if(self.contains(document.URL, 'view=learn')
			 && self.contains(document.URL, '#gamelobby')){
			$('#gameLobbyEvent').trigger('click');
		}
		if(self.contains(document.URL, 'view=mycontent')){
			if( self.contains(document.URL, '#myquizzes')){
				$('#myQuizzesEvent').trigger('click');
			}
			if( self.contains(document.URL, '#mygames')){
				$('#myGamesEvent').trigger('click');
			}
		}
	}
	/*
	* Checks if obj1 contains obj2
	* @return Returns true if contains, else false
	*/
	this.contains = function(obj1, obj2){
		return obj1.indexOf(obj2) > -1;
	}
}

function htmlspecialchars(string, quote_style, charset, double_encode) {

  var optTemp = 0,
    i = 0,
    noquotes = false;
  if (typeof quote_style === 'undefined' || quote_style === null) {
    quote_style = 2;
  }
  string = string.toString();
  if (double_encode !== false) { // Put this first to avoid double-encoding
    string = string.replace(/&/g, '&amp;');
  }
  string = string.replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');

  var OPTS = {
    'ENT_NOQUOTES': 0,
    'ENT_HTML_QUOTE_SINGLE': 1,
    'ENT_HTML_QUOTE_DOUBLE': 2,
    'ENT_COMPAT': 2,
    'ENT_QUOTES': 3,
    'ENT_IGNORE': 4
  };
  if (quote_style === 0) {
    noquotes = true;
  }
  if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
    quote_style = [].concat(quote_style);
    for (i = 0; i < quote_style.length; i++) {
      // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
      if (OPTS[quote_style[i]] === 0) {
        noquotes = true;
      } else if (OPTS[quote_style[i]]) {
        optTemp = optTemp | OPTS[quote_style[i]];
      }
    }
    quote_style = optTemp;
  }
  if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
    string = string.replace(/'/g, '&#039;');
  }
  if (!noquotes) {
    string = string.replace(/"/g, '&quot;');
  }

  return string;
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
							json[1] = {'href' : attachment, 'alt' : 'attachment', 'class' : 'image-container'};
							break;

						case 'youtube':
							json[0] = 'div';
							json[1] = { 'class' : 'video-container'};
							json[2] = [	'iframe', {
								'src' : quizzenger.markdown.getEmbedUrlFromYouTubeUrl(attachment),
								'frameborder' : '0',
								'allowfullscreen' : ''
							}];
							break;

						case 'vimeo':
							json[0] = 'div';
							json[1] = { 'class' : 'video-container'};
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