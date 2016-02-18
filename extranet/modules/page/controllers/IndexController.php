<?php
/**
* Displays the tree and displays the blocks associated with the requested page
*
* The system generates and displays the tree. If the requested page contains
* blocks, the system find and display the results.
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
* @version    CVS: <?php $ ?> Id:$
*/

class Page_IndexController extends Cible_Controller_Block_Abstract
{
    protected $_moduleID  = '999';
    protected $_moduleTitle = 'page';

    function indexAction(){
        // retrieve the ID of the requested page
        $pageID = $this->view->pageID = $this->_getParam( 'ID' );

        $pageDetails = new PagesIndex();
        $pageDetailsSelect = $pageDetails->select();
        $pageDetailsSelect->where('PI_PageID = ?', $pageID)
                          ->where('PI_LanguageID = ?', $this->_defaultEditLanguage);
        $pageDetailsData = $pageDetails->fetchRow($pageDetailsSelect)->toArray();
        $this->view->assign("pageTitle",$pageDetailsData["PI_PageTitle"]);

        $authData = $this->view->user;
        $authID     = $authData['EU_ID'];
        $hasAccess = true;
        $hasAccessToStructure = false;
        $hasCurrentPageAccess = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$pageID,"data", $this->view->isAdministrator());
        if ($hasCurrentPageAccess)
            $hasAccessToStructure = Cible_FunctionsAdministrators::checkAdministratorPageAccess($authID,$pageID,"structure", $this->view->isAdministrator());

        if (!$hasCurrentPageAccess)
        {
            $isPageAdmin = $this->view->aclIsAllowed($this->_moduleTitle, 'edit', true);
            if ($isPageAdmin)
                $hasAccessToStructure = true;
        }

        if (!$hasCurrentPageAccess && !$isPageAdmin)
            $hasAccess = false;

        if ($hasAccess){
            $authData = $this->view->user;
            $authID   = $authData['EU_ID'];

            $this->view->assign('hasAccessToStructure', $hasAccessToStructure);

            // Retrieve the page view layout
            $page = new Pages();
            $page_select = $page->select()->setIntegrityCheck(false);
            $page_select->from('Pages')
                        ->join('Views', 'Pages.P_ViewID = Views.V_ID')
                        ->where('P_ID = ?', $pageID);

            $page_info = Cible_FunctionsPages::getPageViewDetails($pageID);

            $template_file = 'index/' . $page_info['V_Path'];
            $_zone_count = $page_info['V_ZoneCount'];


            // make a request to get all the blocks to be displayed
            $blocks = new Blocks();
            $select = $blocks->select()->setIntegrityCheck(false);
            $select->from('Blocks')
                    ->join('Modules', 'Modules.M_ID = Blocks.B_ModuleID')
                    ->join('Pages', 'Blocks.B_PageID = P_ID')
                    ->join('BlocksIndex', 'Blocks.B_ID = BlocksIndex.BI_BlockID')
                    ->where('Blocks.B_PageID = ?', $pageID)
                    ->where('BlocksIndex.BI_LanguageID = ?', $this->_defaultEditLanguage)
                    ->order('Blocks.B_Position ASC');

            //Send the results to the view
            $rows = $blocks->fetchAll($select);

            $_blocks = array();

            foreach($rows as $row){
                // create the placeholder object if not already defined
                if( !isset( $_blocks[$row['B_ZoneID']] ) )
                   $_blocks[$row['B_ZoneID']] = array();

                $_blocks[$row['B_ZoneID']][] = $row->toArray();
            }

            $this->view->assign('template_file', $template_file);
            $this->view->assign('zone_count', $_zone_count);
            $this->view->assign('blocks', $_blocks);

            // Load the modules in the view
            $Modules = new Modules();
            $modules = $Modules->fetchAll();
            $this->view->assign('modules', $modules->toArray());
        }
        else{
            $this->view->assign('template_file', "");
            $this->view->assign('error_message_permission', $this->view->getCibleText('error_message_permission'));
        }
    }

    function beforecropAction(){
        /* "upload_pic/313x313_Koala.jpg" */
        $this->view->assign('returnPath',$this->_getParam('returnPath'));
        //$this->view->assign('picToCrop',$this->_getParam('picToCrop'));
        $this->view->assign('picToCrop',"upload_pic/313x313_Koala.jpg");
    }



    function addAction(){ Throw new Exception('Not implemented.'); }
    function editAction(){ Throw new Exception('Not implemented.'); }
    function deleteAction(){ Throw new Exception('Not implemented.'); }
}
?>
