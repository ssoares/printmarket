<?php
    
    class FormEvents extends Cible_Form_Module
    {
        public function __construct($options = null)
        {
            $this->tableFieldPrefix = '';
            $this->tableName = 'EventsData';
            $this->_addSubmitSaveClose = true;

            parent::__construct($options);

            $imageSrc   = $options['imageSrc'];
            $eventID     = $options['eventID'];
            $isNewImage = $options['isNewImage'];

            if($eventID == '')
                $pathTmp = $this->_imagesFolder . "/tmp";
            else
                $pathTmp = $this->_imagesFolder . "/$eventID/tmp";

            // hidden specify if new image for the events
            $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value'=>$isNewImage));
            $newImage->removeDecorator('Label');
            $this->addElement($newImage);

            if(isset($options['categoriesList']) && $options['categoriesList'] == 'true')
            {
                // select box category (Parameter #1)
                $categoryEvents = new Zend_Form_Element_Select('Param1');
                $categoryEvents->setLabel(Cible_Translation::getCibleText('categoryEvents'))
                ->setAttrib('class','largeSelect');

                $categories = new Categories();
                $select = $categories->select()->setIntegrityCheck(false)
                                     ->from('Categories')
                                     ->join('CategoriesIndex', 'C_ID = CI_CategoryID')
                                     ->where('C_ModuleID = ?', 7)
                                     ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
                                     ->order('CI_Title');

                $categoriesArray = $categories->fetchAll($select);

                foreach ($categoriesArray as $category){
                    $categoryEvents->addMultiOption($category['C_ID'],$category['CI_Title']);
                }

                $this->addElement($categoryEvents);
            }

            // Date picker
            $datePicker = new Cible_Form_Element_DateRangePicker('DateRange');
            $datePicker->setLabel(Cible_Translation::getCibleText('datesEvents'))
                ->setRequired()
                ->addValidator('NotEmpty', true, array('messages' => array(
                    'isEmpty' => Cible_Translation::getCibleText('validation_message_invalid_date')//,
                    )));

            $this->addElement($datePicker);

            // Status
            $status = new Zend_Form_Element_Checkbox('Status');
            $status->setLabel(Cible_Translation::getCibleText('onlineEvents'));
            $status->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

            $this->addElement($status);

            // IMAGE
            $imageTmp  = new Zend_Form_Element_Hidden('ImageSrc_tmp');
            $imageTmp->removeDecorator('Label');
            $this->addElement($imageTmp);

            $imageOrg  = new Zend_Form_Element_Hidden('ImageSrc_original');
            $imageOrg->removeDecorator('Label');
            $this->addElement($imageOrg);

            $imageView = new Zend_Form_Element_Image('ImageSrc_preview', array('onclick'=>'return false;'));
            $imageView->setImage($imageSrc);
            $this->addElement($imageView);

            $imagePicker = new Cible_Form_Element_ImagePicker('ImageSrc', array( 'onchange' => "document.getElementById('imageView').src = document.getElementById('ImageSrc').value",
                                                                                    'associatedElement' => 'ImageSrc_preview',
                                                                                    'pathTmp'=>$pathTmp,
                                                                                    'contentID'=>$eventID
                                                                        ));
            $imagePicker->removeDecorator('Label');
            $this->addElement($imagePicker);

            $imageAlt = new Zend_Form_Element_Text("ImageAlt");
            $imageAlt->setLabel(Cible_Translation::getCibleText('form_label_description_image'))
            ->setAttrib('class','stdTextInput');

            $this->addElement($imageAlt);

            
            $imageAlt = new Zend_Form_Element_Text("ImageAlt");
            $imageAlt->setLabel(Cible_Translation::getCibleText('form_label_description_image'))
            ->setAttrib('class','stdTextInput');
            $this->addElement($imageAlt);    
            
            
            $ED_Allstar= new Zend_Form_Element_Checkbox('ED_Allstar');
            $ED_Allstar->setLabel($this->getView()->getCibleText('show_only_allstar'));
            $ED_Allstar->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));
            $this->addElement($ED_Allstar);
            
            
            
            // Breif text
            $Location= new Cible_Form_Element_Editor('Location', array('mode'=>Cible_Form_Element_Editor::ADVANCED));
            $Location->setLabel(Cible_Translation::getCibleText('placeEvents'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => Cible_Translation::getCibleText('validation_message_invalid_text'))))
            ->setAttrib('class','mediumEditor');
            $label = $Location->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->addElement($Location);
            
            $Brief = new Zend_Form_Element_Text("Brief");
            $Brief->setLabel(Cible_Translation::getCibleText('shortLongEvents'))
                    ->setRequired(true)
            ->setAttrib('class','stdTextInput');
            $label = $Brief->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->addElement($Brief);   

            // Text
            $text = new Cible_Form_Element_Editor('Text', array('mode'=>Cible_Form_Element_Editor::ADVANCED));
            $text->setLabel(Cible_Translation::getCibleText('texteLongEvents'))
            ->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => Cible_Translation::getCibleText('validation_message_invalid_text'))))
            ->setAttrib('class','mediumEditor');

            $label = $text->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($text);

            $categoryID = new Zend_Form_Element_Hidden('CategoryID');

            $this->addElement($categoryID);
        }
    }
?>
