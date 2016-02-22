<?php
/* Pas un vrai script, j'ai mis ce code là pour m'aider à faire des formulaires */
class FormContact extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;
        $this->_disabledLangSwitcher = true;
        if (!empty($options['object'])){
            $this->_object = $options['object'];
            unset($options['object']);
        }
        parent::__construct($options);
        $this->getDisplayGroup('leftGrp')->setLegend(null);
        $this->getDisplayGroup('rightGrp')->setLegend(null);
        // Add captcha to the form
//        $this->getView()->formAddCaptcha($this, array("class" => "col-md-12",
//            'tagCaptcha' => 'div', 'tagRefresh' => 'div'));

        // Submit button
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel($this->getView()->getCibleText('button_submit'))
            ->setAttrib('class', 'btn btn-primary')
            ->setDecorators(array(
                'ViewHelper',
                'Errors',
                array('row' => 'HtmlTag', array('tag' => 'div', 'class' => 'btn-wr col-md-12')))
                );

        $this->addElement($submit);
        $this->addDisplayGroup(array(
            'captcha',
            'submit',
            'refresh_captcha'
            ), 'captcha-submit');
        $this->getDisplayGroup('captcha-submit')
            ->setDecorators(array('FormElements', 'Fieldset'))
            ->setOrder(2000);

        $this->setAttrib('class', 'form')
            ->setDecorators(array('FormElements', 'Form'));
    }
}
