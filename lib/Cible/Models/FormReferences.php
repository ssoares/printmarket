<?php
    
class FormReferences extends Cible_Form_Multilingual
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        parent::__construct($options);

        $labelCSS = Cible_FunctionsGeneral::getLanguageLabelColor($options);

        $imageSrc   = $options['imageSrc'];
        $dataId     = $options['dataId'];
        $imgField   = $options['imgField'];
        $isNewImage = $options['isNewImage'];
        $moduleName = $options['moduleName'];

        if ($dataId == '')
            $pathTmp = "../../../../../data/images/"
                    . $moduleName . "/tmp";
        else
            $pathTmp = "../../../../../data/images/"
                    . $moduleName . "/"
                    . $dataId . "/tmp";

        // hidden specify if new image for the news
//        $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value' => $isNewImage));
//        $newImage->removeDecorator('Label');
//        $this->addElement($newImage);

        // Value of the reference
        $value = new Zend_Form_Element_Text('RI_Value');
        $value->setLabel(
                    $this->getView()->getCibleText('form_reference_value_label'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
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
                                'Errors',
                                array('placement' => 'append')
                            ),
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

        $label = $value->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($value);

        // List of Type
        $type = new Zend_Form_Element_Select('R_TypeRef');
        $type->setLabel(
                    $this->getView()->getCibleText('form_reference_type_label'))
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
                ->setAttrib('class', 'stdSelect')
                ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                'Errors',
                                array('placement' => 'append')
                            ),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag'   => 'dd',
                                    'class' => 'form_title_inline',
                                    'id'    => 'title')
                            ),
                        )
                );

        $oRef = new ReferencesObject();
        $enums = $oRef->getEnum('R_TypeRef');
        $multiOptions = array();
        foreach ($enums as $enum)
        {
            $multiOptions[$enum] = $this->getView()->getCibleText('form_enum_' . $enum);
        }

        $type->addMultiOptions($multiOptions);

        $this->addElement($type);

        // Value of the reference
        $seq = new Zend_Form_Element_Text('R_Seq');
        $seq->setLabel(
                    $this->getView()->getCibleText('form_reference_seq_label'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
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
                                'Errors',
                                array('placement' => 'append')
                            ),
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

//        $label = $seq->getDecorator('Label');
//        $label->setOption('class', $this->_labelCSS);

        $this->addElement($seq);

    }
}