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
 * @version   $Id: FormRetailersProfile.php 804 2012-01-23 17:53:00Z ssoares $id
 */

/**
 * Form to manage the generic profile.
 * Data are used to create account basis.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormRetailersProfile.php 804 2012-01-23 17:53:00Z ssoares $id
 */
class FormRetailersProfile extends Cible_Form
{

    protected $_mode = 'add';

    public function __construct($options = null)
    {
//        $this->_disabledDefaultActions = true;
//        $this->_object = $options['object'];
        unset($options['object']);
        parent::__construct($options);
        // Subform for the retailer status on website
        $retailerForm = new Cible_Form_SubForm();
        $retailerForm->setName('retailerForm')
            ->removeDecorator('DtDdWrapper');
        //checkbox to set the retailers address as valid
        $isValid = new Zend_Form_Element_Checkbox('R_Active');
        $isValid->setLabel($this->getView()->getCibleText('form_label_approved_onweb'));
        $isValid->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        $retailerForm->addElement($isValid);


        $isRetailer = new Zend_Form_Element_Radio('isDistributeur');
        $isRetailer->setLabel($this->getView()->getCibleText('form_label_Display_web'))
                       ->setOrder(0);
        $isRetailer->setSeparator('');
        $isRetailer->setAttrib('class', 'vertAlignRadio');
        $isRetailer->addMultiOptions(
                array(
                    1 => $this->getView()->getCibleText('form_account_no'),
                    2 => $this->getView()->getCibleText('form_account_yes'))
                )
            ->setValue(1);

        $txtFr = new Cible_Form_Element_Html(
            'lblFr',
            array(
                'value' => $this->getView()->getCibleText('form_address_retailer_fr')
            )
        );
        $txtFr->setOrder(1)
        ->setDecorators(
            array(
                'ViewHelper',
                array('label', array('placement' => 'prepend')),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => 'dd',
                        'class' => 'formLanguage')
                ),
            )
        );
        $retailerForm->addElement($txtFr);
        $adressRetailer = new Cible_View_Helper_FormAddress($retailerForm);

        $adressRetailer->enableFields(
            array(
                'name'          => true,
                'firstAddress'  => false,
                'secondAddress' => false,
                'state'         => false,
                'cityTxt'       => false,
                'zipCode'       => false,
                'country'       => false,
                'firstTel'      => false,
                'secondTel'     => false,
                'fax'           => false,
                'email'         => false,
                'webSite'       => false
                )
            );

        $adressRetailer->formAddress();
        $retailerForm->addElement($isRetailer);

        $retailerForm->getElement('AI_SecondTel')->setAttrib('class', 'stdTextInput phoneFree');

        // Subform for the retailer status on website
        $retailerFormEn = new Cible_Form_SubForm();
        $retailerFormEn->setName('retailerFormEn')
            ->removeDecorator('DtDdWrapper');
        $txtEn = new Cible_Form_Element_Html(
            'lblEn',
            array(
                'value' => $this->getView()->getCibleText('form_address_retailer_en')
            )
        );
        $txtEn->setDecorators(
            array(
                'ViewHelper',
                array('label', array('placement' => 'prepend')),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => 'dd',
                        'class' => 'formLanguage')
                ),
            )
        );

        $adressRetailerEn = new Cible_View_Helper_FormAddress($retailerFormEn);
        $adressRetailerEn->enableFields(
            array(
                'name'          => false,
                'firstAddress'  => false,
                'secondAddress' => false,
                'firstTel'      => false,
                'secondTel'     => false,
                'webSite'       => false
                )
            );

        $adressRetailerEn->formAddress();
        $retailerFormEn->addElement($txtEn);

        $retailerFormEn->getElement('AI_SecondTel')->setAttrib('class', 'stdTextInput phoneFree');
        //*** Add subform to the form ***/
        $this->addSubForm($retailerForm, 'retailerForm');
        $this->addSubForm($retailerFormEn, 'retailerFormEn');

    }

}