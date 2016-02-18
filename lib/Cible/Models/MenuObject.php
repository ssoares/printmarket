<?php

class MenuObject {

    protected $_id;
    protected $_title;
    protected $_menuType;
    protected $_db;
    protected $_menusArray = array();
    protected $_buildOnCatalog = false;
    protected $_isSiteMap = false;
    protected $oPages = null;
    public function setIsSiteMap($val) {
        $this->_isSiteMap = $val;
    }
    public function getId() {
        return $this->_id;
    }

    public function getTitle() {
        return $this->_title;
    }
    public function getPagesObject()
    {
        return $this->oPages;
    }

    public function __construct() {
     $this->oPages = new PagesObject();
        $this->_db = Zend_Registry::get('db');
//        Cible_FunctionsGeneral::log(get_class(), debug_backtrace());
    }

    public function initMenu($menu)
    {
        if (is_numeric($menu))
            $this->loadMenuDetailsByID($menu);
        else if (is_string($menu)) {
            $this->loadMenuDetailsByName($menu);

            if ($this->_id == null)
                $this->loadMenuDetailsByID($menu);

            if ($this->_id == null)
                Throw new Exception("Could load the requested menu $menu neither by ID nor by name");
        }
        return $this;
    }

    private function loadMenuDetailsByName($menu) {

        $row = $this->_db->FetchRow('SELECT M_ID, M_MenuType, M_Title FROM Menus WHERE M_Title = ?', $menu);

        if (!empty($row['M_ID'])) {
            $this->_id = $row['M_ID'];
            $this->_title = $row['M_Title'];
            $this->_menuType = $row['M_MenuType'];
        } else {
            Throw new Exception("Menu $menu was not found");
        }
    }

    private function loadMenuDetailsByID($id) {

        $row = $this->_db->FetchRow('SELECT M_ID, M_MenuType, M_Title FROM Menus WHERE M_ID = ?', $id);

        if (!empty($row['M_ID'])) {
            $this->_id = $row['M_ID'];
            $this->_title = $row['M_Title'];
            $this->_menuType = $row['M_MenuType'];
        } else {
            Throw new Exception("Menu with ID: $id was not found");
        }
    }

    public function populate($parent = 0, $extranet = false) {
        $menu = $this->retrieveChildItems($parent, $extranet);

        return $menu;
    }

    private function retrieveChildItems($parentId, $extranet = false) {
        $tmp_menu = array();

        $menuItemObject = new MenuItem();

        $select = $menuItemObject->select()
                ->setIntegrityCheck(false)
                ->from('MenuItemData')
                ->joinInner('MenuItemIndex', 'MID_ID = MII_MenuItemDataID')
                ->where('MID_MenuID = ?', $this->_id)
                ->where('MID_ParentID = ?', $parentId)
                ->order('MID_Position');

        if ($extranet){
            $langId = Cible_Controller_Action::getDefaultEditLanguage();
        }else{
            $langId = Zend_Registry::get('languageID');
            $select->where('MID_Show = ?', 1);
        }
        $select->where('MII_LanguageID = ?', $langId);
        if (Zend_Registry::isRegistered('user')) {
            $user = Zend_Registry::get('user');
            if (!$user && SESSIONNAME == 'application')
                $select->where('MID_Secured = ?', 0);
        }

        $menuItems = $menuItemObject->fetchAll($select)->toArray();
        foreach ($menuItems as $item) {
            $menu_item = array();
            if ($item['MII_PageID'] > 0 && !$extranet) {
                $page = $this->oPages->populate($item['MII_PageID'], $langId);
                if ((isset($page['PI_Status'])) && ($page['PI_Status'] > 0))
                    $menu_item = array(
                        'ID' => $item['MID_ID'],
                        'MID_Show' => $item['MID_Show'],
                        'MID_Show_Sitemap' => $item['MID_Show_Sitemap'],
                        'MID_FontIcon' => $item['MID_FontIcon'],
                        'Title' => $item['MII_Title'],
                        'Link' => $item['MII_Link'],
                        'PageID' => $item['MII_PageID'],
                        'Style' => $item['MID_Style'],
                        'menuImage' => $item['MID_Image'],
                        'loadImage' => $item['MID_loadImage'],
                        'menuImgAndTitle' => $item['MID_ImgAndTitle'],
                        'Placeholder' => $item['MII_Placeholder']
                    );
            } else
                $menu_item = array(
                    'ID' => $item['MID_ID'],
                    'MID_Show' => $item['MID_Show'],
                    'MID_Show_Sitemap' => $item['MID_Show_Sitemap'],
                    'MID_FontIcon' => $item['MID_FontIcon'],
                    'Title' => $item['MII_Title'],
                    'Link' => $item['MII_Link'],
                    'PageID' => $item['MII_PageID'],
                    'Style' => $item['MID_Style'],
                    'menuImage' => $item['MID_Image'],
                    'loadImage' => $item['MID_loadImage'],
                    'menuImgAndTitle' => $item['MID_ImgAndTitle'],
                    'Placeholder' => $item['MII_Placeholder']
                );
            $submenu = $this->retrieveChildItems($item['MID_ID'], $extranet);
            if (preg_match('/useCatalog/', $item['MID_Style']) && ($this->_buildOnCatalog || SESSIONNAME == 'extranet')) {
                $menu_item['Placeholder'] = 0;
                $oCatalog = new CatalogObject();
                $buildOnObj = $oCatalog->getBuildSubMenuOn();
                $collections = new $buildOnObj();
                $catalogMenu = $collections->buildCatalogMenu($menu_item, array('nesting' => 1));

                $submenuCatalog = $catalogMenu['child'];
                $submenu = array_merge($submenuCatalog, $submenu);
            }


            if ($submenu) {
                $menu_item['child'] = $submenu;
            }
            if (!empty($menu_item))
                array_push($tmp_menu, $menu_item);
        }

        return $tmp_menu;
    }

