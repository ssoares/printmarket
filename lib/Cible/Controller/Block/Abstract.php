<?php
/**
 * Cible
 *
 * @category   Cible
 * @package    Cible
 * @subpackage Cible
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id: Abstract.php 1851 2016-01-15 22:56:34Z ssoares $
 */

/**
 * Manages actions for block settings
 *
 * @category   Cible
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
abstract class Cible_Controller_Block_Abstract extends Cible_Extranet_Controller_Module_Action
{

    protected $_moduleID = 0;
    protected $_moduleName = '';
    protected $_defaultController = 'index';
    protected $_defaultAction = 'edit';
    protected $_blockID = null;

    public function init()
    {
        parent::init();

        $dirModuleName = $this->getRequest()->getModuleName();
        $this->_moduleName = Cible_Translation::getCibleText("module_$dirModuleName");
        $this->view->assign('moduleName', $this->_moduleName);

        /* if( isset( $this->_config->modules->$dirModuleName->id ) )
          $this->_moduleId = $this->_config->modules->$dirModuleName->id;
          else
          Throw new Exception('Fails to set moduleId');

          if( isset( $this->_config->modules->$dirModuleName->defaultController ) )
          $this->_defaultController = str_replace("'",'',$this->_config->modules->$dirModuleName->defaultController);

          if( isset( $this->_config->modules->$dirModuleName->defaultAction ) )
          $this->_defaultAction = str_replace("'",'',$this->_config->modules->$dirModuleName->defaultAction);
         */
        Zend_Registry::set('baseUrl', $this->view->baseUrl());
        $this->view->TreeView = $this->view->getTreeView();
    }

    public function manageBlockAction()
    {
        $this->view->assign('moduleName', $this->getRequest()->getModuleName());
        $this->view->assign('defaultController', $this->_defaultController);
        $this->view->assign('defaultAction', $this->_defaultAction);
        $this->view->assign('params', $this->getRequest()->getParams());
    }

    public function indexBlockAction()
    {
        $this->view->assign('moduleName', $this->getRequest()->getModuleName());
        $this->view->assign('defaultController', $this->_defaultController);
        $this->view->assign('defaultAction', $this->_defaultAction);
        $this->view->assign('params', $this->getRequest()->getParams());
    }

    public function listPositionsAction()
    {
        $_pageID = $this->_request->getParam('ID');
        $_zoneID = $this->_request->getParam('zoneID');

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $positions = Cible_FunctionsBlocks::getAllPositions($_pageID, $_zoneID);
        $cpt = count($positions);
        for ($i = 0; $i < $cpt; $i++)
        {
            $string = str_replace('%TEXT%',
                $positions[$i]['BI_BlockTitle'],
                Cible_Translation::getCibleText("form_select_option_position_below"));
            $positions[$i]['BI_BlockTitle'] = htmlentities($string, null, 'UTF-8');
        }
        //echo Zend_Json::encode( Cible_FunctionsBlocks::getAllPositions($_pageID, $_zoneID) );
        echo Zend_Json::encode($positions);
    }

    public function addBlockAction()
    {
        $_pageID = $this->getRequest()->getParam('ID');
        if (Cible_ACL::hasAccess($_pageID))
        {
            // get page informations
            $page = $this->view->page = Cible_FunctionsPages::getPageDetails($_pageID);
            $page_info = $page->toArray();

            $_baseUrl = $this->getFrontController()->getBaseUrl();
            $_success = false;

            $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
            $this->view->assign('success', $_success);
            $_options = array(
                'baseDir' => $_baseUrl,
                'pageID' => $_pageID,
                'duplicateId' => $page['P_DuplicateId'],
                'siteOrigin' => $page['P_FromSite'],
                'moduleID' => $this->_moduleID,
                'blockID' => '0',
                'addAction' => true
            );

            $form = $this->getForm($_options);

            //get position of all pages on the same level of the current page
            $position = Cible_FunctionsBlocks::getAllPositions($_pageID, 1);

            // fill select zone
            $form = Cible_FunctionsBlocks::fillSelectZone($form, $page_info['V_ZoneCount']);

            // fill select position
            $form = Cible_FunctionsBlocks::fillSelectPosition($form, $position, "add");

            // action if the user add block
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if (isset($formData['blocks']))
                    $form->getElement('BI_BlockTitle')->clearValidators()->setRequired(false);

                if ($form->isValid($formData))
                {
                    // add block
                    $oBlock = new BlocksObject();
                    $blockID = $oBlock->setProperties(array('pageId' => $_pageID, 'siteOrigin' => $page_info['P_FromSite']))
                        ->insert($formData, $this->_defaultEditLanguage);
                    $oMCVP = new ModCatViewPageObject();
                    $data = $formData;
                    $data['MCVP_PageID'] = $_pageID;
                    $data['MCVP_ModuleID'] = $this->_moduleID;
                    $oMCVP->setAction('insert')->manageData($data);

                    if (method_exists($this, 'add'))
                        $this->add($blockID);

                    if ($this->_isXmlHttpRequest)
                    {
                        $_success = true;
                        $this->view->assign('success', $_success);
                        $this->view->assign('blockID', $blockID);
                    }
                    else
                    {
                        // redirect
                        $this->_redirect('/page/manage/index/ID/' . $_pageID);
                    }
                }
                else
                {
                    $form->populate($formData);
                }
            }
            // send the form to the view
            $this->view->form = $form;

            $this->view->jQuery()->enable();
        }
    }

    public function editBlockAction()
    {
        // variables
        $_pageID = $this->_getParam('ID');
        $_blockID = $this->_getParam('blockID');
        $_baseDir = $this->getFrontController()->getBaseUrl();
        $this->view->assign('success', false);

        if (Cible_ACL::hasAccess($_pageID))
        {
            // get page informations
            $page = $this->view->page = Cible_FunctionsPages::getPageDetails($_pageID);
            // generate the form
            $_options = array(
                'baseDir' => $_baseDir,
                'pageID' => $_pageID,
                'siteOrigin' => $page['P_FromSite'],
                'duplicateId' => $page['P_DuplicateId'],
                'moduleID' => $this->_moduleID,
                'blockID' => $_blockID,
                'addAction' => false
            );

            $form = $this->getForm($_options);

            // get block informations
            $block = Cible_FunctionsBlocks::getBlockDetailsByLangID($_blockID, $this->_currentEditLanguage);

            $this->view->assign('block_zone', $block['B_ZoneID']);
            $this->view->assign('blockID', $block['B_ID']);

            // send form the the viewer
            $form->submit->setLabel('Enregistrer');
            $this->view->form = $form;

            // action if user save
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $oMCVP = new ModCatViewPageObject();
                    $data = $formData;
                    $data['MCVP_PageID'] = $_pageID;
                    $data['blockId'] = $_blockID;
                    $oMCVP->setAction('')->manageData($data);
                    $this->saveBlock($formData, $_pageID, $_blockID);
                    if (!$this->_isXmlHttpRequest)
                        $this->_redirect('/page/manage/index/ID/' . $_pageID);
                    else
                    {
                        $this->view->assign('success', true);
                        $this->view->assign('block_id', $block['B_ID']);
                        $this->view->assign('blockTitle', $block['BI_BlockTitle']);
                        $this->view->assign('blockLangID', $this->_currentEditLanguage);
                        $this->view->assign('blockDescription', $this->getManageDescription($_blockID));
                    }
                }
            }
            else
            {
                if ($_blockID > 0)
                {
                    $block = Cible_FunctionsBlocks::getBlockDetailsByLangID($_blockID, $this->_currentEditLanguage);

                    $form->populate($block->toArray());

                    $blockParameters = Cible_FunctionsBlocks::getBlockParameters($_blockID);

                    foreach ($blockParameters->toArray() as $parameter)
                    {
                        $element = $form->getElement("Param{$parameter['P_Number']}");
                        $test = explode(',', $parameter['P_Value']);
                        if (count($test) > 1 && is_numeric($test[0])){
                            $parameter['P_Value'] = explode(',', $parameter['P_Value']);
                        }
                        if ($element){
                            $element->setValue($parameter['P_Value']);
                        }else{
                            echo "Param{$parameter['P_Number']} is not found";
                    }
                }
            }
        }
        }
        $this->view->jQuery()->enable();
    }

    public function deleteBlockAction()
    {
        // variables
        $_pageID = (int) $this->_getParam('ID');
        $_blockID = (int) $this->_getParam('blockID');

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {

                // if is set delete, then delete
                $delete = isset($_POST['delete']);

                if ($delete && $_blockID > 0)
                {
                    $oMCVP = new ModCatViewPageObject();
                    $data = array('MCVP_PageID' => $_pageID, 'blockId' => $_blockID);
                    $oMCVP->setAction('delete')->manageData($data);
                    // never remove the news themselves
                    /* DELETE ROW IN BLOCK TABLE(S)   */
                    if (method_exists($this, 'delete'))
                        $this->delete($_blockID);

                    $this->deleteBlock($_blockID);

                    if ($this->_isXmlHttpRequest)
                    {
                        $this->disableView();
                        echo Zend_Json::encode(array('result' => true));
                        return;
                    }
                }
                $this->_redirect('/page/manage/index/ID/' . $_pageID);
            }
            else
            {
                if ($_pageID > 0)
                {
                    // get page information
                    $page = new Pages();
                    $this->view->page = $page->fetchRow('P_ID=' . $_pageID);

                    $pageIndex = new PagesIndex();
                    $select = $pageIndex->select()
                            ->where("PI_PageID = ?", $_pageID)
                            ->where("PI_LanguageID = ?", Zend_Registry::get("languageID"));
                    $this->view->pageIndex = $pageIndex->fetchRow($select);

                    // get block information
                    $blockIndex = new BlocksIndex();
                    $select = $blockIndex->select()
                            ->where('BI_BlockID = ?', $_blockID)
                            ->where('BI_LanguageID = ?', Zend_Registry::get("languageID"));
                    $this->view->blockIndex = $blockIndex->fetchRow($select);
                }
            }
        }
    }

    public function moveUpBlockAction()
    {
        $pageID = (int) $this->_getParam('ID');
        $blockID = (int) $this->_getParam('blockID');

        $this->updatePosition($blockID, -1);

        $this->_redirect('/page/manage/index/ID/' . $pageID);
    }

    public function moveDownBlockAction()
    {
        $pageID = (int) $this->_getParam('ID');
        $blockID = (int) $this->_getParam('blockID');

        $this->updatePosition($blockID, +1);

        $this->_redirect('/page/manage/index/ID/' . $pageID);
    }

    public function updateZoneBlockAction()
    {
        $this->disableView();

        $_pageID = $this->getRequest()->getParam('ID');
        $_blockID = $this->getRequest()->getParam('blockID');
        $_currentZoneID = null;
        $_currentIds = null;

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                $_currentZoneID = $formData['currentZoneID'];
                $_currentIds = Zend_Json::decode($formData['currentIds']);

                $db = $this->_db;

                $i = 1;
                foreach ($_currentIds as $_id)
                {
                    $where = $db->quoteInto('B_ID = ?', $_id);
                    $n = $db->update('Blocks', array('B_ZoneID' => $_currentZoneID, 'B_Position' => $i), $where);
                    $i++;
                }

                if (!empty($formData['previousZoneID']))
                {
                    $_previousZoneID = $formData['previousZoneID'];
                    $_previousIds = Zend_Json::decode($formData['previousIds']);

                    $i = 1;
                    foreach ($_previousIds as $_id)
                    {
                        $where = $db->quoteInto('B_ID = ?', $_id);
                        $n = $db->update('Blocks', array('B_ZoneID' => $_previousZoneID, 'B_Position' => $i), $where);
                        $i++;
                    }
                }

                echo Zend_Json::encode(array('result' => true));
                return;
            }
        }
        echo Zend_Json::encode(array('result' => false));
    }

    public function setOnlineBlockAction()
    {
        $this->disableView();

        $_pageID = $this->getRequest()->getParam('ID');
        $_blockID = $this->getRequest()->getParam('blockID');

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                $db = $this->_db;
                $where = $db->quoteInto('B_ID = ?', $_blockID);
                $n = $db->update('Blocks', array('B_Online' => $formData['state'] == 'online' ? 1 : 0), $where);

                if ($n == 1)
                {
                    echo Zend_Json::encode(array('result' => true));
                    return;
                }
            }
        }
        echo Zend_Json::encode(array('result' => false));
    }

    public function setSecureBlockAction()
    {
        $this->disableView();

        $_pageID = $this->getRequest()->getParam('ID');
        $_blockID = $this->getRequest()->getParam('blockID');

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                $db = $this->_db;
                $where = $db->quoteInto('B_ID = ?', $_blockID);
                $n = $db->update('Blocks', array('B_Secured' => $formData['state'] == 'deny' ? 1 : 0), $where);

                if ($n == 1)
                {
                    echo Zend_Json::encode(array('result' => true));
                    return;
                }
            }
        }
        echo Zend_Json::encode(array('result' => false));
    }

    public function activateBlockAction()
    {
        $this->disableView();

        $_pageID = $this->getRequest()->getParam('ID');
        $_blockID = $this->getRequest()->getParam('blockID');

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                $db = $this->_db;
                $where = $db->quoteInto('B_ID = ?', $_blockID);
                $n = $db->update('Blocks', array('B_ZoneID' => $formData['zoneID']), $where);

                if ($n == 1)
                {
                    echo Zend_Json::encode(array('result' => true));
                    return;
                }
            }
        }
        echo Zend_Json::encode(array('result' => false));
    }

    public function deactivateBlockAction()
    {
        $this->disableView();

        $_pageID = $this->getRequest()->getParam('ID');
        $_blockID = $this->getRequest()->getParam('blockID');

        if (Cible_ACL::hasAccess($_pageID))
        {
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                $db = $this->_db;
                $where = $db->quoteInto('B_ID = ?', $_blockID);
                $n = $db->update('Blocks', array('B_ZoneID' => -1), $where);

                if ($n == 1)
                {
                    echo Zend_Json::encode(array('result' => true));
                    return;
                }
            }
        }
        echo Zend_Json::encode(array('result' => false));
    }

    protected function getManageDescription($blockID = null)
    {
        $viewName = Cible_FunctionsBlocks::getBlockParameter($blockID, '999');
        $listParams = '';

        if ($viewName == 'index')
        {
            $identifier = "form_select_option_view_default_index";
            $listParams = '';
        }
        else
        {
            $identifier = "form_select_option_view_{$this->getRequest()->getModuleName()}_{$viewName}";
            $listParams = "<div class='block_params_list'><strong>";
            $listParams .= $this->view->getCibleText('label_view');
            $listParams .= "</strong>" . $this->view->getCibleText($identifier) . "</div>";
        }

        return $listParams;
    }

    protected function getIndexDescription($blockID = null)
    {
        return '';
    }

    /**
     * get-icon will return the current block icon in format 32x32
     * Unless the format parameter is specified
     * Test if the prefix is only lower case letters, and have at least 2
     * characters and 15 maximun. Default is icon
     *
     * Test valid extension file. Default is gif
     *
     */
    public function getIconAction()
    {
        $format = $this->getRequest()->getParam('format');
        $prefix = $this->getRequest()->getParam('prefix');
        $ext = $this->getRequest()->getParam('ext');

        // if format is empty or invalid, use 16x16
        if (empty($format) || !in_array($format, array('16x16', '32x32', '48x48', '55x55')))
            $format = '32x32';

        // if file name prefix is empty or invalid, use icon
        if (empty($prefix) || !preg_match("/^[a-z-_]{2,20}$/", $prefix))
            $prefix = 'icon';

        // if file extension is empty or invalid, use .gif
        if (empty($ext) || !in_array($ext, array('gif', 'jpg', 'jpeg', 'png')))
            $ext = 'png';

        $ressource_path = $this->getFrontController()
                ->getModuleDirectory(
                    $this->getRequest()
                    ->getModuleName()) . "/ressources/icons/{$prefix}-{$format}.{$ext}";

        // Disables the view and the layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($ressource_path));
        echo file_get_contents($ressource_path);
    }

    /**
     * get-script will return the current the specific js script for the module
     *
     * @return void
     */
    public function getScriptAction()
    {
        $fileName = $this->getRequest()->getParam('file');
        $extension = $this->getRequest()->getParam('ext');

        // if file name is empty or invalid, use script
        if (empty($fileName) || !preg_match("/^[a-z-_]{2,20}$/", $fileName))
            $fileName = 'script';

        // if file extension is empty or invalid, use .gif
        if (empty($extension) || !in_array($extension, array('js')))
            $extension = 'js';

        $ressource_path = $this->getFrontController()
                ->getModuleDirectory(
                    $this->getRequest()
                    ->getModuleName()) . "/ressources/scripts/{$fileName}.{$extension}";

        // Disables the view and the layout
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        header("Content-type: text/javascript");
        header('Content-Length: ' . filesize($ressource_path));
        echo file_get_contents($ressource_path);
    }

    protected function getForm($options = null)
    {
        $_formName = 'FormBlock' . ucfirst($this->getRequest()->getModuleName());

        $_filename = $this->getFrontController()->getModuleDirectory($this->getRequest()->getModuleName()) . '/models/' . $_formName . '.php';

        if (file_exists($_filename))
        {
            Zend_Loader::autoload($_formName);
            $form = new $_formName($options);
            if (method_exists($form, 'getJsScript')){
                $file = $this->view->locateFile($form->getJsScript(), 'jsBlock');
                $js = file_get_contents(Zend_Registry::get('serverDocumentRoot') . $file);
                $this->view->inlineScript()->appendScript($js);
            }
            return $form;
        }
        else if (Zend_Loader::autoload('Cible_Form_Block'))
        {
            return $form = new Cible_Form_Block($options);
        }
        else
        {
            //trigger_error($e->getMessage(), E_USER_WARNING);
            trigger_error("ClassName $_formName doesn't exists or isn't found in modules' model directory");
            return '';
        }
    }

    public function saveBlock($formData, $pageID, $blockID)
    {
        $block = Cible_FunctionsBlocks::getBlockDetailsByLangID($blockID, $this->_currentEditLanguage);

        $db = Zend_Registry::get("db");

        $block['BI_BlockTitle'] = $formData['BI_BlockTitle'];
        $block->save();

        $db = Zend_Registry::get("db");
        $where = "B_ID = " . $blockID;
        $db->update(
              'Blocks',
              array(
                  'B_ShowHeader' => $formData['B_ShowHeader'],
                  'B_Secured'    => $formData['B_Secured']
                  ),
              $where
          );

        // save parameters
        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        $i = 1;
        foreach ($blockParameters as $parameter)
        {
            $value = '';
            if (isset($formData["Param{$parameter['P_Number']}"])){
                $value = $formData["Param{$parameter['P_Number']}"];
                unset($formData["Param{$parameter['P_Number']}"]);
            }
            if (is_array($value)){
                $value = implode(',', $value);
            }
            $where = "P_BlockID = " . $blockID . " AND P_Number = " . $parameter["P_Number"];
            $db->update('Parameters', array('P_Value' => $value), $where);
            $i++;
        }

        foreach($formData as $key => $param)
        {
            $data= array();
            if (preg_match('/^Param/', $key))
            {
                $oParameters = new ParametersObject();
                // Insert Parameters for the current block
                if (is_array($param)){
                    $data[$key] = implode(',', $param);
                }else{
                    $data[$key] = $param;
                }
                if (!empty($param)){
                    $oParameters->setBlockId($blockID)
                        ->insert($data, $this->_defaultEditLanguage);
                }
            }
        }
    }

    protected function deleteBlock($blockID)
    {
        /*         * ******************************* */
        /* DELETE ROW IN BLOCK TABLE      */
        /*         * ******************************* */
        // define block
        $block = new Blocks();
        $where = 'B_ID = ' . $blockID;

        // get informations of the block to delete
        $blockDetails = $block->fetchRow($where);
        $position = $blockDetails->B_Position;
        $pageID = $blockDetails->B_PageID;

        // delete the block
        $block->delete($where);

        /*         * ******************************* */
        /* DELETE ROWS IN BLOCKINDEX TABLE */
        /*         * ******************************* */
        // define blockIndex
        $blockIndex = new BlocksIndex();
        $where = 'BI_BlockID = ' . $blockID;

        // delete all blockIndex
        $blockIndex->delete($where);

        /*         * ******************************* */
        /* UPDATE POSITION ON BLOCK TABLE */
        /*         * ******************************* */
        // update position of all block in the same page
        $db = Zend_Registry::get("db");
        $where = "(B_Position > " . $position . ") AND B_PageID = " . $pageID;
        $db->update('Blocks', array('B_Position' => new Zend_Db_Expr('B_Position - 1')), $where);

        /*         * ******************************* */
        /* DELETE ROWS IN PARAMETERS TABLE */
        /*         * ******************************* */
        $blockParameters = new Parameters();
        $where = 'P_BlockID = ' . $blockID;

        $blockParameters->delete($where);
    }

    protected function updatePosition($blockID, $n)
    {
        $block = Cible_FunctionsBlocks::getBlockDetails($blockID);
        $pageID = $block['B_PageID'];
        $oldPosition = $block['B_Position'];
        $newPosition = $oldPosition + $n;
        $zoneID = $block['B_ZoneID'];

        $db = Zend_Registry::get("db");
        $where = "B_Position = '{$newPosition}' AND B_ZoneID = '{$zoneID}' AND B_PageID = '{$pageID}'";
        $db->update('Blocks', array('B_Position' => $oldPosition), $where);

        $where = "B_ID = " . $blockID;
        $db->update('Blocks', array('B_Position' => $newPosition), $where);
    }

    public function getManageDescriptionAction()
    {

        if ($this->_isXmlHttpRequest)
            $this->disableLayout();

        $this->view->assign('description',
            $this->getManageDescription($this->_getParam('ID'))
        );
    }

    public function getIndexDescriptionAction()
    {
        if ($this->_isXmlHttpRequest)
            $this->disableLayout();

        $this->view->assign('description',
            $this->getIndexDescription($this->_getParam('ID'))
        );
    }

    public static function getModulePermission()
    {
        return array();
    }

}