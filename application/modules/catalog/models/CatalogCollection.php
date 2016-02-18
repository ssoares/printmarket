<?php
/**
 * Module Catalog
 * Management of the products.
 *
 * @category  Apploication_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogCollection.php 1867 2016-02-08 19:29:11Z freynolds $
 */

/**
 * Manage data from all the catalog tables.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogCollection.php 1867 2016-02-08 19:29:11Z freynolds $
 */
class CatalogCollection
{
    const MODULE_NAME = 'catalog';
    const TYPE_INT = 'int';
    const TYPE_BOOL = 'bool';

    /**
     * The database object instance
     *
     * @var Zend_Db
     */
    protected $_db;
    /**
     * Current language
     *
     * @var int
     */
    protected $_currentLang;
    /**
     * Block id
     *
     * @var int
     */
    protected $_blockID = null;
    /**
     * Parameters of the block
     *
     * @var array
     */
    protected $_blockParams = array();

    protected $_actions = array();

    protected $_keywords = array();
    protected $_keywordsId = array();
    protected $_kwDelimiter = array(',', ' ');
    protected $_filter   = array();
    protected $_catId    = 0;
    protected $_prodId   = 0;
    protected $_skuId    = 0;
    protected $_limit    = 9;
    protected $_type     = 'list.phtml';
    protected $_bonus    = false;
    protected $_oCategory = null;
    protected $_oProducts = null;
    protected $_oSKU = null;
    protected $_oPC = null;
    protected $_oPK = null;
    protected $_oPT = null;
    protected $_buildSubMenuOn = "CatalogCategoriesObject";
    protected $_orderBy = array('P_Seq ASC','P_Number ASC');
    protected $_search = false;
    protected $_robots = false;
    protected $_paramsType = array(
        1 => self::TYPE_INT,
        2 => self::TYPE_INT,
        3 => self::TYPE_INT,
        4 => self::TYPE_BOOL,
        5 => self::TYPE_BOOL,
        6 => self::TYPE_BOOL,
        7 => self::TYPE_INT,
    );
    protected $_exclude = array('index', 'page', 'keywords');

    function getExclude()
    {
        return $this->_exclude;
    }

    function setExclude($exclude)
    {
        $this->_exclude = array_merge($this->_exclude, $exclude);
        return $this;
    }

    public function getBuildSubMenuOn()
    {
        return $this->_buildSubMenuOn;
    }

    /**
     * Fetch the parameter value
     *
     * @param int $param_name Number identifying the parameter
     *
     * @return string
     */
    public function getBlockParam($param_name)
    {
        return $this->_blockParams[$param_name];
    }

    /**
     * Getter for hasBonus. The product allows to cumulate bonus point.
     *
     * @return bool
     */
    public function getBonus()
    {
        return $this->_bonus;
    }

    /**
     * Return the number of product by page
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }

    /**
     * Return the category id
     *
     * @return int
     */
    public function getCatId()
    {
        return $this->_catId;
    }

    /**
     * Return the product id
     *
     * @return int
     */
    public function getProdId()
    {
        return $this->_prodId;
    }

    public function setProdId($prodId)
    {
        $this->_prodId = $prodId;
        return $this;
    }

    /**
     * Return the parameters array
     *
     * @return array
     */
    public function getBlockParams()
    {
        return $this->_blockParams;
    }

    /**
     * Return the filter attribute.
     *
     * @return array
     */
    public function getFilter()
    {
        return $this->_filter;
    }
    /**
     * Return the actions attribute
     *
     * @return array
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * Get the keywords delimiter for tokenfield plugin.
     *
     * @return string   default : ',' comma
     */
    public function getKwDelimiter()
    {
        return $this->_kwDelimiter;
    }

    /**
     * Set keywords delimiter for tokenfield plugin.
     *
     * @param string|array $kwDelimiter
     * @return \CatalogCollection
     */
    public function setKwDelimiter($kwDelimiter, $replaceDefault = false)
    {
        if (is_array($kwDelimiter)){
            $this->_kwDelimiter = $replaceDefault ?
                $kwDelimiter : array_merge($this->_kwDelimiter, $kwDelimiter);
        }elseif($replaceDefault){
            $this->_kwDelimiter[0] = $kwDelimiter;
        }else{
            array_push($this->_kwDelimiter, $kwDelimiter);
        }
        return $this;
    }

    public function getOrderBy()
    {
        return $this->_orderBy;
    }

    public function getSearch()
    {
        return $this->_search;
    }

    public function setOrderBy($orderBy)
    {
        $this->_orderBy = $orderBy;
        return $this;
    }

