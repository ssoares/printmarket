<?php

/**
 * Module Banners
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banners
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBannerFeatured.php 153 2011-07-04 20:41:52Z ssoares $
 */

/**
 * Form to add a new collection.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBannerFeatured.php 153 2011-07-04 20:41:52Z ssoares $id
 */
class FormBannerFeatured extends Cible_Form_Multilingual {

    protected $_numberImageFeature;
    protected $_isNewImage;
    protected $_imageSrc;
    protected $_dataId;
    protected $_moduleName;
    protected $_filePath;
    protected $_listVideo;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);

        $this->setParameters($options);
        if ($this->_dataId == ''){
            $pathTmp = $this->_imagesFolder . "/featured/tmp";
        }else{
            $pathTmp = $this->_imagesFolder . "/featured/$this->_dataId/tmp";
        }
        // Name of the banner
        $name = new Zend_Form_Element_Text('BF_Name');
        $name->setLabel(
                        $this->getView()->getCibleText('form_label_name'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(
                        'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                    )
                        )
                )
                ->setDecorators(
                        array(
                            'ViewHelper',
                            'Errors',
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

        $this->addElement($name);

        for($x=1; $x<=$this->_numberImageFeature;$x++){

            $isNewImage = 'isNewImage' . $x;

            $newImage = new Zend_Form_Element_Hidden($isNewImage, array('value' => $this->_isNewImage[$x]));
            $newImage->removeDecorator('Label');

            $this->addElement($newImage);
            // Image to load
            $IF_Img_tmp = 'IF_Img' . $x . '_tmp';
            $IF_Img_original = 'IF_Img' . $x . '_original';
            $IF_Img_preview = 'IF_Img' . $x . '_preview';
            $IF_Img = 'IF_Img' . $x;

            $imageTmp = new Zend_Form_Element_Hidden($IF_Img_tmp);
            $imageTmp->removeDecorator('Label');
            $this->addElement($imageTmp);

            $imageOrg = new Zend_Form_Element_Hidden($IF_Img_original);
            $imageOrg->removeDecorator('Label');
            $this->addElement($imageOrg);

            $imageView= new Zend_Form_Element_Image($IF_Img_preview,
                    array('onclick' => 'return false;')
            );


            $imageView->setImage($this->_imageSrc[$x])->removeDecorator('Label')
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array(
                                    array('row' => 'HtmlTag'),
                                    array(
                                        'tag' => 'dd',
                                        'class' => 'alignCenter')
                                ),
                            )
            );

            $this->addElement($imageView);
            //echo $IF_Img . $pathTmp . "<br />";
            $imagePicker = new Cible_Form_Element_ImagePicker($IF_Img, array(
                'onchange' => "document.getElementById('" . $IF_Img . "').src = document.getElementById('" . $IF_Img . "').value",
                'associatedElement' => $IF_Img_preview,
                'pathTmp' => $pathTmp,
                'contentID' => $this->_dataId
            ));
            $imagePicker->setLabel('')
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array(
                                    array('row' => 'HtmlTag'),
                                    array(
                                        'tag' => 'dd',
                                        'class' => 'alignCenter')
                                ),
                            )
            );

            $imagePicker->removeDecorator('Label');
            $this->addElement($imagePicker);

            // label for the image
            $IFI_Label = 'IFI_Label' . $x;
            $labelX = new Zend_Form_Element_Text($IFI_Label);
            $labelX->setLabel(
                            $this->getView()->getCibleText('form_label_label'))
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array('label', array('placement' => 'prepend')),
                                array(
                                    array('row' => 'HtmlTag'),
                                    array(
                                        'tag' => 'dd',
                                        'class' => 'form_title_inline')
                                ),
                            )
                    )
                    ->setAttrib('class', 'stdTextInput');

            $label = $labelX->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($labelX);

            $IFI_Url = 'IFI_Url' . $x;

            $classUrl = 'urlImg';
            if (!$options['hasVideo'])
                $classUrl = '';

            $urlX = new Zend_Form_Element_Text($IFI_Url);
            $urlX->setLabel(
                            $this->getView()->getCibleText('form_label_url'))
                    ->removeDecorator('DtDdWrapper')
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array('label', array('placement' => 'prepend')),
                                array(
                                    array('row' => 'HtmlTag'),
                                    array(
                                        'tag' => 'dd',
                                        'class' => $classUrl)
                                ),
                            )
                    )
                    ->setAttrib('class', 'stdTextInput');

            $label = $urlX->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($urlX);


            $IFI_UrlVideo = 'IFI_UrlVideo' . $x;
            $optionUrlVideo = new Zend_Form_Element_Radio($IFI_UrlVideo);
              $optionUrlVideo->setRequired(false)
                ->removeDecorator('DtDdWrapper')
                ->addMultiOption('0', $this->getView()->getCibleText('extranet_form_label_url'),'number1')
                ->addMultiOption('1', $this->getView()->getCibleText('extranet_form_label_video'),'number2')
                ->setSeparator('')
                ->setDecorators(
                    array(
                        'ViewHelper',
                        array('label', array('placement' => 'prepend')),
                        array(
                            array('row' => 'HtmlTag'),
                            array(
                                'tag' => 'dd',
                                'class' => 'radioMediaSrc')
                        ),
                    )
                )
              ;
            if ($options['hasVideo']){
                $this->addElement($optionUrlVideo);
            }

            $IFI_Text1 = 'IFI_TextA' . $x;
            $IFI_Text2 = 'IFI_TextB' . $x;

            $IFI_Text1X = new Zend_Form_Element_Text($IFI_Text1);
            $IFI_Text1X->setLabel(
                            $this->getView()->getCibleText('form_label_text_1'))
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array('label', array('placement' => 'prepend')),
                                array(
                                    array('row' => 'HtmlTag'),
                                    array(
                                        'tag' => 'dd',
                                        'class' => 'form_title_inline')
                                ),
                            )
                    )
                    ->setAttrib('class', 'stdTextInput');

            $label = $IFI_Text1X->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($IFI_Text1X);

            $IFI_Text2X = new Zend_Form_Element_Text($IFI_Text2);
            $IFI_Text2X->setLabel(
                $this->getView()->getCibleText('form_label_text_2'))
                ->setDecorators(
                    array(
                        'ViewHelper',
                        array('label', array('placement' => 'prepend')),
                        array(
                            array('row' => 'HtmlTag'),
                            array(
                                'tag' => 'dd',
                                'class' => 'form_title_inline')
                        ),
                    )
                )
                ->setAttrib('class', 'stdTextInput');

            $label = $IFI_Text2X->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

