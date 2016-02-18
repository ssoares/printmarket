/*
 * Create a pop-up to display GUI to manage the related list of values.
 * author: ssoares
 */
(function($) {
    $.fn.valuesList = function(method) {

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
                tableList: '#valuesList',
                trTemplate: '#trTemplate',
                selected: '.selectVal',
                baseUrl: '',
                params: {
                    dropdown: null,
                    list: null,
                    pos: null,
                    id: null,
                    currentObj: null
                },
                referencesAction: '/utilities/references/list-values',
                dialog: {},
                mode: {edit: false},
                post: {url: ''},
                msgAlert: '',
                label: '',
                refresh: false,
                addIndex: 0
            };
            return this.each(function() {
                var o = $.extend({}, defaults, options);
                o.msgAlert = o.msgAlert.replace(/<br \/>/g, '\r\n');
                $(this).bind('click', function() {
                    o.params.list = methods._setParamValue($(this), 1);
                    o.params.dropdown = methods._setDropdownName($(this));
                    o.params.currentObj = $(this);
                    methods.display(o);
                    return false;
                });
                $('input[id^=val]').live('blur', function() {
                    o.mode.edit = false;
                    methods.saveEdition($(this), o);
                    methods.toggleEdition($(this).parents('td:first'), o);
                    return false;
                });
                $('td.allowChange').live('click', function() {
                    methods.toggleEdition($(this), o);
                    o.mode.edit = true;
                    return false;
                });
                $('td.delete, span.removeBtn').live('click', function() {
                    methods.deleteRow($(this), o);
                    return false;
                });
                $('a.displayAddLine').live('click', function() {
                    methods.insertAddRow($(this), o);
                    return false;
                });
                $('span.addBtn').live('click', function() {
                    methods.insertRow($(this), o);
                    return false;
                });
                $('label.handler').live('mouseenter', function() {
                    methods.sortRows(o);
                    return false;
                });
            });
        },
        display: function(o)
        {
            o.dialog = methods._modalWindow(o);
            var url = o.baseUrl + o.referencesAction + '/typeRef/' + o.params.list;
            $.get(url, function(data) {
                o.dialog.html(data);
                methods.getCheckedbox(o);
                if ($('#selectAll').length)
                    o.addIndex = 1;
            });
            o.dialog.dialog('open');
        },
        getCheckedbox: function(o)
        {
            if ($('#selectAll').length)
            {
                o.addIndex = 1;
                var obj = o.params.currentObj.prev('dd').find('input[id$=' + o.params.dropdown + ']');
                if(!obj.length)
                    obj = o.params.currentObj.parent('dl').find('input[id$=' + o.params.dropdown + ']');
                var idsList = obj.val().split(',');
                var nbChecked = 0;
                if (parseInt(idsList[0]) > 0)
                $.each(idsList, function(i, id) {
                    var el = $('#sel-' + id);
                    el.attr('checked', 'checked');
                    nbChecked = i + 1;
                });

                if ($('input[id^=sel-]').length === nbChecked)
                    $('#selectAll').attr('checked', 'checked');
            }
        },
        getCheckedboxLevel: function(o)
        {
            var idsList = o.params.currentObj.prev('dd').children('input[id$=' + o.params.dropdown + ']').val().split(',');
            var nbChecked = 0;
            $.each(idsList, function(i, id) {
                $('#sel-' + id).attr('checked', 'checked');
                nbChecked = i + 1;
            });

            if ($('input[id^=sel-]').length === nbChecked)
                $('#selectAll').attr('checked', 'checked');
        },
        toggleEdition: function(elem, o)
        {
            var lbl = elem.children('span:first');
            var val = elem.children('span:last');
            if (!o.mode.edit)
            {
                lbl.toggle();
                val.toggle();
                var tmp = val.children('input').val();
                val.children('input').focus();
                val.children('input').val('');
                val.children('input').val(tmp);
            }
        },
        insertRow: function(elem, o)
        {
            if ($('#selectAll').length)
                o.addIndex = 1;
            var tds = elem.parent().siblings();
            var lang = methods._setParamValue($(tds[1 + o.addIndex]), 1);
            var lbl = $(tds[1 + o.addIndex]).children('span:first');
            var val = $(tds[1 + o.addIndex]).children('span:last').children('input');

            var pos = 0;
            var prevExists = $(tds[1 + o.addIndex]).parent().prev();
            if (prevExists.length)
                pos = parseInt(methods._setParamValue($(tds[1 + o.addIndex]).parent().prev(), 2));
            o.params.pos = pos + 100;
            var data = {
                'RI_Value': val.val(),
                'R_Seq': o.params.pos,
                'R_TypeRef': o.params.list,
                'lang': lang
            };
            lbl.text(val.val());
            o.post.url = o.baseUrl + o.referencesAction + '/actionKey/add/';
            $.post(o.post.url, data, function(result) {
                if (result)
                {
                    tds.each(function(index)
                    {
                        if (index > 1 + o.addIndex)
                        {
                            o.post.url = o.baseUrl + o.referencesAction + '/actionKey/edit/';
                            lang = methods._setParamValue($(this), 1);
                            lbl = $(this).children('span:first');
                            val = $(this).children('span:last').children('input');

                            lbl.text(val.val());

                            data = {
                                'id': result,
                                'RI_Value': val.val(),
                                'R_TypeRef': o.params.list,
                                'R_Seq': o.params.pos,
                                'lang': lang
                            };
                            $.post(o.post.url, data, function(data) {
                            });
                        }
                    });
                    methods.display(o);
                    methods.setRefresh(o, true);
                }
            });
        },
        saveEdition: function(elem, o)
        {
            var tdParent = elem.parents('td:first');
            var pos = methods._setParamValue(tdParent.parent(), 2);
            var lbl = tdParent.children('span:first');
            var prevValue = lbl.text();
            var id = 0;
            if (elem.val() != prevValue)
            {
                var lang = methods._setParamValue(tdParent, 1);
                lbl.text(elem.val());
                id = methods._setParamValue(elem, 1);
                o.post.url = o.baseUrl + o.referencesAction + '/actionKey/edit/';
                var data = {
                    'id': id,
                    'RI_Value': elem.val(),
                    'R_Seq': pos,
                    'R_TypeRef': o.params.list,
                    'lang': lang
                };

                $.post(o.post.url, data, function(data) {
                    methods.setRefresh(o, true);
                });
            }

        },
        deleteRow: function(elem, o, force)
        {
            var id = 0;
            if (elem.is('span'))
            {
                elem = elem.parent();
                $('a.displayAddLine').show();
            }

            id = methods._setParamValue(elem, 1);

            if (id > 0)
            {
                o.post.url = o.baseUrl + o.referencesAction + '/actionKey/delete';
                var data = {
                    'id': id,
                    'delete': 1
                };
                if (force)
                    data.force = 1;
                $.post(o.post.url, data, function(data) {
                    if (data == 'true')
                    {
                        if (force)
                            methods.clearValues(elem, o, id)
                        elem.parent().remove();
                        methods.setRefresh(o, true);
                        return false;
                    }
                    else
                    {
                        var answer = confirm(o.msgAlert);
                        if (answer)
                        {
                            methods.deleteRow(elem, o, true);
                            methods.setRefresh(o, true);
                        }
                        else
                            methods.setRefresh(o, false);
                    }
                });
            }
            else
                elem.parent().remove();

        },
        insertAddRow: function(elem, o)
        {
            var row = $(o.trTemplate).find('tr').clone();
            $(o.tableList).children('tbody').append(row);
            elem.hide();

        },
        sortRows: function(o)
        {
            $(o.tableList + " tbody.sortable").sortable({
                handler: ".handler",
                placeholder: "ui-state-highlight",
                stop: function(event, ui) {
                    var sequences = new Array();
                    var trParent = ui.item;
                    var prevTr = trParent.prev();
                    var nextTr = trParent.nextAll();
                    var pos = 100;
                    var oldPos = parseInt(methods._setParamValue(trParent, 2));
                    var id = methods._setParamValue(trParent.children('.allowChange'), 0);
                    if (prevTr.length)
                        pos += parseInt(methods._setParamValue(prevTr, 2));

                    trParent.attr('id', methods._replaceParamValue(trParent, 2, pos));
                    sequences.push({'R_ID': id, 'R_Seq': pos});

                    if (nextTr.length)
                    {
                        var prevPos = pos;
                        nextTr.each(function() {
                            var seq = prevPos + 100;
                            id = methods._setParamValue($(this).children('.allowChange'), 0);
                            sequences.push({'R_ID': id, 'R_Seq': seq});
                            $(this).attr('id', methods._replaceParamValue($(this), 2, seq));
                            prevPos = seq;

                            if (prevPos == oldPos)
                            {
                                return false;
                            }
                        });
                    }
                    o.post.url = o.baseUrl + o.referencesAction + '/actionKey/edit-pos/';
                    $.post(o.post.url, {data: sequences});
                }
            });
            $(o.tableList + " tbody.sortable").disableSelection();
        },
        setRefresh: function(o, value) {
            o.refresh = value;
        },
        clearValues: function(elem, o, id)
        {
            var text = $('dd[id^=' + o.params.dropdown + ']');
            var idsList = $('input[id$=' + o.params.dropdown + ']');
            var labels = methods._getLabelsList(elem, id);
            text.each(function() {
                var lbl = $(this).find('label');
                lbl.remove();
                var txt = $(this).text().trim();
                var newText = methods._removeValue(txt, labels, ", ");
                $(this).text(newText).prepend(lbl);
            });
            var tmp = new Array();
            idsList.each(function(){
                var ids = $(this).val();
                tmp[0] = id;
                var newIds = methods._removeValue(ids, tmp, ',');
                $(this).val(newIds);
            });
        },
        _getLabelsList: function(elem, id) {
            var labels = new Array();
            var siblings = elem.siblings('td[id^=' + id + '-]');
            siblings.each(function() {
                labels.push($(this).find('input').val());
            });

            return labels;
        },
        _removeValue: function(text, labels, separator) {
            if (separator === undefined) separator = ',';
            var newText = text;
            var myArray = text.split(separator);
            $.each(myArray, function(i, val) {
                $.each(labels, function(j, lbl) {
                    if (val == lbl) {
                        myArray.splice(i, 1);
                    }
                });
            });
            newText = myArray.join(separator);

            return newText;
        },
        _setParamValue: function(elem, index)
        {
            var elemId = elem.attr('id');
            var tmpVal = elemId.split('-');
            var value = tmpVal[index];

            return value;

        },
        _setDropdownName: function(elem)
        {
            var elemId = methods._setParamValue(elem, 0);
            var tmpVal = elemId.split('_');
            var value = tmpVal[1];

            return value;

        },
        _getLabelValue: function(key, o)
        {
            var url = o.baseUrl + o.referencesAction + '/actionKey/get-label/';
            $.ajax({
                url: url,
                async: false,
                dataType: 'json',
                data: {'typeRef': key},
                success: function(data) {
                    o.label = data;
                }
            });
        },
        _replaceParamValue: function(elem, index, replaceVal)
        {
            var elemId = elem.attr('id');
            var tmpVal = elemId.split('-');
            tmpVal[index] = replaceVal;
            var value = tmpVal.join('-');

            return value;

        },
        _modalWindow: function(o)
        {
            var imgPath = o.baseUrl + "/themes/default/images/loading.gif";
            var loading = $('<img src="' + imgPath + '" alt="loading" class="loading">');
            var dialog = null;
            if ($('#dialogForm').length){
                dialog = $('#dialogForm').html('');
            } else {
                dialog = $('<div id="dialogForm" title=""></div>').html('');
            }

            dialog.append(loading.clone());
            dialog.dialog({
                width: 500,
                height: 600,
                modal: true,
                autoOpen: false,
                open: function(event, ui) {
                    methods._getLabelValue(o.params.list, o);
                    var title = 'Liste des valeurs de : ' + o.label;
                    o.dialog.dialog('option', 'title', title);
                    methods.setRefresh(o, false);
                },
                close: function() {
//                    if(o.refresh)
//                    {
                    o.post.url = o.baseUrl + o.referencesAction + '/actionKey/rebuild';
                    $.getJSON(
                            o.post.url,
                            {'typeRef': o.params.list},
                    function(data) {
                        if (o.addIndex)
                        {
                            var tmpid = new Array();
                            var tmpName = new Array();
                            $(o.selected).each(function() {
                                if ($(this).attr('checked'))
                                {
                                    var id = $(this).val();
                                    tmpid.push(id);
                                    tmpName.push(data[id]);
                                }
                            });
                            var text = o.params.currentObj.next('dd[id^=' + o.params.dropdown + ']');
                            var label = text.find('label');
                            text.html(tmpName.join(', '));
                            text.prepend(label);
                            var obj = o.params.currentObj.prev('dd').find('input[id$=' + o.params.dropdown + ']');
                            if (!obj.length)
                                obj = o.params.currentObj.parent('dl').find('input[id$=' + o.params.dropdown + ']')

                            obj.val(tmpid.join(','));
                        }
                        else
                        {
                            // Defini la nouvelle liste
                            var opt = '';
                            // Construit la nouvelle liste de valeur qui sera ajoutée
                            $.each(data, function(index, value) {
                                opt += '<option value="' + index + '">' + value + '</option>';
                                opt += '\r\n';

                            });
                            //Défini la ou les listes à mettre à jour
                            var list = $('select[id$=' + o.params.dropdown + ']');
                            // Pour toutes les listes (1-n):
                            list.each(function() {
                                var currentList = $(this);
                                var parent = currentList.parents('div.ui-tabs-panel:first');
                                var parentId = '';
                                if (parent.length)
                                    parentId = '#' + parent.attr('id');
                                var id = currentList.attr('id');
                                // Récupérer la valeur déjà sélectionnée
                                var selected = parseInt($('#' + id + ' option:selected').attr('value'));
                                // Efface la liste existante sans enlever le valeur vide initiale
                                $(parentId + ' #' + id + ' option:gt(0)').remove();
                                // Ajoute sous la valeur vide la nouvelle liste
                                currentList.append(opt);
                                // Selectionne la valeur qui était déjà sélectionné si elle existe encore dans la nouvelle liste
                                currentList.val(selected);
                            });
                        }
                        o.dialog.dialog('destroy');
                        $('#dialogForm').remove();
                    }
                    );
                    return false;
//                    }
                }
            });

            return dialog;
        }
    }

})(jQuery);

