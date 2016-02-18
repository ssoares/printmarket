(function($) {
    $.fn.siteSwitch = function(method) {

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
                elem: '#sitelist',
                params: {},
                dialog: {},
                post: {url: '/default/index/ajax/'},
                baseUrl : ''

            };
            return this.each(function() {
                var o = $.extend({}, defaults, options);
                $(o.elem).change(function(){
                    $.post(
                        o.baseUrl + o.post.url,
                        {actionAjax : 'setEnv', term : $(this).val()},
                        function(data){
                            if (data)
                                $(location).attr('href', o.baseUrl);
                        },
                        'json'
                    );
                    return false;
                });
            });
        }}
})(jQuery);