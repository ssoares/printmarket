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
class FormRelationshipProfile extends Cible_Form_GenerateForm
{

    public function __construct($options = null)
    {
        $this->_disabledLangSwitcher = true;
        $this->_object = $options['object'];

        unset($options['object']);
        parent::__construct($options);

        $columnLeft = $this->getDisplayGroup('columnLeft');
        $columnBottom = $this->getDisplayGroup('columnBottom');
        $columnBottom->setLegend(null);
        $columnLeft->setLegend(null);

    }

    public function populate(array $values)
    {
        if (empty($values['GPF_FollowedBy'])){
            $this->getElement('GPF_FollowedBy')->setValue($this->getView()->user['EU_ID']);
        }
        return parent::populate($values);
    }

    public function isValid($data)
    {
        return parent::isValid($data);
    }
}