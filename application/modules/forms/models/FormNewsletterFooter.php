<?php

/* Pas un vrai script, j'ai mis ce code là pour m'aider à faire des formulaires */

class FormNewsletterFooter extends Cible_Form {

    public function __construct($options = null) {

        $baseUrl = Zend_Registry::get('web_root');
        $this->_disabledDefaultActions = true;
        parent::__construct($options);
        $baseDir = $this->getView()->baseUrl() . '/';
        $this->getView()->jQuery()->addJavascriptFile("{$this->getView()->baseUrl()}/js/jquery/jquery.maskedinput.min.js");


        $script1 = <<< EOS

            $('.phone_format').mask('(999) 999-9999? x99999');
            $('.postalCode_format').mask('a9a 9a9');
            $('.birthDate_format').mask('9999-99-99');

EOS;
        $this->getView()->headScript()->appendScript($script1);
        $script2 = <<< EOS

            function refreshCaptcha(id){
                $.getJSON('{$this->getView()->baseUrl()}/forms/index/captcha-reload',
                    function(data){
                        $("dd#dd_captcha img").attr({src : data['url']});
                        $("#"+id).attr({value: data['id']});
                });
            }

EOS;

        /*$this->getView()->headScript()->appendScript($script2);
        // name
        $name = new Zend_Form_Element_Text('newsletter-name');
        $name->setLabel($this->getView()->getCibleText('forms_label_newsletter_name'))
                ->setAttrib('placeholder', $this->getView()->getCibleText('forms_label_placeholder_newsletter_name'))
                ->setAttrib('class', 'forms-input-newsletter-name forms-input-text');

        $this->addElement($name);*/
        // email
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');
        
        $email = new Zend_Form_Element_Text('newsletter-email');
        $email->setLabel($this->getView()->getCibleText('form_label_newsletter_email'))
                //->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_field_required'))))
                //->addValidator('EmailAddress', true, array('messages' => Cible_Translation::getCibleText('validation_message_emailAddressInvalid')))
                //->setRequired(true)
                ->setAttrib('placeholder', $this->getView()->getCibleText('forms_label_placeholder_newsletter_email'))
                ->setAttrib('class', 'forms-input-newsletter-email');

        $this->addElement($email);


        // Captcha
        //$this->getView()->formAddCaptcha($this);
        // Submit button
        $submit = new Zend_Form_Element_Submit('newsletter-submit');
        $submit->setLabel($this->getView()->getCibleText('button_submit_newsletter_footer'))
                ->removeDecorator('DtDdWrapper');
        $submit->addDecorators(array(
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-div'))
        ));


        $this->setElementDecorators(array(
                    'ViewHelper',
                    'Errors',
                    array('Description', array('tag' => 'p', 'class' => 'description')),
                    array('HtmlTag', array('class' => 'form-div')),
                    //array('Label', array('class' => 'form-label'))
                ))
                ->setDecorators(
                        array(
                            'FormElements',
                            'Form'
                        )
        );

        $this->addElement($submit);
    }

}

?>
