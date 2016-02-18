<?php

class Users_IndexController extends Cible_Extranet_Controller_Module_Action
{
    protected $_labelSuffix;
    protected $_colTitle      = array();
    protected $_moduleID      = 20;
    protected $_defaultAction = '';
    protected $_defaultRender = 'list';
    protected $_moduleTitle   = 'users';
    protected $_name          = 'index';
    protected $_ID            = 'id';
    protected $_currentAction = '';
    protected $_actionKey     = '';
    protected $_imageSrc      = '';

    protected $_imageFolder;
    protected $_rootImgPath;
    protected $_formName   = '';
    protected $_joinTables = array();
    protected $_objectList = array(
        'general'    => 'GenericProfilesObject'
    );
    protected $_actionsList = array();

    protected $_disableExportToExcel = false;
    protected $_disableExportToPDF   = false;
    protected $_disableExportToCSV   = false;
    protected $_enablePrint          = false;
    protected $_formatData           = false;
    protected $_constraint;
    protected $_genericId;
    protected $_oMember;
    protected $_separ = '||';

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // Sets the called action name. This will be dispatched to the method
        $this->_currentAction = $this->_getParam('action');

        // The action (process) to do for the selected object
        $this->_actionKey = $this->_getParam('actionKey');

        $this->_formatName();
        $this->view->assign('cleaction', $this->_labelSuffix);

        $dataImagePath = "../../"
                . $this->_config->document_root
                . "/data/images/";

