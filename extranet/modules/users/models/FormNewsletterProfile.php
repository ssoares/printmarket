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
 * @version   $Id: FormNewsletterProfile.php 1788 2015-06-18 20:07:52Z ssoares $id
 */

/**
 * Form to manage the newsletter registration.
 * Allows to define which newsletter the user will recieve.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormNewsletterProfile.php 1788 2015-06-18 20:07:52Z ssoares $id
 */
class FormNewsletterProfile extends Cible_Form
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = false;
        unset($options['object']);

        parent::__construct($options);

        $label = new Cible_Form_Element_Html(
            'txtAddToNewsletter',
            array(
                'value'=>$this->getView()->getCibleText('profile_addto_newletter_label')
            )
        );
        $label->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'h2')),
        ));
        $type = new Zend_Form_Element_Select('NP_TypeID');
        $type->setLabel('Type de profil');

        $oRef = new ReferencesObject();
        $types = $oRef->getListValues('typeClient', null, true);
        $type->addMultiOptions($types);

        $datePicker = new Cible_Form_Element_DatePicker('NP_SubscriptionDate', array('jquery.params'=> array('changeYear'=>true, 'changeMonth'=> true)));
        $datePicker->setLabel($this->getView()->getCibleText('form_label_date'))
        ->setRequired(true)
        ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
        ->addValidator('Date', true, array('messages' =>
            array( 'dateNotYYYY-MM-DD' => $this->getView()->getCibleText('validation_message_invalid_date'),
                'dateInvalid' => $this->getView()->getCibleText('validation_message_invalid_date'),
                'dateFalseFormat' => $this->getView()->getCibleText('validation_message_invalid_date')
            )));
        $oList = new Cible_Newsletters_Lists();
        $oList->setConfig($this->_config)->setParameters()->getInfo();
        $cat = $oList->getResults();
        $catLst[$cat['id']] = $cat['name'];

        $chkCat = new Zend_Form_Element_MultiCheckbox("NP_Categories");
        $chkCat->addMultiOptions($catLst);
        $chkCat->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'id' => '', 'class' => 'label_after_checkbox')),
        ));

        $externalId = new Zend_Form_Element_Hidden('NP_ExternalId');
        $externalId->removeDecorator('Label');
        $this->addElement($type);
        $this->addElement($datePicker);
        $this->addElement($label);
        $this->addElement($chkCat);
        $this->addElement($externalId);
    }

}
