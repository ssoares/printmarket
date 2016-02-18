<?php

abstract class Cible_FunctionsPages extends DataObject
{
    public static function getPageDetails($PageID, $langId = null){
        $langId = is_null($langId) ? Cible_Controller_Action::getDefaultEditLanguage() : $langId;
        $Pages = new PagesIndex();
        $Select = $Pages->select()
                        ->setIntegrityCheck(false)
                        ->from('Pages')
                        ->joinLeft('PagesIndex','Pages.P_ID = PagesIndex.PI_PageID')
                        ->joinLeft('Views', 'Pages.P_ViewID = Views.V_ID')
                        ->where('PagesIndex.PI_LanguageID = ?', $langId)
                        ->where('PagesIndex.PI_PageID= ?', $PageID);


        $row = $Pages->fetchRow($Select);

        if( empty( $row ) ){

            $Pages = new Pages();
            $Select = $Pages->select()
                            ->where('P_ID = ?', $PageID);

            $row = $Pages->fetchRow($Select);
        }

        return $row;
    }

    public static function getMenuDetails($menuId, $langId = null, $menuTitle){
        $langId = is_null($langId) ? Zend_Registry::get('currentEditLanguage') : $langId;
        if(extension_loaded('apc')){
            $menu = apc_exists('oMenu') ? apc_fetch('oMenu') : new MenuObject();
        }else{
            $menu = new MenuObject();
        }
        $menu->initMenu($menuTitle);
        $menuItem = $menu->getMenuItemById($menuId);

        return $menuItem;
    }

    /**
     * Fetch menuID from menu.
     *
     * @param int $menuID
     * @param int $pageId
     *
     * @return array
     */
    public static function findMenuID($menuID,$pageId)
    {
        $lang  = Zend_Registry::get("languageID");

        $parentArray = new Pages();
        $select = $parentArray->select()
        ->setIntegrityCheck(false)
        ->from('MenuItemIndex')
            ->join('MenuItemData', 'MID_ID = MII_MenuItemDataID')
            ->where('MII_PageID = ?', $pageId)
            ->where('MID_MenuID = ?',$menuID)
            ->where('MII_LanguageID = ?', $lang);

        return $parentArray->fetchRow($select);
    }

    public static function getPageNameByID($pageId, $lang = null, $isSitemap = false){
          if( $lang == null )
            $lang = Zend_Registry::get('languageID');

          $db = Zend_Registry::get("db");
          $select = $db->select()
              ->from('Pages', array())
              ->joinLeft('PagesIndex', 'P_ID = PI_PageID', 'PI_PageIndex')
              ->where('PI_PageID = ?', $pageId)
              ->where('PI_Status = ?', 1)
              ->where('PI_LanguageID = ?', $lang);
          if ($isSitemap)
              $select->where('P_ShowSiteMap = ?', 1);

          $page_index = $db->fetchRow($select);

          return $page_index['PI_PageIndex'];
    }

    public static function getPageLinkByID($pageId, $lang = null){
          $page = self::getPageNameByID($pageId, $lang);

          $baseUrl = Zend_Registry::get('baseUrl');
          return "$baseUrl/$page";
    }

    public static function getActionNameByLang($actionName, $lang = null){

          if( is_null($lang) )
            $lang = Zend_Registry::get('languageID');

          $db = Zend_Registry::get("db");
          $viewID = $db->fetchOne("SELECT MV_ID FROM ModuleViews WHERE MV_Name = '{$actionName}'");

          return $db->fetchOne("SELECT MVI_ActionName FROM ModuleViewsIndex WHERE MVI_ModuleViewsID = '{$viewID}' AND MVI_LanguageID = '{$lang}'");
      }

    public static function getPageViewDetails($pageID){
        $page = new Pages();
        $page_select = $page->select()->setIntegrityCheck(false);
        $page_select->from('Pages')
                    ->join('Views', 'Pages.P_ViewID = Views.V_ID')
                    ->where('P_ID = ?', $pageID);

        return $page->fetchRow($page_select)->toArray();
    }

    public static function getLayoutPath($id){
        $db = Zend_Registry::get('db');
        $select = $db->select();

        $select->from('Pages',array())
               ->joinLeft('Layouts', 'Pages.P_LayoutID = Layouts.L_ID', array('L_Path'))
               ->where('Pages.P_ID = ?', $id);

       // echo $select;
        //exit;
        return $db->fetchOne($select);
    }