    public static function getAllMenuTitles($siteType = 's') {

        $db = Zend_Registry::get('db');

        $rows = $db->FetchAll('SELECT M_ID, M_Title, M_MenuType, M_BgColor FROM Menus WHERE M_SiteType = "' . $siteType . '" order by M_ID');

        $tmp = array();

        foreach ($rows as $row) {
            array_push($tmp, array(
                'ID' => $row['M_ID'],
                'Title' => $row['M_Title'],
                'Type' => $row['M_MenuType'],
                'BgColor' => $row['M_BgColor']
            ));
        }

        return $tmp;
    }

    public static function getAllMenuList($section = '', $sitemap = false) {

        $db = Zend_Registry::get('db');
        $select = $db->select()
                ->from('Menus', array(
                    'M_ID',
                    'M_Title',
                    'M_MenuType',
                    'M_BgColor',
                    'M_Section',
                    'M_Seq',
                    'M_ShowSitemap'))
                ->order('M_Seq');

        if (!empty($section))
            $select->where('M_Section = ?', $section);

        if (!empty($section))
            $select->where('M_Section = ?', $section);


        if ($sitemap)
            $rows = self::_buildSitemapList($select);
        else
            $rows = $db->fetchAll($select);

        return $rows;
    }

    private static function _buildSitemapList(Zend_Db_Select $select) {
        $data = array();
        $db = Zend_Registry::get('db');

        $select->where('M_ShowSitemap = ?', 1);

        $row = $db->fetchAll($select);

        foreach ($row as $key => $value) {
            array_push($data, $value['M_ID']);
        }

        return $data;
    }

    public function addItem($parentID, $item) {

        $position = $this->_db->fetchCol("SELECT count(*) FROM MenuItemData WHERE MID_ParentID = '$parentID' AND MID_MenuID = '{$this->_id}'");
        $position = $position[0];

        $menuData = new MenuItem();
        $_data = $menuData->createRow();
        $_data->setFromArray(array(
            'MID_MenuID' => $this->_id,
            'MID_ParentID' => $parentID,
            'MID_Position' => $position,
            'MID_Secured' => $item['menuItemSecured'],
            'MID_Image' => $item['menuImage'],
            'MID_loadImage' => $item['loadImage'],
            'MID_Show' => $item['MID_Show'],
            'MID_FontIcon' => isset($item['MID_FontIcon'])?$item['MID_FontIcon']:'',
            'MID_Show_Sitemap' => $item['MID_Show_Sitemap'],
            'MID_ImgAndTitle' => $item['menuImgAndTitle'],
            'MID_Style' => $item['Style']
                )
        );

        $_data->save();
        $_dataId = $this->_db->lastInsertId();

        $menuIndex = new MenuItemIndex();
        $_index = $menuIndex->createRow();
        $_index->setFromArray(
                array(
                    'MII_LanguageID' => $item['languageID'],
                    'MII_MenuItemDataID' => $_dataId,
                    'MII_Title' => $item['Title'],
                    'MII_Link' => !empty($item['Link']) ? $item['Link'] : '',
                    'MII_PageID' => !empty($item['PageID']) ? $item['PageID'] : '-1',
                    'MII_Placeholder' => $item['Placeholder']
                )
        );

        $_index->save();

        return $_dataId;
    }

