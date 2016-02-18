<?php
/**
 * Cible Solutions - Vêtements SP
 * Product management. Data import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ProduitDemandeObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data in database for the products.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ProduitDemandeObject extends DataObject
{
    protected $_dataClass   = 'ProduitDemandeData';
    protected $_dataId      = 'PD_ID';
    protected $_constraint  = 'PD_DemandeSoumissionID';
    protected $_dataColumns = array(
        'PD_ID'        => 'PD_ID',
        'PD_ProduitID' => 'PD_ProduitID',
        'PD_DemandeSoumissionID' => 'PD_DemandeSoumissionID'
    );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();

    protected $_query;

    /**
     * Delete requested product when a quote request is deleted.
     *
     * @param int $id Deleted quote request id.
     *
     * @return void
     */
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
    public function getByDemandeId($id)
    {
        $db = $this->_db;

        $select = $db->select()
            ->from($this->_oDataTableName)
            ->where($this->_constraint . " = ?", $id);
    
        $data = $db->fetchAll($select);

        return $data;
    }
}