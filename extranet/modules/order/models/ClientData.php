<?php
/**
 * Cible Solutions - Vêtements SP
 * Quote request management. Data import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ClientData.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Database access to the table GenericProfiles for clients import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ClientData extends Zend_Db_Table
{
    protected $_name = 'GenericProfiles';
}