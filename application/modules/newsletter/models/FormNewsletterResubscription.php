<?php
class FormNewsletterResubscription extends Cible_Form
{
    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;
        $baseDir = $this->getView()->baseUrl();
        parent::__construct($options);
        $this->setAttrib('class','noFormStyle');

        // Captcha
        $this->getView()->formAddCaptcha($this);

        $termsAgreement = new Zend_Form_Element_Checkbox('termsAgreement');
        $langId = Zend_Registry::get('languageID');
        $title = $this->_config->site->title->$langId;
        $replace = array('##SITENAME##' => $title);
        $termsAgreement->setLabel($this->getView()->getCibleText('form_label_agree', null, $replace));
        $termsAgreement->setAttrib('class','long-text')->setUncheckedValue(null);
        $termsAgreement->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            'Errors',
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox largedd')),
        ));
        $termsAgreement->setRequired(true);
        $termsAgreement->addValidator('notEmpty', true, array(
          'messages' => array(
            'isEmpty'=>$this->getView()->getClientText('terms_agreement_error_message')
          )
        ));
        $this->addElement($termsAgreement);

        // action button
        $resubscribeButton = new Zend_Form_Element_Submit('resubscribe');
        $resubscribeButton->setLabel($this->getView()->getCibleText('button_submit'))
                        ->setAttrib('id', 'submitSave')
                        ->setAttrib('class','stdButton')
                        ->removeDecorator('Label')
                        ->removeDecorator('DtDdWrapper');


        $this->addElement($resubscribeButton);

        $this->addDisplayGroup(array('resubscribe'),'actions');

        $requiredFields = new Zend_Form_Element_Hidden('RequiredFields');
        $requiredFields->setLabel('<span class="field_required">*</span>'
                . $this->getView()->getCibleText('form_field_required_label')
                . '<br /><br />');

        $requiredFields->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_required_fields ie7hide'))
        ));
        $this->addElement($requiredFields);


        $actions = $this->getDisplayGroup('actions');
        $this->setDisplayGroupDecorators(array(
            'formElements',
            'fieldset',
            array(array('outerHtmlTag' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'dd-submit-button'))
        ));
    }
}