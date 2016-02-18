<?php
  class FormNewsletterRecipient extends Cible_Form{
      public function __construct($options = null)
      {
            parent::__construct($options);

            // salutation
            $salutation = new Zend_Form_Element_Select('salutation');
            $salutation->setLabel('Salutation :')
            ->setAttrib('class','largeSelect');

            $categoriesData = $this->getView()->getAllSalutation();
            foreach ($categoriesData as $categoryData){
                $salutation->addMultiOption($categoryData['C_ID'], $categoryData['CI_Title']);
            }

            $this->addElement($salutation);

            // fName
            $fname = new Zend_Form_Element_Text('firstName');
            $fname->setLabel($this->getView()->getCibleText('form_label_fname'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class','stdTextInput');

            $this->addElement($fname);

            // lName
            $lname = new Zend_Form_Element_Text('lastName');
            $lname->setLabel($this->getView()->getCibleText('form_label_lname'))
                ->setRequired(true)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
                ->setAttrib('class','stdTextInput');

            $this->addElement($lname);

            // email
            $regexValidate = new Cible_Validate_Email();
            $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');

            $email = new Zend_Form_Element_Text('email');
            $email->setLabel($this->getView()->getCibleText('form_label_email'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addFilter('StringToLower')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->addValidator($regexValidate)
            ->setAttrib('class','stdTextInput');

            $this->addElement($email);



      }
  }
?>