//            $this->addElement($IFI_Text2X);


            $IF_Style = 'IF_Style' . $x;
            $style = new Zend_Form_Element_Select($IF_Style);
            $style->setLabel($this->getView()->getCibleText('form_style_label'))
                ->setAttrib('class', 'largeSelect')
                ;

            $listStyles = array(
                'style1' => 'Style 1',
                'style2' => 'Style 2',
                'style3' => 'Style 3',
            );
            $style->addMultiOption('', $this->getView()->getCibleText('form_select_default_label'));
            $style->addMultiOptions($listStyles);

//            $this->addElement($style);

            $IF_Effect = 'IF_Effect' . $x;
            $effect = new Zend_Form_Element_Select($IF_Effect);
            $effect->setLabel($this->getView()->getCibleText('form_effect_label'))
                ->setAttrib('class', 'largeSelect')
                ;

            $listEffect = array(
                'sec0' => 'Style over 0 secondes',
                'sec1' => 'Style over 1 secondes',
                'secUp1' => 'Style up 1 secondes',
            );
            $effect->addMultiOption('', $this->getView()->getCibleText('form_select_default_label'));
            $effect->addMultiOptions($listEffect);

//            $this->addElement($effect);

            $imageDisplay = 'imageDisplay' . $x;
            $this->addDisplayGroup(
                array(
                    $isNewImage,
                    $IF_Img_tmp,
                    $IF_Img_original,
                    $IF_Img_preview,
                    $IF_Img,
                    $IFI_Label,
                    $IFI_Url,
                    $IFI_UrlVideo,
                    $IFI_Text1,
                    $IFI_Text2,
                    $IF_Style,
                    $IF_Effect
                    ),
                $imageDisplay
                );

            $this->getDisplayGroup($imageDisplay)
                ->setLegend('Image ' .$x)
                    ->setAttrib('class', 'imageGroup first')
                    ->removeDecorator('DtDdWrapper');
        }
    }

    /**
     * Set all the parameters for the form.
     *
     * @param array $params Options from the controller to build the form.
     *
     * @return void
     */
    public function setParameters($params = array())
    {
        foreach ($params as $property => $value)
        {
            if ($property == 'BlockID')
                $property = 'blockID';

            $propertyName = '_' . $property;

            if (property_exists($this, $propertyName))
            {
                $this->$propertyName = $value;
            }
        }
    }
}


