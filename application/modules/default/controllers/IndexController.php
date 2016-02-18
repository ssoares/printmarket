<?php

class IndexController extends Cible_Controller_Action
{

   /**
    * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function siteMapAction(){
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $newsRob = new DefaultRobots();
        $dataXml = $newsRob->getXMLFile($this->_request->getParam('lang'));
        parent::siteMapAction($dataXml);
    }

    function indexAction()
    {
        $ParamsArray = $this->_request->getParams();
        $Pages = new PagesIndex();
        $Select = $Pages->select()->setIntegrityCheck(false)
            ->from('PagesIndex')
            ->join('Languages','Languages.L_ID = PagesIndex.PI_LanguageID')
            ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
            ->join('Page_Themes', 'Page_Themes.PT_ID = Pages.P_ThemeID')
            ->join('Views', 'Views.V_ID = Pages.P_ViewID')
            ->where('Pages.P_Home = ?', 1)
            ->where('PagesIndex.PI_LanguageID = ?', $this->_config->defaultInterfaceLanguage)
            ->limit(1);

        $Row = $Pages->fetchRow($Select);
        // if the controller is found in the database
        if(count($Row) == 1){
            // call page controller to display blocks
            $this->_helper->actionStack('index','page','default', array('Row' => $Row, 'Param' => $ParamsArray));
            $this->disableView();
        }
    }

}
