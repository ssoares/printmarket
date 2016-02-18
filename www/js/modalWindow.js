/*
 * Create a pop-up to display alerts or messages.
 * author: ssoares
 */
(function($) {
    var defaults = {
        element: {},
        langId: 1,
        containerId: '',
        category: 0,
        baseUrl : ''
    };
    $.fn.modalWindow = function(method){
        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.modalWindow' );
        }
    }
    var methods = {
        init : function( options ) {
            $.extend(defaults,options);
            var o = $.extend({}, defaults, options);
            return this.each(function(){
                $(this).data('modalWindow-options', options)
                $('a.close, #fade').bind('click', methods.close());
                methods.display(o.element, o.langId, o.containerId);
                return false;
            });

        },
        display: function(element, langId, containerId)
        {
            if  (element == undefined)
                element = $(this);

            this.popID = element.attr('rel'); //Get Popup Name
            var popID = this.popID;
            var popURL = element.attr('href'); //Get Popup href to define size

            //Pull Query & Variables from href URL
            var query= popURL.split('?');
            var dim= query[1].split('&');
            var popWidth = dim[0].split('=')[1]; //Gets the first query string value

            var param = 0;
            var catId = this.category;
            if (dim.length > 1)
            {
                param = dim[1].split('=')[1];
                catId = 0;
            }

            var relValue = element.attr('rel');
            if (relValue == 'hiddenStaticText' || relValue == 'hiddenConfidential' || relValue == 'hiddenAgreement')
            {
                this._appendContent(catId, popID, langId, param, popWidth);
            }
            // Get the text and add it to the body
            else if (catId != undefined)
            {
                this._addText(catId, popID, langId, param, popWidth);
            }
            else if (containerId != undefined)
            {
                this._displayMessage(catId, popID, langId, param, popWidth, containerId);
            }
        },

        close: function()
        {
            //Close Popups and Fade Layer
            $('#fade , .popup_block').fadeOut(function() {
            $('#fade, a.close').remove();  //fade them both out
            return false;
            });
        },
        _addText: function(catId, popID, langId, param, popWidth)
        {
            $.getJSON(
                this.baseUrl + '/catalog/index/category-texts/',
                {
                    categoryId: catId,
                    typeText: this.popID,
                    langId : langId,
                    param : param
                },
                function(data){
                    var title = data.TITLE;
                    var text  = data.TEXT;
                    var popID = this.popID;
                    $('#' + popID).remove();

                    var content = '<div id="' + popID + '" class="popup_block">';
                    //                    content += '<h2>' + title + '</h2>';
                    content += text;
                    content += '</div>';
                    $('body').append(content);
                    var popHeight = $('#' + popID).height();

                    //Get the window height and width
                    var winH = $(window).height();
                    var winW = $(window).width();
                    var height = popHeight + 10;
                    var topVal = winH/2-popHeight/2;
                    var leftVal = winW/2-$('#' + popID).width()/2;
                    var roundValue = Math.round(winH * 0.90);
                    if (popHeight > roundValue || (popHeight + topVal) > roundValue)
                    {
                        height = roundValue;
                        topVal = 10;
                    }

                    //Fade in the Popup and add close button
                    $('#' + popID).fadeIn().css({
                        'width': Number( popWidth ),
                        'height' : height
                    }).prepend('<a href="#" class="close"><img src="' + this.baseUrl + '/themes/default/images/common/close.png" class="btn_close" title="Close Window" alt="Close" /></a>');

                    //Set the popup window to center
                    $('#' + popID).css('top', topVal );
                    $('#' + popID).css('left',leftVal );
                    //Fade in Background
                    //Add the fade layer to bottom of the body tag.
                    $('body').append('<div id="fade"></div>');
                    //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies
                    $('#fade').css({
                        'filter' : 'alpha(opacity=80)'
                    }).fadeIn();

                    return false;
                }
                );
        },
        _appendContent: function(catId, popID, langId, param, popWidth, containerId)
        {
            var content   = $('#' + popID).html();
            var popHeight = $('#' + popID).height();
            $('#' + popID).remove();
            $('body').append('<div id="' + popID + '" class="popup_block">' + content + '</div>');
            //Get the window height and width
            var winH = $(window).height();
            var winW = $(window).width();
            var topVal = winH/2-popHeight/2;
            var leftVal = winW/2-popWidth/2;
            var height = popHeight + 10;
            var roundValue = Math.round(winH * 0.90);
            if (popHeight > roundValue || (popHeight + topVal) > roundValue)
            {
                height = roundValue;
                topVal = 10;
                //Fade in the Popup and add close button
                $('#' + popID).fadeIn().css({
                    'width': Number( popWidth ),
                    'height' : height
                }).prepend('<a href="#" class="close"><img src="' + this.baseUrl + '/themes/default/images/common/close.png" class="btn_close" title="Close Window" alt="Close" /></a>');
            }
            else
            {
                $('#' + popID).fadeIn().css({
                    'width': Number( popWidth )
                }).prepend('<a href="#" class="close"><img src="' + this.baseUrl + '/themes/default/images/common/close.png" class="btn_close" title="Close Window" alt="Close" /></a>');
            }

            //Set the popup window to center
            $('#' + popID).css('top', topVal );
            $('#' + popID).css('left',leftVal );

            //Fade in Background
            $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
            $('#fade').css({
                'filter' : 'alpha(opacity=80)'
            }).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies

            return false;

        },
        _displayMessage: function(catId, popID, langId, param, popWidth, containerId)
        {
            var title = $(containerId).children('p.title').text();
            var text  = $(containerId).children('div.content').html();

            var content = text;

            $('body').append('<div id="' + popID + '" class="popup_block facebook">' + content + '</div>');
            var containerPopup = $('#' + popID);
            //Get the window height and width
            var popHeight = containerPopup.height();
            var winH = $(window).height();
            var winW = $(window).width();
            var topVal = winH/2-popHeight/2;
            var leftVal = winW/2-popWidth/2;
            var height = popHeight + 10;
            var roundValue = Math.round(winH * 0.90);
            var style = {
                'width': Number( popWidth ),
                'position': 'absolute'
            };
            if (popHeight > roundValue || (popHeight + topVal) > roundValue)
            {
                height = roundValue;
                topVal = 10;
                var style = {
                    'width': Number( popWidth ),
                    'position': 'absolute',
                    'height' : height
                };
            }
            //Fade in the Popup and add close button
            containerPopup.fadeIn().css(style).prepend('<a href="#" class="close"><img src="' + defaults.baseUrl + '/themes/default/images/common/close-popup.png" class="btn_close" title="Close Window" alt="Close" /></a>');
            //Set the popup window to center
            containerPopup.css('top', topVal );
            containerPopup.css('left',leftVal );

            //Fade in Background
            $('body').append('<div id="fade"></div>'); //Add the fade layer to bottom of the body tag.
            $('#fade').css({
                'filter' : 'alpha(opacity=80)'
            }).fadeIn(); //Fade in the fade layer - .css({'filter' : 'alpha(opacity=80)'}) is used to fix the IE Bug on fading transparencies

            return false;

        }
    }
})(jQuery);