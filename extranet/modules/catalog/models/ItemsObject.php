<?php
/**
 * Module Catalog
 * Management of the Items.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ItemsObject.php 1856 2016-01-26 11:20:40Z ssoares $id
 */
class ItemsObject extends DataObject
{
    protected $_dataClass   = 'ItemsData';
    protected $_indexClass      = 'ItemsIndex';
    protected $_indexLanguageId = 'II_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'I_ProductID';
    protected $_titleField      = 'II_Name';
    protected $_valurlField     = 'II_ValUrl';
    protected $_orderBy         = '';
    protected $_addSubFolder    = false;
    protected $_name            = 'items';
    protected $_totalInches     = 0;

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
        $listArray     = array();

        $select = $this->getAll($langId, false);

        $select->where($this->_constraint . ' = ?', $id)
            ->order('II_Name');

        $data = $this->_db->fetchAll($select);

        $TITLE = 'Items(associez les items aux produits dans la GESTION DES ITEMS)';

        foreach($data as $key => $item)
        {
            $listArray[$key][] = $item['II_Name'];

        }
        $html = Cible_FunctionsGeneral::generateHTMLTable($TITLE, array(array('Title' =>'')), $listArray);

        return $html;
    }

    public function _productsSrc()
    {
        $oProd = new ProductsObject();
        return $oProd->getList();
    }

    public function insert($data, $langId)
    {
        $data = $this->_formatInputData($data);
        $id = parent::insert($data, $langId);

        return $id;
    }

    public function save($id, $data, $langId)
    {
        $data = $this->_formatInputData($data);
        $saved = parent::save($id, $data, $langId);

        return $saved;
    }

    /**
     * Format values before being saved.
     *
     * @param array $data data to save in db
     * @return array
     */
    protected function _formatInputData(array $data)
    {
        $data = parent::_formatInputData($data);
        foreach ($data as $key => $value)
        {
            switch ($key)
            {
                case 'I_PriceDetail':
                case 'I_DiscountPercent':
                    $data[$key] = str_replace(array(',', ' '), array('.', ''), $value);
                    break;
                case 'I_Number':
                    $value = Cible_FunctionsGeneral::formatValueForUrl($value);
                    $data[$this->_valurlField] = $value . '-' . $data[$this->_valurlField];
                    break;

                default:
                    break;
            }
        }
        
        return $data;
    }
}
