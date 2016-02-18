// Manage ajax update for the form module

/**
 * Call the controller for update
 * Manage the displaying of all the parameters
 *
 * @param object elem    The element having changed to update.
 * @param object event   The event captured on the element.
 *
 * @version $Id: headerFormActions.js 1505 2014-03-20 18:44:30Z ssoares $
 *
 * @return void
 */
var updateParam = {
    module : 'form',
    init : function(elem, event)
    {
        var elemId = '#' + elem.attr('id');
        var langID = $('#langSelector').val();
        var object = $(elemId).prevAll().find('form:first').attr('id');
        var formId = $(elemId).prevAll().find('form:first').attr('formid');
        var attrId = 'id';
        var action = false;

        if (formId == undefined)
        {
            if ($(elem).parents('form').attr('formid'))
                attrId = 'formid';

            formId = $(elem).parents('form').attr(attrId);
            object = $(elem).parents('form').attr('id');
            var isVisible = $(elemId).parents().find('form#' + object).is(':visible');

        }

//        if (isVisible)
//        {
            var params = this.setFormData(elem, formId, langID, object);
            if (!object.match('section_'))
                action = true;
            $(elem).bind(
                event.type,
                updateParam._sendData(elemId, params, action)
            );
//        }

        return false;
    },

    /**
     * Create a formated string to be included in the url for the ajax post
     *
     * @param {object} elem   The current DOM element to process.
     * @param {int}    formId The form id to defined witch one to update in db.
     * @param {int}    langID The language id.
     * @param {string} object Defined the name of the php object to use.
     *
     * @return {string} params A string added in the ajax post url.
     */
    setFormData : function(elem, formId, langID, object)
    {
        var params  = "";
        var field   = elem.get(0).type;
        var fieldId = elem.attr('id');
        var prefixe = 'FI_';

        if (object != 'Form')
        {
            var tmpObj = object.split("_");
            object     = tmpObj[0].charAt(0).toUpperCase()
                            + tmpObj[0].substring(1);
            formId     = tmpObj[1];
        }

        if (field == "text" || field == "textarea")
        {
            var formTitle = elem.val();

            if (!fieldId.match("new"))
            {
                var tmpPref = fieldId.split("_");
                prefixe     = tmpPref[0] + "_";
            }
            else
            {
                fieldId = fieldId.replace('new', 'FI_');
            }
//            if ($.browser.msie)
//            {
                formTitle = escape( encodeURIComponent(formTitle));
//            }
            params = prefixe + object + "ID/" + formId + "/";
            params += fieldId + "/" + formTitle + "/";
            params += prefixe + "LanguageID/" + langID;

        }
        else if (field == "checkbox" || field == "radio")
        {
            var showTitle = 1;
            var isChecked = elem.is(':checked');
            if (!isChecked)
                showTitle = 0;
            tmpPref = fieldId.split("_");
            prefixe = tmpPref[0];

            params += prefixe + "_ID/" + formId + "/";
            params += prefixe + "I_LanguageID/" + langID + "/";
            params += fieldId + "/" + showTitle;

        }
        params += "/";

        if (prefixe == 'FRO' || prefixe == 'FROI')
            object = 'responseOption';

        if (prefixe == 'FN_')
            object = 'notification';
        params += "model/" + object + "/";

        if (object.match('Validation') && (prefixe != "FRO" || prefixe != "FROI"))
        {
            var category  = elem.attr('category');
            var validator = elem.attr('validator');

            params += "FQVT_Category/" + category + "/";
            params += "FQV_TypeID/" + validator + "/"
        }

        return params;
    },

    /**
     * Send a post request to server.
     * If it is to update form parameters do more actions on response.
     *
     * @param {string}  elemId  The id tag name to for actionResponse method
     * @param {string}  params  Parameters formated to be added to the URL
     * @param {boolean} action  To initiate more actions for form title.
     *
     * @return void
     */
    _sendData : function(elemId, params, action)
    {
        var baseUrl = defaultProperties.getBaseUrl();
        $.ajax( {
            type : 'POST',
            url : baseUrl + '/form/index/updateformparam/' + params,
            datatype : 'json',
            success : function(data)
            {
                if (data == 1 && action == true)
                {
                    updateParam.actionResponse(elemId);
                }
                $('*').die();
                return false;
            }
        });

        return false;
    },

    /**
     * Update and display main form title according to the modified element.
     *
     * @this  {actionResponse}
     * @param {string} elemId The id tag name of the current processed element.
     */
    actionResponse : function(elemId)
    {
        var data = $(elemId).val();

        switch (elemId)
        {
        case '#FI_Title':
            $('input#newTitle').hide();
            $('h1#title').find('em').html(data);
            $("#currentTitle").html(data);
//            return false;
        break;
        case '#newTitle':
            $('input#newTitle').hide();
            $('h1#title').find('em').html(data);
            $("#currentTitle").html(data);
            $("#currentTitle").show();
//            return false;
            break;
        default:
            break;
        }
    },

    findUpdateType: function (newSectionParent, sectionIndex, ID, currElem)
    {
        var prevSection = $([]);
        var nextSection = $([]);

        if (sectionIndex != 0)
            prevSection = newSectionParent.children().eq(sectionIndex-1);

        nextSection = newSectionParent.children().eq(sectionIndex+1);

        //  1.1- Si la section insérée est la première
        if (nextSection.attr('id') == null && prevSection.attr('id') == null)
        {
            $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val('1');
            updateParam.updateSequence(currElem, prevSection);
        }
        //  1.2- Si la section est insérée avant la 1ere
        else if (nextSection.attr('id') != null && prevSection.attr('id') == null)
        {
            var currSeq = 1;
            $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val('1');
            updateParam.updateSequence(currElem, prevSection);

        }
        //  1.3- Si la section est insérée en dernier
        else if (nextSection.attr('id') == null && prevSection.attr('id') != null)
        {
            var prevSeq = prevSection.children().find('form').children('input[id$=_Seq]').val();
            var seqField = $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val();
            $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val(++prevSeq);
            seqField = $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val();
            updateParam.updateSequence(currElem, prevSection);
        }
        //  1.4- Si la section est insérée entre 2 autres sections
        else if (nextSection.attr('id') != null && prevSection.attr('id') != null)
        {
            prevSeq = prevSection.children().find('form').children('input[id$=_Seq]').val();

            currSeq = parseInt(prevSeq) + 1;
            $(currElem).children().find('form#' + ID).children('input[id$=_Seq]').val(currSeq);
            // Updat47 eDB
            updateParam.updateSequence(currElem, prevSection);

        }
    },

    updateSequence: function (currElem, prevSection)
    {
        var url     = '';
        var currSeq = 1;
        var baseUrl = defaultProperties.getBaseUrl();

            if (currElem.search('section') > 0)
            {

                url = baseUrl + "/form/section/update/model/section/";

                $('li[id^=section_]').each(function (){
                    if ($(this).prev().attr("id")){
                        var prevSeq = $(this).prev().children().find('form').children('input[id$=_Seq]').val();
                        currSeq = parseInt(prevSeq) + 1;
                    }

                    $(this).children().find('form').children('input#FS_Seq').val(currSeq);
                    var newSeq = $(this).children().find('form').children('input[id$=_Seq]').val();
                    var sectionId = $(this).attr('id').replace('section_', '');

                    updateParam._sendSeqSection(sectionId, newSeq, url);
                });
            }
            else if ($(currElem).attr('elementtype') == 'textzone' ||
                     $(currElem).attr('elementtype') == 'question')
            {
                var sp = $(currElem).parents('li[id^=section_]');  // section parent test
                url = baseUrl + "/form/element/update/model/element/";

                $('li[id^=element_]').each(function (){
                    if ($(this).prev().attr("id")){
                        var prevSeq = $(this).prev().children().find('form').children('input[id$=_Seq]').val();
                        currSeq = parseInt(prevSeq) + 1;
                    }

                    $(this).children().find('form').children('input[id$=_Seq]').val(currSeq);
                    var newSeq = $(this).children().find('form').children('input[id$=_Seq]').val();
                    var id     = $(this).attr('id').replace('element_', '');

                    var sectionParent = $(this).parents('li[id^=section_]');
                    var sectionId = sectionParent.attr('id').replace('section_','');
                    updateParam._sendSeqElement(id, newSeq, url, sectionId);

                });
            }
    },

    _sendSeqSection: function(id, newSeq, url)
    {
        $.getJSON(
                url,
                {
                    'FSI_LanguageID': $('#langSelector').val(),
                    'FS_ID': id,
                    FS_Seq: newSeq
                },
               function(data){
               }
            );
    },

    _sendSeqElement: function(id, newSeq, url, newSecId)
    {
        $.getJSON(
                url,
                {
                    'FTI_LanguageID': $('#langSelector').val(),
                    'FE_ID': id,
                    'FE_SectionID': newSecId,
                    'FE_Seq': newSeq
                },
               function(data){
               }
            );
    },

    updatePageBreak : function(newSectionParent, pageBreak)
    {
        var section   = newSectionParent.attr('id');
        var sectionId = section.replace('section_', '');
        var baseUrl   = defaultProperties.getBaseUrl();

        if (pageBreak == undefined)
            pageBreak = newSectionParent.find('form').children('input#FS_PageBreak').val();

        // UpdateDB
        $.getJSON(
            baseUrl + "/form/section/update/model/section/",
            {
                'FSI_LanguageID': $('#langSelector').val(),
                'FS_ID': sectionId,
                'FS_FormID': $("#Form").attr('formid'),
                'FS_PageBreak': pageBreak
            },
           function(data){
           }
        );

       }
};