    public function setSearch($search)
    {
        $this->_search = empty($search)?:true;

        return $this;
    }
    
    public function getRobots()
    {
        return $this->_robots;
    }
    
    public function setRobots($robots)
    {
        $this->_robots = empty($robots)?:true;

        return $this;
    }   

    public function setKeywordsId($keywordsId)
    {

        $this->_keywordsId = empty($keywordsId)?array():explode(',', $keywordsId);
        return $this;
    }

    /**
     * Class constructor
     *
     * @param int $blockID Id of the block. Default value = null
     */
    public function __construct($params = array())
    {
        if(isset($params['lang'])){
            $this->_currentLang = $params['lang'];
        }
        else{
            $this->_currentLang = Zend_Registry::get('languageID');
        }
        $this->_db = Zend_Registry::get('db');

        $this->setParameters($params);
        if (Zend_Registry::isRegistered('filterFields')){
            $this->setExclude(Zend_Registry::get('filterFields'));
        }

    }

    /**
     * Set parameters given in the url
     * @param array $params Parameters from url to set to build le product list.
     * @return void
     */
    public function setParameters($params = array())
    {
        foreach ($params as $property => $value)
        {
            if ($property == 'BlockID')
                $property = 'blockID';

            $methodName = 'set' . ucfirst($property);

            if (property_exists($this, '_' . $property)
                && method_exists($this, $methodName))
            {
                $this->$methodName($value);
            }
        }
    }

    public function setBlockID($value)
    {
        $this->_blockID = $value;
        $_params = Cible_FunctionsBlocks::getBlockParameters($value);
        foreach ($_params as $param)
        {
            $type = isset($this->_paramsType[$param['P_Number']])?$this->_paramsType[$param['P_Number']]:null;
            switch($type)
            {
                case self::TYPE_INT:
                    $this->_blockParams[$param['P_Number']] = (int)$param['P_Value'];
                    break;
                case self::TYPE_BOOL:
                    $this->_blockParams[$param['P_Number']] = (bool)$param['P_Value'];
                    break;
                default:
                    $this->_blockParams[$param['P_Number']] = $param['P_Value'];
                    break;
            }
        }
    }

    public function setActions($value)
    {
        $tmpArray = explode("/", trim($value, '/'));

        foreach ($this->_exclude as $value)
        {
            $key = array_search($value, $tmpArray);
            if ($key)
                unset($tmpArray[$key]);
            if($value != 'index' && $key)
                unset($tmpArray[$key + 1]);
        }

        $lastVal = end($tmpArray);
        if ($lastVal == "")
            array_pop($tmpArray);

        $this->_actions = $tmpArray;
    }
    public function setCatId($value)
    {
        $this->_catId = $value;
    }

    public function setType($value)
    {
        $this->_type = $value;
    }

    public function getType()
    {
        return $this->_type;
    }

    public function setLimit($value)
    {
        $this->_limit = $value;
    }

    public function setKeywords($value)
    {
        $default = Cible_Translation::getCibleText('form_search_catalog_keywords_label');
        if (!empty($value) && $value != $default){
            $this->_keywords = explode($this->_kwDelimiter[0], trim($value));
        }
    }

    public function setFilter($filters)
    {
        if (is_array($filters))
        {
            foreach ($filters as $value)
            {
                $data = explode('_', $value);
                $this->_filter[$data[0]] = $data[1];
            }
        }
        else
        {
            $data = explode('_', $filters);
            $this->_filter[$data[0]] = $data[1];
        }
    }

    public function getOCategory()
    {
        return $this->_oCategory;
    }

    public function getOProducts()
    {
        return $this->_oProducts;
    }

