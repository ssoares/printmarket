<?php

class FormPage extends Cible_Form_Block_Multilingual
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);
        $this->setName('page');
        //$imageSrc = $options['imageSrc'];
        $pageID = !empty($options['pageID'])?$options['pageID']:null;
        $imageHeaderArray = $options['imageHeaderArray'];
        $imageBackHeaderArray = $options['imageBackHeaderArray'];


        // contains the id of the page
        $id = new Zend_Form_Element_Hidden('id');
            $id->removeDecorator('Label');
            $id->removeDecorator('HtmlTag');

        // input text for the title of the page
        $title = new Zend_Form_Element_Text('PI_PageTitle');
        $title->setLabel($this->getView()->getCibleText('label_titre_page'))
        ->setRequired(true)
            ->setOrder(1)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_field_required')))
        ->setAttrib('class','stdTextInput')
        ->setAttrib('onBlur','javascript:fillInControllerName();');
        $lblTit = $title->getDecorator('Label');
        $lblTit->setOption('class', $this->_labelCSS);

        // input text for the index of the page
        $uniqueIndexValidator = new Zend_Validate_Db_NoRecordExists('PagesIndex', 'PI_PageIndex');
        $uniqueIndexValidator->setMessage($this->getView()->getCibleText('label_index_already_exists'), Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND);

        $reservedWordValidator = new Cible_Validate_Db_NoRecordExists('Modules', 'M_MVCModuleTitle');
        $reservedWordValidator->setMessage($this->getView()->getCibleText('label_index_reserved'), Zend_Validate_Db_NoRecordExists::ERROR_RECORD_FOUND);

        $index = new Zend_Form_Element_Text('PI_PageIndex');
        $index->setLabel($this->getView()->getCibleText('label_name_controller'))
        ->setRequired(true)
            ->setOrder(2)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addFilter('StringToLower')
        ->addValidator('NotEmpty',true,array('messages'=>Cible_Translation::getCibleText('error_field_required')))
        ->addValidator('stringLength', true, array(1,255,'messages' => array(
                                                                        Zend_Validate_StringLength::TOO_SHORT =>$this->getView()->getCibleText('label_index_more_char'),
                                                                        Zend_Validate_StringLength::TOO_LONG =>$this->getView()->getCibleText('label_index_less_char')
                                                                        )))
        ->addValidator('regex', true, array('/^[a-z0-9][a-z0-9_-]*[a-z0-9]$/', 'messages' => $this->getView()->getCibleText('label_only_character_allowed')))
        ->addValidator($uniqueIndexValidator, true)
        ->addValidator($reservedWordValidator, true)
        ->setAttrib('class','stdTextInput');
        $lblId = $index->getDecorator('Label');
        $lblId->setOption('class', $this->_labelCSS);

        //Select the type of site for the page
        $siteType = new Zend_Form_Element_hidden('P_SiteType');
        $siteType->removeDecorator('Label');
        $siteType->removeDecorator('HtmlTag');