    public static function getAvailableLayouts($siteType = 's'){
        $db = Zend_Registry::get('db');

        return $db->fetchAll('SELECT * FROM Layouts WHERE L_SiteType = "' . $siteType . '"');
    }

    public static function getAvailableTemplates($siteType = 's'){
        $db = Zend_Registry::get('db');

        return $db->fetchAll('SELECT * FROM Views WHERE V_SiteType = "' . $siteType . '" AND V_ZoneCount > 0 ORDER BY V_ID');
    }
    public static function getAvailableThemes($siteType = 's'){
        $db = Zend_Registry::get('db');

        return $db->fetchAll('SELECT * FROM Page_Themes WHERE PT_SiteType = "' . $siteType . '" ORDER BY PT_Name');
    }

    public static function getAllPositions($ParentID){
        $Positions  = Zend_Registry::get("db");
        $Select     = $Positions->select()
                                ->from('PagesIndex')
                                ->join('Pages','Pages.P_ID = PagesIndex.PI_PageID')
                                ->where('P_ParentID = ?', (int)$ParentID)
                                ->where('PI_LanguageID = ?', Zend_Registry::get("languageID"))
                                ->where('P_Home = ?', 0)
                                ->order('P_Position');

        return $Positions->fetchAll($Select);
    }

    public static function fillSelectPosition($Form, $PositionsArray, $Action){
        $TotalPos = count($PositionsArray);
        if ($TotalPos > 0){
            $Cpt=0;
            foreach ($PositionsArray as $Pos){
                if($Cpt == 0){
                    $Form->P_Position->addMultiOption($Pos["P_Position"], 'PremiÃ¨re position');
                    if ($Cpt == $TotalPos-1 && $Action == "add"){
                        $Form->P_Position->addMultiOption($Pos["P_Position"]+1, 'DerniÃ¨re position');
                    }
                }
                elseif($Cpt == $TotalPos-1){
                    if($Action == "add"){
                        $Form->P_Position->addMultiOption($Pos["P_Position"], str_replace('%TEXT%',$PositionsArray[$Cpt-1]["PI_PageTitle"],Cible_Translation::getCibleText("form_select_option_position_below")));
                        $Form->P_Position->addMultiOption($Pos["P_Position"]+1, 'DerniÃ¨re position');
                    }
                    else{
                        $Form->P_Position->addMultiOption($Pos["P_Position"], 'DerniÃ¨re position');
                    }
                }
                else{
                    $Form->P_Position->addMultiOption($Pos["P_Position"], str_replace('%TEXT%',$PositionsArray[$Cpt-1]["PI_PageTitle"],Cible_Translation::getCibleText("form_select_option_position_below")));
                }
                $Cpt++;
            }
        }
        else{
           $Form->P_Position->addMultiOption('1', 'PremiÃ¨re position');
        }
        return $Form;
    }

    public static function fillSelectLayouts($Form, $layouts){

        foreach($layouts as $layout){
            $Form->P_LayoutID->addMultiOption( $layout['L_ID'] , Cible_Translation::getCibleText("form_select_option_pageLayouts_".$layout['L_ID']));
        }

        return $Form;
    }

    public static function fillSelectTemplates($Form, $templates){

        foreach($templates as $template){
            $Form->P_ViewID->addMultiOption( $template['V_ID'] , Cible_Translation::getCibleText("form_select_option_zoneViews_".$template['V_ID']));
        }

        return $Form;
    }
    public static function fillSelectThemes($Form, $themes){

        $Form->P_ThemeID->addMultiOption( 0 , Cible_Translation::getCibleText("form_select_default_label"));
        foreach($themes as $theme){
            $Form->P_ThemeID->addMultiOption( $theme['PT_ID'] , Cible_Translation::getCibleText("form_select_option_theme_".$theme['PT_Name']));
        }

        return $Form;
    }