var defaultProperties = {
    baseUrl: null,
    defaultTitle: null,
    editTextZoneTitle: null,
    editTextZoneDescription: null,
    deletePageBreakAlert: null,
    sectionPageBreakAlert: null,
    el: null,
    elFirstPosition: null,
    init: function(
        baseUrl,
        defaultTitle,
        editTextZoneTitle,
        editTextZoneDescription,
        deletePageBreakAlert,
        sectionPageBreakAlert,
        el,
        elFirstPosition)
    {
        this.baseUrl                 = baseUrl;
        this.defaultTitle            = defaultTitle;
        this.editTextZoneTitle       = editTextZoneTitle;
        this.editTextZoneDescription = editTextZoneDescription;
        this.deletePageBreakAlert    = deletePageBreakAlert;
        this.sectionPageBreakAlert   = sectionPageBreakAlert;
        this.el                      = el;
        this.elFirstPosition         = elFirstPosition;
    },

    getBaseUrl: function (){
        return this.baseUrl;
    },

    getDefaultTitle: function (){
        return this.defaultTitle;
    },

    getEditTextZoneTitle: function (){
        return this.editTextZoneTitle;
    },

    getEditTextZoneDescription: function (){
        return this.editTextZoneDescription;
    },

    getDeletePageBreakAlert: function (){
        return this.deletePageBreakAlert;
    },

    getSectionPageBreakAlert: function (){
        return this.sectionPageBreakAlert;
    },

    getEl: function (){
        return this.el;
    },

    getElFirstPosition: function (){
        return this.elFirstPosition;
    }
}
/**
 * Manage the display of form elements according to the given id.
 */