//        $siteType = new Zend_Form_Element_Select('P_SiteType');
//        $siteType->setLabel($this->getView()->getCibleText('label_site_type'));
//        $siteType->addMultiOptions(
//            array(
//            's' => $this->getView()->getCibleText('label_site_type_s'),
//            'm' => $this->getView()->getCibleText('label_site_type_m')
//            )
//        );
        $siteType->setValue($options['siteType']);
        $this->addElement($siteType);
        // textarea for the meta and title of the page
        $metaTitle = new Zend_Form_Element_Textarea('PI_MetaTitle');
        $metaTitle->setLabel('Titre (meta)')
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        //->setAttrib('class','stdTextarea');
        ->setAttrib('class','stdTextareaShort');
        $lblMetaTitle= $metaTitle->getDecorator('Label');
        $lblMetaTitle->setOption('class', $this->_labelCSS);
        // field for the canonical link of the page
        $canonical = new Zend_Form_Element_Text('PI_CanonicalLink');
        $canonical->setLabel($this->getView()->getCibleText('form_label_canonical_link'))
            ->setRequired(false)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class','largeTextInput');
        $label = $canonical->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        // textarea for the meta description of the page
        $metaDescription = new Zend_Form_Element_Textarea('PI_MetaDescription');
        $metaDescription->setLabel($this->getView()->getCibleText('label_description_meta'))
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        //->setAttrib('class','stdTextarea');
        ->setAttrib('class','stdTextareaShort');
        $lblMetaDescr= $metaDescription->getDecorator('Label');
        $lblMetaDescr->setOption('class', $this->_labelCSS);

        // textarea for the meta keywords of the page
        $metaKeyWords = new Zend_Form_Element_Textarea('PI_MetaKeywords');
        $metaKeyWords->setLabel($this->getView()->getCibleText('label_keywords_meta'))
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        //->setAttrib('class','stdTextarea');
        ->setAttrib('class','stdTextareaShort');
        $lblMetaKey= $metaKeyWords->getDecorator('Label');
        $lblMetaKey->setOption('class', $this->_labelCSS);

        // textarea for the meta keywords of the page
        $metaOthers = new Zend_Form_Element_Textarea('PI_MetaOther');
        $metaOthers->setLabel($this->getView()->getCibleText('label_other_meta'))
        ->setRequired(false)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        //->setAttrib('class','stdTextarea');
        ->setAttrib('class','stdTextareaShort');
        $lblMetaOther= $metaOthers->getDecorator('Label');
        $lblMetaOther->setOption('class', $this->_labelCSS);

        // select box for the templates
        $layout = new Zend_Form_Element_Select('P_LayoutID');
        $layout->setLabel($this->getView()->getCibleText('label_layout_page'))
               ->setAttrib('class','stdSelect');

        // select box for the templates
        $template = new Zend_Form_Element_Select('P_ViewID');
        $template->setLabel($this->getView()->getCibleText('label_model_page'))
        ->setAttrib('class','stdSelect');

        // select box for the templates
        $theme = new Zend_Form_Element_Select('P_ThemeID');
        $theme->setLabel($this->getView()->getCibleText('form_label_P_ThemeID'))
        ->setAttrib('class','stdSelect');


        // checkbox for the status (0 = offline, 1 = online)
        $status = new Zend_Form_Element_Checkbox('PI_Status');
        $status->setValue(1)
            ->setOrder(6);
        $status->setLabel($this->getView()->getCibleText('form_check_label_online'));
        $status->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        $lblStatus= $status->getDecorator('Label');
        $lblStatus->setOption('class', $this->_labelCSS);

        // checkbox for the show title of the page (0 = offline, 1 = online)
        $showTitle = new Zend_Form_Element_Checkbox('P_ShowTitle');
        $showTitle->setValue(1);
        $showTitle->setLabel($this->getView()->getCibleText('form_check_label_show_title'));
        $showTitle->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));


        // image group
        // ImageSrc
        /*$imageSrc = new Zend_Form_Element_Select('P_BannerGroupID');
        $imageSrc->setLabel($this->getView()->getCibleText('form_banner_image_group_extranet'))->setAttrib('class','stdSelect');
        $imageSrc->addMultiOption('', 'Sans image');

        $group = new GroupObject();
        $groupArray = $group->groupCollection();
        foreach ($groupArray as $group1)
        {
            $imageSrc->addMultiOption($group1['BG_ID'],$group1['BG_Name']);
        }*/

        $config = Zend_Registry::get('config')->toArray();

        $showEntete = 0;
        $showBackground = 0;
        if(!empty($config['image']['entete']['show'])){
            if($config['image']['entete']['show']==1){
                $showEntete = 1;
                $imageSrc = new Zend_Form_Element_Select('PI_TitleImageSrc');
                $imageSrc->setLabel("Image de l'entête")->setAttrib('class','stdSelect');
                $imageSrc->addMultiOption('', 'Sans image');
                $i = 1;
                foreach($imageHeaderArray as $img => $path)
                {
                    $imageSrc->addMultiOption($path, $path);
                    $i++;
                }
                $lbl= $imageSrc->getDecorator('Label');
                $lbl->setOption('class', $this->_labelCSS);
            }
            else{
                $imageSrc = new Zend_Form_Element_Hidden('PI_TitleImageSrc');
                $lbl= $imageSrc->getDecorator('Label');
                $lbl->setOption('class', "textInvisible");
            }
        }
        else{
            $imageSrc = new Zend_Form_Element_Hidden('PI_TitleImageSrc');
            $lbl= $imageSrc->getDecorator('Label');
            $lbl->setOption('class', "textInvisible");

        }
        if(!empty($config['image']['background']['show'])){
            if($config['image']['background']['show']==1){
                $showBackground = 1;
                $imageBackSrc = new Zend_Form_Element_Select('PI_ImageBackground');
                $imageBackSrc->setLabel("Image de background")->setAttrib('class','stdSelect');
                $imageBackSrc->addMultiOption('', 'Sans image');
                $i = 1;
                foreach($imageBackHeaderArray as $img => $path)
                {
                    $imageBackSrc->addMultiOption($path, $path);
                    $i++;
                }

                $lbl= $imageBackSrc->getDecorator('Label');
                $lbl->setOption('class', $this->_labelCSS);
            }
            else{
                $imageBackSrc = new Zend_Form_Element_Hidden('PI_ImageBackground');
                $lbl= $imageBackSrc->getDecorator('Label');
                $lbl->setOption('class', "textInvisible");
            }
        }
        else{
            $imageBackSrc = new Zend_Form_Element_Hidden('PI_ImageBackground');
            $lbl= $imageBackSrc->getDecorator('Label');
            $lbl->setOption('class', "textInvisible");
        }


        $altImage = new Zend_Form_Element_Text('PI_AltPremiereImage');
        $altImage->setLabel($this->getView()->getCibleText('label_altFirstImage'))
            ->setAttrib('class','stdTextInput');
        $lblImage= $altImage->getDecorator('Label');
        $lblImage->setOption('class', $this->_labelCSS);

