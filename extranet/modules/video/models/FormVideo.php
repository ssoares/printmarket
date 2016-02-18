<?php


class FormVideo extends Cible_Form_Multilingual
{

    public function __construct($options = null)
    {

        //exit;
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);

        $imageSrc = $options['imageSrc'];

        $pathTmp = "../../../../../data/images/tmp";

        $VI_ID = new Zend_Form_Element_Hidden('VI_ID');
        $VI_ID->removeDecorator('Label');
        $this->addElement($VI_ID);

        $newPoster = new Zend_Form_Element_Hidden('isNewPoster', array('value' => $options['isNewPoster']));
        $newPoster->removeDecorator('Label');
        $this->addElement($newPoster);

        $newMP4 = new Zend_Form_Element_Hidden('isNewMP4', array('value' => $options['isNewMP4']));
        $newMP4->removeDecorator('Label');
        $this->addElement($newMP4);

        $newWEBM = new Zend_Form_Element_Hidden('isNewWEBM', array('value' => $options['isNewWEBM']));
        $newWEBM->removeDecorator('Label');
        $this->addElement($newWEBM);

        $newOGG = new Zend_Form_Element_Hidden('isNewOGG', array('value' => $options['isNewOGG']));
        $newOGG->removeDecorator('Label');
        $this->addElement($newOGG);

        $V_Autoplay = new Zend_Form_Element_Checkbox('V_Autoplay');
        $V_Autoplay->setLabel($this->getView()->getCibleText('form_label_video_autoplay'));
        $V_Autoplay->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        $this->addElement($V_Autoplay);

