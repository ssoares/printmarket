<?php

class FormBannerImage extends Cible_Form_Multilingual
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);

        $imageSrc   = $options['imageSrc'];
        $dataId     = $options['dataId'];
        $imgField   = $options['imgField'];
        $isNewImage = $options['isNewImage'];
        $showCrop   = $options['showCrop'];

        if ($dataId == '')
            $pathTmp = $this->_imagesFolder . "/tmp";
        else
            $pathTmp = $this->_imagesFolder . "/$dataId/tmp";

        // hidden specify if new image for the news
        $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value' => $isNewImage));
        $newImage->setDecorators(array('ViewHelper'));
        $this->addElement($newImage);

        // Image for the product line
        $imageTmp  = new Zend_Form_Element_Hidden($imgField . '_tmp');
        $imageTmp->setDecorators(array('ViewHelper'));
        $this->addElement($imageTmp);

        $imageOrg  = new Zend_Form_Element_Hidden($imgField . '_original');
        $imageOrg->setDecorators(array('ViewHelper'));
        $this->addElement($imageOrg);

        // Name of the group of banner
        // Set the texte for the image
        //champs texte avec tiny
        $textDescription = new Cible_Form_Element_Editor('BII_Text', array('mode' => Cible_Form_Element_Editor::ADVANCED));
        $textDescription->setLabel($this->_view->getCibleText('form_banner_image_text_label'))
            //->addFilter('StripTags')
            ->addFilter('StringTrim')
            //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->_view->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','mediumEditor');
        $label = $textDescription->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $textUrl = new Zend_Form_Element_Text('BII_Url');
        $textUrl->setLabel($this->_view->getCibleText('form_banner_image_texturl_label'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class','stdText');
        $label = $textUrl->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $sequence = new Zend_Form_Element_Text('BI_Seq');
        $sequence->setLabel($this->_view->getCibleText('form_banner_image_seq_label'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class','stdSequence');
        $label = $sequence->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $groupImage = new Zend_Form_Element_Select('BI_GroupID');
        $groupImage->setLabel($this->_view->getCibleText('form_banner_image_group'))
        ->setAttrib('class','largeSelect');

        $group = new GroupObject();
        $groupArray = $group->groupCollection();
        foreach ($groupArray as $group1){
            $groupImage->addMultiOption($group1['BG_ID'],$group1['BG_Name']);
        }
        if (!empty($options['groupId']))
            $groupImage->setValue ($options['groupId']);

        $label = $groupImage->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

         // Image for the product line
        $imageView = new Zend_Form_Element_Image(
                $imgField . '_preview',
                array('onclick'=>'return false;')
            );
        $imageView->setImage($imageSrc);

        $imagePicker = new Cible_Form_Element_ImagePicker($imgField, array(
        'onchange' => "document.getElementById('imageView').src = document.getElementById('" . $imgField . "').value",
            'associatedElement' => $imgField . '_preview',
            'pathTmp'=>$pathTmp,
            'contentID'=>$dataId,
            'showCrop' => $showCrop
                    ));
        $imagePicker->removeDecorator('Label');

        $this->addElement($imageView);
        $this->addElement($imagePicker);
        $this->addElement($groupImage);
//        $this->addElement($blockBulle);
        $this->addElement($textDescription);
        $this->addElement($textUrl);
        $this->addElement($sequence);

    }
}