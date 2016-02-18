<?php


class FormFilter extends Cible_Form {


    public function __construct($options = null) {

        $this->_disabledDefaultActions = true;

        parent::__construct($options);

        $this->setAttrib("id", "FiltreRepertoire");

        $filterList = new Zend_Form_Element_Select('filterList');
        $filterList->removeDecorator("Label")
            ->setAttrib('class','filterList');

        $oRef = new GroupeObject();
        $listValues = $oRef->groupsList();
        $filterList->addMultiOptions($listValues);

        if(!empty($options["listId"]))
            $filterList->setValue($options["listId"]);

        $this->addElement($filterList);

        $hiddenfield = new Zend_Form_Element_Hidden("selectedAlpha");
        $hiddenfield->removeDecorator("DtDdWrapper")
                ->removeDecorator("Label");

        $this->addElement($hiddenfield);

        $this->addDisplayGroup(array('filterList'), 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->setAttrib('class', 'formColonneUn');
        $this->getDisplayGroup('formColonneUn')->removeDecorator('DtDdWrapper');

        $oRep = new RepertoireCollection();
        $values = $oRep->_regionGrpSrc($options["listId"]);
        if (count($values) > 1)
        {
            $listeAlpha = new Zend_Form_Element_Select('listeAlpha');
            $listeAlpha->addMultiOptions($values);
            $listeAlpha->setValue($options['alpha']);
            $this->addElement($listeAlpha);
            $this->addDisplayGroup(array('listeAlpha','Alpha'), 'formColonneDeux');
            $this->getDisplayGroup('formColonneDeux')->setAttrib('class', 'formColonneDeux');
            $this->getDisplayGroup('formColonneDeux')->removeDecorator('DtDdWrapper');
        }

    }

}
?>