        // Title
        $V_Alias = new Zend_Form_Element_Text('V_Alias');
        $V_Alias->setLabel($this->getView()->getCibleText('form_label_video_alias'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput');
        $label = $V_Alias->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);
        $this->addElement($V_Alias);

        // Title
        $VI_Name = new Zend_Form_Element_Text('VI_Name');
        $VI_Name->setLabel($this->getView()->getCibleText('form_label_video_name'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput');
        $label = $VI_Name->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);
        $this->addElement($VI_Name);

        // Title
        $V_Width = new Zend_Form_Element_Text('V_Width');
        $V_Width->setLabel($this->getView()->getCibleText('form_label_video_width'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput');
        $label = $V_Width->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);
        $this->addElement($V_Width);

        // Title
        $V_Height = new Zend_Form_Element_Text('V_Height');
        $V_Height->setLabel($this->getView()->getCibleText('form_label_video_height'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class', 'stdTextInput');
        $label = $V_Height->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);
        $this->addElement($V_Height);

        $VI_Poster_tmp = new Zend_Form_Element_Hidden('VI_Poster_tmp');
        $VI_Poster_tmp->removeDecorator('Label');
        $this->addElement($VI_Poster_tmp);

        $imageOrg = new Zend_Form_Element_Hidden('VI_Poster_original');
        $imageOrg->removeDecorator('Label');
        $this->addElement($imageOrg);

        $imageOrg = new Zend_Form_Element_Hidden('VI_Poster_original');
        $imageOrg->removeDecorator('Label');
        $this->addElement($imageOrg);


        $VI_WEBM_tmp = new Zend_Form_Element_Hidden('VI_WEBM_tmp');
        $VI_WEBM_tmp->removeDecorator('Label');
        $this->addElement($VI_WEBM_tmp);

        $VI_OGG_tmp = new Zend_Form_Element_Hidden('VI_OGG_tmp');
        $VI_OGG_tmp->removeDecorator('Label');
        $this->addElement($VI_OGG_tmp);

        // Image for the product line
        $imageView = new Zend_Form_Element_Image('VI_Poster_preview',
                array('onclick' => 'return false;')
        );
        $imageView->setAttrib('width', $options['V_Width']);
        $imageView->setAttrib('height', $options['V_Height']);
        $imageView->setImage($imageSrc);
        $this->addElement($imageView);

        $imagePickerPoster = new Cible_Form_Element_ImagePicker(
                'VI_Poster',
                array(
                    'onchange' => "document.getElementById('imageView').src = document.getElementById('VI_Poster').value",
                    'associatedElement' => 'VI_Poster_preview',
                    'pathTmp' => $pathTmp,
                    'contentID' => $options['VI_ID']
            ));
        $imagePickerPoster->removeDecorator('Label');

        $this->addElement($imagePickerPoster);

        $VI_MP4_tmpt = new Zend_Form_Element_Hidden('VI_MP4', array('value' => $options['VI_MP4']));
        $VI_MP4_tmpt->removeDecorator('Label');
        $this->addElement($VI_MP4_tmpt);

        $VI_MP4_Name_tmp = new Zend_Form_Element_Hidden('VI_MP4_Name');
        $VI_MP4_Name_tmp->removeDecorator('Label');
        $this->addElement($VI_MP4_Name_tmp);

        // Technical specs of the product.
        $video1 = new Cible_Form_Element_FileManager(
                'VI_MP4',
                array(
                    //  'associatedElement' => 'productFormBotPub',
                    'displayElement' => 'video1',
                    'pathTmp' => "/videos",
                    'contentID' => $this->_dataId,
                    'setInit' => true
                )
        );

        $video1->setLabel(
                $this->getView()->getCibleText('form_label_video_VI_MP4'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'form_title_inline',
                            'id' => 'title')
                    ),
                )
            )
            ->setAttrib('class', 'stdTextInput');

        $label = $video1->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($video1);

        $VI_WEBM_tmpt = new Zend_Form_Element_Hidden('VI_WEBM', array('value' => $options['VI_WEBM']));
        $VI_WEBM_tmpt->removeDecorator('Label');
        $this->addElement($VI_WEBM_tmpt);

        $VI_WEBM_Name_tmp = new Zend_Form_Element_Hidden('VI_WEBM_Name');
        $VI_WEBM_Name_tmp->removeDecorator('Label');
        $this->addElement($VI_WEBM_Name_tmp);

        // Technical specs of the product.
        $video2 = new Cible_Form_Element_FileManager(
                'VI_WEBM',
                array(
                    //  'associatedElement' => 'productFormBotPub',
                    'displayElement' => 'video2',
                    'pathTmp' => "/videos",
                    'contentID' => $this->_dataId,
                    'setInit' => true
                )
        );
        $video2->setLabel(
                $this->getView()->getCibleText('form_label_video_VI_WEBM'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'form_title_inline',
                            'id' => 'title')
                    ),
                )
            )
            ->setAttrib('class', 'stdTextInput');

        $label = $video2->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($video2);

        $VI_OGG_tmpt = new Zend_Form_Element_Hidden('VI_OGG', array('value' => $options['VI_OGG']));
        $VI_OGG_tmpt->removeDecorator('Label');
        $this->addElement($VI_OGG_tmpt);

        $VI_OGG_Name_tmp = new Zend_Form_Element_Hidden('VI_OGG_Name');
        $VI_OGG_Name_tmp->removeDecorator('Label');
        $this->addElement($VI_OGG_Name_tmp);

        // Technical specs of the product.
        $video3 = new Cible_Form_Element_FileManager(
                'VI_OGG',
                array(
                    //  'associatedElement' => 'productFormBotPub',
                    'displayElement' => 'video3',
                    'pathTmp' => "/videos",
                    'contentID' => $this->_dataId,
                    'setInit' => true
                )
        );
        $video3->setLabel(
                $this->getView()->getCibleText('form_label_video_VI_OGG'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'class' => 'form_title_inline',
                            'id' => 'title')
                    ),
                )
            )
            ->setAttrib('class', 'stdTextInput');

        $label = $video3->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($video3);

        $VI_Description = new Cible_Form_Element_Editor('VI_Description', array('mode' => Cible_Form_Element_Editor::ADVANCED, 'value' => $options['VI_Description']));
        $VI_Description->setLabel($this->getView()->getCibleText('form_label_video_description'));
        $label = $VI_Description->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($VI_Description);
    }

}