function assignPage(from_id, from_val, to_id, to_val){
    if (typeof baseUrl !== 'undefined')
    {
        var url = baseUrl + "/page/manage/ajax/actionAjax/findLink";
        var elem = $('#P_DuplicateId_lbl');
        $.post(
        url,
        {term: currentPage},
        function(data){
            if (data == 'true' && (pageId != from_val || elem.val() == ''))
            {
                elem.val(pageName);
                 $( "#dialog-message" ).dialog({
                    modal: true,
                    width: 600,
                    open: function(){
                        $( ".ui-dialog" ).css('z-index', 200000);
                    },
                    buttons: {
                        Ok: function() {
                        $( this ).dialog( "close" );
                        }
                    }
                });
            }
            else
            {
                $('#' + from_id).val(from_val);
                $('#'+ to_id).val(to_val);
                $('#'+ to_id + '_lbl').val(to_val);
            }
        });
    }
    else
    {
            $('#' + from_id).val(from_val);
            $('#'+ to_id).val(to_val);
    }
}

function openPagePicker(pickerId){
    $('#'+pickerId).slideDown('fast');
}

function closePagePicker(pickerId){
    $('#'+pickerId).slideUp('fast');
}

function openTypePanel(type){
    switch( type ){
        case 'page':
            $('.pageSelectionGroup').css('display','block');
            $('.externalLinkSelectionGroup').css('display','none');
            $('#ControllerName').val('');
            break;
        case 'external':
            $('.pageSelectionGroup').css('display','none');
            $('.externalLinkSelectionGroup').css('display','block');
            $('#pagePicker').val('');
            $('#MenuLink').val('');
            break;
        case 'placeholder':
            $('.pageSelectionGroup').css('display','none');
            $('.externalLinkSelectionGroup').css('display','none');
            $('#pagePicker').val('');
            $('#MenuLink').val('');

    };
}