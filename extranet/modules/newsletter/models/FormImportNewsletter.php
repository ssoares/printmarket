<?php
  class FormImportNewsletter extends Cible_Form{
      public function __construct($options = null)
      {
            parent::__construct($options);
            
            $this->setAttrib('enctype', 'multipart/form-data');
            
            $inputFile = new Zend_Form_Element_File('file');
            $inputFile->setLabel( $this->_view->getCibleText('importNewsletterMembers_select_file_label') )
                      ->setRequired(true)
                      ->addValidator('Count', false, 1, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
            
            $this->addElement( $inputFile );
      }
  }
