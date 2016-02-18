<?php
/**
 * Cible Solutions - Vêtements SP
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ItemDemandeObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data in database for the products.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ItemDemandeObject extends DataObject
{
    protected $_dataClass   = 'ItemDemandeData';
    protected $_dataId      = 'ID_ID';
    protected $_constraint  = 'ID_DemandeSoumissionID';
    protected $_dataColumns = array(
        'ID_ProduitDemandeID'    => 'ID_ProduitDemandeID',
        'ID_DemandeSoumissionID' => 'ID_DemandeSoumissionID',
        'ID_ItemID'              => 'ID_ItemID',
        'ID_TailleID'            => 'ID_TailleID',
        'ID_Quantite'            => 'ID_Quantite'
    );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();

    protected $_query;

    public function deleteByDemandeId($id)
    {
        $this->_db->delete(
            $this->_oDataTableName,
            $this->_db->quoteInto("{$this->_constraint} = ?", $id)
            );
    }

    /**
     * Fetches data by quote request id
     * 
     * @param int $id
     *
     * @return array
     */
    public function getByProductId($id)
    {
        $db = $this->_db;

        $select = $db->select()
            ->from($this->_oDataTableName)
            ->where("ID_ProduitDemandeID = ?", $id);

        $data = $db->fetchAll($select);

        return $data;
    }
}