<?php
    
class FormParameters extends Cible_Form
{
    public function __construct($options = null)
    {
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

        // Value of the shipping free
        $valueShippingFees = new Zend_Form_Element_Text('CP_ShippingFees');
        $valueShippingFees->setLabel(
                    $this->getView()->getCibleText('form_parameters_montant_label'))
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

        $label = $valueShippingFees->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);


        // Value of the shipping fees limit
        $valueShippingFeesLimit = new Zend_Form_Element_Text('CP_ShippingFeesLimit');
        $valueShippingFeesLimit->setLabel(
                    $this->getView()->getCibleText('form_parameters_limit_transport_label'))
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

        $label = $valueShippingFeesLimit->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        // Value of the COD
        $valueMontantFraisCOD = new Zend_Form_Element_Text('CP_MontantFraisCOD');
        $valueMontantFraisCOD->setLabel(
                    $this->getView()->getCibleText('form_parameters_COD_label'))
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

            $label = $valueMontantFraisCOD->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);


        // Email for the command
        $valueAdminOrdersEmail = new Zend_Form_Element_Text('CP_AdminOrdersEmail');
        $valueAdminOrdersEmail->setLabel(
                    $this->getView()->getCibleText('form_parameters_email_label'))
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

            $label = $valueAdminOrdersEmail->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);


        // Id of the free Item
        // List of products
        $oItems = new ItemsObject();
        $listItem = $oItems->itemsCollection(Zend_Registry::get('currentEditLanguage'));

        $itemID = new Zend_Form_Element_Select('CP_FreeItemID');
        $itemID->setLabel(
                    $this->getView()->getCibleText('form_parameters_free_item_label'))
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

        $itemID->addMultiOption('', $this->getView()->getCibleText('form_select_default_label'));
        $itemID->addMultiOptions($listItem);

       

        // Value of the minimum amount to have a free item
        $valueFreeMiniAmount = new Zend_Form_Element_Text('CP_FreeMiniAmount');
        $valueFreeMiniAmount->setLabel(
                    $this->getView()->getCibleText('form_parameters_free_item_minimum_label'))
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

            $label = $valueFreeMiniAmount->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);


        // Value of the minimum amount to have a free item
        $valueBonusPointDollar = new Zend_Form_Element_Text('CP_BonusPointDollar');
        $valueBonusPointDollar->setLabel(
                    $this->getView()->getCibleText('form_parameters_bonus_label'))
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

            $label = $valueBonusPointDollar->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

           // Value of the canadian tax TPS
        $valueTauxTaxeFed = new Zend_Form_Element_Text('CP_TauxTaxeFed');
        $valueTauxTaxeFed->setLabel(
                    $this->getView()->getCibleText('form_parameters_taxe_label'))
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

            $label = $valueTauxTaxeFed->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);


        $this->addElement($valueShippingFees);
        $this->addElement($valueShippingFeesLimit);
        $this->addElement($valueMontantFraisCOD);
        $this->addElement($valueAdminOrdersEmail);
        $this->addElement($itemID);
        $this->addElement($valueFreeMiniAmount);
        $this->addElement($valueBonusPointDollar);
        $this->addElement($valueTauxTaxeFed);

        

    }
}