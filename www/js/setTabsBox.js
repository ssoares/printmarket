/*
 * Creates the good html from content zone and loads the jquery UI tabs.
 * $Id: setTabsBox.js 721 2013-04-23 19:23:26Z ssoares $
 */

(function($) {
    $.fn.setTabsBox = function(method) {

        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.valuesList');
        }
    }
    var methods = {
        init: function(options) {
            var defaults = {
                tabsContainer: '.tabsContainer',
                incr:1,
                index: 0
            };
            var o = $.extend({}, defaults, options);
            return this.each(function() {
                var tabsContainers = $(this).find(o.tabsContainer);
                tabsContainers.each(function(){
                    var tabs = $(this).children('ul').children('li');
                    var currentContainer = $(this).clone();
                    currentContainer.children('ul').html('');
                    var zIndex = 100;
                    tabs.each(function(j){
                        o.index = (j+ o.incr);
                        var tmpHtml = $(this).next('ul').children().html();
//                        if ($.browser.msie  && parseInt($.browser.version, 10) === 7)
//                            tmpHtml = $(this).children().children().html();

                        var tmpContent = $('<div></div>').append(tmpHtml);
                        tmpContent.append('<hr class="clearBoth"/>');
                        tmpContent.attr('id', 'tabs-' + o.index)
                        currentContainer.append(tmpContent) ;
                        var classFirst = '';
                        if (j === 0)
                            classFirst = ' first';

                        var tmpLabel = $(this).text();
//                        if ($.browser.msie  && parseInt($.browser.version, 10) === 7)
//                        {
//                            if (tmpLabel.match(/\r/) || tmpLabel.match(/\n/))
//                            {
//                                var tmp = tmpLabel.split(/\r\n|\r|\n/g);
//                                tmpLabel = tmp[0];
//                            }
//                        }

                        var label = $('<a></a>').attr('href', '#tabs-' + o.index).text(tmpLabel);
                        var tabTitle = $('<h2></h2>').attr('class', "tabTitle").append(label);
                        $(this).html(tabTitle);
                        $(this).prepend($('<span class="left'+classFirst+'"></span>'));
                        $(this).append($('<span class="right"></span>'));
                        $(this).css("z-index",zIndex);
                        currentContainer.children('ul').append($(this));
                        zIndex--;
                    });
                    o.incr = o.index + 1;
                    $(this).html(currentContainer.html());
                });
                tabsContainers.tabs({
                    select: function(event, ui){
                        var current = $(ui.tab).parents('li:first');
                        var borders = current.children('.left, .right');
                        current.siblings().children('.left, .right').removeClass('ui-state-active');
                        borders.addClass('ui-state-active');
                    }
                });
                var selected = tabsContainers.find('li.ui-tabs-selected');
                var borders = selected.children('.left, .right');
                borders.addClass('ui-state-active');
            });
        }
    }

})(jQuery);