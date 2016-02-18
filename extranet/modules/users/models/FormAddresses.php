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
 * @version   $Id: FormAddresses.php 487 2011-05-20 03:15:37Z ssoares $id
 */

/**
 * Form to manage addresses.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Users
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormAddresses.php 487 2011-05-20 03:15:37Z ssoares $id
 */
class FormAddresses extends Cible_Form
{

    public function __construct($options = null)
    {
        $this->_disabledDefaultActions = true;

        parent::__construct($options);
        
        $countries = Cible_FunctionsGeneral::getCountries();
        $states = Cible_FunctionsGeneral::getStates();

        
    }

}