    public static function deleteAllChildPage($ParentID){
        if($ParentID <> 0){
            $Pages = new Pages();
            $Select = $Pages->select()
                            ->where("P_ParentID = ?", $ParentID);

            $PageArray = $Pages->fetchAll($Select);
            foreach($PageArray as $Page){
                $PageID = $Page["P_ID"];
                Cible_FunctionsPages::deleteAllChildPage($PageID);
                Cible_FunctionsPages::deleteAllBlock($PageID);

                $pageSelect = new PagesIndex();
                $select = $pageSelect->select()
                ->where('PI_PageID = ?',$PageID);
                $pageData = $pageSelect->fetchAll($select)->toArray();

                foreach($pageData as $page){
                    $indexData['moduleID']  = 0;
                    $indexData['contentID'] = $PageID;
                    $indexData['languageID'] = $page['PI_LanguageID'];
                    $indexData['action'] = 'delete';
                    Cible_FunctionsIndexation::indexation($indexData);
                }

                $PageObj = new Pages();
                $Where = 'P_ID = ' . $PageID;
                $PageObj->delete($Where);

                $PageIndex = new PagesIndex();
                $Where = 'PI_PageID = ' . $PageID;
                $PageIndex->delete($Where);

                //echo("DELETE PAGE : ".$Page["P_ID"]."<br/>");
                //echo("DELETE PAGEINDEX : ".$Page["P_ID"]."<br/>");
            }
        }
    }

    public static function deleteAllBlock($PageID){
        $textSelect = new Blocks();
        $select = $textSelect->select()->setIntegrityCheck(false)
        ->from('Blocks')
        ->where('B_PageID = ?',$PageID)
        ->join('TextData', 'TD_BlockID = B_ID');
        $textData = $textSelect->fetchAll($select);

        foreach($textData as $text){
            $indexData['moduleID']  = $text['B_ModuleID'];
            $indexData['contentID'] = $text['TD_ID'];
            $indexData['languageID'] = $text['TD_LanguageID'];
            $indexData['action'] = 'delete';
            Cible_FunctionsIndexation::indexation($indexData);
        }


        $Blocks = new Blocks();
        $Where  = "B_PageID = " . $PageID;
        $Blocks->delete($Where);
    }

    public static function findChildPage($ParentID, $lang = null, $type = 's'){
        if( $lang == null)
            $lang = Zend_Registry::get("languageID");

        if (Zend_Registry::isRegistered('currentSite'))
            $siteSrc = Zend_Registry::get('currentSite');

        if (Zend_Registry::isRegistered('pageSiteSrc'))
        {
            $var = Zend_Registry::get('pageSiteSrc');
            if (!empty($var))
                $siteSrc = Zend_Registry::get('pageSiteSrc');
        }
        if (!empty($siteSrc))
        {
            $dbs = Zend_Registry::get('dbs');
            $dbAdapter = $dbs->getDb($siteSrc);
            $childArray = new Pages(array('db' => $dbAdapter));
        }
        else
        $childArray = new Pages();
        $select = $childArray->select()
        ->setIntegrityCheck(false)
        ->from('Pages')
        ->join('PagesIndex','Pages.P_ID = PagesIndex.PI_PageID')
        ->where('Pages.P_ParentID = ?', $ParentID)
        ->where('PagesIndex.PI_LanguageID = ?', $lang)
        ->where('Pages.P_SiteType = ?', $type)
        ->order('Pages.P_Position');

        return $childArray->fetchAll($select);
    }
    /**
     * Fecth data from the parent page for front-end usage.
     *
     * @param int $pageId
     *
     * @return array
     */
    public static function findParentPageID($pageId)
    {
        $lang        = Zend_Registry::get("languageID");
        $parentArray = new Pages();

        $select = $parentArray->select()
        ->setIntegrityCheck(false)
        ->from('Pages')
            ->joinLeft('PagesIndex', 'PI_PageID = P_ID')
            ->where('Pages.P_ID = ?', $pageId)
            ->where('PagesIndex.PI_LanguageID = ?', $lang);

        return $parentArray->fetchRow($select);
    }

    public static function getAllPagesDetailsArray($ParentID = 0, $lang = null, $type = 's')
    {
        $pages = Cible_FunctionsPages::findChildPage($ParentID, $lang, $type)->toArray();

        $i=0;
        foreach ($pages as $page){
            $pages[$i]['child'] = Cible_FunctionsPages::getAllPagesDetailsArray($page['P_ID'], $lang, $type);
            $i++;
        }

        return $pages;

    }

