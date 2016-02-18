var defaultProperties = {
    baseUrl: null,
    moduleAction:null,
    loadImg: '',
    lang: 'en',
    el: null,
    nTr: null,
    params: '',
    releaseId: 0,
    categoryId: 0,
    startDate: '',
    endDate: '',
    table: 'viewers',
    report: '',
    init: function(
        baseUrl,
        moduleAction,
        loadImg,
        lang,
        el)
    {
        this.baseUrl = baseUrl;
        this.moduleAction = moduleAction;
        this.loadImg = loadImg;
        this.lang    = lang;
        this.el      = el;

        var relId = $('#releases').val();
        this.releaseId = relId;
        this.setParams('/releaseId/' + relId);

        var start = $('#startDate').val();
        this.startDate = start;
        this.setParams('/startDate/' + start);

        var end = $('#endDate').val();
        this.endDate = end;
        this.setParams('/endDate/' + end);

        var catId = 0;
        if ($('#categories').is(':visible'))
        {
            catId = $('#categories').val();
            this.setParams('/categoryId/' + catId);
        }


        this.categoryId = catId;
    },
    setEl: function (object){
        this.el = object;
    },
    setLoadImg: function (img){
        this.loadImg = img;
    },
    setReleaseId: function (id){
        this.releaseId = id;
    },
    setCategoryId: function (id){
        this.categoryId = id;
    },
    setStartDate: function (date){
        this.startDate = date;
    },
    setEndDate: function (date){
        this.endDate = date;
    },
    getTableDetails: function (){
        if (this.releaseId == 'empty')
            this.table = 'detailsReasons';
        else
            this.table = 'viewers';

        return this.table;
    },
    setnTr: function (ntr){
        this.nTr = ntr;
    },
    setParams: function (params){
        var slash = "";
        if (params.indexOf('/') != 0)
            slash = '/';

        var keyVal = params.split('/');
        var regexp = new RegExp(keyVal[1], 'gi');
        var hasVal = this.params.match(regexp);

        if (hasVal)
        {
            var position = this.params.search(keyVal[1]) + keyVal[1].length;
            var value = this.params.substr(position, 2);
            var substRegex = new RegExp('/' + keyVal[1] + value, 'gi');
            this.params = this.params.replace(substRegex, params);
        }
        else
            this.params += slash + params;

    },
    getBaseUrl: function (){
        return this.baseUrl;
    },
    getUrlAction: function (){
        return this.baseUrl + this.moduleAction;
    },
    getEl: function (){
        return this.el;
    },
    getnTr: function (){
        return this.nTr;
    },
    getParams: function (){
        return this.params;
    },
    resetParams: function (){
        this.params = '';
    }
};

var loadDetailsData = {
    init: function (){
        $.getJSON(
            defaultProperties.getUrlAction() + defaultProperties.params,
            {
              'startDate': defaultProperties.startDate ,
              'endDate': defaultProperties.endDate
            },
            function (data){
                defaultProperties.el.fnOpen( defaultProperties.nTr, data, 'details' );
            }
        );
    }
};
var filterRelease = {
    load: function (target, id){
        $.getJSON(
            defaultProperties.getUrlAction() + defaultProperties.params,
            function (data){
                $(target).html(data);
                dataTableReport.init(id);
                $('#consultations tbody td img').click();
            }
        );
    }
};

