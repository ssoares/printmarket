<?php
/**
 * Cible Solutions - Vêtements SP
 * Product management. Data import.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: RequestedProductObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data in database for the products.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class RequestedProductObject extends DataObject
{
    protected $_dataClass   = 'RequestedProductData';
    protected $_dataId      = 'PD_ID';
    protected $_dataColumns = array(
        'productId' => 'PD_ProduitID',
        'requestId' => 'PD_DemandeSoumissionID'
    );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();

    protected $_query;

}