    public function updateItem($parentID, $item) {

        $menuItemData = new MenuItem();
        $menuIndex = new MenuItemIndex();

        $menuItemData->update(
                array(
            'MID_Style' => $item['Style'],
            'MID_Show' => $item['MID_Show'],
            'MID_Show_Sitemap' => $item['MID_Show_Sitemap'],
            'MID_FontIcon' => $item['MID_FontIcon'],
            'MID_Secured' => $item['menuItemSecured'],
            'MID_Image' => $item['menuImage'],
            'MID_loadImage' => $item['loadImage'],
            'MID_ImgAndTitle' => $item['menuImgAndTitle']
                ), $this->_db->quoteInto('MID_ID = ?', $parentID)
        );

        $where = array();
        $where[] = $this->_db->quoteInto('MII_MenuItemDataID = ?', $parentID);
        $where[] = $this->_db->quoteInto('MII_LanguageID = ?', $item['languageID']);

        $row = $menuIndex->fetchRow($where);
        if ($row) {
//              $menuItemData->update(
//                  array('MID_Style' => $item['Style']),
//                  $this->_db->quoteInto('MID_ID = ?', $parentID)
//                  );
            $menuIndex->update(
                    array(
                'MII_Title' => $item['Title'],
                'MII_Link' => !empty($item['Link']) ? $item['Link'] : '',
                'MII_PageID' => !empty($item['PageID']) ? $item['PageID'] : '-1',
                'MII_Placeholder' => $item['Placeholder']
                    ), $where
            );
        } else {
            $menuIndex->insert(array(
                'MII_Title' => $item['Title'],
                'MII_Link' => !empty($item['Link']) ? $item['Link'] : '',
                'MII_PageID' => !empty($item['PageID']) ? $item['PageID'] : '-1',
                'MII_Placeholder' => $item['Placeholder'],
                'MII_MenuItemDataID' => $parentID,
                'MII_LanguageID' => $item['languageID']
            ));
        }
    }

    public function updatePositions($positions) {
        foreach ($positions as $position => $object) {
            $this->updatePosition(0, $position, $object);
        }
    }

    private function updatePosition($parentID, $pos, $obj) {

        $db = $this->_db;

        $db->update('MenuItemData', array(
            'MID_ParentID' => $parentID,
            'MID_Position' => $pos
                ), "MID_ID = {$obj['id']}"
        );

        if (!empty($obj['children'])) {
            foreach ($obj['children'] as $position => $object) {
                $this->updatePosition($obj['id'], $position, $object);
            }
        }
    }

    public function autogenerateFromId($id, $recursive = false) {
        $db = $this->_db;

        // if $id is 0, it means we're generating the entire menu from the structure's tree
        if ($id == 0) {

            $parentId = 0;
        }
        // if $id's greater than 0, it means we're generating from a menu item
        else if ($id > 0) {
            $row = $db->fetchRow('SELECT MII_PageID FROM MenuItemIndex WHERE MII_MenuItemDataID = ?', $id);

            if ($row['MII_PageID'] == "-1")
                return false;


            $parentId = $row['MII_PageID'];
        }

        // $id contains the ID of the menu item to add to.
        // $page_id contains the ID of the page to read children from
        $this->autogenerateMenuItemFromPageId($id, $parentId, $recursive);
    }

