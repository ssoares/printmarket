(function($) {
    $.fn.log = function(options){
        var defaults = {
            logTab: '#log',
            baseUrl: '',
            lang: 'fr',
            oTable: {}

        };
        var o = $.extend({},defaults,options);

        var logDataTable = {
            init: function(id, insertDetails){
                var sortFirstCol = true;
                if (id == undefined)
                    id = o.logTab;
                /*
                * Insert a 'details' column to the table
                */
                if (insertDetails)
                {
                    var nCloneTh = document.createElement( 'th' );
                    var nCloneTd = document.createElement( 'td' );
                    nCloneTd.innerHTML = '<img src="'+o.baseUrl+'/themes/default/images/treeview-open.png">';
                    nCloneTd.className = "center";

                    $(id + ' thead tr').each( function () {
                        this.insertBefore( nCloneTh, this.childNodes[0] );
                    } );

                    $(id + ' tbody tr').each( function () {
                        this.insertBefore( nCloneTd.cloneNode( true ), this.childNodes[0] );
                    } );

                    sortFirstCol = false;
                }
                /*
                * Initialse DataTables, with no sorting on the 'details' column
                */

                var urlLang = '';
                if (o.lang != 'en')
                    urlLang = o.baseUrl + '/js/datatable/localizations/' + o.lang + '.txt';

                var oTable = $(id).dataTable( {
                    "aoColumnDefs": [
                        {"bSortable": sortFirstCol, "aTargets": [ 0 ]}
                    ],
                    "aaSorting": [[0, 'asc']],
                    "oLanguage":{"sUrl": urlLang},
                    'bRetrieve' : true,
                    'bDestroy' : true,
                    "bPaginate": true,
                    "sPaginationType": "full_numbers",
                    "bLengthChange": true,
                    "bFilter": true,
                    "bSort": true,
                    "bInfo": true,
                    "fnInitComplete": function(oSettings, json) {
                        $('div.dataTables_paginate span').each(function(){
                            if ($(this).attr('id').length > 0)
                                $(this).text(' ');
                        });
                    },
                    "bAutoWidth": true
                });
                o.oTable = oTable;
            }
        };
        logDataTable.init();
    };

})(jQuery);