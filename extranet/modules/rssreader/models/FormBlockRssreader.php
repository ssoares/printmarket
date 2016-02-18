
<?php
    class FormBlockRssreader extends Cible_Form_Block
    {
        protected $_moduleName = 'rssreader';

        public function __construct($options = null)
        {
            $baseDir = $options['baseDir'];
            $pageID = $options['pageID'];

            parent::__construct($options);

            /****************************************/
            // PARAMETERS
            /****************************************/

            // Link of the RSS
            $link = new Zend_Form_Element_Text('Param1');
            $link->setLabel(
                        $this->getView()->getCibleText('form_label_rss_reader_link'))
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
                                'class' => 'form_title_inline',
                                'id'    => 'link')
                            ),
                        )
                    )
            ->setAttrib('class','stdTextInput ');

            $label = $link->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($link);

            // Lien en anglais
            $link_en = new Zend_Form_Element_Text('Param2');
            $link_en->setLabel(
                        $this->getView()->getCibleText('form_label_rss_reader_link_en'))
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
            ->setDecorators(
                    array(
                        'ViewHelper',
                        array('label', array('placement' => 'prepend')),
                        array(
                            array('row' => 'HtmlTag'),
                            array(
                                'tag'   => 'dd',
                                'class' => 'form_title_inline',
                                'id'    => 'link_en')
                            ),
                        )
                    )
            ->setAttrib('class','stdTextInput ');

            $label = $link_en->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);

            $this->addElement($link_en);

             // number of rss link to show in front-end (maxLink)
            $blockRssMax = new Zend_Form_Element_Text('Param3');
            $blockRssMax->setLabel($this->getView()->getCibleText('form_label_rss_reader_link_max'))
                         ->setAttrib('class','smallTextInput');

            $this->addElement($blockRssMax);


            $this->removeDisplayGroup('parameters');

            $this->addDisplayGroup(array('Param3','Param1', 'Param2', 'Param999'),'parameters');
            $parameters = $this->getDisplayGroup('parameters');

        }
    }
?>