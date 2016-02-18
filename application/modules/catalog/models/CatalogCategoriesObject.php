<?php
/**
 * Module Catalog
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogCategoriesObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */

/**
 * Manage data from colletion table.
 *
 * @category  Application_Module
 * @package   Application_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogCategoriesObject.php 1865 2016-02-04 22:29:15Z ssoares $id
 */
class CatalogCategoriesObject extends DataObject
{
    protected $_dataClass   = 'CatalogCategoriesData';
    protected $_indexClass      = 'CatalogCategoriesIndex';
    protected $_indexLanguageId = 'CCI_LanguageID';
    protected $_foreignKey      = 'CC_ParentId';
    protected $_titleField      = 'CCI_Name';
    protected $_valurlField     = 'CCI_ValUrl';
    protected $_orderBy         = 'CC_Seq';
    protected $_nesting         = 0;
    protected $_buildOnCatalog  = false;
    protected $_currentPath  = array();
    protected $_link  = array();
    protected $_level  = 0;
    protected $child = array();
    protected $_isActive = false;
    protected $_searchColumns = array(
        'index' => array(
            'CCI_Name',
            'CCI_Description'
        )
    );

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function getLink()
    {
        return $this->_link;
    }

    /**
     * Setter for the query used to find data.
     *
     * @param Zend_Db_Select $query
     * @return void
     */
    public function setQuery(Zend_Db_Select $query = null)
    {
        $this->_query = $query;
        $this->_query->where('CC_Online = ?', 1);
        return $this;
    }

    /**
     * Fetch the id of a category according the formatted string fron URL.
     *
     * @param string $string
     *
     * @return int Id of the searched category
     */
    public function getIdByName($string)
    {
        $id = 0;
        if (!empty($string))
        {
            $select = $this->_db->select()
                ->from($this->_oDataTableName, $this->_dataId)
                ->joinLeft(
                        $this->_oIndexTableName,
                        $this->_dataId . " = " . $this->_indexId,
                        '')
                ->where("CCI_ValUrl = ?", $string);

            $data = $this->_db->fetchRow($select);
            $id = $data[$this->_dataId];
        }

        return $id;
    }

    public function getDataCatagory($langId = null, $array = true, $id = null)
    {
        $this->_query = parent::getAll($langId, FALSE, $id);

        if ($array)
            return $products = $this->_db->fetchAll($this->_query);
        else
            return $this->_query;
    }

    public function hasChildren($id)
    {
        $hasChildren = false;
        if ($this->_query)
        {
            $this->_query->where($this->_foreignKey . ' = ?', $id);
            $data = $this->_db->fetchAll($this->_query);
            if (count($data) > 0)
                $hasChildren = true;
        }

        return $hasChildren;
    }

    public function setCategoriesLink()
    {
        if (!empty($this->_query))
        {
            $data = $this->_db->fetchAll($this->_query);
            $data = $data [0];
            $this->_link[] = $data[$this->_valurlField];
            if (!empty($data[$this->_foreignKey]))
            {
                $this->getDataCatagory (Zend_Registry::get ('languageID'), false, $data[$this->_foreignKey]);
                $this->setCategoriesLink();
            }
            else
                $this->_link = array_reverse($this->_link);
        }

        return $this;
    }

    public function buildCatalogMenu($menuCatalog, $options = array())
    {
        $langId = Zend_Registry::get('languageID');
        $defaultCatId = 0;
        if (!empty($options) && isset($options['nesting']))
            $this->_nesting = $options['nesting'];
        if (!empty($options) && isset($options['buildOnCatalog']))
            $this->_buildOnCatalog = $options['buildOnCatalog'];
        if (!empty($options) && isset($options['currentPath']))
            $this->_currentPath = $options['currentPath'];
        if(isset($menuCatalog['Link'])){
            $this->_link[] = $menuCatalog['Link'];
            $this->_level++;
        }
        if(Zend_Registry::isRegistered('defaultCategory')
            && !is_null(Zend_Registry::get('defaultCategory'))
            && Zend_Registry::get('defaultCategory') > 0)
        {
            $defaultCatId = Zend_Registry::get('defaultCategory');
            $tmpCat       = $this->populate($defaultCatId, $langId);
            if(empty ($tmpCat[$this->_valurlField]))
                $tmpCat[$this->_valurlField] = "";
            $this->_link[] = $tmpCat[$this->_valurlField];
            $this->_level++;
        }
        if (!$defaultCatId && isset($menuCatalog['PageID'])){
            $opt = array('pageId' => $menuCatalog['PageID'], 'moduleId' => 14);
            $blocks = Cible_FunctionsBlocks::getBlocksFromRelatedPage($opt);
            if (isset($blocks['blocks'])){
                $blockId = current(array_keys($blocks['blocks']));
                $param = Cible_FunctionsBlocks::getBlockParameter($blockId, 1);
                if ($param > 0){
                    $defaultCatId = $param;
                }
            }
        }

        $id = isset($menuCatalog['MID_ID']) ? $menuCatalog['MID_ID'] : $menuCatalog['ID'];
        $title = isset($menuCatalog['Title']) ? $menuCatalog['Title'] : '';

        $this->setQuery($this->getAll($langId, false));
        $this->_query->where($this->_foreignKey . ' = ?', $defaultCatId);
        $categories = $this->_db->fetchAll($this->_query);
        $catalog = array(
            'ID' => $id,
            'Title' => $title,
            'PageID' => '',
            'Link' => implode('/', $this->_link),
            'Placeholder' => 0,
            'menuImage' => '',
            'loadImage' => '',
            'menuImgAndTitle' => '',
            'child' => array()
        );
        $cat = $this->_getTree($categories, $catalog, $langId);
        if ($this->_isActive){
            $cat['Style'] = 'active';
        }
        return $cat;
    }

