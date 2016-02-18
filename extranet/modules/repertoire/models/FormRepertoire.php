<?php
class FormRepertoire extends Cible_Form_GenerateForm
{
    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        if (!empty($options['isXmlHttpRequest']))
            $this->_disabledDefaultActions = true;

        $this->_disabledLangSwitcher = FALSE;
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }
        parent::__construct($options);

        $addressSub = new Cible_Form_SubForm();
        $addressSub->setName('address')
            ->removeDecorator('DtDdWrapper');
//            $addressSub->setLegend($this->getView()->getCibleText('form_account_subform_addilling_legend'));
        $addressSub->setAttrib('class', 'addresseClass subFormClass');
        $addr = new Cible_View_Helper_FormAddress($addressSub);
        $addr->setProperty('labelCSS', $this->_labelCSS);
        $addr->enableFields(
            array(
                'email' => true,
                'firstTel' => true,
                'firstExt' => false,
                'name' => false,
            )
        );

        $addr->formAddress();

        $addressSub->addElement($this->getElement('RD_AddressId'));
        $this->addSubForm($addressSub, 'address');
        $this->setAttrib('id', 'repertoire');
    }

    public function isValid($data)
    {
        if (!isset($data['address']['A_StateId']))
                    $this->getSubForm('address')->removeElement('A_StateId');
        return parent::isValid($data);
    }
}