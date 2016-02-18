<?php


class FormFilterDate extends Cible_Form_Multilingual{


    public function __construct($options = null) {

        $this->_disabledDefaultActions = true;
        $this->_disabledLangSwitcher = true;
        $datesList = $options['datesList'];
        unset($options['datesList']);
        parent::__construct($options);

        $this->setAttrib("id", "filterByDate");

        $listeSelect = new Zend_Form_Element_Select('listeFiltre');
        $listeSelect->addMultiOptions($datesList);
        $listeSelect->setValue($options['filtre']);

        $this->addElement($listeSelect);

        $this->addDisplayGroup(array('listeFiltre'), 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->setAttrib('class', 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->removeDecorator('DtDdWrapper');

    }

}