    /**
     * Get the list of the products for the current category
     *
     * @param int $limit
     *
     * @return array
     */
    public function getList()
    {
        $results   = array();

        $this->_oProducts = new ProductsObject();
        $this->_oCategory = new CatalogCategoriesObject();
        $this->_oCategory->setOrderBy('CC_Seq');

        $this->_oPC = new ProductsCategoriesObject();
        $this->_oPK = new ProductsKeywordsObject();
        $this->_oPT = new ProductsTabsObject();
        $this->_oSKU = new ItemsObject();
    
        if (isset($this->_blockParams[1])){
            Zend_Registry::set('defaultCategory', $this->_blockParams[1]);
        }
        if (!empty($this->_blockParams[7])){;
            $this->_limit = $this->_blockParams[7];
        }

        if (!$this->_prodId)
        {
            // If no category selected, set the default one.
            if (!$this->_catId && !$this->_keywords && isset ($this->_blockParams[1])){
                $categoryId = $this->_blockParams[1];
            }else{
                $categoryId = $this->_catId;
            }
            Zend_Registry::set('catId_',$categoryId);
            $catQry = $this->_oCategory->getAll($this->_currentLang,false);
            $hasChildren = $this->_oCategory->setQuery($catQry)->hasChildren($categoryId);
            
            
            
            if ($hasChildren && $this->_blockParams[2] == 1){
                $select = $this->_oCategory->getQuery();
            }else{
                $qry = $this->_oProducts->getAll(
                        $this->_currentLang,
                        false);
                $or =array();
                if ($this->_blockParams[2] == 2 && $this->_blockParams[3] == 2){
                    $c = $this->_oProducts->getDataId() . ' = ' . $this->_oSKU->getForeignKey();
                    $qry = $this->_oSKU->setQuery($qry)
                        ->setJoinCondition($c, false)
                        ->joinFetchData();
                    !$this->_blockParams[4]?: $or[] = $this->_oSKU->getBlockParamsCols(4) . ' = 1';
                    !$this->_blockParams[5]?: $or[] = $this->_oSKU->getBlockParamsCols(5) . ' = 1';
                    !$this->_blockParams[6]?: $or[] = $this->_oSKU->getBlockParamsCols(6) . ' = 1';
                }else{
                    !$this->_blockParams[4]?: $or[] = $this->_oProducts->getBlockParamsCols(4) . ' = 1';
                    !$this->_blockParams[5]?: $or[] = $this->_oProducts->getBlockParamsCols(5) . ' = 1';
                    !$this->_blockParams[6]?: $or[] = $this->_oProducts->getBlockParamsCols(6) . ' = 1';
                }
                if (!empty($or)){
                    $qry->where(implode(' OR ', $or));
                }
                $cfk = $this->_oPC->getForeignKey();
                $tmp = $this->_oPC->setQuery($qry)
                    ->setForeignKey($this->_oProducts->getDataId())
                    ->joinFetchData();
                if ($hasChildren){
                    $categories = $this->_oCategory->getChildrenId($this->_currentLang, $categoryId);
                    $qry = $tmp->where($cfk . ' in (?)', $categories);
                }else{
                    $qry = $tmp->where($cfk . ' = ?', $categoryId, Zend_Db::INT_TYPE);
                }
                $select = $this->_oCategory->setQuery($qry)
                    ->setForeignKey($cfk)
                    ->joinFetchData(false, $this->_currentLang);

                $this->_type = 'list-products.phtml';
            }
            if ($this->_search){
                $select = $this->_addFilterQuery($select);
            }           

            $results = $this->_db->fetchAll($select);
        }
        else
        {
            $product = $this->_oProducts->getAll($this->_currentLang, true, $this->_prodId);
            $tmpArray     = $product[0];
            $dataCategory = $this->_oCategory->populate($this->_catId, $this->_currentLang);
            $results['data'] = array_merge($tmpArray, $dataCategory);
            if ($this->_skuId > 0){
                $items[]  = $this->_oSKU->populate($this->_skuId, $this->_currentLang);
            }else{
                $items  = $this->_oSKU->getItemsByProductId($this->_prodId);
            }
            $results['items'] = $items;

            $results['tabs'] = $this->_oPT->getAll($this->_currentLang, true, $this->_prodId);

            $oImages  = new ProductsImagesObject();
            $oImages->setProductId($this->_prodId);
            $results['images'] = $oImages->getAll($this->_currentLang);

            $oAssocProd = new ProductsAssociationObject();
            $relations  = $oAssocProd->getAll($this->_currentLang, true, $this->_prodId );
            $tmp = array();
            $relatedProd = array();

            foreach ($relations as $relProd)
            {
                if ($relProd['AP_RelatedProductID'] != -1)
                {
                    $tmp = $this->_oProducts->populate($relProd['AP_RelatedProductID'], $this->_currentLang);
                    $category  = $this->_oCategory->getAll($this->_currentLang, true, $tmp['P_CategoryId']);
                    $this->_oCategory->getDataCatagory($this->_currentLang, false, $tmp['P_CategoryId']);
                    $stringUrl = '/';
                    $stringUrl .= implode('/', $this->_oCategory->setCategoriesLink(true)->getLink());
                    $stringUrl .= '/' . $tmp['PI_ValUrl'];
                    $tmp['link'] = $stringUrl;
                    $tmpCat    = $category[0];
                    $tmp       = array_merge($tmp, $tmpCat);

                    $relatedProd[]  = $tmp;
                }
            }
            $results['relatedProducts'] = $relatedProd;
        }


        return $results;
    }

