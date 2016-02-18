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
 * @version   $Id: CatalogCategoriesObject.php 1871 2016-02-09 14:08:39Z freynolds $id
 */

/**
 * Manage data from colletion table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogCategoriesObject.php 1871 2016-02-09 14:08:39Z freynolds $id
 */
class CatalogCategoriesObject extends DataObject
{
    protected $_dataClass       = 'CatalogCategoriesData';
    protected $_indexClass      = 'CatalogCategoriesIndex';
    protected $_indexLanguageId = 'CCI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'CC_ParentId';
    protected $_titleField      = 'CCI_Name';
    protected $_valurlField     = 'CCI_ValUrl';
    protected $_orderBy         = array('CC_Seq ASC', 'CC_ID');
    protected $_query;
    protected $_addSubFolder    = true;
    protected $_name            = 'categories';
    protected $_id = 0;
    protected $_buildOnCatalog  = false;
    protected $_currentPath  = array();
    protected $_nesting         = 0;
    protected $_link  = array();
    protected $_level  = 0;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function getValUrlField()
    {
        return $this->_valurlField;
    }

    public function insert($data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::insert($data, $langId);
    }

    public function save($id, $data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::save($id, $data, $langId);
    }

    /**
     *
     * @return type
     */
    public function _categoriesSrc($addDefault = false)
    {

        $data = $this->getList();
        if (!$addDefault)
            unset($data[0]);

        return $data;
    }

    public function getTitleValue()
    {
        $title = '';
        $langId = Cible_Controller_Action::getDefaultEditLanguage();
        if ($this->_id > 0){
            $data = $this->populate($this->_id, $langId);
            $title = $data[$this->_titleField];
        }

        return $title;
    }

    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case '':
                    $data[$field] = explode(',', $values);

                    break;

                default:
                    break;
            }
        }

        return $data;
    }

    public function populate($id, $langId)
    {
        $data = $this->_formatOutputData(parent::populate($id, $langId));

        return $data;
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
                'Placeholder' => 2,
                'noHandle' => true);
            if (in_array($category[$this->_valurlField], $this->_currentPath)){
                $menu['Style'] = 'active';
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

        $this->_link = array($this->_link[0]);
        if ($this->_level > 1){
            $this->_level--;
        }
        return $catalog;
    }

    public function getList($withKey = false, $langId = null, $noDefault = false)
    {
        $list = array();
        if (is_null($langId))
            $langId = Cible_Controller_Action::getDefaultEditLanguage();

        if (!$noDefault)
            $list[''] = Cible_Translation::getCibleText('form_select_default_label');

        $temp = array();
        if ($withKey){
            $this->_list= array();
            $this->_query = $this->getAll($langId, false);
            $this->_query->where($this->_foreignKey . ' = ?', 0);
          
            $data = $this->findData(null, true);
            foreach ($data as $x => $values){
                $this->_getParents($values);
            }
            $list = $list + $this->_list;

        }else{
            $data  = $this->getAll($langId);
            foreach ($data as $values){
                $list[$values[$this->_dataId]] = $values[$this->_titleField];
            }
        }

        return $list;
    }
    /**
     * Build labels for parents of the category to display in a dropbox.
     * 
     * @param type $values  Data from the category
     * @param type $name    Names of the prvious categories.
     */
    private function _getParents($values, $name = array(), $langId = null)
    {
        if (is_null($langId)){
            $langId = Cible_Controller_Action::getDefaultEditLanguage();
        }
        $parentId = $values[$this->_dataId];
        $children = $this->findData(array($this->_foreignKey => $parentId, $this->_indexLanguageId => $langId));
    
        if (!empty($children))
        {
            $name[] = $values[$this->_titleField];
            foreach($children as $child)
            {
                $this->_getParents($child, $name, $langId);
            }
        }elseif(empty($name)){
            $this->_list[$values[$this->_dataId]] = $values[$this->_titleField];
        }else{
            $this->_list[implode(' > ', $name)][$values[$this->_dataId]] = $values[$this->_titleField];
        }
    }

    /**
     * Build the list of the category's parents to build link
     *
     * @param type $chilId
     * @param type $langId
     * @param type $parents
     * @return array
     */
    public function getParentsForLink($chilId, $langId, $parents = array()){
        $category = $this->populate($chilId, $langId);
        if ($category[$this->_foreignKey] > 0){
            array_unshift($parents, $category[$this->_valurlField]);
            $parents = $this->getParentsForLink($category[$this->_foreignKey], $langId, $parents);
        }elseif(!empty($category[$this->_valurlField])){
            array_unshift($parents, $category[$this->_valurlField]);
        }

        return $parents;
    }
}