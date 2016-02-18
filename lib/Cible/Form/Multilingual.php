<?php

class Cible_Form_Multilingual extends Cible_Form
{

    // Set default lang to ID 1 in case not specified in config
    protected $_currentEditLanguage = 1;
    protected $_currentMode = 'edit';
    protected $_labelCSS;
    protected $_disabledLangSwitcher = false;

    public function __construct($options = null)
    {
        if (!$this->_disabledLangSwitcher)
        {
            $this->_currentEditLanguage = Zend_Registry::get('currentEditLanguage');
            $this->_labelCSS = Cible_FunctionsGeneral::getLanguageLabelColor($options);
        }
        parent::__construct($options);

        if (!$this->_disabledLangSwitcher)
        {
            if (isset($options['addAction']))
                $this->_currentMode = 'add';

            $lang = new Cible_Form_Element_LanguageSelector('langSelector', $this->_params, array('lang' => $this->_currentEditLanguage, 'mode' => $this->_currentMode));
            $lang->setValue($this->_currentEditLanguage)
                ->setDecorators(array(
                    'ViewHelper'//,
                    //array(array('row'=>'HtmlTag'),array('tag'=>'ul', 'class'=>'languages'))
                ));
            if (!$this->_disabledDefaultActions)
                $this->addActionButton($lang);
        }
    }

}