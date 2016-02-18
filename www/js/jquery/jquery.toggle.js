/*
 * ©2014 Lucas Drapeau
 */


(function($) {
    //plugin name
    var pluginName = "cibletoggle";
    //default plugins settings here
    var defaultSettings = {
        eventsMap: []
    };
    //static variable example
    //var staticVariable = 0;
    //static function example
//    var staticFunction = function() {
//        staticVariable++;
//    }
    //private properties and methods
    var members = {
        settings: '',
        privateProperty: 'a private variable',
        $this: null,
        bindEventData: function(el, eventName, evt) {
            var toggleElementData = el.data(eventName + 'evt');
            if (!toggleElementData) {
                toggleElementData = {
                    list: [evt]
                };
            }
            else {
                toggleElementData.list.push(evt);
            }
            el.data(eventName + 'evt', toggleElementData);
        },
        bindEvent: function($el, event) {
            var that = this;
            $el.off(event);
            $el.on(event, function(e) {
                var evtList = $el.data(e.type + 'evt').list;
                for (var i in evtList) {
                    var evt = evtList[i];
                    var str = $(this).attr('data-toggle-' + evt.name);
                    var list = str.split(',');
                    for (var j in list) {
                        switch (evt.typeName) {
                            case('add'):
                                $(list[j]).addClass(evt.className);
                                break;
                            case('remove'):
                                $(list[j]).removeClass(evt.className);
                                break;
                            case('toggle'):
                                $(list[j]).toggleClass(evt.className);
                                break;
                            case('collapse'):
                                var $item = $(list[j]);
                                if ($item.is('.collapse')) {
                                    if (!$item.is('.in'))
                                        $item.collapse('show');
                                    else
                                        $item.collapse('hide');
                                }
//                                $(list[j]).collapse('toggle');
                                break;
                            case('modal'):
                                var title = $(list[j]).attr('data-toggle-modal-title');
                                var content = $(list[j]).html();
                                that.spawnModal(title, content);
                                break;
                            default:
                                break;
                        }
                    }
                }
            });
        },
        spawnModal: function(title, content) {
            $('body').append('<div id="modal-new-01" class="modal fade">' +
                    '<div class="modal-dialog">' +
                    '<div class="modal-content content">' +
                    '<div class="modal-header">' +
                    '<button type="button" class="modal-close close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>' +
                    '<h4 class="modal-title">' + title + '</h4>' +
                    '</div>' +
                    '<div class="modal-body">' +
                    content +
                    '</div>' +
                    '<div class="modal-footer">' +
                    //'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>' +
                    //'<button type="button" class="btn btn-primary">Save changes</button>' +
                    '</div>' +
                    '</div><!-- /.modal-content -->' +
                    '</div><!-- /.modal-dialog -->' +
                    '</div><!-- /.modal -->');
            $('#modal-new-01').on('hidden.bs.modal', function() {
                $(this).remove();
            })
            $('#modal-new-01').modal('show');

        },
        parseEachEvents: function(evt) {
            var that = this;
            var eventList = evt.events.split(' ');
            for (var k in eventList) {
                var toggleElements = that.$this.find('[data-toggle-' + evt.name + ']');
                toggleElements.each(function() {
                    var $this = $(this);
                    that.bindEventData($this, eventList[k], evt);
                    if (evt.name == 'collapse') {
                        that.handleCollapse($this, evt);
                    }
                    that.bindEvent($this, eventList[k]);
                });
            }
        },
        handleCollapse: function($el, evt) {
            var str = $el.attr('data-toggle-' + evt.name);
            var list = str.split(',');
            for (var h in list) {
                var $target = $(list[h]);
                this.bindEventData($target, evt.name, {'caller': $el});
                $target.on('show.bs.collapse', function() {
                    var evtList = $(this).data(evt.name + 'evt').list;
                    for (var j in evtList) {
                        evtList[j].caller.addClass('triggered-collapse');
                    }
                });
                $target.on('hidden.bs.collapse', function() {
                    var evtList = $(this).data('collapseevt').list;
                    for (var j in evtList) {
                        evtList[j].caller.removeClass('triggered-collapse');
                    }
                });
            }
        },
        //constructor, sort of
        init: function(obj) {
            this.$this = $(obj);
            for (var i in this.settings.eventsMap) {
                var evt = this.settings.eventsMap[i];
                if (!$('html').is('.touch') || evt.touch) {
                    this.parseEachEvents(evt);
                }
            }

        }
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

