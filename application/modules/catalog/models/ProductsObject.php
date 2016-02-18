<?php
/**
 * Module Catalog
 * Management of the products.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */

/**
 * Manage data from products table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */
class ProductsObject extends DataObject
{
    protected $_dataClass   = 'ProductsData';
    protected $_indexClass      = 'ProductsIndex';
    protected $_indexLanguageId = 'PI_LanguageID';
    protected $_foreignKey      = 'CPC_CategoryId';
    protected $_titleField      = 'PI_Name';
    protected $_valurlField     = 'PI_ValUrl';
    protected $_orderBy         = 'P_Seq ASC';
    protected $_query;
    protected $_searchColumns = array(
        'data' => array(
            'P_Number',
            'P_IsNew',
            'P_Solde',
            'P_Closeout'
        ),
        'index' => array(
            'PI_Name',
            'PI_Description',
        )
    );
    protected $_blockParamsCols = array(
        4 => 'P_IsNew',
        5 => 'P_Solde',
        6 => 'P_Closeout'
    );

    public function getBlockParamsCols($index = null)
    {
        if ($index > 0 && isset($this->_blockParamsCols[$index])){
            $data = $this->_blockParamsCols[$index];
        }else{
            $data = $this->_blockParamsCols;
        }
        return $data;
    }

    public function getTitleField(){
        return $this->_titleField;
    }

    /**
     * Build a request or get the products data according to parameters.
     * @param int $langId   Language id.
     * @param bool $array   Default : true. If false then return the query
     *                      instead of the array of data.
     * @param int $id   Product id, it returns an array with this product data only.
     * @return array|Zen_Db_Select
     */
    public function getProducts($langId = null, $array = true, $id = null){

        if (isset($this->_query) && $this->_query instanceof Zend_Db_Select){
            $this->_query->joinLeft($this->_oDataTableName,
                'CPC_ID = ' . $this->_foreignKey);
        }else{
            $this->_query = $this->_db->select()->from($this->_oDataTableName);
        }
        $this->_query->joinLeft($this->_oIndexTableName,
            $this->_dataId . " = " . $this->_indexId);
        $this->_query->where('P_Inactive = ?', 0);

        if (!is_null($langId)){
            $this->_query->where("{$this->_indexLanguageId} = ?", $langId);
        }

        if ($array){
           return $products = $this->_db->fetchAll($this->_query);
        }else{
           return $this->_query;
        }
    }

    /**
     * Set filters for search by keywords.
     *
     * @param Zend_Db_Select $select The begining of the query to complete.
     *
     * @return void
     */
    public function autocompleteSearch($value, $langId = 1)
    {
        $select = parent::getAll($langId, false);

//        $select->where('P_Type = ?', 'catalog');

        $this->keywordExist(
                array($value),
                $select,
                $langId);

        $products = $this->_db->fetchAll($select);

        return $products;

    }

    /**
     * Fetch the id of a product according the formatted string from URL.
     *
     * @param string $string
     *
     * @return int Id of the searched category
     */
    public function getIdByName($string)
    {
        $select = $this->_db->select()
                ->from($this->_oDataTableName, $this->_dataId)
                ->joinLeft(
                        $this->_oIndexTableName,
                        $this->_dataId . " = " . $this->_indexId,
                        '')
                ->where($this->_valurlField . " LIKE ?", "%" . $string . "%")
                ;

        $id = $this->_db->fetchRow($select);

        return $id[$this->_dataId];
    }
}