    public static function buildUlLiMenu($baseUrl, $parentID = 0, $lang = null, $type = 's'){

        if($lang == null)
            $lang = Zend_Registry::get('currentEditLanguage');

        $pages_list = Cible_FunctionsPages::getAllPagesDetailsArray(0, $lang, $type);
        $_pages = array();
        $vRender = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $view = $vRender->view;
        foreach($pages_list as $page){
            $authData = $view->user;
            $authID     = $authData['EU_ID'];
            $hasAccess = true;
            $hasAccessToStructure = false;
            $hasCurrentPageAccess = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$page['P_ID'],"data", $view->isAdministrator());
            if ($hasCurrentPageAccess){
                $hasAccessToStructure = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$page['P_ID'],"structure", $view->isAdministrator());
            }
            if (!$hasCurrentPageAccess){
                $isPageAdmin = $view->aclIsAllowed('page', 'edit');
                $hasAccessToStructure = $isPageAdmin?true:false;
            }
            $hasAccess = !$hasCurrentPageAccess && !$isPageAdmin? false : true;
            if ($hasAccess){
                $pageTitle = $page['PI_PageTitle'];
                if ($page['P_Home'] == 1 || $page['P_HomeMobile']){
                    $pageTitle = Cible_Translation::getCibleText('label_homepage');
                }
                $tmp = array(
                    'ID' => $page['P_ID'],
                    'Title' => $pageTitle,
                    'onClick' => "{$baseUrl}/page/index/index/ID/{$page['P_ID']}/site/{$type}"
                );

                if( !empty($page['child']) ){
                    $tmp['child'] = self::fillULLIChildren($baseUrl, $page['child']);
                }
                array_push($_pages, $tmp);
            }
        }
        return $_pages;
    }

    /**
     * Make a query to find if a page is a child of any other page other than 0
     *
     * @param  int     $pageID if null, takes the current page ID
     * @param  int     $languageID if null, takes the current language ID
     *
     * @return bool
     */
    public static function getIfIsChildOfOtherPage($pageID=null, $languageID=null)
    {
        $pageID = is_null($pageID) ? Zend_Registry::get('pageID') : $pageID;
        $languageID = is_null($languageID) ? Zend_Registry::get('languageID') : $languageID;
        $Pages = Zend_Registry::get("db");
        $Select = $Pages->select()
                ->from('Pages')
                ->join('PagesIndex', 'PI_PageID = P_ID')
                ->where('Pages.P_ID = ?', $pageID)
                ->where('PI_LanguageID = ?', $languageID)
                ->where('Pages.P_ParentID <> ?', '0');
        $Rows = $Pages->fetchAll($Select);
        if(!empty($Rows)){
            return true;
        }
        else{
            return false;
        }
    }

    public static function fillULLIChildren($baseUrl, $children){
        $_pages = array();

        foreach($children as $page){
            $tmp = array(
                'ID' => $page['P_ID'],
                'Title' => $page['PI_PageTitle'],
                'onClick' => "{$baseUrl}/page/index/index/ID/{$page['P_ID']}"
            );

            if( !empty($page['child']) )
                $tmp['child'] = self::fillULLIChildren($baseUrl, $page['child']);

            array_push($_pages, $tmp);
        }

        return $_pages;
    }

    public static function buildBreadcrumb($pageID, $lang = null){
        if( $lang == null )
            $lang = Cible_Controller_Action::getDefaultEditLanguage();

        $_breadcrumb = array();

        while($pageID != 0){

            $details = self::getPageDetails($pageID, $lang);
            array_push($_breadcrumb, $details['PI_PageTitle']);
            $pageID = $details['P_ParentID'];
        }

        $_breadcrumb = array_reverse($_breadcrumb);

        return implode( ' > ', $_breadcrumb);
    }

    public static function buildTextBreadcrumb($pageID, $lang = null){
        if( $lang == null )
            $lang = Cible_Controller_Action::getDefaultEditLanguage();

        $_baseUrl = Zend_Registry::get('baseUrl');
        $_breadcrumb = array();
        $_first = true;

        while($pageID != 0){

            $_class = '';

            if( $_first ){
                $_first = false;
                $_class = 'current_page';
            }

            $details = self::getPageDetails($pageID, $lang);
            array_push($_breadcrumb, "<a href='{$_baseUrl}/page/index/index/ID/{$pageID}' class='{$_class}'>{$details['PI_PageTitle']}</a>");
            $pageID = $details['P_ParentID'];
        }

        $_breadcrumb = array_reverse($_breadcrumb);

        return implode( ' > ', $_breadcrumb);
    }

    public static function getHomePageDetails(){
        $pagesSelect = new Pages();
        $select = $pagesSelect->select()->setIntegrityCheck(false)
        ->from('Pages')
        ->join('PagesIndex', 'PI_PageID = P_ID')
        ->where('PI_LanguageID = ?', Zend_Registry::get('languageID'))
        ->where('P_Home = 1');

        return $pagesSelect->fetchRow($select)->toArray();
    }

    public static function buildClientBreadcrumb($pageID, $level=0, $showHome=true, $lang = null){
        if( $lang == null )
            $lang = Zend_Registry::get('languageID');

        $_baseUrl = Zend_Registry::get('baseUrl');

        $_breadcrumb = array();
        $_first = true;

        while($pageID != 0){

            $_class = '';

            if( $_first ){$_class = 'current_page';}

            $details = self::getPageDetails($pageID, $lang);

            $link = $_first ? '' : "<a href='{$_baseUrl}/{$details['PI_PageIndex']}' class='{$_class}'>{$details['PI_PageTitle']}</a>";
            array_push($_breadcrumb, $link);
            $pageID = $details['P_ParentID'];

            if( $_first ){$_first = false;}
        }

        if($showHome){
            $homeDetails = self::getHomePageDetails();

            /*if(empty($_baseUrl))
                $_baseUrl = '/';*/

            $link = "<a href='{$_baseUrl}/{$homeDetails['PI_PageIndex']}' class='{$_class}'>".strtoupper($homeDetails['PI_PageTitle'])."</a>";
            array_push($_breadcrumb, $link);
        }
        $_breadcrumb = array_reverse($_breadcrumb);

        for($i=0;$i<$level;$i++){
            array_splice($_breadcrumb,$i+1,1);
        }

        // add the > after the breadcrumb when only on item is found
        if( count($_breadcrumb) == 1 )
            return "{$_breadcrumb[0]} > ";
        else
            return implode( ' > ', $_breadcrumb);
    }

    public static function buildClientBreadcrumbMenu($selectedItemMenuID, $level=0, $menuTitle, $showHome=true, $lang = null, $options = array()){
        if( $lang == null )
            $lang = Zend_Registry::get('languageID');

        $_baseUrl = Zend_Registry::get('baseUrl');

        $_breadcrumb = array();
        $_first = true;
        $_class = '';
        $link   = '';

        while($selectedItemMenuID != 0){
            if( $_first ){$_class = 'current_page';}

            $details     = self::getMenuDetails($selectedItemMenuID, $lang, $menuTitle);
            $details['MII_Title'] = str_replace("<br>", "", $details['MII_Title']);
            $details['MII_Title'] = str_replace("<br />", "", $details['MII_Title']);

            $pageDetails = self::getPageDetails($details['MII_PageID'], $lang);
            if($details['MII_PageID'] == -1){
                $link = "<span class='current'>" . $details['MII_Title'] . "</span>";
            }else{
                $link = $_first ? "" : "<a href='{$_baseUrl}/{$pageDetails['PI_PageIndex']}' class='{$_class}'>{$details['MII_Title']}</a>";
            }
            array_push($_breadcrumb, $link);
            $selectedItemMenuID = $details['MID_ParentID'];

            if( $_first ){$_first = false; $_class = '';}
        }

        return $_breadcrumb;
    }

    public static function getSectionParentPageId($pageID, $sectionID){
        $lang = Zend_Registry::get('languageID');
        while($pageID != 0){

            $details = self::getPageDetails($pageID, $lang);

            if( $details['P_ParentID'] == $sectionID)
                return $pageID;

            $pageID = $details['P_ParentID'];
        }
    }

    public static function getPageByModule($moduleId, $viewName, $langId = null)
    {
        if (!$langId)
            $langId = Zend_Registry::get('languageID');

        $db  = Zend_Registry::get("db");

        $select = $db->select()
                ->from('Pages')
                ->distinct()
                ->joinLeft('PagesIndex', 'Pages.P_ID = PagesIndex.PI_PageID')
                ->joinLeft('Blocks', 'Blocks.B_PageID = Pages.P_ID', array())
                ->joinLeft('Modules', 'Blocks.B_ModuleID = Modules.M_ID', array())
                ->joinLeft('ModuleViews', 'Modules.M_ID = ModuleViews.MV_ModuleID', array('MV_Name'))
                ->where('Modules.M_ID = ?', $moduleId)
                ->where('ModuleViews.MV_Name = ?', $viewName)
                ->where('PagesIndex.PI_LanguageID = ?', $langId)
                ;

        $row = $db->fetchAll($select);
        if(!$row)
                return '';

        return $row;

    }

    /**
     * Find the first level menu for the page id.
     * (NB: this is to test and to custom for oher projects works for 1 level)
     *
     * @param int $pageId Id of the page
     * @param int $langId Language id, if null, the language id stored in the registry will be used
     *
     * @return array
     */
    public static function getMenuByPageId($pageId, $langId = null)
    {
        if (!$langId)
            $langId = Zend_Registry::get('languageID');

        $db  = Zend_Registry::get("db");

        $select = $db->select()
                ->from('MenuItemData')
                ->distinct()
                ->joinLeft('MenuItemIndex', 'MID_ID = MII_MenuItemDataID')
                ->where('MII_PageID = ?', $pageId)
                ->where('MII_LanguageID = ?', $langId)
                ->where('MID_ParentID = ?', 0)
                ;

        $row = $db->fetchAll($select);

        if(!$row)
                return '';

        return $row;
    }

    /**
     * Retrive the current theme. If there is none then find its parents theme.
     *
     * @param mixed $page data for the current page
     * @return string
     */
    public static function getCurrentTheme($page)
    {
        $theme = '';

        if (!empty($page['P_ThemeID']))
            $theme = self::getTheme((int)$page['P_ThemeID']);
        elseif (!empty($page['P_ParentID']))
        {
            $parentId = $page['P_ParentID'];
            $page = self::getPageDetails($parentId);
            if (!empty($page['P_ThemeID']))
                $theme = self::getTheme((int)$page['P_ThemeID']);
            else
                $theme = self::getCurrentTheme($page);
        }

        return $theme;

    }
    /**
     * Retrieve the theme folder.
     *
     * @param int|string $id Id or name
     * @return string
     */
    public static function getTheme($id)
    {
        $db = Zend_Registry::get('db');
        $query = $db->select()
            ->from('Page_Themes', array('PT_Folder'));

        if (is_numeric($id) && $id > 0){
            $query->where('PT_ID = ?', $id);
        }elseif(is_string($id) && !empty ($id)){
            $query->where('PT_Name = ?', $id);
        }

        $theme = $db->fetchOne($query);

        return $theme;
    }

    /**
     * Retrieve the image for the current page. <br />
     * If there is none then find its parents image.
     *
     * @param array $page data for the current page
     * @param bool  $sub
     * @param string $type The folder to select image [background|header]
     * @param string $default The name of the default image to set if filled.
     * @return string
     */
    public static function getPageImage($page, $sub = false, $type = 'header', $default = 'default.jpg')
    {
        $path = '';
        $params = array();
        $defaultImg = 'default/' . $default;
        $typeArray = array('background' => 'PI_ImageBackground',
            'header' => 'PI_TitleImageSrc');
        $fieldImage = $typeArray[$type];

        if (!empty($page[$fieldImage])){
            $cfg = Zend_Registry::get('config');
            $cropPrefix = $cfg->banners->source->crop->maxWidth
                . 'x' . $cfg->banners->source->crop->maxHeight . '_';
            $img = $cropPrefix . $page[$fieldImage];
            $params = array('page', $type, $img) ;
        }elseif (!empty($page['P_ParentID'])){
            $page = self::getPageDetails($page['P_ParentID']);
            $params = self::getPageImage($page, $sub, $type, $default);
        }
        if(empty ($params) && !empty($default)){
            $params = array('page', $type, $defaultImg);
        }
        if (!empty($params) && is_array($params)){
            $path = implode('/',$params);
        }else{
            $path = $params;
        }
        return $path;

    }

}