    public function setDataByName()
    {
        $lastVal = end($this->_actions);
        $oCat = new CatalogCategoriesObject();
        $categoryId = $oCat->getIdByName($lastVal);
        if ($categoryId > 0){
            $this->_catId = $categoryId;
        }
        elseif (!$this->_catId && count($this->_actions) > 2)
        {
            $index = 2;
            $actionLength = count($this->_actions);
            $oProd = new ProductsObject();
            $this->_prodId = $oProd->getIdByName($lastVal);
            if (!$this->_prodId){
                $val = $this->_actions[$actionLength - 2];
                $this->_prodId = $oProd->getIdByName($val);
                $item = new ItemsObject();
                $this->_skuId = $item->getItemByUrlValue($lastVal);
                $index = 3;
            }
            $val = $this->_actions[$actionLength - $index];
            $this->_catId = $oCat->getIdByName($val);
        }
        elseif (!$this->_prodId)
        {
            $defaultCat = Cible_FunctionsBlocks::getBlockParameter($this->_blockID, 1);
            if($defaultCat)
                $this->_catId = $defaultCat;
        }

    }

    protected function _addFilterQuery(Zend_Db_Select $select)
    {
        $search = array('index' => $this->_keywords);
        $haskeyFilter = !empty($this->_keywords) ? true: false;

        $from = $select->distinct()->getPart('from');
        $isJoined = array_key_exists($this->_oProducts->getDataTableName(), $from);
        if ($haskeyFilter && !$isJoined){
            $fk = $this->_oProducts->getForeignKey();
            $select = $this->_oProducts->setQuery($select)
                ->setNoColumns(true, true)
                ->setForeignKey($this->_oProducts->getDataId())
                ->joinFetchData(false, $this->_currentLang);
            $this->_oProducts->setForeignKey($fk);
            $orWhere[] = $this->_buildWhereClause($search, $this->_oProducts);
        }
        $isJoined = array_key_exists($this->_oPK->getDataTableName(), $from);
        if ($haskeyFilter && !$isJoined){
            $fk = $this->_oPK->getForeignKey();
            $select = $this->_oPK->setQuery($select)
                ->setNoColumns(true, true)
                ->setForeignKey($this->_oProducts->getDataId())
                ->joinFetchData();
            $this->_oPK->setForeignKey($fk);
        }
        $isJoined = array_key_exists($this->_oPT->getDataTableName(), $from);
        if ($haskeyFilter && !$isJoined){
            $fk = $this->_oPT->getForeignKey();
            $select = $this->_oPT->setQuery($select)
                ->setNoColumns(true, true)
                ->setForeignKey($this->_oProducts->getDataId())
                ->joinFetchData(false, $this->_currentLang);
            $this->_oPT->setForeignKey($fk);
            $orWhere[] = $this->_buildWhereClause($search, $this->_oPT);
        }

        if($haskeyFilter){
            $oRef = new ReferencesObject();
            $oRef->setNoColumns(true, true);
            $select = $oRef->setQuery($select)
                ->setOrderBy(null)
                ->setForeignKey($this->_oPK->getForeignKey())
                ->joinFetchData(false, $this->_currentLang);

            $orWhere[] = $this->_buildWhereClause($search, $oRef);
            $orWhere[] = $this->_buildWhereClause($search, $this->_oCategory);
            if (array_key_exists($this->_oProducts->getDataTableName(), $from)){
            }
            $select->where(implode(' OR ', $orWhere));
        }
        if (!empty($this->_orderBy)){
            $order = $this->_setOrder($select->getPart('order'));
            $select->reset('order')->order($order);
        }

        return $select;
    }    

    public function getDetails($id, $itemId = 0, $resume = false)
    {
        $products   = array();

        $oProduct = new ProductsObject();
        $oItem    = new ItemsObject();

        $products['data'] = $oProduct->populate(
                    $id,
                    $this->_currentLang);

        if($itemId)
            $products['items'] = $oItem->populate($itemId, $this->_currentLang);

        return $products;
    }

