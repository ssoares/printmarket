<?php
    
class FormGroup extends Cible_Form
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

        // Name of the group of banner
        $valueName = new Zend_Form_Element_Text('BG_Name');
        $valueName->setLabel(
                    $this->getView()->getCibleText('form_banner_name_label'))
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

        $label = $valueName->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($valueName);
    }
}