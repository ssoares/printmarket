/*
 * &copy;2014 Lucas Drapeau
 * usage :
 * html:
 * <el>
 *  <container>
 *      <children>
 *      <children>
 *
 *      css :
 *      el {
 overflow:hidden;
 height: (whatever you want, but must be set);
 position:relative;
 }
 el container {
 position:absolute;
 width:100%;
 }
 el children {
 display:block;
 }
 javascript:
 $(el).verticalSlider(options)
 */


(function($) {
    var pluginName = "verticalSlider";
    var defaultSettings = {
        childrenSelector: 'li',
        containerSelector: 'ul',
        arrowPrevious: '',
        arrowNext: '',
        startingSlide: 0,
        transitionTime: 400,
        fadeCss: true,
        flipHorizontal: false,
        loop: false,
        sliderTooShortCallback: null
    };
    var members = {
        settings: '',
        numberOfSlides: 0,
        activeSlide: null,
        $container: null,
        $children: null,
        $this: null,
        $buttonPrev: null,
        $buttonNext: null,
        thisHeight: 0,
        containerHeight: 0,
        position: 0,
        isAtEnd: false,
        extractDelta: function(e) {
            if (e.wheelDelta) {
                return e.wheelDelta;
            }
            if (e.originalEvent) {
                if (e.originalEvent.wheelDelta)
                    return e.originalEvent.wheelDelta;
                if (e.originalEvent.deltaY)
                    return e.originalEvent.deltaY;
                if (e.originalEvent.detail)
                    return e.originalEvent.detail;
            }

        },
        hidePrev: function() {
            if (this.settings.fadeCss)
                this.$buttonPrev.addClass('hide');
            else
                this.$buttonPrev.stop().fadeOut(400);
        },
        hideNext: function() {
            if (this.settings.fadeCss)
                this.$buttonNext.addClass('hide');
            else
                this.$buttonNext.stop().fadeOut(400);
        },
        showPrev: function() {
            if (this.settings.fadeCss)
                this.$buttonPrev.removeClass('hide');
            else
                this.$buttonPrev.stop().fadeIn(400);
        },
        showNext: function() {
            if (this.settings.fadeCss)
                this.$buttonNext.removeClass('hide');
            else
                this.$buttonNext.stop().fadeIn(400);
        },
        calculatePosition: function(slide) {
            var pos = this.$children.eq(slide).position();
            if (!this.settings.flipHorizontal) {
                return pos.top;
            }
            else {
                return pos.left;
            }
        },
        changeSlide: function(slide, transition) {
            if (slide < 0)
                slide = 0;
            if (slide >= this.numberOfSlides)
                slide = this.numberOfSlides;
            //calculate position
            this.position = this.calculatePosition(slide);

            if (this.position <= 0) {
                this.position = 0;
                this.activeSlide = 0;
                this.showNext();
                this.hidePrev();
                this.isAtEnd = false;
            }
            else if (this.position < this.containerHeight - this.thisHeight) {
                this.activeSlide = slide;
                this.showNext();
                this.showPrev();
                this.isAtEnd = false;
            }
            else {
                if (!this.isAtEnd)
                    this.activeSlide = slide;
                this.isAtEnd = true;
                this.hideNext();
                this.showPrev();
                this.position = this.containerHeight - this.thisHeight;
            }

            this.executeTransition(transition);

        },
        changeNextSlide: function() {
            this.changeSlide(this.activeSlide + 1, true);
        },
        changePreviousSlide: function() {
            this.changeSlide(this.activeSlide - 1, true);
        },
        executeTransition: function(transition) {
            var propertyCss = (this.settings.flipHorizontal) ? 'left' : 'top';
            //actual transition
            var newPosTransition = {};
            newPosTransition[propertyCss] = '-' + this.position + 'px';

            if (transition && this.settings.transitionTime > 0)
                this.$container.stop().animate(newPosTransition, this.settings.transitionTime);
            else
                this.$container.stop().css(newPosTransition);
        },
        desactivatePlugin: function() {
            this.$this.addClass('disabled');
            this.$buttonNext.addClass('disabled');
            this.$buttonPrev.addClass('disabled');
        },
        enablePlugin: function() {
            this.$this.removeClass('disabled');
            this.$buttonNext.removeClass('disabled');
            this.$buttonPrev.removeClass('disabled');
        },
        methods: {
            recalculateSize: function(that) {
                //calculate height in vertical mode
                if (!that.settings.flipHorizontal) {
                    that.thisHeight = that.$this.height();
                    that.containerHeight = that.$container.height();
                }
                else {
                    that.thisHeight = that.$this.width();
                    that.containerHeight = that.$container.width();
                }
                
                if (that.thisHeight > that.containerHeight) {
                    if (that.settings.sliderTooShortCallback) {
                        var e = {
                            currentTarget: that.$this
                        }
                        that.settings.sliderTooShortCallback(e);
                    }
                    that.desactivatePlugin();
                }
            }
        },
        init: function(obj) {


            var that = this;
            this.$this = $(obj);
            //check if any verticalSlider has been binded on the element previously


            this.$container = this.$this.find(this.settings.containerSelector);

            //if previous arrow exists
            if (this.settings.arrowPrevious != '') {
                this.$buttonPrev = $(this.settings.arrowPrevious);
                this.$buttonPrev.click(function() {
                    if (!$(this).hasClass('disabled'))
                        that.changePreviousSlide();
                })
            }
            //if next arrow exists
            if (this.settings.arrowNext != '') {
                this.$buttonNext = $(this.settings.arrowNext);
                this.$buttonNext.click(function(e) {
                    if (!$(this).hasClass('disabled'))
                        that.changeNextSlide();
                })
            }
            //calculate height in vertical mode
            this.methods.recalculateSize(this);

            this.$children = this.$this.find(this.settings.childrenSelector);//get the element's children
            this.numberOfSlides = this.$children.length;//get the number of elements
            if (this.numberOfSlides <= 0) {//if there's no children with the matched selector, abord
                return;
            }


            //if in vertical mode, bind the scroll event
            if (!this.settings.flipHorizontal) {
                this.$this.bind('mousewheel DOMMouseScroll', function(e) {
                    if (that.extractDelta(e) >= 0) {
                        that.changePreviousSlide();
                    }
                    else {
                        that.changeNextSlide();
                    }
                    e.preventDefault();
                })
            }
            //activate the whole thing
            this.activeSlide = this.settings.startingSlide;
            this.changeSlide(this.settings.startingSlide, false);

            $(window).resize(function() {
                this.methods.recalculateSize(this);
            })
        }
    }

    $.fn.variableSlider = function(options) {
        if (typeof options === "string") {
            return this.each(function() {
                var data = $(this).data('data' + pluginName);
                if (typeof data === 'object') {
                    if (data.methods[options])
                        data.methods[options](data, Array.prototype.slice.call(arguments, 1));
                }
            });
        }
        else {
            var settings = $.extend(defaultSettings, options);

            return this.each(function() {
                if (typeof $(this).data('data' + pluginName) === 'undefined') {
                    var dataToSave = Object.create(members);
                    dataToSave.settings = settings;
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