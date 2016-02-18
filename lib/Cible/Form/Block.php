<?php
class Cible_Form_Block extends Cible_Form_Block_Multilingual
{
    protected $_moduleName = '';

    public function __construct($options = null)
    {
        $add = $options['addAction'];
        if (!$add)
            unset($options['addAction']);
        
        parent::__construct($options);

        $this->setAttrib('class', 'form_block');

        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        // contains the id of the page
        $pId = new Zend_Form_Element_Hidden('B_PageID');
        $pId->removeDecorator('Label')
            ->removeDecorator('HtmlTag')
            ->setValue($pageID);
        $this->addElement($pId);
        // contains the id of the diplicated block.
        $dId = new Zend_Form_Element_Hidden('B_DuplicateId');
        $dId->removeDecorator('Label')
            ->removeDecorator('HtmlTag');
        $this->addElement($dId);
        // contains the module Id
        $mId = new Zend_Form_Element_Hidden('B_ModuleID');
        $mId->removeDecorator('Label')
            ->removeDecorator('HtmlTag');
        if (isset($options['moduleID']))
            $mId->setValue($options['moduleID']);
        $this->addElement($mId);
        // online field to set if copy
        $online = new Zend_Form_Element_Hidden('B_Online');
        $online->removeDecorator('Label')
            ->removeDecorator('HtmlTag');
        $this->addElement($online);
        if(isset($options['moduleName']))
            $this->setName($options['moduleName']);

        // input text for the title of the text module
        $blockTitle = new Zend_Form_Element_Text('BI_BlockTitle');
        $blockTitle->setLabel($this->getView()->getCibleText('form_label_title'))
        ->setRequired(true)
        ->addFilter('StripTags')
        ->addFilter('StringTrim')
        ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
        ->setAttrib('class','stdTextInput');

        $this->addElement($blockTitle);

        // select box for the position of the module
        $security = new Zend_Form_Element_Select('B_Secured');
        $security->setLabel($this->getView()->getCibleText('manage_block_secured_status'))
            ->setAttrib('class','stdSelect')
            ->addMultiOption('0', $this->getView()->getCibleText('manage_block_secured_none'))
            ->addMultiOption('1', $this->getView()->getCibleText('manage_block_secured_logged'))
            ->addMultiOption('2', $this->getView()->getCibleText('manage_block_secured_notlog'))
            ->setRegisterInArrayValidator(false);

        $this->addElement($security);

        // checkbox for determine if show block title in frontend
        $showBlockTitle = new Zend_Form_Element_Checkbox('B_ShowHeader');
        $showBlockTitle->setLabel($this->getView()->getCibleText('form_label_B_ShowHeader'))
            ->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));
        $showBlockTitle->setValue(0);

        $this->addElement($showBlockTitle);

        $_request = Zend_Controller_Front::getInstance()->getRequest();
        $_action = $_request->getActionName();

        if( $_action == 'add-block'){

            // select box for the zone of the module
            $zone = new Zend_Form_Element_Select('B_ZoneID');
            $zone->setLabel($this->getView()->getCibleText('form_label_zone'))
            ->setAttrib('class','largeSelect');

            $this->addElement($zone);

            // select box for the position of the module
            $position = new Zend_Form_Element_Select('B_Position');
            $position->setLabel($this->getView()->getCibleText('form_label_position'))
            ->setAttrib('class','largeSelect')
            ->setRegisterInArrayValidator(false);

            $this->addElement($position);
        }

        // submit button  (save)
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->getView()->getCibleText('button_add'))
                ->setAttrib('id', 'submitSave')
                ->setAttrib('class','stdButton')
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $this->addElement($submit);

        // cancel button (don't save and return to the main page)
        $cancel = new Zend_Form_Element_Button('cancel');

        $cancel->setLabel($this->getView()->getCibleText('button_cancel'))
                ->setAttrib('class','stdButton')
                ->setAttrib('onclick', "document.location.href='$baseDir/page/manage/index/ID/$pageID'")
                ->removeDecorator('DtDdWrapper')
                ->removeDecorator('Label');

        $this->addElement($cancel);

        // create an action display group with element name previously added to the form
        $this->addDisplayGroup(
            array('submit', 'cancel'),
            'actions'
        );

        // Set the decorators we want for the display group
        $this->setDisplayGroupDecorators(array('FormElements', 'Fieldset',array('HtmlTag', array('tag' => 'dd')),));

        $viewSelector = new Zend_Form_Element_Select('Param999');
        $viewSelector->setLabel( $this->getView()->getCibleText('form_select_label_associated_view') )
                    ->setAttrib('class','stdSelect')
                    ->setOrder(1);

        $optViews = array();
        if( $this->_moduleName ){
            foreach(Cible_FunctionsModules::getAvailableViews( $this->_moduleName ) as $view){
                $optViews[$view['MV_Name']] =  $this->getView()->getCibleText("form_select_option_view_{$this->_moduleName}_{$view['MV_Name']}");
            }

            asort($optViews);
            $viewSelector->addMultiOptions($optViews);
        }

        if( count($viewSelector->options) == 0)
            $viewSelector->addMultiOption('index',$this->getView()->getCibleText("form_select_option_view_default_index"));

        $this->addElement($viewSelector);

        $this->addDisplayGroup(array('Param999'),'parameters');
        $parameters = $this->getDisplayGroup('parameters');

        if (!empty($options['duplicateId']))
        {
            $blocks = new Zend_Form_Element_Radio('blocks');
            $blocks->setLabel($this->getView()->getCibleText('form_label_blocks'))->setSeparator('');
            $opts = array('pageId' => $options['duplicateId'], 'siteOrigin' => $options['siteOrigin'], 'moduleId' => $options['moduleID']);
            $data = Cible_FunctionsBlocks::getBlocksFromRelatedPage($opts);
            if (empty($data['options']))
                $blocks->setLabel($this->getView()->getCibleText('form_label_blocks_empty'));
                if (!$add)
                $blocks->setLabel($this->getView()->getCibleText('form_label_content_from_duplicated_blocks'));

            $this->addElement($blocks);
            $this->addDisplayGroup(array('blocks'),'linkBlocks');
            $linkBlocks = $this->getDisplayGroup('linkBlocks');
            $linkBlocks->setLegend($this->getView()->getCibleText('form_fieldset_legend_linkBlocks'));

            if (!empty($data['options']) && $add)
            {
                $list = array(0 => $this->getView()->getCibleText('form_label_noValue')) + $data['options'];

                $blocks->addMultiOptions($list)->setValue('0');
                $jsBlocks = json_encode($data['blocks']);
                $script =<<< EOS
                    var jsBlocks = {$jsBlocks}
                $('#fieldset-linkBlocks input').change(function(){
                    if ($(this).val() != 0)
                    {
                        var option = $(this).val();
                        var label = $(this).parent().text();
                        $( "#dialog-confirm" ).dialog({
                            resizable: false,
    //                        height:140,
                            modal: true,
                            buttons: {
                                "{$this->getView()->getCibleText('form_label_load')}": function() {
                                    $.each(jsBlocks,function(id, block){
                                        if(id == option)
                                        {
                                            $.each(block,function(key, value){
                                                if ($('#' + key).length > 0 && key != 'B_PageID')
                                                {
                                                    if ($('#' + key).is(':checkbox') && value > 0)
                                                        $('#' + key).attr('checked', 'checked');
                                                    else
                                                    {
                                                        if (key == 'BI_BlockTitle')
                                                            value = label;
                                                        $('#' + key).val(value);
                                                    }
                                                }
                                            });
                                            $('#B_DuplicateId').val(id);
                                        }
                                    });
                                    $( this ).dialog( "close" );
                                },
                                "{$this->getView()->getCibleText('button_cancel')}": function() {
                                    $( this ).dialog( "close" );
                                }
                            }
                        });
                    }
                    else
                    {
                        $('#B_DuplicateId').val(0);
                    }
                });
EOS;
                $this->getView()->inlineScript()->appendScript($script);
            }
        }
    }
}