var displayForm = {
    elem : null,
    init : function (ID, defaultTitle){
        $('#' + ID + ' p.section_title').hide();
        $('#' + ID + ' input[id$=_Title]').show();
        $('#' + ID + ' input[id$=_Title]').val(defaultTitle);
        $('#' + ID + ' input[id$=_Title]').focus();
    },

    click : function(elem, curTitle)
    {

        switch (elem)
        {
        case 'scrollDownFormHeader':
            var newTitleVisible = $('input#newTitle').is(':visible');
            var currentTitleVisible = $('#currentTitle').is(':visible');
            if (newTitleVisible || currentTitleVisible)
            {
                $('input#newTitle').hide();
                $('#currentTitle').hide();
            }

            curTitle = $("#currentTitle").html();
            $('input#FI_Title').val(curTitle);
            $('#scrollUpFormHeader').show();
            $("#formData").slideDown();
            $("#" + elem).hide();
            $('html, body').animate({scrollTop:100}, 'slow');
            break;
        case 'scrollUpFormHeader':
            $("#formData").slideUp();
            $('#scrollDownFormHeader').show();
            $('#currentTitle').show();
            $("#" + elem).hide();
            $('html, body').animate({scrollTop:50}, 'slow');
            break;
        default:
            var divParent    = elem.parent().parents('.section');
            var currentForm  = 'form#' + divParent.find('form.params').attr('id');
            var currentTitle = $(divParent).find('p.section_title:first');
            var inputTitleId = '#' + $(divParent).find('input[id$=_Title]').attr('id');
            var titleValue   = $(currentForm + " " + inputTitleId).val();

            currentTitle.text(titleValue);

            var element = divParent.find(currentForm);

            $(element).slideToggle('1000', function(){
                if ($(this).is(':visible'))
                    $(this).find(inputTitleId).focus();

            });
            currentTitle.slideToggle();
            break;
        }
    }
};

var manageLanguage = {
    lang: null,
    init: function(currentLang){
        var url = baseUrl + "/form/index/getlanguages/";
        $.getJSON(
            url,
            function (response){
            	var themePath = "/themes/default/images/";
                $(response).each(function(lang){

                	if (this.L_ID != currentLang)
            		{

                		var alt    = this.L_Title;
                		var suffix = this.L_Suffix;
                		var src    = baseUrl + themePath + suffix + '.png';
                		var flag   = '<img alt="' + alt
                					+ '" class="' + this.L_ID
                					+ '" id="lang_' + suffix
                					+ '" src="' + src
                					+ '" title="' + alt + '"/>';

                		$("#langSwitcher").append(flag);
            		}
                });
            }
        );


    },

    switchLang : function(elem){
    	var currentLang = $(elem).attr('class');
    	var elemId      = "#" + $(elem).attr('id');
    	var formId  	= $(elemId).parent().prevAll().find('form:first').attr('formid');
        var baseUrl     = defaultProperties.getBaseUrl();

        $("#langSelector").val(currentLang);
        $(elemId).remove();
        this.init(baseUrl, currentLang);

        var url = baseUrl + "/form/index/reload/FI_FormID/" + formId
        		+ "/FI_LanguageID/" + currentLang + "/";
        $.getJSON(
            url,
            function (response){
            	if (response)
                {
                    $("#currentTitle").html(response);
                    $("#FI_Title").val(response);
                    $('h1#title').find('em').html(response);
            	}
            	else
            	{
                    $("#currentTitle").hide();
                    $('input#newTitle').val();
                    $('input#newTitle').css('display', 'inherit');
                    $('input#newTitle').focus();
            	}
            }
        );
    }
};

/**
 * Manage action to delete form parts
 */
var deletelink = {

    init: function( target, confirmMsg){

        var section_li    = null;
        var sectionId     = null;
        var sectionParent = null;
        var sectionIndex  = null;
        var ID            = null;
        var currElem      = null;
        var item          = $(target).attr('elementtype');
        var baseUrl       = defaultProperties.getBaseUrl();

        switch (item)
        {
            case 'section':
                if(confirm(confirmMsg)){
                    // Supprimer tous les éléments attachés à la section
                    section_li    = $(target).parents('.section_li');
                    sectionId     = section_li.attr('id').replace('section_', '');
                    sectionParent = section_li.parent()
                    sectionIndex  = sectionParent.children().index(section_li);
                    ID            = section_li.attr('id');
                    currElem      = "#" + ID;

                    $.getJSON(
                        baseUrl + '/form/section/delete/model/section',
                        {
                            'FS_ID': sectionId
                        },
                        function (deleted)
                        {
                            if(deleted)
                            {
                                //   3.- Si la section contient un saut de page
                                //     3..- Supprimer le visuel du saut de page
                                section_li.slideUp("normal", function(){
                                    $(this).remove();
                                //   3.- Updater les séquences des sections du formulaire

                                    updateParam.findUpdateType(
                                        sectionParent,
                                        sectionIndex,
                                        ID,
                                        currElem
                                    );
                                });
                            }
                        }
                    );
                }
                break;
            case 'breakpage':
                if(confirm(confirmMsg)){
                    // Supprimer tous les éléments attachés à la section
                    section_li    = $(target).parents('.section_li');
                    sectionId     = section_li.attr('id').replace('section_', '');
                    sectionParent = $(target).parents('.section_breakpage_li');

                    section_li.find('form').children('input#FS_PageBreak').val('0');
                    updateParam.updatePageBreak(section_li, 0);

                    //   3.- Si la section contient un saut de page
                    //     3..- Supprimer le visuel du saut de page
                    sectionParent.slideUp("normal", function(){
                        $(this).remove();
                    });
                }

                break;
            case 'textzone':
                if(confirm(confirmMsg)){
                    // Supprimer l'élément textzone
                    section_li    = $(target).parents('.element_textzone');
                    sectionId     = section_li.attr('id').replace('element_', '');
                    sectionParent = section_li.parent()
                    sectionIndex  = sectionParent.children().index(section_li);
                    ID            = section_li.attr('id');
                    currElem      = "#" + ID;

                    $.getJSON(
                        baseUrl + '/form/element/delete/model/element',
                        {
                            'FE_ID': sectionId
                        },
                        function (deleted)
                        {
                            if (deleted)
                            {
                                //     3..- Supprimer le visuel
                                section_li.slideUp("normal", function(){
                                    $(this).remove();
                                //   3.- Updater les séquences des éléments du formulaire

                                    updateParam.findUpdateType(
                                        sectionParent,
                                        sectionIndex,
                                        ID,
                                        currElem
                                    );
                                });
                            }
                        }
                    );
                }
                break;
            case 'question':
                if(confirm(confirmMsg)){
                    // Supprimer l'élément textzone
                    section_li    = $(target).parents('.element_question');
                    sectionId     = section_li.attr('id').replace('element_', '');
                    sectionParent = section_li.parent()
                    sectionIndex  = sectionParent.children().index(section_li);
                    ID            = section_li.attr('id');
                    currElem      = "#" + ID;

                    $.getJSON(
                        baseUrl + '/form/element/delete/model/element',
                        {
                            'FE_ID': sectionId
                        },
                        function (deleted)
                        {
                            if (deleted)
                            {
                                //     3..- Supprimer le visuel
                                section_li.slideUp("normal", function(){
                                    $(this).remove();
                                //   3.- Updater les séquences des éléments du formulaire
                                    updateParam.findUpdateType(
                                        sectionParent,
                                        sectionIndex,
                                        ID,
                                        currElem
                                    );
                                });
                            }
                        }
                    );
                }
                break;
            case 'option':
                if(confirm(confirmMsg)){
                    // Supprimer l'élément textzone
                    var section   = $(target).parents('.option_response');
                    var targetId  = $(target).attr('id')
                    var start     = targetId.search("_");
                    var length    = targetId.length;
                    sectionId     = targetId.substr(start + 1, length);
                    sectionParent = section.parent();
                    sectionIndex  = sectionParent.children().index(section);
                    ID            = section.attr('id');
                    currElem      = "#" + ID;

                    $.getJSON(
                        baseUrl + '/form/question/delete-option/model/responseOption',
                        {
                            'FRO_ID': sectionId
                        },
                        function (deleted)
                        {
                            if (deleted)
                            {
                                //     3..- Supprimer le visuel
                                    section.children().remove();
                                section.slideUp("normal", function(){
                                    $(this).remove();
                                //   3.- Updater les séquences des éléments du formulaire
                                    updateParam.findUpdateType(
                                        sectionParent,
                                        sectionIndex,
                                        ID,
                                        currElem
                                    );
                                });
                            }
                        }
                    );
                }
                break;
            default:
                break;
        }
    },

    _delSection: function(id){
        var baseUrl = defaultProperties.getBaseUrl();
        $.getJSON(
            baseUrl + '/form/section/delete/model/section',
            {
                'FS_ID': id
            }
        );
    }
};

