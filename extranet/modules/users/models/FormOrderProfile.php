<?php

/**
 * Module Users
 * Data management for the registered users.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormOrderProfile.php 1367 2013-12-27 04:19:31Z ssoares $id
 */

/**
 * Form to manage specific data.
 * Fields will change for each project.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormOrderProfile.php 1367 2013-12-27 04:19:31Z ssoares $id
 */
class FormOrderProfile extends Cible_Form
{

    public function __construct($options = null)
    {
//        $this->_disabledDefaultActions = true;
//        if (isset($options['object']))
//            $this->_object = $options['object'];

        unset($options['object']);
        parent::__construct($options);
        $this->setAttrib('id', 'orders');
        //  Status of the customer to access to the cart and order process
        $status = new Zend_Form_Element_Select('MP_Status');
        $status->setLabel($this->getView()->getCibleText('form_label_account_status'));
        $statusList = array(
            '-1' => 'Désactivé',
            '0' => 'Email non validé',
            '1' => 'À valider',
            '2' => 'Activé'
        );
        $status->addMultiOptions($statusList);
        $this->addElement($status);

        // Company name
        $company = new Zend_Form_Element_Text('MP_CompanyName');
        $company->setLabel($this->getView()->getCibleText('form_label_company'))
            ->setRequired(false)
//                ->setOrder()
            ->setAttribs(array('class' => 'stdTextInput'));

        $this->addElement($company);
        // new password
        $password = new Zend_Form_Element_Password('MP_Password');
        $password->setLabel($this->getView()->getCibleText('form_label_newPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');


        // password confirmation
        $passwordConfirmation = new Zend_Form_Element_Password('passwordConfirmation');
        $passwordConfirmation->setLabel($this->getView()->getCibleText('form_label_confirmNewPwd'))
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->setAttrib('class', 'stdTextInput')
            ->setAttrib('autocomplete', 'off');

        if (Zend_Registry::get('pwdOn'))
        {
            $this->addElement($password);
            $this->addElement($passwordConfirmation);
        }

        // Billing address
        $addressFacturationSub = new Cible_Form_SubForm();
        $addressFacturationSub->setName('addressFact')
            ->removeDecorator('DtDdWrapper');
        $addressFacturationSub->setLegend($this->getView()->getCibleText('form_account_subform_addBilling_legend'));
        $addressFacturationSub->setAttrib('class', 'addresseBillingClass subFormClass');
        $billingAddr = new Cible_View_Helper_FormAddress($addressFacturationSub);
        $billingAddr->enableFields(
                array(
                    'firstAddress',
                    'secondAddress',
                    'state',
                    'cityTxt',
                    'zipCode',
                    'country',
                    'firstTel',
                    'seconfTel',
                    'fax'
                    )
                );

        $billingAddr->formAddress();

        $addrBill = new Zend_Form_Element_Hidden('MP_BillingAddrId');
        $addrBill->removeDecorator('label');
        $addressFacturationSub->addElement($addrBill);

        $this->addSubForm($addressFacturationSub, 'addressFact');

        /* delivery address */
        $addrShip = new Zend_Form_Element_Hidden('MP_ShippingAddrId');
        $addrShip->removeDecorator('label');

        $addressShippingSub = new Cible_Form_SubForm();
        $addressShippingSub->setName('addressShipping')
            ->removeDecorator('DtDdWrapper');;
        $addressShippingSub->setLegend($this->getView()->getCibleText('form_account_subform_addShipping_legend'));
        $addressShippingSub->setAttrib('class', 'addresseShippingClass subFormClass');

        $shipAddr = new Cible_View_Helper_FormAddress($addressShippingSub);
        $shipAddr->duplicateAddress($addressShippingSub);
        $shipAddr->enableFields(
            array(
                'firstAddress',
                'secondAddress',
                'state',
                'cityTxt',
                'zipCode',
                'country',
                'firstTel',
                'seconfTel',
                'fax'
                )
            );

        $shipAddr->formAddress();

        $addressShippingSub->addElement($addrShip);
        $this->addSubForm($addressShippingSub,'addressShipping');

        $this->addSubForm($addressShippingSub, 'addressShipping');

    }

    public function isValid($data)
    {
        $passwordConfirmation = $this->getElement('passwordConfirmation');
        if (Zend_Registry::get('pwdOn') && !empty($data['GP_Password']))
        {
            $passwordConfirmation->setRequired(true)
                ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('error_message_password_isEmpty'))));

            $Identical = new Zend_Validate_Identical($data['GP_Password']);
            $Identical->setMessages(array('notSame' => $this->getView()->getCibleText('error_message_password_notSame')));
            $passwordConfirmation->addValidator($Identical);
        }

        return parent::isValid($data);
    }
}