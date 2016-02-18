<?php
    class FormGallery extends Cible_Form_Block_Multilingual{
        public function __construct($options = null)
      {
            $galleryID = $options['galleryID'];
            $imageSrc  = $options['imageSrc'];
            $isNewImage = $options['isNewImage'];

            if ($galleryID)
                $this->_addSubmitSaveClose = true;

            parent::__construct($options);

            // show online
            $showOnline = new Zend_Form_Element_Checkbox('G_Online');
            $showOnline->setValue(1);
            $showOnline->setLabel($this->getView()->getCibleText('form_label_showOnline'));
            $showOnline->setDecorators(array(
                    'ViewHelper',
                    array('label', array('placement' => 'append')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
                ));

            $this->addElement($showOnline);

            // gallery position
            $position = new Zend_Form_Element_Text('G_Position');
            $position->setLabel($this->getView()->getCibleText('form_label_position'));
            $position->setRequired(true);
            $position->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
            $position->addValidator('Int', true, array('messages' => array('notInt' => $this->getView()->getCibleText('validation_message_int_field'))));
            $this->addElement($position);


            // default gallery image
            $imageTmp  = new Zend_Form_Element_Hidden('ImageSrc_tmp');
            $imageTmp->setDecorators(array(
                'ViewHelper',
                array(array('row'=>'HtmlTag'),array('tag'=>'dd', 'openOnly'=>true))
            ));
            $this->addElement($imageTmp);

            // hidden specify if new image for the gallery
            $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value'=>$isNewImage));
            $newImage->setDecorators(array(
                'ViewHelper'
            ));
            $this->addElement($newImage);

            $imageOrg  = new Zend_Form_Element_Hidden('ImageSrc_original');
            $imageOrg->setDecorators(array(
                'ViewHelper',
                array(array('row'=>'HtmlTag'),array('tag'=>'dd', 'closeOnly'=>true))
            ));
            $this->addElement($imageOrg);

            $imageView = new Zend_Form_Element_Image('ImageSrc_preview', array('onclick'=>'return false;'));
            $imageView->setImage($imageSrc);
            $imageView->removeDecorator('label');
            $this->addElement($imageView);

            $imagePicker = new Cible_Form_Element_ImageGalleryPicker('ImageSrc', array('associatedElement' => 'imageView',
                                                                                    'galleryID' => $galleryID));
            $imagePicker->removeDecorator('Label');
            $this->addElement($imagePicker);


             $blockCategory = new Zend_Form_Element_Select('G_CategoryID');
            $blockCategory->setLabel('Catégorie de cette galerie')
            ->setAttrib('class','largeSelect')
            ->setOrder(5);

            $categories = new Categories();
            $select = $categories->select()->setIntegrityCheck(false)
                                 ->from('Categories')
                                 ->join('CategoriesIndex', 'C_ID = CI_CategoryID')
                                 ->where('C_ModuleID = ?', 9)
                                 ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
                                 ->order('CI_Title');
            $categoriesArray = $categories->fetchAll($select);

            foreach ($categoriesArray as $category){
                $blockCategory->addMultiOption($category['C_ID'],$category['CI_Title']);
            }

            $gall = new Categories();
            $select = $gall->select()->setIntegrityCheck(false)
                         ->from('Galleries')
                         ->where('G_ID = ?', $galleryID);

            $gallArray = $gall->fetchAll($select);

            $blockCategory->setValue($gallArray[0]['G_CategoryID']);
            $this->addElement($blockCategory);



            // Title
            $title = new Zend_Form_Element_Text('GI_Title');
            $title->setLabel($this->getView()->getCibleText('form_label_title'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class','stdTextInput');
            $label = $title->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->addElement($title);

//            $description = new Zend_Form_Element_Text('GI_Description');
//            $description->setLabel($this->getView()->getCibleText('form_label_description'))
//                    ->setRequired(false);
            // Description
            $description = new Cible_Form_Element_Editor('GI_Description', array('mode' => Cible_Form_Element_Editor::ADVANCED));

            $description->setLabel($this->getView()->getCibleText('form_label_description'))
//                ->setRequired(true)
//                ->addFilter('StripTags')
//                ->addFilter('StringTrim')
//                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class','mediumEditor');
            $label = $description->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
            $this->addElement($description);

      }
    }
?>