        if(isset($this->_objectList[$this->_currentAction]))
            $this->_imageFolder = $dataImagePath
                    . $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction] . "/";

        if(isset($this->_objectList[$this->_currentAction]))
            $this->_rootImgPath = Zend_Registry::get("www_root")
                    . "/data/images/"
                    . $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction] . "/";

        // Set the default objects according to the modules list;
        $modulesProfiles = Cible_FunctionsModules::modulesProfile();
        $this->view->profilesList = $modulesProfiles;

        foreach ($modulesProfiles as $id => $name)
        {
            $value = $name['M_MVCModuleTitle'];
            if (!in_array($value, $this->_objectList))
            {
                switch ($value)
                {
                    case 'newsletter':
                        $this->_objectList[$value] = 'NewsletterProfilesObject';
                        break;
                    case 'order':
                        $this->_objectList[$value] = 'MemberProfilesObject';
                        break;
                    case 'donors':
                        $this->_objectList[$value] = 'DonorsObject';
                        break;
                    case 'companies':
                        $this->_objectList[$value] = 'CompaniesDonorObject';
                        break;
                    case 'relationship':
                        $this->_objectList[$value] = 'GenericProfilesFollowObject';
                        break;
                    case 'teams':
                        $this->_objectList[$value] = 'TeamsObject';
                        break;

                    default:
                        break;
                }
                $this->_moduleID = $id;
            }

            $this->_objectList = array_unique($this->_objectList);

        }

        $this->view->headScript()->appendFile($this->view->locateFile('jquery.validate.min.js', 'jquery'));
        $this->view->headScript()->appendFile($this->view->locateFile('additional-methods.min.js', 'jquery'));
        $this->view->headLink()->appendStylesheet($this->view->locateFile('profile.css'));
    }

    public function ajaxAction()
    {
        $this->disableLayout();
        $this->disableView();

        if ($this->_isXmlHttpRequest)
        {
            $this->_actionKey = $this->_getParam('op');
            $this->_genericId = $this->_getParam('genericId');
            $this->_currentAction = $this->_getParam('profile');
            $actionName = $this->_currentAction . 'Action';

            $params = $this->$actionName();

        }
    }

    /**
     * Allocates action for GenericProfile management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function generalAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'GP_LastName'  => array('width' => '150px'),
                'GP_FirstName' => array('width' => '150px'),
                'GP_Email'     => array('width' => '300px')
                );

            $this->_joinTables = array();

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables);

                return $params;
            }

            $this->_formName = 'FormGenericProfile';
//            $this->_oMember = new MemberProfilesObject();

            $this->_redirectAction();
        }
    }
    /**
     * Allocates action for newsletter profiles management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function newsletterAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'NP_ProfileId'  => array('width' => '150px'),
                'NP_Categories' => array('width' => '150px')
                );

            $this->_joinTables = array('CompaniesProfilesAssociationObject', 'GenericProfilesObject');

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => 'FormNewsletterProfile');

                return $params;
            }

            $this->_formName = 'FormNewsletterProfile';
            if($this->_isXmlHttpRequest && $this->_request->isPost())
            {
                $_POST['NP_ProfileId'] = $this->_genericId;
                if ($this->_actionKey == 'edit')
                    $this->_formatData = true;
            }

            $this->_redirectAction();
        }
    }
    /**
     * Allocates action for detailed members data management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function detailsAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'MP_GenericProfileMemberID'  => array('width' => '150px'),
                'MP_Company' => array('width' => '150px')
                );

            $this->_joinTables = array('GenericProfile');

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => 'FormMembersProfile');

                return $params;
            }

            $this->disableLayout();
            $this->disableView();
            $this->_formName = 'FormMembersProfile';
            $this->_redirectAction();
        }
    }
    /**
     * Allocates action for profiles of order data management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function orderAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {

//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array(
                'R_GenericProfileId'  => array('width' => '150px'),
                'R_Status' => array('width' => '150px')
                );

            $this->_joinTables = array('GenericProfile');

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => 'FormOrderProfile');

                return $params;
            }

            $this->_formName = 'FormOrderProfile';
//            $this->_oMember = new MemberProfilesObject();
            if($this->_isXmlHttpRequest && $this->_request->isPost())
            {
                $_POST['MP_GenericProfileMemberID'] = $this->_genericId;
                if ($this->_actionKey == 'add')
                    $_POST['MP_Status'] = -1;
            }
            $this->_redirectAction();
        }
    }

    /**
     * Allocates action for data management.<br />
     * Prepares data utilized to activate controller actions.
     *
     * @access public
     *
     * @return void
     */
    public function relationshipAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array();

            $this->_joinTables = array('GenericProfilesObject', 'GenericProfilesAssociationObject');

            $this->_formName = 'FormRelationshipProfile';
            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => $this->_formName);

                return $params;
            }

            if($this->_isXmlHttpRequest && $this->_request->isPost()){

            }

            $this->_redirectAction();
        }
    }
    public function companiesAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array();

            $this->_joinTables = array('GenericProfilesObject');
            $this->_formName = 'FormCompaniesDonorProfile';

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => $this->_formName);

                return $params;
            }

            if($this->_isXmlHttpRequest && $this->_request->isPost()){
            }

            $this->_redirectAction();
        }
    }
    public function donorsAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array();

            $this->_joinTables = array('GenericProfilesObject');
            $this->_formName = 'FormDonorsProfile';

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => $this->_formName);

                return $params;
            }

            if($this->_isXmlHttpRequest && $this->_request->isPost()){
            }
            $this->_redirectAction();
        }
    }

    public function teamsAction($getParams = false)
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
//            $this->_disableExportToExcel = true;
            $this->_constraint = '';
            $this->_colTitle = array();

            $this->_joinTables = array('GenericProfilesObject');
            $this->_formName = 'FormTeamsProfile';

            if($getParams)
            {
                $params = array(
                    'columns'    => $this->_colTitle,
                    'joinTables' => $this->_joinTables,
                    'formName' => $this->_formName);

                return $params;
            }

            if($this->_isXmlHttpRequest && $this->_request->isPost()){
            }

            $this->_redirectAction();
        }
    }

    private function _listAction($objectName)
    {
        $page = $this->_getParam('page');
        $lang = $this->_getParam('lang');
        if (!$lang)
        {
            $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
            $langId = $this->_defaultEditLanguage;
        }
        else
        {
            $langId = Cible_FunctionsGeneral::getLanguageID($lang);
            $this->_registry->currentEditLanguage = $langId;
        }

        if ($page == '')
            $page = 1;
        // Create the object from parameter
        $oData = new $objectName();

        // get needed data to create the list
        $columnData  = $oData->getDataColumns();
        $dataTable   = $oData->getDataTableName();
        $indexTable  = $oData->getIndexTableName();
        $columnIndex = $oData->getIndexColumns();
        $tabId = $oData->getDataId();
        //Set the tables from previous collected data
        $this->_tables = array(
            $dataTable => $columnData,
            $indexTable => $columnIndex
        );
        // Set the select query to create the paginator and the list.
        $select = $oData->getAll($langId, false);
        $select->where($oData->getDelField() . ' = ?', 0);
        $params = array('constraint' => $oData->getForeignKey());
        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        $select = $this->_addJoinQuery($select, $params);
        // Set the the header of the list (columns name used to display the list)
        $field_list = $this->_colTitle;
        // Set the options of the list = links for actions (add, edit, delete...)
        $options = $this->_setActionsList($tabId, $page);
        //Create the list with the paginator data.
        $mylist = New Cible_Paginator($select, $this->_tables, $field_list, $options);
        // Assign the view script for rendering
        $this->_helper->viewRenderer->setRender($this->_defaultRender);
        //Assign to the render the list created previously.
        $this->view->assign('mylist', $mylist);
    }

    /**
     * Add action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function addAction()
    {
        $oDataName = $this->_objectList[$this->_currentAction];
        $oData     = new $oDataName();
        $page = $this->_getParam('page');

        $lang = $this->_getParam('lang');
        if (!$lang)
        {
            $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
            $langId = $this->_defaultEditLanguage;
        }
        else
        {
            $langId = Cible_FunctionsGeneral::getLanguageID($lang);
            $this->_registry->currentEditLanguage = $langId;
        }

        $baseDir = $this->view->baseUrl() . "/";

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if (isset($formData[$this->_imageSrc]) && $formData[$this->_imageSrc] <> "")
                    if ($formData[$this->_imageSrc] <> "")
                        $imageSrc = $this->_rootImgPath
                                . "tmp/mcith/mcith_"
                                . $formData[$this->_imageSrc];
            }

            $cancelUrl    = $baseDir;
            $returnModule = $this->_getParam('returnModule');
            $returnAction = $this->_getParam('returnAction');
            if (!empty($returnModule))
                $cancelUrl .= $returnModule . '/';
            else
                $cancelUrl .= $this->_moduleTitle . "/";

            $cancelUrl .= $this->_name . "/";

            if (!empty($returnAction))
                $cancelUrl = str_replace('_', '/', $returnAction) . '/';
            else
                $cancelUrl .= 'general/';

            $cancelUrl .= "page/" . $page;

            // generate the form
            $form = new $this->_formName(array(
                'baseDir'    => $baseDir,
                'cancelUrl'  => $cancelUrl,
                'moduleName' => $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction],
                'imageSrc'   => $imageSrc,
                'imgField'   => $this->_imageSrc,
                'dataId'     => '',
                'object'     => $oData,
                'mode'       => 'add',
                'isNewImage' => true
            ));

            $this->view->form = $form;

            // action
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($this->_isXmlHttpRequest)
                {
                    $formData = $this->_mergeFormData($formData);
//                    if (isset ($formData['MP_Status']))
//                        $oData->save($formData['MP_GenericProfileMemberID'], $formData, $langId);
//                    else
                        $recordID = $oData->insert($formData, $langId);
                    $data = array(
                        'success' => true,
                        'tabTitle' => $this->view->getCibleText('profile_tab_title_' . $this->_currentAction)
                    );
                    echo json_encode ($data);
                }

//                $formData['LANGUAGE'] = $this->getCurrentEditLanguage();
                if ($form->isValid($formData))
                {
                    if (!$this->_isXmlHttpRequest)
                    {
                        $formData = $this->_mergeFormData($formData);
                        $recordID = $oData->insert($formData, $langId);
                        $formData['MP_GenericProfileMemberID'] = $recordID;
                        $formData['MP_Status'] = -2;
//                        $this->_oMember->insert($formData, $langId);
                    }
                    /* IMAGES */
                    if (!empty($this->_imageSrc))
                    {
                        mkdir($this->_imageFolder . $recordID)
                                or die("Could not make directory");
                        mkdir($this->_imageFolder . $recordID . "/tmp")
                                or die("Could not make directory");
                    }

                    if ($form->getValue($this->_imageSrc) <> '')
                    {
                        //Get config data
                        $config = Zend_Registry::get('config')->toArray();
                        //Set sizes for the image
                        $srcOriginal       = $this->_imageFolder . "tmp/" . $form->getValue($this->_imageSrc);
                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
                        $originalMaxWidth  = $config[$this->_moduleTitle]['image']['original']['maxWidth'];

                        $originalName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $originalMaxWidth
                                        . 'x'
                                        . $originalMaxHeight
                                        . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc)
                        );


                        $srcMedium = $this->_imageFolder
                                . "tmp/medium_"
                                . $form->getValue($this->_imageSrc);
                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
                        $mediumName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $mediumMaxWidth
                                        . 'x'
                                        . $mediumMaxHeight
                                        . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc)
                        );

                        $srcThumb = $this->_imageFolder
                                . "tmp/thumb_"
                                . $form->getValue($this->_imageSrc);
                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
                        $thumbName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $thumbMaxWidth
                                        . 'x'
                                        . $thumbMaxHeight
                                        . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc)
                        );

                        copy($srcOriginal, $srcMedium);
                        copy($srcOriginal, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcOriginal,
                                            'maxWidth' => $originalMaxWidth,
                                            'maxHeight' => $originalMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcMedium,
                                            'maxWidth' => $mediumMaxWidth,
                                            'maxHeight' => $mediumMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcThumb,
                                            'maxWidth' => $thumbMaxWidth,
                                            'maxHeight' => $thumbMaxHeight)
                        );

                        rename($srcOriginal, $this->_imageFolder . $recordID . "/" . $originalName);
                        rename($srcMedium, $this->_imageFolder . $recordID . "/" . $mediumName);
                        rename($srcThumb, $this->_imageFolder . $recordID . "/" . $thumbName);
                    }

                    $data = array(
                        'L_ModuleID' => 313,
                        'L_Action' => $this->_actionKey . ucfirst($this->_currentAction),
                        'L_Data' => array($this->_currentAction . 'Id' => $recordID)
                    );
                    $options = array(
                        'moduleId' => $this->_moduleID,
                        'data' => $data
                    );
