/*
 * �2014 Lucas Drapeau
 * usage : un peu de customisation sur magnifix popup pour 1- le faire fonctionner avec prettyPhoto 2- ajouter des effets custom
 */

(function($) {
    //plugin name
    var pluginName = "customMagnificPopup";
    //attribute on a
    var attrToFind = "data-magnific";
    //container custom
    var attrContainer = "data-magnificcontainer";
    //selecteur des miniatures
    var delegateDefault = "*:not(.cycle-sentinel)>.prettyPhoto";
    //html du popup
    var markup = '<div class="mfp-figure">' +
            '<div class="mfp-toptitle"></div>' +
            '<div class="mfp-close"></div>' +
            '<div class="mfp-img-wrapper"><div class="mfp-img"></div></div>' +
            '<div class="mfp-bottom-bar">' +
            '<div class="mfp-title"></div>' +
            '<div class="mfp-counter"></div>' +
            '</div>' +
            '</div>';
    //default plugins settings here
    var defaultSettings = {
        lang: 'en'
    };
    var translations = {
        'fr': function() {
            $.extend(true, $.magnificPopup.defaults, {
                tClose: 'Fermer (Esc)', // Alt text on close button
                tLoading: 'Chargement...', // Text that is displayed during loading. Can contain %curr% and %total% keys
                gallery: {
                    tPrev: 'Précédente (Touche gauche)', // Alt text on left arrow
                    tNext: 'Suivate (Touche droite)', // Alt text on right arrow
                    tCounter: '%curr% de %total%' // Markup for "1 of 7" counter
                },
                image: {
                    tError: '<a href="%url%">L\'image</a> n\'a pas pus être téléchargée.' // Error message when image could not be loaded
                },
                ajax: {
                    tError: '<a href="%url%">Le contenu</a> n\'a pas pus être téléchargé.' // Error message when ajax request failed
                }
            });
        }
    };

    var preventDefault = function(e) {
        e.preventDefault();
    }
    //private properties and methods    
    var members = {
        settings: '',
        $this: null,
        mfpFigureOptions: '',
        toggleAnim: false,
        groups: [],
        //constructor, sort of
        onResize: function() {
            $('.mfp-img').each(function() {
                var h = parseInt($(this).css('max-height'));
                $(this).css('max-height', (h - 80) + 'px');
            })
        },
        onClose: function() {
            $(window).off('DOMMouseScroll mousewheel', preventDefault);
            $("body").off('touchmove', preventDefault);
        },
        onOpen: function(obj) {
            var cont = $(obj.currItem.el.context).find('img').attr('alt') || "";
            this.mfpFigureOptions = $(obj.currItem.el.context).attr('data-magnific-options') || "";
            $('.mfp-figure').attr('class', 'mfp-figure').addClass(this.mfpFigureOptions).find('.mfp-toptitle').html(cont);
            $('.mfp-container').prepend("<div class='mfp-container-transition hidden'></div>")
            $(window).on('DOMMouseScroll mousewheel', preventDefault);
            $("body").on('touchmove', preventDefault);
        },
        init: function(obj) {
            this.$this = $(obj);
            that = this;
            if(translations[this.settings.lang]) {
                translations[this.settings.lang]();
            }
            //archive the groups
            this.$this.find(delegateDefault).each(function() {
                var group = $(this).attr(attrToFind);
                if (group && $.inArray(group, that.groups) == -1) {
                    that.groups.push(group);
                }
            });
            for (var i in this.groups) {
                var key = this.groups[i];
                var container = this.$this;
                var delegate = delegateDefault;
                if (key != "") {
                    container = this.$this.find('[' + attrContainer + '=' + key + ']')
                    delegate += '[' + attrToFind + '=' + key + ']';
                }
                container.magnificPopup({
                    delegate: delegate,
                    fixedContentPos: false,
                    type: 'image',
                    mainClass: 'mfp-fade',
                    gallery: {
                        enabled: true
                    },
                    callbacks: {
                        beforeChange: function() {
                            if (this.content && this.supportsTransition) {
                                $el = $('.mfp-container-transition');
                                $el.removeClass('hidden')
                                var className = (this.direction) ? "mfp-transition-right" : "mfp-transition-left";

                                $el.html('<div class="mfp-content ' + className + '"><div class="mfp-figure ' + that.mfpFigureOptions + '">' + this.content.html() + '</div></div>')

                            }
                        },
                        afterChange: function() {
                            if (this.supportsTransition) {
                                $trans = $('.mfp-content');
                                if (this.direction) {
                                    $trans.removeClass("mfp-transition-left").addClass('mfp-transition-right')
                                }
                                else {
                                    $trans.removeClass("mfp-transition-right").addClass('mfp-transition-left')
                                }

                                var cont = $(this.currItem.el.context).find('img').attr('alt') || "";
                                $('.mfp-container > .mfp-content').find('.mfp-toptitle').html(cont);
                            }
                            that.onResize();

//                            $(".touch").find(".mfp-content").swipe({
//                                //Generic swipe handler for all directions
//                                swipe: function(event, direction, distance, duration, fingerCount) {
//                                    $('.mfp-arrow-' + direction).click();
//                                }
//                            });
                        },
                        open: function() {
                            that.onOpen(this);
                        },
                        resize: function() {
                            that.onResize();
                        },
                        close: function() {
                            that.onClose();
                        },
                        change: function() {
                            if (this.isOpen) {
                                this.wrap.addClass('mfp-open');
                            }
                        },
                    },
                    zoom: {
                        enabled: false, // By default it's false, so don't forget to enable it
                        duration: 300, // duration of the effect, in milliseconds
                        easing: 'ease-in-out', // CSS transition easing function 
                    },
                    image: {
                        markup: markup, // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button
                        titleSrc: 'title',
                        verticalFit: true, // Fits image in area vertically

                    }
                });
            }
            this.$this.find(delegateDefault + ':not([' + attrToFind + '])').magnificPopup({
                type: 'image',
                zoom: {
                    enabled: false, // By default it's false, so don't forget to enable it
                    duration: 300, // duration of the effect, in milliseconds
                    easing: 'ease-in-out', // CSS transition easing function 
                },
                callbacks: {
                    open: function() {
                        that.onOpen(this);
                    },
                    resize: function() {
                        that.onResize();
                    },
                    afterChange: function() { 
                        that.onResize();
                    },
                    close: function() {
                        that.onClose();
                    }
                },
                image: {
                    markup: markup, // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button
                    titleSrc: 'title',
                    verticalFit: true, // Fits image in area vertically

                }
            });
        }//end init
    }
    //public methods here
    members.methods = {
    };
    //everything below should be left as it is
    $.fn[pluginName] = function(options, args) {
        if (typeof options === "string") {
            return this.each(function() {
                var data = $(this).data('data' + pluginName);
                if (typeof data === 'object') {
                    if (data.methods[options])
                        data.methods[options](data, args);
                }
            });
        }
        else {
            return this.each(function() {
                var settings = $.extend({}, defaultSettings, options);
                if (typeof $(this).data('data' + pluginName) === 'undefined') {
                    var dataToSave = Object.create(members);
                    dataToSave.settings = Object.create(settings);
                    dataToSave.init(this);
                    $(this).data('data' + pluginName, dataToSave);
                }
            });
        }
    };
}(jQuery));
if (!Object.create) {
    Object.create = (function() {
        function F() {
        }

        return function(o) {
            if (arguments.length != 1) {
                throw new Error('Object.create implementation only accepts one parameter.');
            }
            F.prototype = o;
            return new F()
        }
    })()
}