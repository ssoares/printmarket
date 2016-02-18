<?php
/**
 * Module Parameters
 * Management of the references data.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Parameters
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersData.php 435 2011-03-28 03:57:25Z ssoares $
 */

/**
 * Manage data from references table for catalog and orders.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersData.php 435 2011-03-28 03:57:25Z ssoares $
 */
class OrderParametersData extends Zend_Db_Table
{
    protected $_name = 'Order_Parameters';
}