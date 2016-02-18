<?php
    
    class FormCategory extends Cible_Form_Multilingual 
    {
        public function __construct($options = null)
        {
            parent::__construct($options);
            
            $moduleID = -1;
            $moduleName = '';
            
            if( !empty($options['moduleID']) ){
                $moduleID = $options['moduleID'];
                $moduleName = Cible_FunctionsModules::getModuleNameByID($moduleID);
            }
            
            // input text for the title of the text module
            $categoryTitle = new Zend_Form_Element_Text('Title');
            $categoryTitle->setLabel($this->getView()->getCibleText('form_category_title_label'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->_view->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');
            
            $label = $categoryTitle->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            
            $this->addElement($categoryTitle);

            $categoryDescription = new Zend_Form_Element_Textarea('WordingShowAllRecords');
            $categoryDescription->setLabel($this->_view->getCibleText('form_category_view_all_label'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->_view->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextarea');
            
            $label = $categoryDescription->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            
            $this->addElement($categoryDescription);
            
            $views = Cible_FunctionsCategories::getCategoryViews($moduleID);
            
            foreach($views as $view){
                
                $pickerName = $view['MV_Name'];
                
                $controllerName = new Zend_Form_Element_Text("{$pickerName}_controllerName");
                $controllerName->setLabel($this->getView()->getCibleText("form_select_option_view_{$moduleName}_{$pickerName}") )
                               ->setAttrib('onfocus', "openPagePicker('page-picker-{$pickerName}');");
                
                $this->addElement($controllerName);
                
                $pagePicker = new Cible_Form_Element_PagePicker("{$pickerName}_pageID",array(
                    'associatedElement' => "{$pickerName}_controllerName",
                    'onclick' => "javascript:closePagePicker(\"page-picker-{$pickerName}\")"
                ));
                $pagePicker->setLabel($this->_view->getCibleText('form_category_associated_page_label'));
                
                $pagePicker->setDecorators(array(
                    'ViewHelper',
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => "page-picker", 'id' => "page-picker-{$pickerName}")),
                ));

                $this->addElement($pagePicker);
            }
            
            // Check if want to put that category in the RSS Feed
            $showInRss = new Zend_Form_Element_Checkbox('ShowInRss');
            $showInRss->setValue(1);
            $showInRss->setLabel($this->_view->getCibleText('form_category_add_to_rss_label'));
            $showInRss->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));                           
            
            $this->addElement($showInRss);
            
            // How many last repertoire in the RSS Feed
            $rssItemsCount = new Zend_Form_Element_Text('RssItemsCount');
            $rssItemsCount->setLabel($this->getView()->getCibleText('form_category_how_many_items_for_rss_label'))            
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->_view->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','shortTextInput');
            
            $this->addElement($rssItemsCount);
                             
            
            $module = new Zend_Form_Element_Hidden('ModuleID');
            $module->setValue($moduleID)
                     ->removeDecorator('label')
                     ->removeDecorator('DtDdWrapper');
                     
            $this->addElement($module);
        }
    }
?>
