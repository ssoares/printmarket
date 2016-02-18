<?php
/**
 * Module Catalog
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 1872 2016-02-09 19:58:47Z ssoares $id
 */

/**
 * Manage data from products table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ProductsObject.php 1872 2016-02-09 19:58:47Z ssoares $id
 */
class ProductsObject extends DataObject
{
    protected $_dataClass       = 'ProductsData';
    protected $_indexClass      = 'ProductsIndex';
    protected $_indexLanguageId = 'PI_LanguageID';
    protected $_foreignKey      = 'CPC_CategoryId';
    protected $_titleField      = 'PI_Name';
    protected $_valurlField     = 'PI_ValUrl';
    protected $_orderBy         = array('P_Seq ASC');
    protected $_addSubFolder    = true;
    protected $_name            = 'products';

    public function getTitleField(){
        return $this->_titleField;
    }

    public function getValUrlField(){
        return $this->_valurlField;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function _categorySrc()
    {
        $oCat = new CatalogCategoriesObject();
        $list = $oCat->getList(true);
        return $list;
    }

    public function insert($data, $langId)
    {
        $data = $this->_formatInputData($data);
        $id = parent::insert($data, $langId);
        if (isset($data['moreImg']))
        {
            $oProdImg = new ProductsImagesObject();
            $oProdImg->setProductId($id)->insert($data['moreImg'], $langId);
        }
        if (isset($data['productsSet']))
        {
            $obj = new ProductsAssociationObject();
            $obj->setAssociation($id, $data['productsSet'], 'insert');
        }
        $oPC = new ProductsCategoriesObject();
        $oPC->setAssociation($id, $data['P_CategoryId']);
        unset($data['P_CategoryId']);
        if (null !== $data['P_Keywords']){
            $oPK = new ProductsKeywordsObject();
            $oPK->setAssociation($id, $data['P_Keywords']);
        }
        if (isset($data['P_Tabs'])){
            $oPT = new ProductsTabsObject();
            $oPT->setLanguageId($langId)->setAssociation($id, $data['P_Tabs'], 'insert');
        }
        unset($data['P_Keywords']);

        return $id;
    }

    public function save($id, $data, $langId)
    {
        $data = $this->_formatInputData($data);
        if (isset($data['moreImg']))
        {
            $oProdImg = new ProductsImagesObject();
            $oProdImg->setProductId($id)
                ->deleteByProductId()
                ->insert($data['moreImg'], $langId);
        }
        $obj = new ProductsAssociationObject();
        if (isset($data['productsSet']))
            $obj->setAssociation($id, $data['productsSet'], 'save');
        else
            $obj->setAssociation($id, array(), 'delete');

        $oPC = new ProductsCategoriesObject();
        $oPC->setAssociation($id, $data['P_CategoryId']);
        unset($data['P_CategoryId']);
        $oPK = new ProductsKeywordsObject();
        if (!empty($data['P_Keywords'])){
            $oPK->setAssociation($id, $data['P_Keywords']);
            unset($data['P_Keywords']);
        }else{
            $oPK->setAssociation($id, array(), 'delete');
        }
        if (isset($data['P_Tabs'])){
            $oPT = new ProductsTabsObject();
            $oPT->setLanguageId($langId)->setAssociation($id, $data['P_Tabs']);
        }
        unset($data['P_Tabs']);

        $saved = parent::save($id, $data, $langId);

        return $saved;
    }

    public function delete($id)
    {
        $oProdImg = new ProductsImagesObject();
        $oProdImg->setProductId($id)->deleteByProductId();
        $oProdC = new ProductsCategoriesObject();
        $oProdC->deleteAssociation($id);
        $oPk = new ProductsKeywordsObject();
        $oPk->deleteAssociation($id);

        parent::delete($id);
    }

    public function populate($id, $langId)
    {
        $data = parent::populate($id, $langId);

        $obj = new ProductsCategoriesObject();
        $data['P_CategoryId'] = $obj->getCategories($id);
        $oPk = new ProductsKeywordsObject();
        $data['P_Keywords'] = $oPk->getAssociationData($id);
        $oPT = new ProductsTabsObject();
        $tabs = $oPT->setLanguageId($langId)->getAssociationData($id);
        $data = array_merge($data, $tabs);
        return $data;
    }

    public function getRelatedImages($id)
    {
        $oProdImg = new ProductsImagesObject();
        $data = $oProdImg->setProductId($id)->getAll(1, true);

        return $data;
    }

    public function getAssociations($nameAssoc, $id, $langId = 1)
    {
        $related = array();
        $relatedArray = array('products' => 'ProductsAssociationObject');
        $name = $relatedArray[$nameAssoc];
        $association = new $name();
        $selectAssociation = $association->getAll($langId, false);
        $selectAssociation->where($association->getDataId() . " = ?", $id);
        $associationFind = $this->_db->fetchAll($selectAssociation);

        foreach ($associationFind as $assocData)
            $related[] = $assocData[$association->getForeignKey()];

        return $related;
    }
    /**
     * Function to reset columns if needed special display.
     *
     * @param Zend_Db_Select $qry
     * @return \Zend_Db_Select
     */
    public function defineColumns(Zend_Db_Select $qry)
    {
        $qry->reset('columns')
            ->columns(array_keys($this->_dataColumns), $this->_oDataTableName)
            ->columns(array_keys($this->_indexColumns), $this->_oIndexTableName)
            ->columns(array('CCI_Name' => new Zend_Db_Expr('GROUP_CONCAT(CCI_Name separator ", ")')),
                'Catalog_CategoriesIndex')
            ->group($this->_dataId);
        return $qry;
    }
}