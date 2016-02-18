<?php
class FormCrop extends Cible_Form
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = false;
        parent::__construct($options);

        $imageSrcTmp   = $options['imageSrc'];

        $pathTmp = $options['pathTmp'];

        $imageSrc = "";
        if($imageSrcTmp!=""){
            $imageSrc = $options['pathZend'] . $imageSrcTmp;
        }

        $x1  = new Zend_Form_Element_Hidden('x1');
        $x1->setLabel('x1');
        $this->addElement($x1);

        $x2  = new Zend_Form_Element_Hidden('x2');
        $x2->setLabel('x2');
        $this->addElement($x2);

        $y1  = new Zend_Form_Element_Hidden('y1');
        $y1->setLabel('y1');
        $this->addElement($y1);

        $y2  = new Zend_Form_Element_Hidden('y2');
        $y2->setLabel('y2');
        $this->addElement($y2);

        $h  = new Zend_Form_Element_Hidden('h');
        $h->setLabel('h');
        $this->addElement($h);

        $w  = new Zend_Form_Element_Hidden('w');
        $w->setLabel('w');
        $this->addElement($w);

        $ImageSrc  = new Zend_Form_Element_Hidden('ImageSrc');
        $ImageSrc->removeDecorator('Label');
        $this->addElement($ImageSrc);
        $this->setAttrib('id', 'FormCrop');
    }
}