//        $PI_TitleImage2 = new Zend_Form_Element_Text('PI_TitleImage2');
//        $PI_TitleImage2->setLabel($this->getView()->getCibleText('label_PI_TitleImage2'))
//            ->setAttrib('class','stdTextInput');
//        $lblImage2= $PI_TitleImage2->getDecorator('Label');
//        $lblImage2->setOption('class', $this->_labelCSS);


        // input text for the title of the page
        $titleAlt = new Zend_Form_Element_Text('PI_TitleImageAlt');
        $titleAlt->setLabel($this->getView()->getCibleText('label_alt_image'))
        ->setRequired(false)
        ->setAttrib('class','stdTextInput');
        $lblAlt = $titleAlt->getDecorator('Label');
        $lblAlt->setOption('class', $this->_labelCSS);

        $sitesList = new Zend_Form_Element_Select('P_FromSite');
        $sitesList->setLabel($this->getView()->getCibleText('form_label_sitesList_page'))
            ->setOrder(3)
            ->setDecorators(array(
            'ViewHelper',
            array('Label', array('placement' => 'prepend', 'tag' => 'span', 'class' => 'sitesList')),
            array(
                array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => '')
            ),
        ));
        $sites[''] = '';
        $list = $this->getView()->siteList(array('getValues' => true));
        if (count($list) > 1)
        {
            $sites = array_merge($sites,$list);
            unset($sites[Zend_Registry::get('currentSite')]);
            $sitesList->addMultiOptions($sites);
            $duplicateId = new Zend_Form_Element_hidden('P_DuplicateId');
            $duplicateId->removeDecorator('Label');
            $controllerName = new Zend_Form_Element_Text('P_DuplicateId_lbl');
            $controllerName->setLabel( Cible_Translation::getCibleText('form_label_pageToLink') )
                ->setAttrib('onfocus', "openPagePicker('page-picker-pagePicker');")
                ->setAttrib('class','stdTextInput')
                ->setOrder(4)
                ->setDecorators(array(
                    'ViewHelper',
                    array('Label', array('placement' => 'prepend', 'class' => '')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => '')),
                ));

            $pagePicker = new Cible_Form_Element_PagePicker('pagePicker',array(
                'menu'=>'Principal',
                'langId'=>$this->_currentEditLanguage,
                'associatedElement' => 'P_DuplicateId',
                'onclick' => "javascript:closePagePicker(\"page-picker-pagePicker\")"
            ));

            $pagePicker->setLabel(Cible_Translation::getCibleText('form_label_page_picker'))
                ->setOrder(5)
                ->setDecorators(array(
                    'ViewHelper',
                    array('Label', array('placement' => 'prepend', 'class' => 'sitesList')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => "page-picker", 'id' => "page-picker-pagePicker")),
                    ));

            $this->addElement($sitesList);
            $this->addElement($controllerName);
            $this->addElement($duplicateId);
            $this->addElement($pagePicker);
        }

        // add element to the form
        $this->addElements(array(
            $title,
            $index,
            $status,
            $showTitle,
            $siteType,
            $layout,
            $template,
            $theme,
            $imageSrc,
            $imageBackSrc,
            $altImage,
//            $PI_TitleImage2,
            $metaTitle,
            $canonical,
            $metaDescription,
            $metaKeyWords,
            $metaOthers,
            $id)
        );

        $script =<<< EOS
            var baseUrl = "{$this->getView()->baseUrl()}";
            var first = true;
            var currentPage = $pageID;
            var siteFromVal = $('#P_FromSite').val();
            var pageName = $('#P_DuplicateId_lbl').val();
            var pageId = $('#P_DuplicateId').val();
            var ok = true;
            var url = baseUrl + "/page/manage/ajax/actionAjax/findLink";
            $('#P_FromSite').change(function(){
                if (!first)
                {
                    $.post(
                        url,
                        {term: currentPage},
                        function(data){
                            if (data == 'true' && (pageId > 0))
                            {
                                $('#P_FromSite').val(siteFromVal);
                                $('#P_DuplicateId_lbl').val(pageName);
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
                                var urlp = baseUrl + "/page/manage/ajax/actionAjax/pageSiteSrc";
                                $.post(
                                urlp,
                                {term: $('#P_FromSite').val()},
                                function(data){
                                    var tmp = $(data);
                                    $('#pagePicker').val($('#P_DuplicateId').val());
                                    $('#page-picker-pagePicker').html(tmp.html());
                                });
                            }
                        });
                }
                if (first)
                {
                    var urlp = baseUrl + "/page/manage/ajax/actionAjax/pageSiteSrc";
                    $.post(
                    urlp,
                    {term: $(this).val()},
                    function(data){
                        var tmp = $(data);
                        $('#page-picker-pagePicker').html(tmp.html());
                        $('#pagePicker').val($('#P_DuplicateId').val());
                    });
                }

            }).change();
            first = false;
            $('#P_DuplicateId_lbl').blur(function(){
                var elem = $('#P_DuplicateId_lbl');
                if (elem.val() == '')
                {
                    $.post(
                    url,
                    {term: currentPage},
                    function(data){
                        var idSelected = $('#pagePicker').val();

                        if (data == 'true')
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
                    });
                }
            });
EOS;
        $this->getView()->inlineScript()->appendScript($script);
    }

    public function populate(array $values)
    {
        if (!empty($values['P_DuplicateId']))
        {
            $dbs = Zend_Registry::get('dbs');
            $defaultAdapter = $dbs->getDefaultDb();
            $dbAdapter = $dbs->getDb($values['P_FromSite']);
            Zend_Registry::set('db', $dbAdapter);
            $values['P_DuplicateId_lbl'] = Cible_FunctionsPages::getPageNameByID($values['P_DuplicateId']);
            Zend_Registry::set('db', $defaultAdapter);
            $values['pagePicker'] = $values['P_DuplicateId'];
        }

        return parent::populate($values);
    }
}