/**
 * Load and intialize tinymce editor.
 * This will replace a textarea by the tinymce wysiwyg editor.
 *
 * @param string id    Name of the textarea to associate with.
 * @apram string theme Type of editor theme (advanced...)
 *
 * @return void
 */
var loadTinyMce = {
    load: function (id,theme){
        tinyMCE.init({
            // General options
            relative_urls : false,
            remove_script_host : true,

            extended_valid_elements : "iframe[src|width|height|name|align|frameborder|marginheight|marginwidth]",
            //cleanup : false,

            mode : "exact",
            //elements : "{$_id}",
            elements : id,
            //theme : "{$this->_mode}",
            theme : theme,
            plugins : "imagemanager,filemanager,safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            //language : "{$_lang}",
            language : "fr",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontsizeselect,|,forecolor,backcolor",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,insertdate,inserttime",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,advhr,|,print,fullscreen",
            theme_advanced_buttons4 : "cite,abbr,acronym,del,ins,|,visualchars,nonbreaking,pagebreak",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : false,

            // Example content CSS (should be your site CSS)
            //content_css : "{$_cssPath}",
            content_css : "/ss/edith/www/themes/default/css/integration.css",
            theme_advanced_styles : "Grand titre blue =h2_large_blue;Grand titre orange =h2_large_orange;Grand titre rouge =h2_large_red;Moyen titre rouge =h2_medium_red;Grand texte gris =large_gray_text;Lien - Téléchargement =telechargement;Ligne de tableau - Pair=even;Ligne de tableau - Impaire=odd",
            theme_advanced_blockformats : "p,h2",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "lists/template_list.js",
            external_link_list_url : "lists/link_list.js",
            external_image_list_url : "lists/image_list.js",
            media_external_list_url : "lists/media_list.js"
        });
    }
};

/**
 * Manage the behaviour of the section area
 */
var formUI = {
    init: function()
   {
       var baseUrl = defaultProperties.getBaseUrl();
        $("#form_drop_zone").sortable({
        axis: "y",
        appendTo: "body",
        placeholder: 'place-holder',
        handle: '.sortableSection',
        opacity:0.6,
        tolerance:"intersect",
        stop: function(event, ui){
            var newSection = $(ui.item);
            var action = newSection.attr('action');
            var elementType = newSection.attr('elementType');

            // ACTION SECTION
            if(elementType == 'section'){
                var newSectionParent = newSection.parent()
                var sectionIndex = newSectionParent.children().index(newSection);

                /****************** ADD *****************/
                // 1- Si ajout d'une section
                if(action == 'add'){
                    newSection.html('');
                    newSection.removeClass('ui-state-default');
                    newSection.removeClass('element_section');
                    newSection.removeClass('ui-draggable');
                    newSection.removeAttr('action');

                    newSection.addClass('section_li');

                    $.getJSON(
                        baseUrl + "/form/section/show/",
                        {
                            'id': ''
                        },
                        function(data){
                            newSection.append(data['html']);

                            var ID = newSection.find('form').attr('id');
                            var currElem = '#' + ID;
                            newSection.attr('id',ID);
                            test = $('#' + ID + ' #FS_RepeatMax').val();
//                            newSection.find('.section_title').html(ID);
//                            newSection.find('form').attr('id',ID);

                            // 1.- Updater les séquences des autres section du formulaire
                            updateParam.findUpdateType(
                                newSectionParent,
                                sectionIndex,
                                ID,
                                currElem
                            );

                            //   2.- Créer dans la base de donnée la
                            //   section en fr et en avec information de
                            //   base : (titre : nouvelle section - new section)
                            $.getJSON(
                                baseUrl + "/form/section/add/",
                                {
                                    'FSI_LanguageID': $('#langSelector').val(),
                                    'FS_ID': ID.split('_')[1],
                                    'FSI_Title': defaultProperties.getDefaultTitle(),
                                    'FS_RepeatMin': $('#' + ID + ' #FS_RepeatMin').val(),
                                    'FS_RepeatMax': $('#' + ID + ' #FS_RepeatMax').val(),
                                    'FS_ShowTitle': "0",
                                    'FS_FormID': $("#Form").attr('formid'),
                                    'FS_Seq': $('#' + ID + ' #FS_Seq').val(),
                                    'FS_PageBreak': "0",
                                    'FS_Repeat': "1"

                                },
                               function(data){
                                   var ID = 'section_' + data;
                                   newSection.attr('id',ID);
                                   newSection.find('.section_title').html(ID);
                                   newSection.find('form').attr('id',ID);
                               }
                            );

                            //   3.- Mettre le titre en mode édition et le curseur prêt à saisir l'information
                            displayForm.init(ID, defaultProperties.getDefaultTitle());

                            /****************************************/
                            /*************** ÉLÉMENTS ***************/
                            /****************************************/
                            formElement.init();

                            /****************************************/
                            /************** BREAK PAGE **************/
                            /****************************************/
                            pageBreak.init();
                        }
                    );



                }
                // 2- Si déplacement d'une section
                else{
                    var ID = newSection.attr('id');
                    // 2.- Updater les séquences des sections du formulaire
                    var currElem = '#' + ID;
                    updateParam.findUpdateType(
                        newSectionParent,
                        sectionIndex,
                        ID,
                        currElem
                    );
                }
            }
        }
      });

    }
};

