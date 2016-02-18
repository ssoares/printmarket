<?php
    
    class FormSectionForm extends Zend_Form
    {
        /**
         *
         * @param array $options Options to build the form
         */
        public function __construct($options = null)
        {
            parent::__construct($options);
          
            // Title
            $title = new Zend_Form_Element_Text('FSI_Title');
            $title->setLabel(
                    $this->getView()->getCibleText('form_section_title_label')
                    . " <span class='field_required'>*</span>")
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
                            array('row' => 'HtmlTag'),
                            array(
                                'tag'   => 'dd',
                                'class' => 'section_title_edit',
                                'id'    => 'title')
                            ),
                        )
                    )
            ->setAttrib('class','stdTextInput');
            
            $this->addElement($title);

            // Show title
            $showTitle = new Zend_Form_Element_Checkbox('FS_ShowTitle');
            $showTitle->setLabel($this->getView()->getCibleText(
                    'form_section_showtitle_label'));
            $showTitle->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(
                    array('row' => 'HtmlTag'),
                    array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

            $this->addElement($showTitle);

            // Repeat section
            $repeat = new Zend_Form_Element_Checkbox('FS_Repeat');
            $repeat->setLabel($this->getView()->getCibleText(
                    'form_label_has_profil'));
            $repeat->setDecorators(array(
                'ViewHelper',
                array('label', array('placement' => 'append')),
                array(
                    array('row' => 'HtmlTag'),
                    array('tag' => 'dd', 'class' => 'label_after_checkbox')),
            ));

            $this->addElement($repeat);

            // Repeat min
            $repeatMin = new Zend_Form_Element_Text('FS_RepeatMin');
            $repeatMin->setLabel(
                    $this->getView()->getCibleText('form_section_repeatMin_label')
                    . " <span class='field_required'>*</span>")
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
            ->setAttrib('class','section_RepeatMin');

            $this->addElement($repeatMin);

            // Repeat max
            $repeatMax = new Zend_Form_Element_Text('FS_RepeatMax');
            $repeatMax->setLabel(
                    $this->getView()->getCibleText('form_section_repeatMax_label')
                    . " <span class='field_required'>*</span>")
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
            ->setAttrib('class','section_RepeatMax');

            $this->addElement($repeatMax);

            // Sequence - hidden input
            $sequence = new Zend_Form_Element_Hidden('FS_Seq');
            $this->addElement($sequence);

            // page break - hiden input
            $pageBreak = new Zend_Form_Element_Hidden('FS_PageBreak');
            $this->addElement($pageBreak);

            // Set the form id
            $this->setAttrib('id', 'section');
        }
    }
?>
