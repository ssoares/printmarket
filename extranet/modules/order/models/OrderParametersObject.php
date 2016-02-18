<?php
/**
 * Module Parameters
 * Management of the references data for orders and catalog.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Parameters
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersObject.php 435 2011-03-28 03:57:25Z ssoares $
 */

/**
 * Manage data from references table and catalog.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Parameters
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ParametersObject.php 435 2011-03-28 03:57:25Z ssoares $
 */
class OrderParametersObject extends DataObject
{
    protected $_dataClass   = 'OrderParametersData';

    protected $_indexClass      = '';
    protected $_indexLanguageId = '';
   // protected $_constraint      = 'CP_FreeItemID';

}