/**
 * Manage the behaviour of the elements area
 */
var formElement = {
    init: function()
    {
        var baseUrl = defaultProperties.getBaseUrl();
        $("#form_drop_zone").find('.section_drop_zone').sortable({
            appendTo: "body",
            connectWith: ".section_drop_zone",
            axis: "y",
            placeholder: 'place-holder',
            handle: '.sortableElement',
            opacity:0.6,
            tolerance:"intersect",
            stop: function(event, ui){
                var newItem = $(ui.item);
                action = newItem.attr('action');
                elementType = newItem.attr('elementType');
                // Parent de l'element
                var newItemParent = newItem.parent()
                // Index de l'élement
                var itemIndex = newItemParent.children().index(newItem);

                /****************************************/
                /************* ZONE DE TEXTE ************/
                /****************************************/
                if(elementType == 'textzone'){
                    if(action == 'add'){
                        newItem.html('');
                        newItem.removeClass('ui-state-default');
                        //newItem.removeClass('element_textzone');
                        newItem.removeClass('ui-draggable');
                        newItem.removeAttr('action');

                        newItem.addClass('section_element_textzone_li');
                        newItem.addClass('element_textzone');

                        $.getJSON(
                            baseUrl + "/form/element/show/",
                            {
                                'id' : '',
                                'type' : 'textZone'
                            },
                            function(data){
                                newItem.append(data['html']);

                                var elementID  = newItem.find('form').attr('id');
                                var textZoneID = newItem.find('div[id^=textzone_]').attr('id');
                                var FE_ID      = elementID.replace("element_", '');
                                var sectionID  = $("#" + elementID).parents('li.section_li').attr('id');
                                var FE_SectID  = sectionID.replace("section_", '');

                                newItem.attr('id', elementID);

                                $("#page_edit #header #titleHidden").val(defaultProperties.getEditTextZoneTitle());
                                $("#page_edit #header #descriptionHidden").val(defaultProperties.getEditTextZoneDescription());

                                // 1.- Updater les séquences des autres éléments du formulaire
                                var currElem  = "#" + elementID;
                                updateParam.findUpdateType(
                                    newItemParent,
                                    itemIndex,
                                    elementID,
                                    currElem
                                );
                                // 2- Si l'objet est affiché: Sauvegarde en base
                                $.getJSON(
                                    baseUrl + "/form/element/add/model/element",
                                    {
                                        'FTI_LanguageID': $('#langSelector').val(),
                                        'FE_ID': FE_ID,
                                        'FE_SectionID': FE_SectID,
                                        'FE_TypeID': '1',
                                        'FE_Seq': $(currElem + ' input[id$=_Seq]').val()
                                    },
                                    function(insertedID)
                                    {
                                        //3- Si l'élément est créé, on enregistre la zone texte
                                        if (insertedID > 0)
                                        {
                                            var text = $(currElem).find('div.center').find('div.previewText');
                                            //3-1 Sauvegarder en base
                                            $.getJSON(
                                                baseUrl + "/form/text/add/model/text",
                                                {
                                                    'FTI_LanguageID': $('#langSelector').val(),
                                                    'FTI_Text': text.html(),
                                                    'FT_ID': textZoneID.replace('textzone_', ''),
                                                    'FT_ElementID': insertedID
                                                },
                                                function (textID){
                                                    var action = baseUrl
                                                        + "/form/text/edit/element/" + insertedID
                                                        + "/textID/" + textID;

                                                        newItem.find('a.textzone_edit_link').attr('href', action);
                                                }
                                            );
                                        }
                                    }
                                );
                            }
                        );
                    }
                    else{
                        moveElem.init(newItem, newItemParent, itemIndex);
                    }
                }
                /****************************************/
                /*************** QUESTIONS **************/
                /****************************************/
                else if(elementType == 'question'){
                    var questionType = newItem.attr('questionType');

                    if(action == 'add'){
                        switch(questionType){
                            case 'text':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    1
                                );
                                break;
                            case 'multiline':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    2
                                );
                                break;
                            case 'select':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    3
                                );
                                break;
                            case 'singlechoice':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    4
                                );
                                break;
                            case 'multichoice':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    5
                                );
                                break;
                            case 'date':
                                questions.add(
                                    newItem,
                                    newItemParent,
                                    itemIndex,
                                    6
                                );
                                break;
                        }
                    }
                    else
                    {
                        moveElem.init(newItem, newItemParent, itemIndex);
                    }
                }
                $("ul.section_drop_zone").sortable('refresh');
            }
        });
    }
};

/**
 * Manage the behaviour of the page break area
 */