    private function _getTree($categories, $catalog, $langId)
    {
        $menu = array();

        foreach ($categories as $category)
        {
            $this->_link[$this->_level] = $category[$this->_valurlField];
            $menu = array(
                'ID' => $category[$this->_dataId],
                'Title' => $category[$this->_titleField],
                'PageID' => '',
                'Link' => implode('/', $this->_link),
                'menuImage' => $category['CC_imageCat'],
                'loadImage' => 0,
                'menuImgAndTitle' => 0,
                'Placeholder' => 0);
            if (in_array($category[$this->_valurlField], $this->_currentPath)){
                $menu['Style'] = 'active';
                $this->_isActive = true;
            }
            if ($this->_nesting > 0)
            {
                $qry = $this->getAll($langId, false);
                $qry->where($this->_foreignKey . ' = ?', $category[$this->_dataId]);
                $qry->where('CC_Online = ?', 1);
                $children = $this->_db->fetchAll($qry);
                if (!empty($children))
                {
                    $this->_level++;
                    $menu = $this->_getTree ($children, $menu, $langId);
                }

            }
            $catalog['child'][] = $menu;
        }

        if ($this->_level > 1){
            $this->_level--;
        }
        $this->_link = array_slice($this->_link,0,  $this->_level);

        return $catalog;
    }

    /**
     * Retrieve all the id for each category related to $id parameter.
     * @param int $langId
     * @param int $id Category parent tot start the search.
     * @return array
     */
    public function getChildrenId($langId = null, $id = null)
    {
        $list = $children = $data = array();
        $this->getAll($langId, false);
        if ($id > 0){
            $data = $this->getLastChild($id, $langId);
        }else{
            $data = $this->_db->fetchAll($this->_query);
        }

        foreach($data as $category){
            if (empty($category) && $id > 0){
                $category = $this->populate($id, Zend_Registry::get('languageID'));
            }
            $list[$category[$this->_dataId]] = $category[$this->_foreignKey];
            if (array_key_exists($category[$this->_foreignKey], $list)){
                unset($list[$category[$this->_foreignKey]]);
            }
        }
        $children = array_keys($list);

        return $children;
    }

    /**
     * Build a list of the categories above the $id parameter.
     * @param int $chilId   Category id to start search.
     * @param int $langId
     * @param array $parents    Previous parents found.
     * @return array
     */
    public function getParents($chilId, $langId, $parents = array()){
        $category = $this->populate($chilId, $langId);
        if ($category[$this->_foreignKey] > 0){
            array_unshift($parents, $category[$this->_valurlField]);
            $parents = $this->getParents($category[$this->_foreignKey], $langId, $parents);
        }else{
            array_unshift($parents, $category[$this->_valurlField]);
        }

        return $parents;
    }

    /**
     * Find the last child for the category ($id)
     * @param int $id   Category id to start the search.
     * @param int $langId
     * @param array $data Previous child found.
     * @return array
     */
    public function getLastChild($id = 0, $langId = null, $data = array())
    {
        $this->getAll($langId, false);
        if ($id > 0){
            $this->_query->where($this->_foreignKey . ' = ?', $id, 0);
        }
        $tmp = $this->_db->fetchAll($this->_query);
        if (empty($tmp)){
            $this->child[] = $data;
        }else{
            foreach($tmp as $category){
                $this->getLastChild($category[$this->_dataId], $langId, $category);
            }
        }
        return $this->child;
    }
}