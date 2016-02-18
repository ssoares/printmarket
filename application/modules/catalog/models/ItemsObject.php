<?php

/**
 * Module Catalog
 * Management of the Items.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */
class ItemsObject extends DataObject
{

    protected $_dataClass = 'ItemsData';
    protected $_indexClass = 'ItemsIndex';
    protected $_indexLanguageId = 'II_LanguageID';
    protected $_constraint = 'I_ProductID';
    protected $_foreignKey = 'I_ProductID';
    protected $_valurlField     = 'II_ValUrl';
    protected $_searchColumns = array(
        'data' => array(
            'I_ProductCode',
            'I_IsNew',
            'I_Solde',
            'I_IsCloseout'
        ),
        'index' => array(
            'II_Name'
        )
    );
    protected $_renderResume = false;
    protected $_id;
    protected $_blockParamsCols = array(
        4 => 'I_IsNew',
        5 => 'I_Solde',
        6 => 'I_IsCloseout'
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

    /**
     * Setter for id value
     *
     * @param int $value
     */
    public function setId($value)
    {
        $this->_id = $value;
    }

    public function setRenderResume($resume)
    {
        $this->_renderResume = $resume;
    }

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
        (string) $html = "";
        $listArray = array();

        $select = $this->getAll($langId, false);

        $select->where($this->_constraint . ' = ?', $id)
                ->order('II_Name');

        $data = $this->_db->fetchAll($select);

        $TITLE = 'Items(associez les items aux produits dans la GESTION DES ITEMS)';

        foreach ($data as $key => $item)
        {
            $listArray[$key][] = $item['II_Name'];
        }
        $html = Cible_FunctionsGeneral::generateHTMLTable($TITLE, array(array('Title' => '')), $listArray);

        return $html;
    }

    /**
     * Fetch items data for the selected product.
     *
     * @param int $productId Product id.
     *
     * @return array
     */
    public function getItemsByProductId($productId)
    {
        $items = array();
        $select = $this->getAll(Zend_Registry::get('languageID'), false);
        $select->where($this->_constraint . ' = ?', $productId)
            ->order('I_Seq ASC');

        $items = $this->_db->fetchAll($select);

        return $items;
    }

    /**
     * Fetch items data according to the value.
     *
     * @param int $productId Product id.
     *
     * @return array
     */
    public function getItemByUrlValue($string)
    {
        $select = $this->_db->select()
                ->from($this->_oDataTableName, $this->_dataId)
                ->joinLeft($this->_oIndexTableName,
                        $this->_dataId . " = " . $this->_indexId, '')
                ->where($this->_valurlField . " = ?", $string);

        $id = $this->_db->fetchRow($select);

        return $id[$this->_dataId];
    }

    /**
     * Calculate the items price before tax.
     *
     * @param int $quantity Number of items to calculate.
     *
     * @return float
     */
    public function getPrice($quantity = 0, $unitPrice = false)
    {
        $amount       = (float)0;
        $specialPrice = (float)0;

        $tmp  = $this->getAll(null, true, $this->_id);
        $item = $tmp[0];

        $special = $item['I_Special'];

        $amount = $item['I_PriceDetail'] * $quantity;

//        if ($quantity <= $item['I_LimitVol1'])
//            $amount = $item['I_PriceVol1'] * $quantity;
//        elseif ($quantity > $item['I_LimitVol1']
//                && $quantity <= $item['I_LimitVol2'])
//            $amount = $item['I_PriceVol2'] * $quantity;
//        elseif ($quantity > $item['I_LimitVol2'])
//            $amount = $item['I_PriceVol3'] * $quantity;

        if ($special)
        {
            $specialPrice = $quantity * $item['I_PrixSpecial'];

            $isBigger = Cible_FunctionsGeneral::compareFloats($amount, '>', $specialPrice);

            if($isBigger)
                $amount = $specialPrice;
        }

        if ($unitPrice)
            $amount = $amount / $quantity;

        return $amount;
    }
}