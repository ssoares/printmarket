<?php

class FormNotifications extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }
        // variable
        parent::__construct($options);

        $titleEditor = new Cible_Form_Element_Editor(
            'ST_ValueTitle', array('mode' => Cible_Form_Element_Editor::ADVANCED));
        $titleEditor->setLabel(
                $this->getView()->getCibleText('form_legend_blockData'))
            ->setAttrib('class','largeEditor')
            ->setOrder(9);

        $label = $titleEditor->getDecorator('label');
        $label->setOption('class',  $this->_labelCSS);

        $this->addElement($titleEditor);

        $textEditor = new Cible_Form_Element_Editor(
            'ST_ValueText', array('mode' => Cible_Form_Element_Editor::ADVANCED));
        $textEditor->setLabel(
                $this->getView()->getCibleText('form_legend_blockData'))
            ->setAttrib('class','largeEditor')
            ->setOrder(7);

        $label = $textEditor->getDecorator('label');
        $label->setOption('class',  $this->_labelCSS);

        $this->addElement($textEditor);

    }
}