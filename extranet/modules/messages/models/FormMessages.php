<?php
/**
 * Cible Solutions - Vêtements SP
 * Featured Products management.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_FeaturedProducts
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: FormMessages.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Form for featured products.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_FeaturedProducts
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class FormMessages extends Cible_Form_Multilingual
{
    public function __construct($options = null)
    {
        parent::__construct($options);
        $baseDir = $options['baseDir'];
//        $pageID = $options['pageID'];

        $imageSrc   = $options['imageSrc'];
        $dataId     = $options['dataId'];
        $isNewImage = $options['isNewImage'];
        $moduleName = $options['moduleName'];

        if($dataId == '')
            $pathTmp = $this->_imagesFolder . "/tmp";
        else
            $pathTmp = $this->_imagesFolder . "/" . $dataId . "/tmp";

        // hidden specify if new image for the news
        $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value'=>$isNewImage));
        $newImage->removeDecorator('Label');
//        $this->addElement($newImage);

        // Title
        $title = new Zend_Form_Element_Text('MAI_Title');
        $title->setLabel(
                $this->getView()->getCibleText('form_label_MAI_Title'))
            ->addFilter('StringTrim')
            ->setRequired(true)
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
            ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'title')
                        ),
                    )
                )
            ->setAttrib('class','stdTextInput');

        $label = $title->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($title);

        // Text
        $content= new Cible_Form_Element_Editor('MAI_Text', array('mode'=>Cible_Form_Element_Editor::ADVANCED));
            $content->setLabel($this->_view->getCibleText('form_label_MAI_Text'))
            ->setRequired(true)
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
            ->setAttrib('class','mediumEditor');

        $label = $content->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($content);
        // Set Online
        $online = new Zend_Form_Element_Checkbox('MA_Online');
            $online->setLabel($this->_view->getCibleText('form_label_MA_Online'));
            $online->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

            $this->addElement($online);
        // Timeout value
        $timeout = new Zend_Form_Element_Text('MA_Timeout');
        $timeout->setLabel(
                $this->getView()->getCibleText('form_label_MA_Timeout'))
            ->addFilter('StringTrim')
            ->setRequired(true)
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
            ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'timeout')
                        ),
                    )
                )
            ->setValue(24)
            ->setAttrib('class','smallTextInput');

        $this->addElement($timeout);

        // Image for the product line
//        $imageTmp  = new Zend_Form_Element_Hidden('ImageSrc_tmp');
//        $imageTmp->removeDecorator('Label');
//        $this->addElement($imageTmp);
//
//        $imageOrg  = new Zend_Form_Element_Hidden('ImageSrc_original');
//        $imageOrg->removeDecorator('Label');
//        $this->addElement($imageOrg);
//
//        $imageView = new Zend_Form_Element_Image(
//                'ImageSrc_preview',
//                array('onclick'=>'return false;')
//            );
//        $imageView->setImage($imageSrc);
//        $this->addElement($imageView);
//
//        $imagePicker = new Cible_Form_Element_ImagePicker(
//                'ImageSrc',
//                array(
//                    'onchange' => "document.getElementById('imageView').src = document.getElementById('ImageSrc').value",
//                                                                                'associatedElement' => 'ImageSrc_preview',
//                                                                                'pathTmp'=>$pathTmp,
//                                                                                'contentID'=>$dataId
//
//                                                                    ));
//        $imagePicker->removeDecorator('Label');
//        $this->addElement($imagePicker);

    }
}
?>