var pageBreak = {
    init: function ()
    {
        var baseUrl = defaultProperties.getBaseUrl();
        $("#form_drop_zone").find('.breakpage_drop_zone').sortable({
            appendTo: "body",
            connectWith: ".breakpage_drop_zone",
            axis: "y",
            placeholder: 'place-holder',
            opacity:0.6,
            tolerance:"intersect",
            stop: function(event, ui){
                var newSection = $(ui.item);
                var action = newSection.attr('action');
                var elementType = newSection.attr('elementType');

                var newSectionParent = newSection.parents('.section_li');

                /****************** ADD *****************/
                // 1- Si ajout d'un saut de page
                if(action == 'add'){
                    // 1.- S'il n'y a pas de breakpage existant
                    newSection.html('');
                    newSection.removeClass('ui-state-default');
                    newSection.removeClass('element_breakpage');
                    newSection.removeClass('ui-draggable');
                    newSection.removeAttr('action');

                    if(newSectionParent.find('.section_breakpage_li').length == 0)
                    {
                        newSection.addClass('section_breakpage_li');

                        $.getJSON(
                            baseUrl + "/form/section/show-break-page/",
                            {},
                            function(data){
                                newSection.append(data['html']);

                                //newSection.append($('#model_breakpage').html());
                                var ID = 'breakpage_' + newSectionParent.attr('id')
                                newSection.attr('id',ID);
                                newSectionParent.find('form').children('input#FS_PageBreak').val('1');
                                // Updater la section (BD)
                                updateParam.updatePageBreak(newSectionParent);
                            }
                        );

                    }
                    // 1.- S'il y a un breakpage existant
                    else{
                        alert(defaultProperties.deletePageBreakAlert);
                        newSection.slideUp("normal", function(){$(this).remove();});
                    }


                }
                // 2- Si déplacement d'un saut de page (un saut de page doit absoluement se retrouver après une section)
                else
                {
                    newSection.removeClass('ui-state-default');
                    newSection.removeClass('element_breakpage');
                    newSection.removeClass('ui-draggable');
                    newSection.removeClass('ui-draggable-dragging');
                    newSection.removeClass('ui-sortable-helper');

                    newSection.removeAttr("style");


                    var oldSection = $("#"+newSection.attr('id').replace("breakpage_",""));

                    oldSection.find('form').children('input#FS_PageBreak').val('0');
                    updateParam.updatePageBreak(oldSection);

                    var ID = 'breakpage_' + newSectionParent.attr('id')
                    newSection.attr('id',ID);

                    if(newSectionParent.find('.section_breakpage_li').length == 1){
                        //   2.- Updater la section qui contenait le saut de page (enlever l'option "saut de page")
                        //   2.- Updater la section qui reçoit le saut de page (ajouter option "saut de page")
                        ID = 'breakpage_' + newSectionParent.attr('id')
                        newSection.attr('id',ID);
                        newSectionParent.find('form').children('input#FS_PageBreak').val('1');
                        // Updater la section (BD)
                        updateParam.updatePageBreak(newSectionParent);
                    }
                    // 1.- S'il y a un breakpage existant
                    else{
                        oldSection.find('.breakpage_drop_zone').append(newSection);
                        newSectionParent = newSection.parents('.section_li');
                        ID = 'breakpage_' + newSectionParent.attr('id')
                        newSection.attr('id',ID);
                        alert(defaultProperties.sectionPageBreakAlert);

                    }

                }
                $("ul.breakpage_drop_zone").sortable('refresh');
            }
        });
    }
};

/**
 * Call update when a section or an element is moved.
 * Update its sequence.
 */
var moveElem = {
    init: function(newItem, newItemParent, itemIndex)
    {
        newItem.removeClass('ui-state-default');
        //newItem.removeClass('element_textzone');
        newItem.removeClass('ui-draggable');
        newItem.removeClass('ui-draggable-dragging');
        newItem.removeClass('ui-sortable-helper');

        newItem.removeAttr("style");


        var elementID = newItem.find('form').attr('id');
        var currElem  = "#" + elementID;
        updateParam.findUpdateType(
            newItemParent,
            itemIndex,
            elementID,
            currElem
        );
    }
};

/**
 * Manage the questions behaviour for creation/insertion.
 * According to questionType:
 *  1 = Text
 *  2 = Multiline
 *  3 = Select
 *  ...
 *  For all the question types see tables:
 *      Form_QuestionType & Form_QuestionTypeIndex
 */
var questions = {
    add : function(newItem, newItemParent, itemIndex, questionType){
        var baseUrl = defaultProperties.getBaseUrl();
        newItem.html('');
        newItem.removeClass('ui-state-default');
        newItem.removeClass('ui-draggable');
        newItem.removeAttr('action');

        newItem.addClass('section_element_question_li');
        newItem.addClass('element_question');

        $.getJSON(
            baseUrl + "/form/element/show/",
            {
                'id' : '',
                'type' : 'question',
                'questionType' : questionType
            },
            function(data){
                newItem.append(data['html']);

                var elementID  = newItem.children().find('form.hidden').attr('id');
                var questionID = newItem.children().attr('id');
                var FE_ID      = elementID.replace("element_", '');
                var sectionID  = $("#" + elementID).parents('li.section_li').attr('id');
                var FE_SectID  = sectionID.replace("section_", '');

                newItem.attr('id', elementID);

                // 1.- Updater les séquences des autres éléments du formulaire
                var currElem  = "#" + elementID;
                updateParam.findUpdateType(
                    newItemParent,
                    itemIndex,
                    elementID,
                    currElem
                );
                // 2- Si l'objet est affiché: Sauvegarde en base
                $.getJSON(
                    baseUrl + "/form/element/add/model/element",
                    {
                        'FQI_LanguageID': $('#langSelector').val(),
                        'FE_ID': FE_ID,
                        'FE_SectionID': FE_SectID,
                        'FE_TypeID': '2',
                        'FE_Seq': $(currElem + ' input[id$=_Seq]').val()
                    },
                    function(insertedID)
                    {
                        //3- Si l'élément est créé, on enregistre la question
                        if (insertedID > 0)
                        {
                            var text = $(currElem).find('div.center textarea.questionLabel');
                            var type = ($('#' + questionID).attr('class')).replace('question_', '');
                            //3-1 Sauvegarder en base
                            $.getJSON(
                                baseUrl + "/form/question/add/model/question",
                                {
                                    'FQI_LanguageID': $('#langSelector').val(),
                                    'FQI_Title': text.val(),
                                    'FQ_ID': questionID.replace('question_', ''),
                                    'FQ_ElementID': insertedID,
                                    'FQ_TypeID': type
                                },
                                function (id){
                                    var thisFormParams = $(currElem).find('form.qParam');
                                    var paramField = thisFormParams.find('fieldset').children().children('input');

                                    paramField.each(function(){
                                        var data       = '/';
                                        var fieldValue = 0;

                                        if (this.type != undefined)
                                        {
                                            switch (this.type) {
                                                case 'text':
                                                    if ($(this).text().length)
                                                        fieldValue = $(this).text();
                                                    break;
                                                case 'checkbox':
                                                    var isChecked = $(currElem).find('input').is(':checked');
                                                    if (isChecked)
                                                        fieldValue = 1;
                                                    else
                                                        fieldValue = 0;
                                                    break;
                                                case 'textarea':
                                                    if ($(this).html().length)
                                                        fieldValue = $(this).html();
                                                    break;
                                            }

                                            data += 'FQV_TypeID/' + $(this).attr('validator');
                                            data += '/' +'FQVT_Category/' + $(this).attr('category');
                                            data += '/' + $(this).attr('id') + '/' + fieldValue + '/';
                                            $.getJSON(
                                                baseUrl + "/form/question/add-validator/model/questionValidation" + data,
                                                {
                                                    'FQV_QuestionID': id
                                                }
                                            );
                                        }
                                    });
                                    //Ici insert pour les valeurs si présent
                                    var questionWithOptions = [3, 4, 5];

                                    if (questionWithOptions.in_array(questionType))
                                    {
                                        var thisOptionsValues = $(currElem).find('form.qParam table');
                                        var optionsValues     = thisOptionsValues.find('tr.option');

                                        optionsValues.each(function(){
                                            responseOptions.init($(this), id);
                                        });
                                    }else{
                                    }
                                }
                            );
                        }
                    }
                );
            }
        );
    }
}