    private function autogenerateMenuItemFromPageId($itemId, $parentId, $recursive = false) {

        $pageObject = new Pages();

        $select = $pageObject->select()
                ->setIntegrityCheck(false)
                ->from('Pages')
                ->joinInner('PagesIndex', 'PI_PageID = P_ID')
                ->where('PI_LanguageID = ?', Zend_Registry::get('languageID'))
                ->where('P_ParentID = ?', $parentId)
//              ->where('P_SiteType = ?', $_SESSION['siteType'])
                ->order('P_Position');


        $pages = $pageObject->fetchAll($select)->toArray();

        foreach ($pages as $item) {

            $page_id = $item['P_ID'];

            $position = $this->_db->fetchCol("SELECT count(*) FROM MenuItemData WHERE MID_ParentID = '$itemId' AND MID_MenuID = '{$this->_id}'");
            $position = $position[0];

            $langs = Cible_FunctionsGeneral::getAllLanguage();

            foreach ($langs as $lang) {
                $page_details = Cible_FunctionsPages::getPageDetails($page_id, $lang['L_ID']);
                $page_title = !empty($page_details['PI_PageTitle']) ? $page_details['PI_PageTitle'] : null;

                if ($page_title != null) {

                    $found_status = $this->autogenerate_checkIfAlreadyExists($itemId, $page_id, $lang['L_ID']);

                    if (!$found_status['data']) {

                        $menuData = new MenuItem();
                        $_data = $menuData->createRow();
                        $_data->setFromArray(array(
                            'MID_MenuID' => $this->_id,
                            'MID_ParentID' => $itemId,
                            'MID_Position' => $position
                                )
                        );

                        $_data->save();
                        $_dataId = $this->_db->lastInsertId();

                        $menuIndex = new MenuItemIndex();
                        $_index = $menuIndex->createRow();
                        $_index->setFromArray(
                                array(
                                    'MII_LanguageID' => $item['PI_LanguageID'],
                                    'MII_MenuItemDataID' => $_dataId,
                                    'MII_Title' => $page_title,
                                    'MII_Link' => '',
                                    'MII_PageID' => $page_id
                                )
                        );

                        $_index->save();
                    } else if ($found_status['data'] && !$found_status['lang']) {

                        $_dataId = $found_status['dataID'];
                        $menuIndex = new MenuItemIndex();
                        $_index = $menuIndex->createRow();
                        $_index->setFromArray(
                                array(
                                    'MII_LanguageID' => $lang['L_ID'],
                                    'MII_MenuItemDataID' => $_dataId,
                                    'MII_Title' => $page_title,
                                    'MII_Link' => '',
                                    'MII_PageID' => $page_id
                                )
                        );

                        $_index->save();
                    }
                }
            }

            if ($_dataId != -1 && $recursive)
                $this->autogenerateMenuItemFromPageId($_dataId, $page_id, $recursive);
        }
    }

    private function autogenerate_checkIfAlreadyExists($itemId, $pageId, $lang) {

        $menuItem = new MenuItem();
        $select = $menuItem->select();
        $select->from('MenuItemData')
                ->setIntegrityCheck(false)
                ->joinLeft('MenuItemIndex', 'MID_ID = MII_MenuItemDataID', array('MII_Title', 'MII_LanguageID', 'MII_Link', 'MII_PageID'))
                ->where('MID_MenuID = ?', $this->_id);

        $rows = $menuItem->fetchAll($select);

        $dataFound = false;
        $langFound = false;
        $dataID = -1;

        foreach ($rows as $row) {

            if ($row['MID_ParentID'] == $itemId && $row['MII_PageID'] == $pageId && $row['MII_LanguageID'] == $lang) {

                $dataFound = true;
                $langFound = true;
                break;
            } else if ($row['MID_ParentID'] == $itemId && $row['MII_PageID'] == $pageId) {

                $dataFound = true;
                $dataID = $row['MID_ID'];
            }
        }

        return array(
            'data' => $dataFound,
            'lang' => $langFound,
            'dataID' => $dataID
        );
    }

    public function loadItem($id) {

        $menuItem = new MenuItem();
        $select = $menuItem->select();
        $select->from('MenuItemData', array('MID_Style', 'MID_Secured', 'MID_loadImage', 'MID_Show', 'MID_Show_Sitemap', 'MID_Image', 'MID_ImgAndTitle', 'MID_FontIcon'))
                ->setIntegrityCheck(false)
                ->joinInner('MenuItemIndex', 'MID_ID = MII_MenuItemDataID', array('MII_Title', 'MII_Link', 'MII_PageID', 'MII_Placeholder'))
                ->where('MII_LanguageID = ?', Zend_registry::get('currentEditLanguage'))
                ->where('MID_ID = ?', $id);

        $row = $menuItem->fetchRow($select);

        if ($row) {
            $isPage = $row['MII_PageID'] != -1;
            $menuItemType = 'page';

            if ($row['MII_Placeholder'])
                $menuItemType = 'placeholder';

            if (!empty($row['MII_Link']))
                $menuItemType = 'external';

            $tmp = array(
                'MenuTitle' => $row['MII_Title'] != '' ? $row['MII_Title'] : '',
                'MenuLink' => $row['MII_Link'] != '' ? $row['MII_Link'] : '',
                'menuItemSecured' => $row['MID_Secured'],
                'menuImage' => $row['MID_Image'],
                'loadImage' => $row['MID_loadImage'],
                'MID_Show' => $row['MID_Show'],
                'MID_Show_Sitemap' => $row['MID_Show_Sitemap'],
                'MID_FontIcon' => $row['MID_FontIcon'],
                'menuImgAndTitle' => $row['MID_ImgAndTitle'],
                'menuItemType' => $menuItemType,
                'pagePicker' => $isPage ? $row['MII_PageID'] : '',
                'ControllerName' => $isPage ? $this->getControllerNameByID($row['MII_PageID']) : '',
                'MenuTitleStyle' => $row['MID_Style']
            );
        } else {
            $tmp = array(
                'MenuTitle' => '',
                'MenuLink' => '',
                'menuItemSecured' => '',
                'menuImage' => '',
                'loadImage' => '',
                'menuImgAndTitle' => '',
                'MenuType' => 'external',
                'pagePicker' => '',
                'ControllerName' => '',
                'MenuTitleStyle' => '',
                'MID_FontIcon' => ''
            );
        }

        return $tmp;
    }

