<?php

/**
 * Call controllers associated with the page called
 *
 * The system checks all the blocks associated with the page and call proper module controllers to display content
 *
 * PHP versions 5
 *
 * LICENSE:
 *
 * @category   Controller
 * @package    Default
 * @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
 * @copyright  2009 CIBLE Solutions d'Affaires
 * @license    http://www.ciblesolutions.com
 * @version    $Id: PageController.php 1865 2016-02-04 22:29:15Z ssoares $
 */
class PageController extends Cible_Controller_Action {

    /**
     * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
     *
     * This function return the sitemap specific for this module
     *
     * @access public
     *
     * @return a string containing xml sitemap
     */
    public function siteMapAction() {

    }

    public function indexAction() {

        Zend_Registry::set('module', null);
        $Param = $this->_getParam('Param');
        $Action = $Param['action'];

        $auth = Zend_Auth::getInstance();
        $data = (array) $auth->getStorage()->read();
        if (isset($data['EU_ShowError']) && (bool) $data['EU_ShowError'] && !ini_get('display_errors')) {
            ini_set('display_errors', true);
        }

        // if user has an account and is logged
        $islogged = Cible_FunctionsGeneral::getAuthentication();
        Zend_Registry::set('user', $islogged);

        // grab the Id, language and title of the page called
        $Row = $this->_getParam('Row');
        Zend_Registry::set('pageID', $Row['PI_PageID']);

        Zend_Registry::set('languageID', $Row['PI_LanguageID']);
        Zend_Registry::set('currentUrlAction', $this->_request->getParam('action'));

        $isHome = (bool) $Row['P_Home'];
        $this->view->isHome = $isHome;
        $session = new Cible_Sessions(SESSIONNAME);
        $session->languageID = $Row['PI_LanguageID'];
        $this->view->languageId = $Row['PI_LanguageID'];

        Zend_Registry::set('altImageFirst', $Row['PI_AltPremiereImage']);
        Zend_Registry::set('languageSuffix', $Row['L_Suffix']);
        $languageSuffix = $Row['L_Suffix'];
        $this->view->locale = $Row['L_Suffix'];

        $locale = new Zend_Locale($Row['L_Local']);
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('pageTitle', $Row['PI_PageTitle']);
        Zend_Registry::set('pageIndex', $Row['PI_PageIndex']);
        $theme = Cible_FunctionsPages::getCurrentTheme($Row);
        if (empty($theme))
            $theme = Cible_FunctionsPages::getTheme('Default');
        if (empty($theme))
            $theme = $this->_config->site->defaultTheme;

        Zend_Registry::set('currentTheme', $theme);
        $this->view->assign('currentTheme', $theme);
        $this->view->assign('themeClass', str_replace('/', '-', $theme));
        if (!Zend_Registry::isRegistered('selectedItemMenuLevel'))
            Zend_Registry::set('selectedItemMenuLevel', 0);
        Zend_Registry::set('config', $this->_config);

        $absolute_web_root = Zend_Registry::get('absolute_web_root');

        // Set Meta Tags
        $this->view->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0');

        $this->view->headMeta()->appendName('author', $this->_config->info->registredName);
        $this->view->headMeta()->appendName('keywords', $Row['PI_MetaKeywords']);
        $this->view->headMeta()->appendName('robots', 'all, noodp');
        $this->view->headMeta()->appendName('og:type', 'website');
        $this->view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $this->view->placeholder('metaOther')->set($Row['PI_MetaOther']);

        $clientLogo = $this->_config->clientLogo->src;
        Zend_Registry::set('addThis', "{$absolute_web_root}/themes/default/images/{$languageSuffix}/{$clientLogo}");
        $this->view->assign('showTitle', $Row['P_ShowTitle']);
        $this->view->assign('selectedPage', $Row['PI_PageIndex']);

        // To get the parent image if there is no image on this page
        $pageBackground = $this->_rootImgPath . Cible_FunctionsPages::getPageImage($Row, 'background');
        $this->view->assign('imgBackHeader', $pageBackground);

        $pageHeader = $this->_rootImgPath . Cible_FunctionsPages::getPageImage($Row, 'header');

        if (!(bool)$this->_config->useDefaultImg){
            if (!strpos($pageHeader, 'default')){
        $this->view->assign('imgHeader', $pageHeader);
            }
        }else{
            $this->view->assign('imgHeader', $pageHeader);
        }
        //Whatever text that goes in the banners in content pages
        $this->view->assign('bannerTitle', ($Row['PI_AltPremiereImage'] != "") ? $Row['PI_AltPremiereImage'] : $Row['PI_PageTitle'] );

        // To get the parent image if there is no image on this page
        $this->view->assign('PI_Secure', $Row['PI_Secure']);
        $this->view->assign('PI_TitleImageAlt', $Row['PI_TitleImageAlt']);
        $currentPageID = $Row['PI_PageID'];

        // finds the current page layout and swap it
        $layout_file = Cible_FunctionsPages::getLayoutPath($currentPageID);
        $tplName = strtolower($Row['V_Name']);
        $view_template = $Row['V_Path'];
        $mobile = Zend_Registry::get('isMobile');
        if ($mobile) {
            $tmpViewTpl = explode('/', $view_template);
            $tmpViewTpl[1] = self::IS_MOBILE . '-' . $tmpViewTpl[1];
            $view_template = implode('/', $tmpViewTpl);
            $layout_file = str_replace('main', self::IS_MOBILE, $layout_file);
        }
        $this->view->assign('templateName', $tplName);
        $this->_helper->layout->setLayout(str_replace('.phtml', '', $layout_file));

        // put the baseurl on a registry key
        Zend_Registry::set('baseUrl', $this->getFrontController()->getBaseUrl());

        // display the page title on the website
        if (!empty($Row['PI_MetaTitle'])) {
            $this->view->headTitle($Row['PI_MetaTitle']);
            $this->view->headMeta()->appendName('DC.title', $Row['PI_MetaTitle']);
        } else {
            $this->view->headTitle($this->_config->site->title->$Row['PI_LanguageID']);
            $this->view->headTitle()->setSeparator(' > ');
            $this->view->headTitle(Zend_Registry::get("pageTitle"));
        }

        // display metadata on the website
        if (!empty($Row['PI_MetaDescription'])) {
            $this->view->metaDescription = $Row['PI_MetaDescription'];
            $this->view->headMeta()->appendName('description', $Row['PI_MetaDescription']);
        } else {
            $this->view->metaDescription = Zend_Registry::get("pageTitle");
            $this->view->headMeta()->appendName('description', Zend_Registry::get("pageTitle"));
        }
        $this->view->metaKeywords = $Row['PI_MetaKeywords'];
        $this->view->pageTitle($Row['PI_PageTitle'], null);
        $this->view->nbZones = $Row['V_ZoneCount'];

        // make a request to get all the blocks to be displayed
        $blocksData = Cible_FunctionsBlocks::getBlocks();

        // Actions to be called in the view for rendering the page's blocks
        $blocks = array();

        // for all blocks to display, call the proper controller module
        foreach ($blocksData as $Row) {
            $Module = $Row['M_MVCModuleTitle'];
            $ActionIndex = $Row['B_Action'];

            $Param['BlockID'] = $Row['B_ID'];
            $Param['secured'] = $Row['B_Secured'];
            $Param['showHeader'] = $Row['B_ShowHeader'];

            if (!isset($blocks[$Row['B_ZoneID']]))
                $blocks[$Row['B_ZoneID']] = array();

            array_push($blocks[$Row['B_ZoneID']], array(
                'action' => $ActionIndex,
                'controller' => 'index',
                'module' => $Module,
                'params' => $Param
            ));
        }

        $this->view->assign('blocks', $blocks);
        $this->view->assign('view_template', $view_template);
        $this->view->assign('currentPageID', $currentPageID);
        $this->view->assign('absolute_web_root', $absolute_web_root);

        $this->view->header = "header.phtml";
        $this->view->footer = "footer.phtml";
        $this->view->analytic = "google.analytic.phtml";
        if (!empty($session->currentSite)) {
            $tmp = '../application/layouts/partials/';
            if (file_exists($tmp . $session->currentSite . '.' . $this->view->header)) {
                $this->view->header = $session->currentSite . '.' . $this->view->header;
            }
            if (file_exists($tmp . $session->currentSite . '.' . $this->view->footer)) {
                $this->view->footer = $session->currentSite . '.' . $this->view->footer;
            }
            if (file_exists($tmp . $session->currentSite . '.' . $this->view->analytic)) {
                $this->view->analytic = $session->currentSite . '.' . $this->view->analytic;
            }
        }

        if ($this->_config->cssSpritesheet->enabled) {
            $themePath = Zend_Registry::get('fullDocumentRoot');
            $themePath .= Zend_Registry::get("www_root");
            $themePath .= $this->view->setThemePath($theme);
            $config = array(
                'folder' => $themePath . 'images/sprites',
                'outputImage' => $themePath . 'images/common/spritesheet.png',
                'outputCSS' => $themePath . 'less/custom/spritesheet.less'
            );
            Cible_CssSpritesheet::factory($config);
        }

        if ($this->_config->lessGeneration->enabled) {
            $this->view->cssFromLess();
        }

        $i = 0;
        foreach ($this->_config->themes->default->styles as $style) {
            $pathFile = $this->view->locateFile($style->filename);
            if (!is_null($style->ie_version)) {
                $this->view->headLink()->offsetSetStylesheet((200 + $i), $pathFile, $style->media, $style->ie_version);
                $this->view->headLink()->prependStylesheet($pathFile, $style->media, $style->ie_version);
            } elseif (!is_null($style->site)) {
                $this->view->headLink()->offsetSetStylesheet(-1, $pathFile, $style->media);
                $this->view->headLink()->prependStylesheet($pathFile, $style->media);
            } else {
                $this->view->headLink()->offsetSetStylesheet((200 + $i), $pathFile, $style->media);
                $this->view->headLink()->prependStylesheet($pathFile, $style->media);
            }
            $i++;
        }

//        $this->view->headLink()->offsetSetStylesheet(9999, $this->view->locateFile('print.css'), 'print');
//        $this->view->headLink()->prependStylesheet($this->view->locateFile('print.css'), 'print');
        $canonical = '';
        if (!empty($Row['PI_CanonicalLink'])){
            $canonical = $Row['PI_CanonicalLink'];
        }elseif ($isHome && Zend_Registry::get("languageID")
            == $this->_config->defaultInterfaceLanguage){
            if (strlen($this->_request->getPathInfo()) > 1){
                $canonical = $absolute_web_root;
            }else{
                $canonical = $absolute_web_root . $this->_request->getPathInfo();
            }
        }
            $this->view->headLink(array('rel' => 'canonical', 'href' => $canonical));

        //form polyfills
        $this->view->headScript()->appendFile($this->view->locateFile('bootstrap-select.min.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('bootstrap-tokenfield.min.js'));
        $this->view->headScript()->appendFile($this->view->locateFile('typeahead.bundle.min.js'));
//        $this->view->headScript()->appendFile($this->view->locateFile('sticky-placeholder.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('prettyCheckable.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.inputnumber.js', 'jquery'));
        //magnific popup
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.touchSwipe.min.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.magnific.popup.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.magnific.popup.custom.js', 'jquery'));

        $this->view->headScript()->appendFile($this->view->locateFile('jquery.googlemaps.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.toggle.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('jquery.scrolly.js', 'jquery'));

        if ($this->_config->fontController->embeded) {
            $this->view->headScript()->appendFile($this->view->locateFile('font-controller.js', 'jquery'));
            $this->view->headScript()->appendFile($this->view->locateFile('jquery.cookie.js', 'jquery'));
        }

        if ($this->_config->addthisWidget->embeded) {
            $this->view->headScript()->appendFile($this->view->locateFile('addthis_widget.js'));
        }

        if ($this->_config->bootstrap->enabled == 1) {
            $this->view->headScript()->appendFile($this->view->locateFile('bootstrap.js'));
            $this->view->headLink()->offsetSetStylesheet(-110, $this->view->locateFile('bootstrap.css'), 'all');
        }

        //doit ABSOLUMENT être en premier
//        $this->view->headLink()->offsetSetStylesheet(-100, $this->view->locateFile('integration.css'), 'all');
//        $this->view->headLink()->appendStylesheet($this->view->locateFile('integration.css'), 'all');

        if ($this->_config->setBgStyle)
            $this->view->setBgStyle();
    }

}
