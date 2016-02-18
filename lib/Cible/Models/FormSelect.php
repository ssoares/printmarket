<?php


class FormSelect extends Cible_Form
{

    public function __construct($options = null)
    {

        $this->_disabledDefaultActions = true;

        parent::__construct($options);

        $this->setAttrib("id", "filterByDate");

        $listeSelect = new Zend_Form_Element_Select('listeFiltre');
        $listeSelect->setLabel($this->getView()->getCibleText('form_label_filterDate'));
        $listeSelect->addMultiOptions($options['dates']);
        $listeSelect->setValue($options['filtre']);

        $this->addElement($listeSelect);

        $this->addDisplayGroup(array('listeFiltre'), 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->setAttrib('class', 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->removeDecorator('DtDdWrapper');
    }

}