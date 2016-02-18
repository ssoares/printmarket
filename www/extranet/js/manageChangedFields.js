/*
 * Load view for the permises
 * author: ssoares
 */
(function($) {
    $.fn.fieldStatus = function(method){

        if ( methods[method] ) {
            return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
        } else if ( typeof method === 'object' || ! method ) {
            return methods.init.apply( this, arguments );
        } else {
            $.error( 'Method ' +  method + ' does not exist on jQuery.permises' );
        }
    }

    var methods = {
        init : function( options ) {
            var defaults = {
                tabIndex: 0,
                baseUrl : '',
                params:{
                    id:null,
                    actionKey:'add'
                },
                mode: {
                    edit:true
                },
                prevValue: ''

            };
            return this.each(function(){
                var o = $.extend({}, defaults, options);
                if (o.mode.edit)
                {
                    o.params.actionKey = 'edit';
                    methods.setValidate(o);
                }
            });
        },
        setEditedTabStyle: function(elem, o, reset){
            var currentTab = methods._getCurrentTab(elem);
            if (!elem.hasClass('modified'))
                elem.addClass('modified');
            if (currentTab && !currentTab.hasClass('modified'))
                currentTab.addClass('modified');
            if (reset)
            {
                elem.removeClass('modified');
                var hasModification = true;
                var nbModif = $(elem).parents('div.container:first').find('.modified').length;

                if (nbModif < 1)
                    hasModification = false;

                if (!hasModification)
                    currentTab.removeClass('modified');
            }
        },
        setValidate: function(o){
            $(':input, select').live('focus', function(event){
                o.prevValue = $(this).val();
                $(this).attr('data-previous-value', o.prevValue);
                var param = $(this).attr('firstValue');
                if ($(this).attr('firstValue') == undefined)
                    $(this).attr('firstValue', o.prevValue);
            });
            $(':input, select').live('change blur', function(event){
                var value = $(this).val();
                if (value != o.prevValue)
                    methods.setEditedTabStyle($(this), o);
                if (value == $(this).attr('firstValue'))
                    methods.setEditedTabStyle($(this), o, true);
                if (o.prevValue == "-1"){
                    $(this).attr('data-previous-value', value);
                }
            });
        },
        _getCurrentTab: function(elem){
            var divParent = elem.parents('div[class^=ui-tabs-panel]:first');
            var id = divParent.attr('id');
            var container = divParent.parent();
            var currentTab = container.children('ul[class^=ui-tabs-nav]:first').find('a[href=#'+ id +']');

            return currentTab;
        }
    }

})(jQuery);