    public function getKeywordsByCategory($catId = 0, $term = '')
    {
        $list = array();
        $oRef = new ReferencesObject();
        $this->_oPC = new ProductsCategoriesObject();
        $this->_oPK = new ProductsKeywordsObject();
        $qryC = $this->_oPC->getAll(null, false);

        if ($catId > 0){
            $this->_oCategory = new CatalogCategoriesObject();
            $c = $this->_oCategory->getChildrenId(null, $catId);
            $operator = count($c) > 1 ? ' in (?)' : ' = ?';
            count($c) > 1 ?: $c = $c[0];
            $qryC->where($this->_oPC->getForeignKey() . $operator, empty($c) ? $catId : $c);
        }

        $kfk = $this->_oPK->getForeignKey();
        $qryK = $this->_oPK->setQuery($qryC)
            ->setForeignKey($this->_oPC->getDataId())
            ->joinFetchData();
        if (!empty($term)){
            $qryK->where($oRef->getTitleField() . ' like ?', '%' . $term . '%');
        }
        $data = $oRef->setQuery($qryK)
            ->setForeignKey($kfk)
            ->joinFetchData(true, $this->_currentLang);

        foreach($data as $value)
        {
            $list[$value[$oRef->getDataId()]] = array(
                'id' => $value[$oRef->getDataId()],
                'value' => $value[$oRef->getTitleField()],
                'label' => $value[$oRef->getTitleField()]
                );
        }

        return $list;
    }

    private function _buildWhereClause($data, $obj)
    {
        $clause = null;
        $searchCol = $obj->getSearchColumns();
        $forceExact = $obj->getForceExact();
        foreach ($data as $key => $value)
        {
            $tmpVal = is_array($value)?:explode(' ', $value);
            if (count($tmpVal) > 1){
                $value = $tmpVal;
            }
            if (array_key_exists($key, $searchCol))
            {
                if (is_array($searchCol[$key]))
                {
                    foreach ($searchCol[$key] as $column){
                        if (is_string($value)){
                            $where[] = $forceExact ?
                                $this->_db->quoteInto("{$column} = ?", $value)
                                : $this->_db->quoteInto("{$column} like '%{$value}%'", $value);
                        }elseif (is_integer($value)){
                            $where[] = $this->_db->quoteInto($column . ' = ?', (int)$value);
                        }elseif (is_null($value)){
                            $where[] = $this->_db->quoteInto($column . ' is null');
                        }elseif(is_array($value)){
                            foreach($value as $val){
                                if (is_string($val)){
                                    $where[] = $forceExact ?
                                        "{$column} = '{$val}'"
                                        : "{$column} like '%{$val}%'";
                                }elseif (is_integer($val)){
                                    $where[] = $this->_db->quoteInto($column . ' = ?',$val);
                                }
                            }
                        }
                     }
                    $clause = implode(' OR ', $where);
                }
            }
        }

        return $clause;
    }

    private function _setOrder(array $values)
    {
        foreach($values as $key => $data)
        {
            $tmp = explode(' ', $this->_orderBy);
            if ($data[0] == $tmp[0]){
                $values[$key] = $this->_orderBy;
            }else{
                $values[$key] = implode(' ', $data);
            }
        }

        return $values;
    }
    
    
    
    /**
     * Build a list of all the categories and all the products for the robots.     
     * @param int $langId     
     * @return array
     */
    public function getProductsUrl($langId = null){
        if(!isset($langId)){
            $langId = $this->_currentLang;        
        }
        $productsURLValid = array();        
        $this->setRobots(true);        
        $oCategory = new CatalogCategoriesObject();        
        $oCategories = $oCategory->getAll($langId);       
        foreach ($oCategories as $oCat){          
            $urlFullCat = "";
            foreach($oCategory->getParents($oCat['CC_ID'],$langId) as $key => $val){
                $urlFullCat .= $val . "/";            
            }
            array_push($productsURLValid,$urlFullCat);
            $productsURL = $this->getListForRobots($oCat['CC_ID'], $urlFullCat, $langId);
            foreach ($productsURL as $proURL){
                array_push($productsURLValid,$proURL);
            }            
        }
        return $productsURLValid;
    }
    
    /**
     * Build a list of all the products for the robots.  
     * @param int $catID 
     * @param str $urlFullCat   
     * @param int $langId     
     * @return array
     */
    public function getListForRobots($catID, $urlFullCat, $langId = null){
        $returnA = array();
        if($this->_robots==true){            
            if(!isset($langId)){
                $langId = $this->_currentLang;        
            }
            $prods = new ProductsObject();
            $query = $prods->getAll($langId, false);            
            $query->join("Catalog_Products_Categories", "CPC_ProductId = P_ID");
            $query->join("Catalog_CategoriesData", "CPC_CategoryId = CC_ID");
            $query->join("Catalog_CategoriesIndex", "CC_ID = CCI_CategoryID");            
            $query->where("CPC_CategoryId=?", $catID);
            $query->where("CCI_LanguageID=?", $langId);            
            $products = $this->_db->fetchAll($query);   
            foreach($products as $key => $val){
                array_push($returnA,$urlFullCat . $val['PI_ValUrl']);
            } 
        }        
        return $returnA;
    }
    
    
}
