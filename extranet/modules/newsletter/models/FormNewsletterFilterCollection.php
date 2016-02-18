<?php
    class FormNewsletterFilterCollection extends Cible_Form{
        public function __construct($options = null)
        {
            parent::__construct($options);
          
            /***************************************/
            // Collection subform 
            $collectionForm = new Zend_Form_SubForm();
          
            // Collection name
            $name = new Zend_Form_Element_Text('NFCS_Name');
            $name->setLabel($this->getView()->getCibleText('form_label_collection_name'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class','stdTextInput')
                ->setDecorators(array('ViewHelper', array('label', array('placement' => 'append')), array('Errors', array('placement' => 'append')),
                                                    array(array('row' => 'HtmlTag'),array('tag' => 'dd', 'class' => 'label_before_input'))));


            $collectionForm->addElement($name);
            
            /********************************************************/
            // Add subform to the form
            $this->addSubForm($collectionForm, 'collectionForm'); 
            
            
            /********************************************************/
            // Add button
            $addFilter = new Zend_Form_Element_Button('addFilterSet');
            $addFilter->setLabel($this->getView()->getCibleText('link_add_newsletter_filterSet'));
            $addFilter->setAttrib('class','stdButton');
            $addFilter->setDecorators(array(
                        'ViewHelper',
                        array(array('data'=>'HtmlTag'),array('tag'=>'li')),
                    ));
            $addFilter->setOrder(2);
            
            $this->addActionButton($addFilter);
        }
    }
?>
