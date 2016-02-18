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
 * @version   $Id: FormMembersProfile.php 1242 2013-06-28 21:07:25Z ssoares $id
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
 * @version   $Id: FormMembersProfile.php 1242 2013-06-28 21:07:25Z ssoares $id
 */
class FormCompaniesDonorProfile extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledLangSwitcher = true;
        $this->_object = $options['object'];

        unset($options['object']);
        parent::__construct($options);

        $columnLeft = $this->getDisplayGroup('columnLeft');
//        $columnBottom = $this->getDisplayGroup('columnBottom');
//        $columnBottom->setLegend(null);
        $columnLeft->setLegend(null);

        $totalNbDonation = new Cible_Form_Element_Html('totalNbDonation');
        $columnLeft->addElement($totalNbDonation->removeDecorator('label'));
//        $recurringDonation = new Cible_Form_Element_Html('recurringDonation');
//        $columnLeft->addElement($recurringDonation->removeDecorator('label'));
        $totalDonation = new Cible_Form_Element_Html('totalDonation');
        $columnLeft->addElement($totalDonation->removeDecorator('label'));
        $label = $this->getView()->getCibleText('profile_link_to_donation');
        $value = $this->getView()->link('', $label, array(
            'data-filter-val' => 'don', 'class' => 'shortcutLog'));
        $linkToDonations = new Cible_Form_Element_Html('linkToDonations',
            array('value' => $value));
        $columnLeft->addElement($linkToDonations->removeDecorator('label'));

        // Address
        $addressSub = new Cible_Form_SubForm();
        $addressSub->setName('address')
            ->setAttrib('class', 'columnRight')
            ->setOrder(15)
            ->removeDecorator('DtDdWrapper');
        $addressSub->setLegend($this->getView()->getCibleText('form_label_address'));
        $addr = new Cible_View_Helper_FormAddress($addressSub);
        $addr->enableFields(array(
            'name',
            'firstAddress',
            'secondAddress',
            'state',
            'cityTxt',
            'zipCode',
            'country',
            'firstTel',
            'secondTel',
            'fax'
        ));

        $addr->formAddress();
        $this->addSubForm($addressSub, 'address');
        $addrId = new Zend_Form_Element_Hidden('CIE_AddressId');
        $addrId->setDecorators(array('ViewHelper'));
        $this->addElement($addrId);
    }

    public function populate(array $values)
    {
        $oDad = new DonorsAccountObject();
        $oDad->setProfileId($values['CDO_ProfileId'])
                ->setCieId($values['CDO_CieId']);
        if (isset($values['DO_Mode'])){
            $oDad->setType($values['DO_Mode']);
        }
        $values['accounts'] = $oDad->findData();
        $oExternal = new Cible_ExternalProcess($this->_config, 13);
        $oProcess = $oExternal->getObject();
        $oProcess->setAction('update');
        $oRef = new ReferencesObject();
        $langId = Cible_Controller_Action::getDefaultEditLanguage();
        foreach($values['accounts'] as $key => $account){
            $account['language'] = $this->getView()->language;
            $returnUrl = Zend_Registry::get('absolute_web_root')
            . trim($this->getView()->url(), '/');
            $oProcess->resetUrlPage()->setToken($account['DAD_Token'])
                ->setRequestType($account['DAD_Type'])->setUrlPage()
                ->setRedirectUrl($returnUrl)->setData($account)
                ->setFingerPrint();
            $href = '"'.$oProcess->getExtReqUrl().'"';
            $DADStatus = new Zend_Form_Element_Checkbox($account['DAD_Token']);
            $typeLbl = $oRef->getValueById($account['DAD_Type'], $langId);
            $lbl = $this->getView()->getCibleText('form_label_DAD_Status', null,
                array('replace' => array('##TYPE##' => $typeLbl['value'],
                    '##HREF##' => $href)));
            $DADStatus->setLabel($lbl)
                ->setDecorators(array(
                'ViewHelper',
                "Errors",
                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('class' => 'label_after_checkbox')))
            );
            $DADStatus->setBelongsTo('DAD_Status')
                ->setValue($account['DAD_Status']);
            $this->getDisplayGroup('columnLeft')->addElement($DADStatus);
        }
        foreach($values['DOC'] as $key => $card)
        {
            $cardId = new Zend_Form_Element_Hidden('DOC_Id-' . $card['DOC_Id'],
                array('value' => $card['DOC_Id']));
            $cardId->removeDecorator('label');
            $cardData = new Zend_Form_Element_Text('DOC_ExpirationDate_'. $card['DOC_Id']);
            $cardData->setLabel($this->getView()
                ->getCibleText('form_label_cardNumber')
                . ' ' . $card['DOC_CardNumber'])
                ->setValue($card['DOC_ExpirationDate'])
                ->setAttrib('class', 'number interger');
            $this->getDisplayGroup('columnLeft')->addElement($cardId);
            $this->getDisplayGroup('columnLeft')->addElement($cardData);
        }
        $this->getDisplayGroup('columnLeft')->getElement('totalNbDonation')
            ->setValue($values['totalNbDonation']);
        $this->getDisplayGroup('columnLeft')->getElement('totalDonation')
            ->setValue($values['totalDonation']);
        return parent::populate($values);
    }

    public function isValid($data)
    {
        return parent::isValid($data);
    }
}