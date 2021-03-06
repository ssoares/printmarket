<?php
/**
 * Cible Solutions - Vêtements SP
 * QuoteRequest management.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ItemDemandeData.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Database access to the table "SP_ProduitDemande"
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ItemDemandeData extends Zend_Db_Table
{
    protected $_name = 'SP_ItemDemande';
}