var textEditor = {
    init: function()
    {

    },

    open: function(link, object)
    {
        var editTextZoneTitle       = defaultProperties.getEditTextZoneTitle();
        var editTextZoneDescription = defaultProperties.getEditTextZoneDescription();

        $("#page_edit #header #titleHidden").val(editTextZoneTitle);
        $("#page_edit #header #descriptionHidden").val(editTextZoneDescription);
        getEditPage(link, 'FTI_Text');

    },

    loadText: function (object, url)
    {
        var baseUrl = defaultProperties.getBaseUrl();
        var parent  = object.parents().find("div[class=textzone]");
        var textId  = (parent.attr('id')).replace('textzone_', '');



        $.getJSON(
            baseUrl + "/form/text/get-text/model/text",
            {
                'FT_ID': textId,
                'FTI_LanguageID': $('#langSelector').val()
            },
            function(text){
                object.html(text);
            }
        );
    }
};


/********************************************************
 ***************** OPTIONS LIST MANAGEMENT **************
 ********************************************************/
//Initiate the drop zone and define the sortable parameters
var optionResponse = {
    init: function(){
        var baseUrl = defaultProperties.getBaseUrl();
        $("#form_drop_zone").find('.select_options_drop_zone').sortable({
            appendTo: "table",
            connectWith: ".select_options_drop_zone",
            axis: "y",
            placeholder: 'place-holder',
            opacity:0.6,
            tolerance:"intersect",
            start: function(event, ui) {
                event.preventDefault();
            },
            stop: function(event, ui) {
                var tr = $(event.target).closest('tr');
                $(tr).unbind('mousedown mouseup');
                $(document).unbind('selectstart');
            }
        });
    }
};
//create an object to test if an element is in an array.
Array.prototype.in_array = function(p_val) {
	for(var i = 0, l = this.length; i < l; i++) {
		if(this[i] == p_val) {
			return true;
		}
	}
	return false;
}
//Display a new options line and call the method to insert into db
var newOptionLine = {
    add: function(thisBtn){
        var baseUrl    = defaultProperties.getBaseUrl();
        var target     = thisBtn.next('table').find('tr.option:last');
        var prevElem   = target.find('input.delete').attr('id');
        var tmpVal     = (prevElem).split('_');
        var typeOpt    = tmpVal[0];
        var optionId   = parseInt(tmpVal[1]) + 1;
        var formParent = thisBtn.parent('form').attr('id');
        var start      = formParent.search("_") + 1;
        var length     = formParent.length - start;
        var questionId = formParent.substr(start, length);
        var optionSeq  = parseInt(target.find('#FRO_Seq').val()) + 1;

        $.getJSON(
            baseUrl + "/form/question/add-new-line-to-options/model/responseOption",
            {
                'FRO_Type' : typeOpt,
                'FRO_ID': 0,
                'FRO_Seq': optionSeq
            },
            function(newline)
            {
                target.after(newline['html']);
                var parent = thisBtn.next('table').find('tr.option:last');

                responseOptions.init(parent, questionId);
                moveTr.reorder();
            }
        );
    }
};


var responseOptions = {
  init: function (parent, insertedID){
    var baseUrl = defaultProperties.getBaseUrl();
    var values      = '/';
    var optionValue = 0;
    var td          = parent.children('td');

    td.each(function(){
        var inputField  = $(this).find('input');

        if (inputField[0].type != undefined)
        {
            var attributId = inputField.attr('id');

            switch (inputField[0].type) {
                case 'text':
                    var label = inputField.val();
                    values += attributId + "/" + label + "/";
                    break;
                case 'checkbox':
                    var isChecked = inputField.is(':checked');
                    if (isChecked)
                        optionValue = 1;
                    else
                        optionValue = 0;

                    values += attributId + "/" + optionValue + "/";
                    break;
                case 'radio':
                    var isCheckedRadio = inputField.is(':checked');
                    if (isCheckedRadio)
                        optionValue = 1;
                    else
                        optionValue = 0;

                    values += attributId + "/" + optionValue + "/";
                    break;
                case 'hidden':
                    optionValue = inputField.val();
                    values += attributId + "/" + optionValue + "/";
                    break;
                case 'button':
                    var tmpArray = attributId.split('_');
                    var optType  = tmpArray[0];
                    var optionId = tmpArray[1];

                    if (optType == 'select')
                        values += "FRO_Other/0/";

                    values += "FRO_Type" + "/" + optType + "/";
                    values += "FRO_ID" + "/" + optionId + "/";
                    values += "FROI_ResponseOptionID" + "/" + optionId + "/";
                    break;
            }
        }
    });

    $.getJSON(
        baseUrl + "/form/question/add-response-option/model/responseOption" + values,
        {
            'FROI_LanguageID': $('#langSelector').val(),
            'FRO_QuestionID': insertedID
        }
    );
  }
}

