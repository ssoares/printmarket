<?php
 /**
 * Control errors from controllers or actions unknown
 *
 * The system checks whether the controller or action is present in the database and call the controller page if it does.
 * If not present, a custom 404 page is displayed.
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
class ErrorController extends Cible_Controller_Action
{
    private $_extensions = array('png', 'bmp', 'jpg', 'jpeg', 'gif', 'pdf','doc','xls', 'docx','xlsx', 'csv');

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

    }

    /**
    * This is the function that loop through all modules to construct the robots.txt
    *
    * This function return the robots.txt file all format and ready to be read by a spider robots
    *
    * @access private
    *
    * @return an txt file
    */
    private function robotsAction(){
        $config = Zend_Registry::get('config');
        $robotsString = "";
        $robotsString = header('Content-Type: text/txt');
        if (!$config->disallowRobots)
        {
            $moduleName = ucfirst("DefaultRobots");
            if (class_exists($moduleName))
                $robotsString .= Cible_FunctionsGeneral::getXMLFilesString('default');

            $modules = Cible_FunctionsModules::getModules();
            foreach ($modules as $module)
            {
                $moduleName = ucfirst($module['M_MVCModuleTitle'] . "Robots.php");

                if (self::_isReadable($moduleName)) {
                    $robotsString .= Cible_FunctionsGeneral::getXMLFilesString($module['M_MVCModuleTitle']);
                }
            }

        }
        else
            $robotsString = "User-agent: * \r\nDisallow: /";

       echo $robotsString;
    }

    private function _isReadable($filename)
    {
        if (function_exists('stream_resolve_include_path')) {
            return is_string(stream_resolve_include_path($filename));
        }

        if (!$fh = @fopen($filename, 'r', true)) {
            return false;
        }
        @fclose($fh);
        return true;
    }


    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        Zend_Registry::set('baseUrl', $this->getFrontController()->getBaseUrl());
        $info = pathinfo(urldecode($_SERVER['REQUEST_URI']));
        if (isset($info['extension']) && in_array($info['extension'], $this->_extensions)){
            $filename = Zend_Registry::get('fullDocumentRoot') . trim($_SERVER['REQUEST_URI'], '/');
            if (!file_exists($filename)){
                $errors = $this->_getParam('error_handler');
                $errors->type = 'MISSING_FILE_REQUESTED';
            }
        }
        switch ($errors->type) {
            // if it is a controller error
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:

                // grab the controller, action and settings requested by the user
                $ParamsArray = $errors->request->getParams();
                if($this->_request->controller=="robots.txt")
                {
                    $this->robotsAction();
                    $this->_helper->layout()->disableLayout();
                    $this->_helper->viewRenderer->setNoRender(true);

                    exit;
                }
                $ControllerName = $ParamsArray['controller'];
                $ActionName = $ParamsArray['action'];

                // check if the name of the controller is in the database
                $Pages = new PagesIndex();
                $Select = $Pages->select()->setIntegrityCheck(false)
                    ->from('PagesIndex')
                    ->join('Languages','Languages.L_ID = PagesIndex.PI_LanguageID')
                    ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
//                        ->joinRight('Page_Themes', 'Page_Themes.PT_ID = Pages.P_ThemeID')
                    ->join('Views', 'Views.V_ID = Pages.P_ViewID')
                    ->where('PagesIndex.PI_PageIndex = ?', $ControllerName)
                    ->where('PI_Status = 1')
                    ->limit(1);

                $Row = $Pages->fetchRow($Select);
                $isActive = Cible_FunctionsGeneral::languageIsAvailable($Row['PI_LanguageID']);
                if (!$isActive && count($Row) == 1)
                {
                    $Select = $Pages->select()->setIntegrityCheck(false)
                    ->from('PagesIndex')
                    ->join('Languages','Languages.L_ID = PagesIndex.PI_LanguageID')
                    ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                    ->join('Page_Themes', 'Page_Themes.PT_ID = Pages.P_ThemeID')
                    ->join('Views', 'Views.V_ID = Pages.P_ViewID')
                    ->where('PI_Status = 1')
                    ->where('PagesIndex.PI_LanguageID = ?', $this->_config->defaultInterfaceLanguage)
                    ->where('PagesIndex.PI_PageID = ?', $Row['PI_PageID'])
                    ->limit(1);

                    $Row = $Pages->fetchRow($Select);

                    $this->_helper->redirector->setCode(301);
                    $this->_redirect(Zend_Registry::get('absolute_web_root') . '/' . $Row['PI_PageIndex']);
                }
                // if the controller is found in the database
                if(count($Row) == 1){
                    // call page controller to display blocks
                    $this->_helper->actionStack('index','page','default', array('Row' => $Row, 'Param' => $ParamsArray));

                    // does not render the page Error404
                    $this->disableView();
                }
                else{

                    $session = new Cible_Sessions(SESSIONNAME);

                    $currentLanguageID = !empty($session->languageID) ? $session->languageID : $this->_config->defaultInterfaceLanguage;

                    $page_not_found_id = $this->_config->page_not_found->pageID;

                    $pageIndexName = Cible_FunctionsPages::getPageNameByID($page_not_found_id, $currentLanguageID);

                    // check if the name of the controller is in the database
                    $Pages = new PagesIndex();
                    $Select = $Pages->select()->setIntegrityCheck(false)
                        ->from('PagesIndex')
                        ->join('Languages','Languages.L_ID = PagesIndex.PI_LanguageID')
                        ->join('Pages', 'Pages.P_ID = PagesIndex.PI_PageID')
                        ->join('Page_Themes', 'Page_Themes.PT_ID = Pages.P_ThemeID')
                        ->join('Views', 'Views.V_ID = Pages.P_ViewID')
                        ->where('PagesIndex.PI_PageIndex = ?', $pageIndexName)
                        ->where('PI_Status = 1')
                        ->limit(1);

                    $Row = $Pages->fetchRow($Select);

                    if(count($Row) == 1){
                        // call page controller to display blocks
                        $this->_helper->actionStack('index','page','default', array('Row' => $Row, 'Param' => $ParamsArray));

                        // does not render the page Error404
                        $this->disableView();
                    } else {
                        $this->disableLayout();

                        echo "404 Page not found.";
                    }

                    $this->getResponse()
                         ->setRawHeader('HTTP/1.1 404 Not Found');

                }
            break;

            case 'MISSING_FILE_REQUESTED':
                echo $this->getResponse()->setRawHeader('HTTP/1.1 404 Not Found');
                exit;
                break;
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
//                    $currentLanguageID = Zend_Registry::isRegistered('languageID')? Zend_Registry::get('languageID') : $this->_config->defaultInterfaceLanguage;
//                    $page_not_found_id = $this->_config->page_not_found->pageID;
//                    $pageIndexName = Cible_FunctionsPages::getPageNameByID($page_not_found_id, $currentLanguageID);
//                    $this->_redirect('/' . $pageIndexName);

            default:
                if (!ISDEV)
                {
                    $currentLanguageID = Zend_Registry::isRegistered('languageID')? Zend_Registry::get('languageID') : $this->_config->defaultInterfaceLanguage;
                    $page_not_found_id = $this->_config->page_not_found->pageID;
                    $pageIndexName = Cible_FunctionsPages::getPageNameByID($page_not_found_id, $currentLanguageID);
                    $this->_redirect('/' . $pageIndexName);
                }
                else
                {
                    // application error; display error page, but don't change
                    // status code

                    //                     // ...
                    // Log the exception:
                    $exception = $errors->exception;
                    echo <<< End_of_error
                    <p>
                    <strong>Error</strong>
                    {$exception->getMessage()}
                    </p>
                    <p>
                    <strong>Stack Trace</strong>
                    {$exception->getTraceAsString()}
                    </p>
End_of_error;

                }
                break;
        }
   }
}