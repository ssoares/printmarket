<?php
/**
 * Module Catalog
 * Management of the discount Items.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsPromoObject.php 453 2011-04-14 04:16:53Z ssoares $
 *
 */

/**
 * Manage data from itemsPromo table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsPromoObject.php 453 2011-04-14 04:16:53Z ssoares $
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
    protected $_foreignKey      = 'IP_ItemId';

    /**
     * Fetch items data for the product and build the rendering.
     *
     * @param int $id     Product id
     * @param int $langId 
     *
     * @return string
     */
    public function getAssociatedItems($id, $langId)
    {
//        (string) $html = "";
//        $listArray     = array();
//
//        $select = $this->getAll($langId, false);
//
//        $select->where($this->_constraint . ' = ?', $id)
//            ->order('II_Name');
//
//        $data = $this->_db->fetchAll($select);
//
//        $TITLE = 'Items(associez les items aux produits dans la GESTION DES ITEMS)';
//
//        foreach($data as $key => $item)
//        {
//            $listArray[$key][] = $item['II_Name'];
//
//        }
//        $html = Cible_FunctionsGeneral::generateHTMLTable($TITLE, array(array('Title' =>'')), $listArray);
//
//        return $html;
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
//                'CL_ProductsIndex',
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