//                    $oLog = new Cible_Log($options);
//                    $oLog->log($data);
                    // redirect
                    if (!$this->_isXmlHttpRequest)
                        $this->_redirect($this->_moduleTitle . "/"
                                . $this->_name . "/"
                            . $this->_currentAction . '/actionKey/edit/' . $this->_ID . '/' . $recordID
                            . '/returnModule/' . $returnModule
                            . '/returnAction/' . $returnAction
                        );
                }
                else if(!empty($formData['GP_Email']))
                {
                    if (!$this->_isXmlHttpRequest)
                    {
                        $link = $this->userExistsAction();
                        $this->view->link = $link;
                        $errors = $form->getMessages();
                        $key = key($errors['GP_Email']);
                        if ($key != 'regexNotMatch')
                            $this->view->displayError = true;
                    }
                    $form->populate($formData);
                }
            }
        }
    }

    /**
     * Edit action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function editAction()
    {
        $this->view->headLink()->appendStylesheet($this->view->locateFile('log.css'));
        $this->view->headScript()->appendFile($this->view->locateFile('profile.js'));
        $imageSrc = "";
        $id       = (int) $this->_getParam($this->_ID);
        $page     = (int) $this->_getParam('page');

        $baseDir      = $this->view->baseUrl() . "/";
        $cancelUrl    = $baseDir;
        $returnModule = $this->_getParam('returnModule');
        $returnAction = $this->_getParam('returnAction');
        if (!empty($returnModule))
            $cancelUrl .= $returnModule . '/';
        else
            $cancelUrl .= $this->_moduleTitle . "/";

        $cancelUrl .= $this->_name . "/";

        if (!empty($returnAction))
            $cancelUrl .= $returnAction . '/';
        else
            $cancelUrl .= 'general/';

        $cancelUrl .= "page/" . $page;

        $currentCity = '';

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'edit', true))
        {
            $this->view->id = $id;
            if ($this->_isXmlHttpRequest)
                $this->disableLayout();

            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "page/" . $page;

            // Get data details
            $data = $oData->populate($id, $this->_defaultEditLanguage);

            // image src.
            if (!empty($data[$this->_imageSrc]))
            {
                $config = Zend_Registry::get('config')->toArray();
                $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];

                $this->view->assign(
                        'imageUrl',
                        $this->_rootImgPath
                        . $id . "/"
                        . str_replace(
                                $data[$this->_imageSrc],
                                $thumbMaxWidth
                                . 'x'
                                . $thumbMaxHeight
                                . '_'
                                . $data[$this->_imageSrc],
                                $data[$this->_imageSrc])
                );
                $isNewImage = 'false';

                if ($this->_request->isPost())
                {
                    $formData = $this->_request->getPost();
                    if ($formData[$this->_imageSrc] <> $data[$this->_imageSrc])
                    {
                        if ($formData[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                    . $id
                                    . "/tmp/mcith/mcith_"
                                    . $formData[$this->_imageSrc];

                        $isNewImage = 'true';
                    }
                    else
                    {
                        if ($data[$this->_imageSrc] == "")
                            $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                        else
                            $imageSrc = $this->_rootImgPath
                                    . $id . "/"
                                    . str_replace(
                                            $data[$this->_imageSrc],
                                            $thumbMaxWidth
                                            . 'x'
                                            . $thumbMaxHeight . '_'
                                            . $data[$this->_imageSrc],
                                            $data[$this->_imageSrc]);
                    }
                }
                else
                {
                    if (empty($data[$this->_imageSrc]))
                        $imageSrc = $this->view->baseUrl() . "/icons/image_non_ disponible.jpg";
                    else
                        $imageSrc = $this->_rootImgPath
                                . $id . "/"
                                . str_replace(
                                        $data[$this->_imageSrc],
                                        $thumbMaxWidth
                                        . 'x'
                                        . $thumbMaxHeight . '_'
                                        . $data[$this->_imageSrc],
                                        $data[$this->_imageSrc]);
                }
            }
            // generate the form
            $options = array(
                'moduleName' => $this->_moduleTitle . "/"
                    . $this->_objectList[$this->_currentAction],
                'baseDir'    => $baseDir,
                'cancelUrl'  => $cancelUrl,
                'imageSrc'   => $imageSrc,
                'imgField'   => $this->_imageSrc,
                'dataId'     => $id,
                'data'       => $data,
                'object'     => $oData,
                'mode'       => 'edit',
                'isNewImage' => 'true',
                'disabledFieldsStatus' => true

            );
            if ($this->_isXmlHttpRequest)
                $options['object'] = $oData;

            $form = new $this->_formName($options);

//            if (!empty($this->_oMember))
//            {
//                $memberData = $this->_oMember->getAll(null, true, $id);
//                if (!empty($memberData))
//                {
//                    $memberForm = new FormMembersProfile();
//                    $memberForm->populate($memberData[0]);
//                    $form->addSubForm($memberForm, 'memberForm');
//                    $form->getSubForm('memberForm')->setAttrib('class','DtDdWrapper');
//
//                    if ($this->_request->isPost())
//                    {
//                        $form->getSubForm('memberForm')->populate($data);
//                    }
//                }
//            }

            $this->view->form = $form;

            // Load data from profiles to build the tabs
            $tabsList = array();

            if (!$this->_isXmlHttpRequest)
            {
                $objects = $this->_objectList;
                unset($objects[$this->_currentAction]);
                $tabForm = null;

                foreach ($objects as $key => $objName)
                {
                    $obj = new $objName();
                    $isActive = $obj->findData(array($obj->getForeignKey() => $id));

                    $status = null;
                    if (isset ($isActive['MP_Status']))
                        $status = $isActive['MP_Status'];
                    if (count($isActive) > 0 && $status != -2)
                    {
                        $action = $key . 'Action';
                        $params = $this->$action(true);
                        $logData = $this->getLogData($id, $key);
                        $this->view->assign('log', $logData);
                        $tabForm = new $params['formName'](
                            array(
                                'moduleName' => $this->_moduleTitle . "/" . $key,
                                'baseDir'    => $baseDir,
                                'cancelUrl'  => $cancelUrl,
                                'imageSrc'   => $imageSrc,
                                'imgField'   => $this->_imageSrc,
                                'dataId'     => $id,
                                'object'     => $obj,
                                'data'       => $data,
                                'mode'       => 'edit',
                                'isNewImage' => 'true',
                                'disabledFieldsStatus' => true
                            )
                        );

                        if (isset($isActive['addressFact']))
                        {
                            $this->_setStatesIdList($isActive);
                            $isActive['addressFact']['selectedState'] = $this->view->selectedState;
                            $isActive['addressShipping']['selectedState'] = $this->view->selectedState;
                        }
                        $tabForm->populate($isActive);
                        array_push($tabsList, array($key, $tabForm));
                    }
                }
            }
            $this->view->assign('tabsList', $tabsList);
            // action
            if (!$this->_request->isPost())
            {
                $form->populate($data);
                if ($this->_isXmlHttpRequest)
                {
                    $form->getElement('submitSave')->setAttrib('disabled', true);
                    $render = $this->_name . '/formRenderer.phtml';

                    echo  $this->view->render($render);
                    exit;

                }
            }
            else
            {
                $addrBill = array();
                $addrShip = array();
                $formData = $this->_request->getParams();

                if ($this->_formatData && isset($formData['data']))
                {
                    $formData = $formData['data'];
                    $formData = $this->_mergeFormData($formData);
                }

                if (isset($formData['addressShipping']['duplicate']))
                {
                    $addrBill = $formData['addressFact'];
                    $addrShip = $formData['addressShipping'];

                    if ($formData['addressShipping']['duplicate'] == 1)
                    {
                        $subFormShip = $form->getSubForm('addressShipping');
                        foreach ($subFormShip as $key => $value)
                        {
                            $value->clearValidators()->setRequired(false);
                        }

                        unset($formData['addressShipping']);
                    }
                }

                if ($form->isValid($formData))
                {
                    if (isset($formData['isNewImage']) && $formData['isNewImage'] == 'true' && $form->getValue($this->_imageSrc) <> '')
                    {
                        $config = Zend_Registry::get('config')->toArray();
                        $srcOriginal = $this->_imageFolder
                                . $id
                                . "/tmp/"
                                . $form->getValue($this->_imageSrc);
                        $originalMaxHeight = $config[$this->_moduleTitle]['image']['original']['maxHeight'];
                        $originalMaxWidth = $config[$this->_moduleTitle]['image']['original']['maxWidth'];
                        $originalName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $originalMaxWidth
                                        . 'x'
                                        . $originalMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));


                        $srcMedium = $this->_imageFolder
                                . $id . "/tmp/medium_"
                                . $form->getValue($this->_imageSrc);

                        $mediumMaxHeight = $config[$this->_moduleTitle]['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config[$this->_moduleTitle]['image']['medium']['maxWidth'];
                        $mediumName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $mediumMaxWidth
                                        . 'x'
                                        . $mediumMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));

                        $srcThumb = $this->_imageFolder
                                . $id
                                . "/tmp/thumb_"
                                . $form->getValue($this->_imageSrc);
                        $thumbMaxHeight = $config[$this->_moduleTitle]['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config[$this->_moduleTitle]['image']['thumb']['maxWidth'];
                        $thumbName = str_replace(
                                        $form->getValue($this->_imageSrc),
                                        $thumbMaxWidth
                                        . 'x'
                                        . $thumbMaxHeight . '_'
                                        . $form->getValue($this->_imageSrc),
                                        $form->getValue($this->_imageSrc));

                        copy($srcOriginal, $srcMedium);
                        copy($srcOriginal, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcOriginal,
                                            'maxWidth' => $originalMaxWidth,
                                            'maxHeight' => $originalMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcMedium,
                                            'maxWidth' => $mediumMaxWidth,
                                            'maxHeight' => $mediumMaxHeight)
                        );
                        Cible_FunctionsImageResampler::resampled(
                                        array(
                                            'src' => $srcThumb,
                                            'maxWidth' => $thumbMaxWidth,
                                            'maxHeight' => $thumbMaxHeight)
                        );

                        rename($srcOriginal,
                                $this->_imageFolder
                                . $id . "/" . $originalName);
                        rename($srcMedium,
                                $this->_imageFolder
                                . $id . "/" . $mediumName);
                        rename($srcThumb,
                                $this->_imageFolder
                                . $id . "/" . $thumbName);
                    }

                    if ($this->_isXmlHttpRequest)
                        foreach ($formData as $key => $value)
                            if (is_string($value) && mb_check_encoding($value, 'UTF-8'))
                                $formData[$key] = $value;

                    $oData->save($id, $formData, $this->getCurrentEditLanguage());
                    $data = array(
                        'L_ModuleID' => $this->_moduleID,
                        'L_Action' => $this->_actionKey . ucfirst($this->_currentAction),
                        'L_UserID' => $this->view->user['EU_ID'],
                        'L_Data' => array($this->_currentAction . 'Id' => $id)
                    );
                    $options = array(
                        'moduleId' => $this->_moduleID,
                        'data' => $data
                    );
//                    $oLog = new Cible_Log($options);
//                    $oLog->log($data);
                    if ($this->_isXmlHttpRequest)
                    {
                        $this->disableView();
                        echo json_encode(true);
                    }
                    // redirect
//                    $this->_redirect($returnUrl);
                }
                else
                {
//                    $currentCity  = $formData['retailerForm[A_CityId]'][''];
                    if (isset($data['addressFact[A_StateId]']))
                    {
                        $this->_setStatesIdList($data);
                    }
                    $this->disableView();
                    echo json_encode(false);
                }
            }
        }
    }

    /**
     * Delete action for the current object.
     *
     * @access public
     *
     * @return void
     */
    public function deleteAction()
    {
        // variables
        $page = (int) $this->_getParam('page');
        $blockId = (int) $this->_getParam('blockID');
        $id = (!empty($this->_genericId))?$this->_genericId : (int) $this->_getParam($this->_ID);


        $this->view->action = $this->_currentAction;

        $baseDir      = $this->view->baseUrl() . "/";
        $cancelUrl    = '';

        $returnModule = $this->_getParam('returnModule');
        $returnAction = $this->_getParam('returnAction');
        if (!empty($returnModule))
            $cancelUrl .= $returnModule . '/';
        else
            $cancelUrl .= $this->_moduleTitle . "/";

        $cancelUrl .= $this->_name . "/";

        if (!empty($returnAction))
            $cancelUrl .= $returnAction . '/';
        else
            $cancelUrl .= 'general/';

        $cancelUrl .= "page/" . $page;

        $this->view->return = $cancelUrl;

        $editLink = $this->view->BaseUrl() . '/' . $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $this->_currentAction . "/"
                    . "actionKey/edit/" . $this->_ID . '/' . $id;

        $oDataName = $this->_objectList[$this->_currentAction];
        $oData = new $oDataName();

        if ($this->view->aclIsAllowed($this->_moduleTitle, 'manage', true))
        {
            //Find if there's other profiles linked to this user
            $profiles = $this->getRelatedProfiles($id);
            //Dislcaimer to inform the manager
            if(!empty($profiles))
            {
                $this->view->assign('profiles', $profiles);
                $this->view->assign('editLink', $editLink);
            }

            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($this->_isXmlHttpRequest)
                    $del = true;

                if ($del && $id > 0)
                {
                    if ($this->_isXmlHttpRequest)
                    {
                        $data = array();
                        // generate the form
                        $form = new $this->_formName(array('object' => $oData));
                        $data = $this->_setMemberCols($form);
                        if (!empty($data) && !is_null($this->_oMember))
                            $this->_oMember->save($id, $data, 1);

                        if ($oData != $this->_oMember)
                            $oData->delete($id);
                    }
                    else
                    {
                        //Delete the user
                        $oData->delete($id);
                        //Delete all the related profiles
                        if (!empty($profiles))
                        {
                            foreach ($profiles as $key => $values)
                            {
                                $oProfile = new $values['objName']();
                                if ($key != 'retailers')
                                    $oProfile->delete($id);
                                else
                                    $oProfile->delete($values['R_RetailersProfileId']);

                            }
                        }
                    }

                    if ($this->_isXmlHttpRequest)
                        echo json_encode ($del);
                }
                if (!$this->_isXmlHttpRequest)
                    $this->_redirect($cancelUrl);
            }
            elseif ($id > 0)
            {
                // get date details
                $this->view->data = $oData->populate($id, $this->getCurrentEditLanguage());
            }
        }
    }

    public function getRelatedProfiles($id)
    {
        $profiles = array();
        $objects = $this->_objectList;
        unset($objects[$this->_currentAction]);

        foreach ($objects as $key => $objName)
        {
            $obj = new $objName();
            if ($key != 'retailers')
                $tmpArray = $obj->findData(array($obj->getDataId() => $id));
            else
                $tmpArray = $obj->findData(array($obj->getForeignKey() => $id));

            if (isset($tmpArray[0]) && !empty($tmpArray))
                $profiles[$key] = $tmpArray[0];
            elseif (!empty($tmpArray))
                $profiles[$key] = $tmpArray;

            if (!empty($tmpArray))
                $profiles[$key]['objName'] = $objName;
        }

        return $profiles;
    }

    /**
     * Format the current action name to bu used for label texts translations.
     *
     * @access private
     *
     * @return void
     */
    private function _formatName()
    {
        $this->_labelSuffix = str_replace(array('/', '-'), '_', $this->_currentAction);
    }

    /**
     * Reditects the current action to the "real" action to process.
     *
     * @access public
     *
     * @return void
     */
    private function _redirectAction()
    {
        //Redirect to the real action to process If no actionKey = list page.
        switch ($this->_actionKey)
        {
            case 'add':
                $this->addAction();
                $this->_helper->viewRenderer->setRender('add');
                break;
            case 'edit':
                $this->editAction();
                $this->_helper->viewRenderer->setRender('edit');
                break;
            case 'delete':
                $this->deleteAction();
                $this->_helper->viewRenderer->setRender('delete');
                break;

            default:
                $this->_listAction($this->_objectList[$this->_currentAction]);
                break;
        }
    }

    /**
     * Set options array or the list view. Options are the actions in the page.
     *
     * @access public
     *
     * @param int $tabId Id of the row to be processed.
     * @param int $page  Id of the page if selected with the paginator.
     *
     * @return void
     */
    private function _setActionsList($tabId, $page = 1)
    {
        $commands = array();
        $actions = array();
        $actionPanel = array(
            'width' => '50px'
        );

        $options = array();

        if (count($this->_actionsList) == 0)
            $this->_actionsList = array(
                array('commands' => 'add'),
                array('action_panel' => 'edit', 'delete'),
            );

        foreach ($this->_actionsList as $key => $controls)
        {
            foreach ($controls as $key => $action)
            {
                //Redirect to the real action to process If no actionKey = list page.
                switch ($action)
                {
                    case 'add':
                        $commands = array(
                            $this->view->link($this->view->url(
                                            array(
                                                'controller' => $this->_name,
                                                'action' => $this->_currentAction,
                                                'actionKey' => 'add')),
                                    $this->view->getCibleText('button_add_' . $this->_labelSuffix),
                                    array('class' => 'action_submit add'))
                        );
                        break;

                    case 'edit':
                        $edit = array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $this->view->baseUrl() . "/"
                                . $this->_moduleTitle . "/"
                                . $this->_name . "/"
                                . $this->_currentAction . "/"
                                . "actionKey/edit/"
                                . $this->_ID . "/%ID%/page/" . $page,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            ),
                            'returnUrl' => $this->view->Url() . "/"
                        );

                        $actions['edit'] = $edit;
                        break;

                    case 'delete':
                        $delete = array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url'   => $this->view->baseUrl() . "/"
                                . $this->_moduleTitle . "/"
                                . $this->_name . "/"
                                . $this->_currentAction . "/"
                                . "actionKey/delete/"
                                . $this->_ID . "/%ID%/page/" . $page,
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => $tabId
                                )
                            )
                        );

                        $actions['delete'] = $delete;
                        break;

                    default:

                        break;
                }
            }
        }
        $actionPanel['actions'] = $actions;

        $options = array(
            'commands'     => $commands,
            'action_panel' => $actionPanel
        );
        if ($this->_disableExportToExcel)
            $options['disable-export-to-excel']= 'true';
        if ($this->_disableExportToPDF)
            $options['disable-export-to-pdf']= 'true';
        if ($this->_disableExportToCSV)
            $options['disable-export-to-csv']= 'true';
        if ($this->_enablePrint)
            $options['enable-print']= 'true';

        $options['actionKey'] = $this->_currentAction;

        return $options;
    }

    /**
     * Transforms data of the posted form in one array
     *
     * @param array $formData Data to save.
     *
     * @return array
     */
    protected function _mergeFormData($formData)
    {
        (array)$tmpArray = array();

        if ($this->_formatData)
            $formData = $this->_rebuildData($formData);

        foreach($formData as $key => $data)
        {
            if(is_array($data))
            {
                $tmpArray = array_merge($tmpArray,$data);
            }
            else
                $tmpArray[$key] = $data;
        }

        return $tmpArray;
    }

    /**
     * Add some data from other table, tests the joinTables
     * property. If not empty add tables and join clauses.
     *
     * @param Zend_Db_Table_Select $select
     * @param array $params
     *
     * @return Zend_Db_Table_Select
     */
    private function _addJoinQuery($select, array $params = array())
    {
        if (isset($params['joinTables']) && count($params['joinTables']))
            $this->_joinTables = $params['joinTables'];

        /* If needs to add some data from other table, tests the joinTables
         * property. If not empty add tables and join clauses.
         */
        if(count($this->_joinTables) > 0)
        {
            // Get the constraint attribute = foreign key to link tables.
            $constraint = $params['constraint'];
            // Loop on tables list(given by object class) to build the query
            foreach($this->_joinTables as $key => $object)
            {
                //Create an object and fetch data from object.
                $tmpObject      = new $object();
                $tmpDataTable   = $tmpObject->getDataTableName();
                $tmpIndexTable  = $tmpObject->getIndexTableName();
                //Add data to tables list
                $search = $tmpObject->getSearchColumns();
                if (!empty($search)){
                    $dataCols = isset($search['data']) ? $search['data'] : $search;
                    $indexCols = isset($search['index']) ? $search['index'] : array();
                }else{
                    $dataCols = $tmpObject->getDataColumns();
                    $indexCols = $tmpObject->getIndexColumns();
                }

                $this->_tables[$tmpDataTable] = $dataCols;
                $this->_tables[$tmpIndexTable] = $indexCols;
                //Get the primary key of the first data object to join table
                $tmpDataId  = $tmpObject->getDataId();
                // If it's the first loop, join first table to the current table
                if ($key == 0)
                {
                    $select->joinLeft($tmpDataTable, $tmpDataId . ' = ' . $constraint);
                    //If there's an index table then it too and filter according language
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable,
                                $tmpDataId . ' = ' . $tmpIndexId);
                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                    /* If there's more than one table to link, store the current
                     * table name for the next loop
                     */
                    if (count($this->_joinTables) > 1)
                        $prevConstraint = $tmpObject->getForeignKey();;
                }
                elseif ($key > 0)
                {
                    // We have an other table to join to previous.
                    $tmpDataId = $tmpObject->getDataId();

                    $select->joinLeft(
                            $tmpDataTable,
                            $prevConstraint . ' = ' . $tmpDataId);
                    if (!empty($tmpIndexTable))
                    {
                        $tmpIndexId = $tmpObject->getIndexId();
                        $select->joinLeft(
                                $tmpIndexTable,
                                $constraint . ' = ' . $tmpIndexId);
                        $select->where(
                                $tmpObject->getIndexLanguageId() . ' = ?',
                                $this->_defaultEditLanguage);
                    }
                }
            }
        }

        return $select;
    }

    public function userExistsAction()
    {
        $email   = $this->_getParam('GP_Email');
        $id      = $this->_getParam('id');
        $isValid = true;
        $oGenericProfile = new GenericProfile();
        $profiles = $oGenericProfile->findMembers(array('email' => $email,'member_id'=>$id));

        foreach ($profiles as $key => $profile)
        {
            if ($profile['member_id'] == $id)
            {
                unset($profiles[$key]);
                break;
            }
        }

        if (count($profiles) > 0)
            $isValid = false;

        if (!$id && !$isValid)
        {
            $editLink = $this->view->baseUrl() . "/"
                            . $this->_moduleTitle . "/"
                            . $this->_name . "/"
                            . $this->_currentAction . "/"
                            . "actionKey/edit/"
                            . $this->_ID . "/" . $profiles[0]['member_id'];
            return $editLink;
        }

        if ($this->_isXmlHttpRequest)
        {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            echo json_encode($isValid);
        }

    }

    private function _rebuildData($data = null)
    {
        $build = array();
        if ($this->_formatData)
        {
            $cleaned = urldecode($data);
            $data = explode('&',$cleaned);
        }

        if (!empty($cleaned))
        {
            foreach ($data as $key => $tmp)
            {
                $tmp = explode('=', $tmp);
                if (is_array($tmp))
                {
                    $tmp[0] = str_replace('[]', '', $tmp[0]);
                    if (!array_key_exists($tmp[0], $build))
                        $build[urldecode($tmp[0])] = urldecode($tmp[1]);
                    else
                    {
                        $prevData = $build[$tmp[0]];
                        $value = $prevData . ',' . $tmp[1];
                        $build[$tmp[0]] = $value;
                    }

                }
                else
                    $build[$key] = $tmp;

            }
        }

        return $build;
    }

    private function _setMemberCols($data = null)
    {
        $member = array();

        if ($data instanceof Zend_Form)
        {
            $subForms = $data->getSubForms();
            foreach ($subForms as $index => $subForm)
            {
                $isMemberField = (preg_match('/^MP_/', $index));
                if ($isMemberField)
                $member[$index] = '';
                $elements = $subForm->getElements();
                foreach ($elements as $name => $element)
                {
                    $isMemberField = (preg_match('/^MP_/', $name));
                    if ($isMemberField)
                        $member[$name] = '';
                }
            }
            $elements = $data->getElements();
            foreach ($elements as $name => $element)
            {
                $isMemberField = (preg_match('/^MP_/', $name));
                if ($isMemberField)
                    $member[$name] = '';
            }
        }
        elseif (!empty($data))
        {
            $isMemberField = false;
            foreach ($data as $tmp)
            {
                $isMemberField = (preg_match('/^MP_/', $tmp['name']) || !preg_match('/^[A-Z]*_/', $tmp['name']));
                if ($isMemberField)
                {
                    $member[$tmp['name']] = $tmp['value'];
                }
            }
        }

        if (isset($member['MP_Status']))
            $member['MP_Status'] = -2;

        return $member;
    }

    public function getLogData($userId, $filter = '')
    {
        foreach ($this->view->profilesList as $key => $tmp)
        {
            $exist = array_search($filter, $tmp);
            if ($exist)
            {
                $moduleId = $key;
                break;
            }
        }

        $data = array(
            'userId' => $userId,
            'moduleId' => $moduleId,
            'module' => $filter,
            'language' => $this->_currentInterfaceLanguage,
        );
        $options = array(
            'send' => true,
            'isHtml' => true,
            'moduleId' => $moduleId,
            'event' => 'allLog',
            'type' => 'screen',
            'recipient' => 'admin',
            'data' => $data
        );

        $oNotification = new Cible_Notifications_Screen($options);

        return $oNotification->getMessage();
    }

    private function _setStatesIdList($data)
    {
        $config = Zend_Registry::get('config');
        $current_state = $config->address->default->states;

        if (isset($data['addressFact']['A_StateId']))
            $current_state  = $data['addressFact']['A_StateId'] . $this->_separ;
        if (isset($data['addressShipping']['A_StateId']))
            $current_state .= $data['addressShipping']['A_StateId'] . $this->_separ;
        if (isset($data['retailerForm']['A_StateId']))
            $current_state .= $data['retailerForm']['A_StateId'] ;

        if (isset($data['addressFact[A_StateId]']))
        {
            $current_state  = $data['addressFact[A_StateId]'] . $this->_separ;
            $current_state .= $data['addressShipping[A_StateId]'] . $this->_separ;
            $current_state .= $data['retailerForm[A_StateId]'] ;
        }

        $this->view->assign('selectedState', $current_state);
    }

    /**
     * Export data according to given parameters.
     *
     * @return void
     */
    public function toExcelAction()
    {
        $this->type = 'CSV';
        $this->filename = $this->_actionKey . '.csv';

        $dispatcher = $this->getFrontController()->getDispatcher();
        $actionName = $dispatcher->formatActionName($this->_actionKey);
        $params = $this->$actionName(true);
        $oDataName = $this->_objectList[$this->_actionKey];
        $lines = new $oDataName();
        $foreignKey = $lines->getForeignKey();

        $params['foreignKey'] = $foreignKey;

        $this->tables = array(
            $lines->getDataTableName() => $lines->getDataColumns()
        );

        $this->view->params = $this->_getAllParams();

        $this->fields = $this->_colTitle;
        $this->filters = array();

        $langId = $this->_defaultEditLanguage;

        $select = $lines->getAll($langId, false);
        $this->select = $this->_addJoinQuery($select, $params);

        parent::toExcelAction();
    }
}
