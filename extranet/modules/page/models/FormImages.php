<?php
    class FormImages extends Cible_Form
    {
        public function __construct($options = null)
        {
            $this->_addSubmitSaveClose = false;
            parent::__construct($options);

            $imageSrc = $options['imageSrc'];
        $showCrop   = $options['showCrop'];
            $isNewImage = $options['isNewImage'];
            $pathTmp = $this->_imagesFolder . $options['pathTmp'];

        $imageTmp  = new Zend_Form_Element_Hidden('ImageHeader_tmp');
            $imageTmp->removeDecorator('Label');
            $this->addElement($imageTmp);

        $imageTmpO  = new Zend_Form_Element_Hidden('ImageHeaderOriginal_tmp');
            $imageTmpO->removeDecorator('Label');
            $imageTmpO->setValue($imageSrc);
            $this->addElement($imageTmpO);

        $imageOrg  = new Zend_Form_Element_Hidden('ImageHeader_original');
            $imageOrg->removeDecorator('Label');
            $imageOrg->setValue($imageSrc);
            $this->addElement($imageOrg);

        $imageView = new Zend_Form_Element_Image('ImageHeader_preview', array('onclick'=>'return false;'));
            $imageView->setImage($imageSrc)
            ->setAttrib('class','ImageHeader_previewEntete');
            $this->addElement($imageView);

        $imagePicker = new Cible_Form_Element_ImagePicker('ImageHeader',
            array('onchange' => "document.getElementById('imageView').src = document.getElementById('ImageHeader').value",
            'associatedElement' => 'ImageHeader_preview',
            'pathTmp' => $pathTmp,
            'contentID' => '',
            'showCrop' => $showCrop));
            $imagePicker->removeDecorator('Label');
            $this->addElement($imagePicker);

        }
    }
