/**
 * jQuery Incipit
 * A lightweight jQuery plugin to display highly captivating loading screens.
 * https://github.com/justineSimmet/jquery-incipit
 *
 * Licensed under the MIT license.
 * Copyright 2017 Justine Simmet
 * https://github.com/justineSimmet
 */

(function($) {
    
    var _PATH = _GetFilePath();

    // Default Settings
    var _defaults = {
        backgroundColor : "#FFFFFF",
		borderColor : "#CCCCCC",
		textColor : "#000000",
		spanColor : "#7e7e7e",
        icon : "fading-squares",
		language : "en",
		note : false,
		noteCustom : "",
		logo : false,
		logoSrc : _PATH+'image/your_logo.svg'
    };

	// Default Language Settings in french
	var _fr = {
		defaultError : {
		content : "Je descendais le dernier coteau du Canigou, et, bien que le soleil f没t d茅j脿 couch茅, je distinguais dans la plaine les maisons de la petite ville d鈥橧lle, vers laquelle je me dirigeais.",
			author : "Prosper M茅rim茅e",
			origin : "La V茅nus d'Ille",
		},
		defaultSource : _PATH+"incipit-src.fr.json",
		noteSentence : "Merci de patienter un instant."
	};

	// Default Language Settings in english
	var _en = {
		defaultError : {
			content : "It is a truth universally acknowledged, that a single man in possession of a good fortune, must be in want of a wife.",
			author : "Jane Austen",
			origin : "Pride and Prejudice",
		},
		defaultSource : _PATH+"incipit-src.en.json",
		noteSentence : "Please hold for a moment."
	};

    /**
	 * Use to manage the default settings
	 * @access public
     * @param {Array|object} settings - based on _defaults
     */
    $.IncipitSetup = function(settings){
     $.extend(true, _defaults, settings);
    };

    /**
     * Initiate the plugin at the begin. Allowed new settings.
	 * @access public
     * @param {Array|object} options - based on _defaults
     */
    $.fn.IncipitInit = function(options)
    {
		// Set standard DOM container
		if(options !== undefined ){
			$.IncipitSetup(options);
		}
      var settings    = $.extend(true, {}, _defaults, options),
			icon 		= _GetSelectIcon(settings.icon),
      incipitDiv  = '<div id="incipitContent"><blockquote> </blockquote> </div>',
			contentStyle 	= {
				'background-color' : settings.backgroundColor
			},
			blockquoteStyle 	= {
				'border-top' :'1px solid '+ settings.borderColor+'',
				'border-bottom' :'1px solid '+ settings.borderColor+''
			};
      this.prepend(incipitDiv);

		  // Set correct note if true, else only insert the animate-icon
  		if(settings.note){
  			if(settings.noteCustom !== ""){
          var note = settings.noteCustom;
          $('#incipitContent').prepend('<img src="'+icon+'"/><p style="">'+note+'</p>');
  			}
        else{
    			var note = "";
    			switch(settings.language.toLowerCase()) {
    				case "fr":
    					note = _fr.noteSentence;
    					break;
    				case "en":
    					note = _en.noteSentence;
    					break;
    			}
    			$('#incipitContent').prepend('<img src="'+icon+'" style="margin-bottom: 1rem"/><p style="font-style: italic">'+note+'</p>');
        }
  		}
  		else{
  			$('#incipitContent').prepend('<img src="'+icon+'" style="margin-bottom: 1rem"/>');
  		}

  		// Set ogo if truethe l
  		if(settings.logo){
  			$('#incipitContent').append('<img src="'+settings.logoSrc+'" style="margin-top: 1rem" />');
  		}

  		$('<style>#incipitContent blockquote:after{color:'+settings.borderColor+'}</style>').appendTo('head');
          $('#incipitContent').css(contentStyle).find('blockquote').css(blockquoteStyle);
      };

    /**
     * Call the plugin with a specific action.
	 * @access public
     * @param {string} action - show or hide
     */
    $.Incipit = function(action){
        switch (action.toLowerCase()) {
            case "show":
                _Show();
                break;
            case "hide":
                _Hide();
                break;
        }
    };

	$.IncipitDestroy = function(){
		_Hide();
		$.extend(true, {}, _defaults);
		$('#incipitContent').remove();
	};

    /**
	 * Display the plugin based on previously initiated elements.
     * @access private
     */
    function _Show(){
        var incipitDiv          = $('#incipitContent')
            // incipitBlockquote   = incipitDiv.find('blockquote');

		    var settings    = $.extend(true, {}, _defaults);

        _Resize();

        // Get then display Incipit Plugin data
        // _GetIncipitSource(incipitBlockquote, settings);
        // incipitBlockquote.animate({
        //     opacity: 1
        // }, 300 );
        incipitDiv.css('display', 'flex').animate({
            opacity: 1
        }, 500 );
    }

    /**
     * Hide the plugin based on previously initiated elements.
	 * @access private
     */
    function _Hide(){        

        // Hide Incipit Plugin data and display elements on the current
        $('#incipitContent').animate({
            opacity: 0
        }, 500 ).find('blockquote').animate({
            opacity: 0
        }, 300 ).html('');

        setTimeout(function(){
          $('#incipitContent').css('display', 'none');
          _Resize('reverse');
        }, 800);

    }

    /**
     * Select and insert a data from a local JSon object.
	 * @access private
     * @param {string} target - the dom element created by IncipitInit
     */
    function _GetIncipitSource(target, settings){
		var translate = {};
		switch (settings.language.toLowerCase()) {
			case 'fr':
				translate = _fr;
				break;
			case 'en':
				translate = _en;
				break;
		}

      _LoadIncipitSource(translate.defaultSource, function(response) {
			if(response !== null){
				var data            = JSON.parse(response),
					ressource       = data.material,
					maxLenght       = ressource.length,
					randomIncipit   = Math.floor(Math.random() * (maxLenght - 0)),
					outpuIncipit    = ressource[randomIncipit],
					formatedOutput  = '<p class="incipit-text" style="color :'+settings.textColor+'">'+outpuIncipit.content+'</p><p class="incipit-source">'+outpuIncipit.author+'<span style="color :'+settings.spanColor+'">, '+outpuIncipit.origin+'</span></p>';
				target.html(formatedOutput);
			}
			else{
				var defaultError = translate.defaultError;
				var formatedOutput  = '<p class="incipit-text" style="color :'+settings.textColor+'">'+defaultError.content+'</p><p class="incipit-source">'+defaultError.author+'<span style="color :'+settings.spanColor+'">, '+defaultError.origin+'</span></p>';
				target.html(formatedOutput)
			}
        });
    }

    /**
	 * Fetch the JSon object incipit-src.json
	 *
     * @param {function} callback - the callback function to treat the XMLHttpRequest() object.
     */
    function _LoadIncipitSource(source, callback) {
        var xobj = new XMLHttpRequest();
        xobj.overrideMimeType("application/json");
        xobj.open('GET', source, true);
        xobj.onreadystatechange = function() {
            if (xobj.readyState == 4 && xobj.status == 200) {
                callback(xobj.response);
            }else{
				callback(null);
			}
        };
        xobj.send(null);
    }

	/**
	 * Fetch the right icon
	 * @access private
	 * @param string
	 * @returns {string}
	 */
	function _GetSelectIcon(string){
    var scripts = document.getElementsByTagName('script');
    var index = scripts.length - 1;
    var myScript = scripts[index];
    
		var href = '';
		switch (string.toLowerCase()) {
			case "arrow":
				href = _PATH+"image/arrow.svg";
				break;
			case "download":
				href = _PATH+"image/download.svg";
				break;
			case "fading-balls":
				href = _PATH+"image/fading_balls.svg";
				break;
			case "fading-lines":
				href = _PATH+"image/fading_lines.svg";
				break;
			case "fading-squares":
				href = _PATH+"image/fading_squares.svg";
				break;
			case "oval-circle":
				href = _PATH+"image/oval_circle.svg";
				break;
			case "penduleum":
				href = _PATH+"image/penduleum.svg";
				break;
			case "round-block":
				href = _PATH+"image/round_block.svg";
				break;
			case "solid-snake":
				href = _PATH+"image/solid_snake.svg";
				break;
			case "upload":
				href = _PATH+"image/upload.svg";
				break;
			default :
				href = _PATH+"image/fading_squares.svg";
				break;
		}

		return href;
	};

  /**
   * Get the real path of the current script file
   * @access private
   * @return string
   */
  function _GetFilePath(){
    var currentFile = document.currentScript.src;
    var urlCount = currentFile.lastIndexOf('/') +1;
    return currentFile.substring(0,urlCount);
  }

  /**
   * Action on the body size
   * @param {string} [action] define wich action to do, progress or reverse
   * @access private
   */
  function _Resize(action = "progress"){
    if( action == "progress"){
      var screeWidth = $(window).width(),
          screenHeight = $(window).height();
      $("body").css('height', screenHeight);
      $("body").css('width', screeWidth);
      $("body").css("overflow", 'hidden');
    }
    if( action == "reverse"){
      $("body").removeAttr("style");
    }
  }
})(jQuery);