<?php
  class FormProfileVerification extends Cible_Form{
      public function __construct($options = null)
      {
          $this->_disabledDefaultActions = true;
          
          parent::__construct($options);
          
          $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag'=>'table', 'class'=> 'profile-verification')),
            'Form'
          ));
          
          $email = new Zend_Form_Element_Text('email_verification');
          $email->setLabel($this->getView()->getCibleText('form_label_email'))
                ->setRequired(true)
                ->setAttrib('class','email-textbox')
                ->addValidator('NotEmpty',true,array('messages'=> Cible_Translation::getCibleText('error_field_required')))
                ->addValidator('EmailAddress', true, array('messages'=> Cible_Translation::getCibleText('error_invalid_email')))
                ->addFilter('StringTrim')
                ->addFilter('StringToLower')
                ->setDecorators(array(
                    'ViewHelper',
                    'Description',
                    'Errors',
                    'Label',
                    array(array('data'=>'HtmlTag'), array('tag' => 'td')),
                    array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'openOnly'=>true))
                ));
                
          
          $this->addElement($email);
          
          $continue = new Zend_Form_Element_Submit('continue');
          $continue->setLabel($this->getView()->getCibleText('button_continue'))
                    ->setAttrib('class','blueish-button')
                    ->setAttrib('onmouseover',"this.className='blueish-button-over'")
                    ->setAttrib('onmouseout',"this.className='blueish-button'")
                    ->setDecorators(array(
                        'ViewHelper',
                        'Description',
                        'Errors',
                        array(array('data'=>'HtmlTag'), array('tag' => 'td', 'class' => 'continue')),
                        array(array('row'=>'HtmlTag'),array('tag'=>'tr', 'closeOnly'=>true))
                    ));
          
          $this->addElement($continue);
      }
  }