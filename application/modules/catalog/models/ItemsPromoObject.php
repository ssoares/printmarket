<?php
/**
 * Module Catalog
 * Management of the discount Items.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsPromoObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */

/**
 * Manage data from itemsPromo table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsPromoObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */
class ItemsPromoObject extends DataObject
{
    protected $_dataClass   = 'ItemsPromoData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();
    
    protected $_indexClass      = '';
//    protected $_indexId         = '';
//    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';

    /**
     * Fetch items data for the special offer items.
     *
     * @param int $id     Product id
     *
     * @return array
     */
    public function getAssociatedItems($id)
    {
//        (string) $html = "";
        $listArray     = array();
//
        $select = $this->getAll(null, false);
//
        $select->where('IP_ConditionItemId = ?', $id)
            ->order('IP_ItemId');

        $data = $this->_db->fetchAll($select);

        return $data;
    }


     /**
     * Build the list of items for a dropdown select.
     * Data are grouped by product.
     *
     * @param int $langId
     *
     * @return array
     */
    public function itemsCollection($langId)
    {
//        (array) $array = array();
//
//        $select = $this->getAll($langId, false);
//
//        $select->joinLeft(
//                'Catalog_ProductsIndex',
//                'PI_ProductID = I_ProductID',
//                'PI_Name',
//                array())
//            ->where('II_LanguageID = ?', $langId)
//            ->where('PI_LanguageID = ?', $langId)
//            ->order('PI_Name')
//        ;
//
//        $items =$this->_db->fetchAll($select);
//
//        foreach ($items as $data)
//        {
//            $key = $data['PI_Name'];// . "-" . $data['SCI_Name'];
//            if(!array_key_exists($key, $array)){
//                $array[$key] = array($data['I_ID'] => $data['II_Name']);
//            }
//            else{
//                $array[$key][$data['I_ID']] = $data['II_Name'];
//            }
//        }
//        return $array;
    }
}