// Manage ajax update for the form module

/**
 * Call the controller for update
 * Manage the displaying of all the parameters
 *
 * @param object elem    The element having changed to update.
 * @param object event   The event captured on the element.
 *
 * @return void
 */
var updateOptionParam = {
    module : 'form',
    init : function(elem, event)
    {
        var elemId = '#' + elem.attr('id');
        var langID = $('#langSelector').val();
        var object = elem.parents('tr').attr('id');
        var tmpVal  = object.split('_');
        var formId = tmpVal[1];
        var action = false;

        var params = this.setOptionsData(elem, formId, langID, object);
        $(elem).bind(
            event.type,
            updateParam._sendData(elemId, params, action)
        );

        return false;
    },

    /**
     * Create a formated string to be included in the url for the ajax post
     *
     * @param {object} elem   The current DOM element to process.
     * @param {int}    formId The form id to defined witch one to update in db.
     * @param {int}    langID The language id.
     * @param {string} object Defined the name of the php object to use.
     *
     * @return string params A string added in the ajax post url.
     */
    setOptionsData : function(elem, formId, langID, object)
    {
        var params  = "";
        var field   = elem.get(0).type;
        var fieldId = elem.attr('id');
        var prefixe = 'FI_';

        if (field == "text" || field == "textarea")
        {
            var formTitle = elem.val();
            var tmpObj = object.split("_");
            object     = tmpObj[0].charAt(0).toUpperCase()
                            + tmpObj[0].substring(1);

            if (!fieldId.match("new"))
            {
                var tmpPref = fieldId.split("_");
                prefixe     = tmpPref[0] + "_";
            }
            else
            {
                fieldId = fieldId.replace('new', 'FI_');
            }

            if (object == 'ResponseOption')
            {
                formTitle = escape( encodeURIComponent(formTitle));
            }
            params = prefixe + object + "ID/" + formId + "/";
            params += fieldId + "/" + formTitle + "/";
            params += prefixe + "LanguageID/" + langID;

        }
        else if (field == "checkbox" || field == "radio")
        {
            var showTitle = 1;
            var isChecked = elem.is(':checked');
            if (!isChecked)
                showTitle = 0;
            tmpPref = fieldId.split("_");
            prefixe = tmpPref[0];

            params += prefixe + "_ID/" + formId + "/";
            params += prefixe + "I_LanguageID/" + langID + "/";
            params += fieldId + "/" + showTitle;

        }
        else if (field == 'hidden')
        {
            var sequence = elem.val();

            tmpPref = fieldId.split("_");
            prefixe = tmpPref[0];

            params += prefixe + "_ID/" + formId + "/";
            params += prefixe + "I_LanguageID/" + langID + "/";
            params += fieldId + "/" + sequence;
        }
        params += "/";

        if (prefixe == 'FRO' || prefixe == 'FROI')
            object = 'responseOption';

        if (formId == undefined)
        {
            var tmp = ((elem.parents('form:first').attr('id'))).split('_');
            var questionId = tmp[1];
            params += "FRO_QuestionID/" + questionId + "/";
        }
        params += "model/" + object + "/";

        return params;
    }
}

var moveTr = {
    init: function()
    {
        var mouseX, mouseY, lastX, lastY = 0;

        $(document).mousemove(function(e) {mouseX = e.pageX;mouseY = e.pageY;});

        var need_select_workaround = typeof $(document).attr('onselectstart') != 'undefined';

//        $('td#FRO_Seq').css('cursor', 'move')
        $('table tbody tr td#FRO_Seq').live('mousedown', function (e) {
            lastY  = mouseY;
            var tr = $(this).parent();//.parents('tr:first');
            tr.fadeTo('fast', 0.2);

            $('tr', tr.parent() ).not(tr).mouseenter(function(){
                if (mouseY > lastY) {
                    $(this).after(tr);
                } else {
                    $(this).before(tr);
                }
                lastY = mouseY;
            });

            $('body').live('mouseup', function () {
               tr.fadeTo('fast', 1);
               $('tr', tr.parent()).unbind('mouseenter');
               $('body').unbind('mouseup');

                if (need_select_workaround)
                    $(document).unbind('selectstart');

               moveTr.reorder();

            });
            e.preventDefault();

            if (need_select_workaround)
                $(document).bind('selectstart', function () {return false;});

            return false;
        }).css('cursor', 'move');

    },
    reorder: function () {
            var position = 1;
            $('table tbody tr.option ').each(function () {
                // Change the text of the first TD element inside this TR
                var seqField = $(this).children().find('input#FRO_Seq');
                seqField.val(position);

                var tmp = $(this).attr('id');
                var id  = tmp.replace('responseOption_', '');
                var url = defaultProperties.getBaseUrl() + '/form/index/updateformparam/model/responseOption';
                $.getJSON(
                    url,
                    {
                        'FROI_LanguageID': $('#langSelector').val(),
                        'FRO_ID': id,
                        'FRO_Seq': seqField.val()
                    },
                   function(data){
                   }
                );
                position += 1;
            });
        }
};

var moveToolBox = {
    init: function(elem)
    {
        var top = $('#headerZone').offset().top + $('#headerZone').height();
        var scroll = elem.scrollTop();
        var speed = 500;

        if(scroll >= top){
            defaultProperties.el.stop().animate({
                top: top + (scroll-top+15)
            }, speed, function(){
                //Animation complete
            });
        }
        else{
            defaultProperties.el.stop().animate({
                top: top
            }, speed, function(){
                //Animation complete
            });
        }
    }
};