var dataTableReport = {
    elem : null,
    init: function(loadImg, releaseId, catId){

        if (releaseId == null && defaultProperties.releaseId > 0)
            releaseId = defaultProperties.releaseId;
        if (catId == null && defaultProperties.categoryId > 0)
            catId = defaultProperties.categoryId;

        $("#tabContainer").tabs({
            cache: true,
            ajaxOptions: {data : {
                    releaseId: releaseId,
                    categoryId: catId,
                    startDate: defaultProperties.startDate,
                    endDate: defaultProperties.endDate
                }},
            load: function (e, ui) {
                $('a', ui.panel).click(function() {
                    url = this.href;
                    $(ui.panel).load(url);
                    return false;
                });
                $(ui.panel).find(".tab-loading").remove();
                var tab   = $(ui.tab);
                var container = $(tab).attr('href');
                var table = $(container).children('table').attr('id');
                if (dataTableReport.elem == null){
                    defaultProperties.report = tab.attr('id');
                }

                var insertDetails = false;
//                if (ui.index == 0)
//                    insertDetails = true;
                dataTableReport.initDataTable('#' + table, insertDetails);

                if (releaseId > 0)
                    $('#opened tbody td img').click();
            },
            select: function (e, ui) {
                dataTableReport.elem = null;
                var panel = $(ui.panel);
                if (panel.is(":empty")) {
                   panel.html('<div class="tab-loading center">' + loadImg + ' Loading...</div>')
               }
               var tmp = $(ui.tab).attr('id');
               var filter = tmp + 'Filter';
               $('.tab-filters').hide();
               $('.' + filter).show();
            }
         });

    },
    initDataTable : function(id, insertDetails){
        var sortFirstCol = true;
        var urlAjax = defaultProperties.getUrlAction() + '/' + 'reportTab/'
            + defaultProperties.report;
        /*
         * Insert a 'details' column to the table
         */
        if (insertDetails)
        {
            var nCloneTh = document.createElement( 'th' );
            var nCloneTd = document.createElement( 'td' );
            nCloneTd.innerHTML = '<img src="'+defaultProperties.baseUrl+'/themes/default/images/treeview-open.gif">';
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
        if (defaultProperties.lang != 'en')
            urlLang = defaultProperties.baseUrl + '/js/datatable/localizations/' + defaultProperties.lang + '.txt';

        var oTable = $(id).dataTable( {
            "aoColumnDefs": [
                {"sWidth": "20%", "bSortable": sortFirstCol, "aTargets": [ 0 ]}
            ],
            "aaSorting": [[1, 'asc']],
            "oLanguage":{"sUrl": urlLang},
            'bRetrieve' : true,
            'bDestroy' : true,
            "bPaginate": true,
            "sPaginationType": "full_numbers",
            "bLengthChange": true,
            "bFilter": false,
            "bSort": false,
            "bInfo": true,
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": urlAjax,
            "fnServerParams": function ( aoData ) {
                aoData.push(
                    {name : "releaseId", value: defaultProperties.releaseId},
                    {name : "startDate", value: defaultProperties.startDate},
                    {name : "endDate", value: defaultProperties.endDate}
            )},
            "fnInitComplete": function(oSettings, json) {
                $('div.dataTables_paginate span').each(function(){
                    if ($(this).attr('id').length > 0)
                        $(this).text(' ');
                });
            },
            "bAutoWidth": true
        });

        defaultProperties.setEl(oTable);
    },
    toggleDetails: function(object, tableId){
        dataTableReport.initDataTable(tableId, false);
        var oTable = defaultProperties.getEl();
        var nTr = object.parentNode.parentNode;
        defaultProperties.resetParams();
        defaultProperties.setnTr(nTr);

        if ( object.src.match('treeview-close') )
        {
            /* This row is already open - close it */
            object.src = defaultProperties.baseUrl + "/themes/default/images/treeview-open.gif";
            oTable.fnClose( nTr );
        }
        else
        {
            var parent = $(object).parents('tr:first').attr('id');
            var releaseId = parent.replace('nl-', '');
            defaultProperties.setParams('/report/articles/releaseId/' + releaseId);
            /* Open this row */
            object.src = defaultProperties.baseUrl + "/themes/default/images/treeview-close.gif";
            loadDetailsData.init();
        }
    },
    activateTab: function(index){
        $("#tabContainer").tabs({selected: index});

        var myTab = $("#tabContainer").tabs();
        var selected = myTab.tabs('option', 'selected');
        dataTableReport.init(defaultProperties.loadImg, $('#releases').val());
        myTab.tabs('load', selected);
    },
    tabFilter: function(value){
        defaultProperties.report = value;
        var myTab = $("#tabContainer").tabs();
        var selected = myTab.tabs('option', 'selected');
        dataTableReport.init(defaultProperties.loadImg);
        myTab.tabs('load', selected);
    },
    dateFilter: function(start, end){
        defaultProperties.setStartDate(start);
        defaultProperties.setEndDate(end);
        var myTab = $("#tabContainer").tabs();
        var selected = myTab.tabs('option', 'selected');
        dataTableReport.init(defaultProperties.loadImg);
        myTab.tabs('load', selected);
    },
    usersReport: function(title){
        var dialogWin = $('<div></div>').append($(defaultProperties.loadImg));

        dialogWin
            .load(
                defaultProperties.getUrlAction() + defaultProperties.params,
                {
                    'startDate': defaultProperties.startDate ,
                    'endDate': defaultProperties.endDate
                },
                function(){
                    dataTableReport.initDataTable('#' + defaultProperties.getTableDetails(), false);
            }
        )
            .dialog({
                show: 'blind',
                hide: 'fold',
                modal: true,
                title: title,
                width: '70%',
                minHeigt: 300
            });

            dialogWin.dialog({
                close: function (event, ui){
                    $(dialogWin).remove();
                    defaultProperties.setReleaseId(0);
                    defaultProperties.el.dataTable({'bDestroy':true});
                }
            });
    }
};