    public function deleteItem($id) {
        $db = $this->_db;

        $db->delete('MenuItemIndex', "MII_MenuItemDataID = '$id'");
        $db->delete('MenuItemData', "MID_ID = '$id'");

        $sub_page_ids = $db->fetchAll("SELECT MID_ID FROM MenuItemData WHERE MID_ParentID = '$id'");

        foreach ($sub_page_ids as $pageId) {
            $this->deleteItemsAndChildren($pageId['MID_ID']);
        }
    }

    private function deleteItemsAndChildren($id) {
        $db = $this->_db;

        $db->delete('MenuItemIndex', "MII_MenuItemDataID = '$id'");
        $db->delete('MenuItemData', "MID_ID = '$id'");

        $sub_page_ids = $db->fetchAll("SELECT MID_ID FROM MenuItemData WHERE MID_ParentID = '$id'");

        foreach ($sub_page_ids as $pageId) {
            $this->deleteItemsAndChildren($pageId['MID_ID']);
        }
    }

    private function getControllerNameByID($pageId, $lang = null) {
        if ($lang == null)
            $lang = Zend_Registry::get('currentEditLanguage');

        $page_index = $this->_db->fetchRow('SELECT PI_PageIndex FROM PagesIndex WHERE PI_PageID = ? AND PI_LanguageID = ?', array($pageId, $lang));

        return "/{$page_index['PI_PageIndex']}/";
    }

    public function getMenuItemById($menuItemId) {
        $lang = Zend_Registry::get('languageID');

        $db = $this->_db;
        $select = $db->select()
                ->from('MenuItemData')
                ->joinLeft('MenuItemIndex', 'MenuItemData.MID_ID = MenuItemIndex.MII_MenuItemDataID')
                ->where('MenuItemIndex.MII_LanguageID = ?', $lang)
                ->where('MenuItemData.MID_ID = ?', $menuItemId);


        $row = $db->fetchRow($select);

        return $row;
    }

    public function buildFooterMenus($itemId = 0) {
        $footerMenuArray = array();


        foreach ($this->_menusArray as $item) {
            $this->_id = $item['M_ID'];
            $footerMenuArray[] = $this->retrieveChildItems($itemId);
        }
        return $footerMenuArray;
    }

    /**
     * Return data for the selected menu item
     *
     * @param int    $pageId Id of the page linlked to this menu.
     * @param string $string Title of the menu.
     *
     * @return array
     */
    public function getMenuItemByPageId($pageId = null, $string = "") {
        $lang = Zend_Registry::get('languageID');

        $db = $this->_db;
        $select = $db->select()
                ->from('MenuItemData')
                ->joinLeft('MenuItemIndex', 'MenuItemData.MID_ID = MenuItemIndex.MII_MenuItemDataID')
                ->where('MenuItemIndex.MII_LanguageID = ?', $lang)
                ->where('MenuItemData.MID_MenuID =?', $this->_id);

        if ($pageId != null)
            $select->where('MenuItemIndex.MII_PageID = ?', $pageId);
        if (!empty($string))
            $select->where('MII_Title LIKE ?', '%' . $string . '%');

        $row = $db->fetchRow($select);

        return $row;
    }

    public static function getAllMenuItemForSiteMapRobots($lang) {
        $db = Zend_Registry::get('db');
        $select = $db->select('distinct')
                ->distinct()
                ->from('MenuItemData', array(''))
                ->joinLeft('MenuItemIndex', 'MenuItemData.MID_ID = MenuItemIndex.MII_MenuItemDataID', array(''))
                ->joinLeft('PagesIndex', 'PI_PageID = MII_PageID', array('PI_PageIndex'))
                ->where('MID_Show = 1')
                ->where('MID_Show_Sitemap=1')
                ->where('MID_Secured=0')
                ->where('PI_LanguageID = ?', $lang)
                ->where('MenuItemIndex.MII_LanguageID = ?', $lang);

        $rows = $db->fetchAll($select);
        return $rows;
    }

}
