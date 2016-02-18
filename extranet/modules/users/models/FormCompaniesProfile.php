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
class FormCompaniesProfile extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledLangSwitcher = true;
        $this->_object = $options['object'];

        unset($options['object']);
        parent::__construct($options);

//        $memberForm = new Cible_Form_SubForm(array('name' => 'membersForm'));

    }

    public function isValid($data)
    {
        return parent::isValid($data);
    }
}