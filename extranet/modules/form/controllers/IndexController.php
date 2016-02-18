<?php
/**
 * Class Form - Manage actions for the module administration.
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class Form - Manage actions for the module administration.
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class Form_IndexController extends Cible_Controller_Categorie_Action
{
    protected $_moduleID      = 13;
    protected $_defaultAction = 'edit';
    protected $_name          = 'form';

    /**
     * Initiate some specific variables on loading
     *
     * @return void
     */
    public function  init()
    {
        parent::init();
        //Add specific style sheet
        $this->view->headLink()->prependStylesheet(
                $this->view->locateFile('moduleForm.css')
            );

    }
    /**
     * Delete a form and the corresponding elements.
     *
     * return void
     */
    public function deleteAction()
    {
        $this->view->title = "Suppression d'un formulaire";

        if ($this->view->aclIsAllowed($this->_name, 'manage', true))
        {
            // variables
            $formID = (int)$this->_getParam('formID');
            // generate the form
            $returnUrl =  "/form/index/list/";

            $this->view->assign('return', "{$this->view->baseUrl()}{$returnUrl}");

            $formSelect = new Form();
            $select     = $formSelect->getFormList($formID);
            $formData   = $formSelect->fetchRow($select);

            $this->view->form = $formData->toArray();
            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $formID > 0)
                {
                    // Delete the emails list
                    $oNotification = new FormNotificationObject();
                    $oNotification->delete($formID);
                    // Delete the form;
                    $formDel = new FormObject();
                    $formDel->deleteAll($formID);
                 }

                 $this->_redirect($returnUrl);
            }
        }
    }

    /**
     * Get the list of the forms to display
     *
     * return void
     */
    public function listAction()
    {
        if ($this->view->aclIsAllowed($this->_name,'manage',true))
        {
            // Define the tables columns for data search
            $tables     = array(
                'Form'      => array('F_ID'),
                'FormIndex' => array('FI_FormID', 'FI_LanguageID', 'FI_Title')
            );
            // Define the colums list to display, with some parameters
            $field_list = array(
                'F_ID'     => array('width' => '50px'),
                'FI_Title' => array('width' => '300px'),
            );

            $this->view->params = $this->_getAllParams();
            $pageID  = $this->_getParam( 'pageID' );

            $forms  = new Form();
            $select = $forms->getFormList();

            // Define the options to display on the page
            $options = array(
                    'commands' => array(
                        $this->view->link($this->view->url(
                                array(
                                    'controller'=>'index',
                                    'action'=>'add'
                                    )
                                ),
                                $this->view->getCibleText('button_add_form'),
                                array('class'=>'action_submit add')
                            )
                    ),
                    'disable-export-to-excel' => 'true',
                    'enable-print' => 'true',
                    'filters' => array(
                        'form-status-filter' => array(
                            'label' => 'Filtre 1',
                            'default_value' => null,
                            'associatedTo' => 'S_Code',
                            'choices' => array(
                                '' => $this->view->getCibleText('filter_empty_status'),
                                'online' => $this->view->getCibleText('status_online'),
                                'offline' => $this->view->getCibleText('status_offline')
                            )
                        )
                    ),
                    'action_panel' => array(
                        'width' => '50',
                        'actions' => array(
                            'edit' => array(
                                'label' => $this->view->getCibleText('button_edit'),
                                'url' => "{$this->view->baseUrl()}/form/index/edit/formID/%ID%",
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'F_ID'
                                )
                            ),
//                            'response' => array(
//                                'label' => $this->view->getCibleText('button_response'),
//                                'url' => $this->view->baseUrl()
//                                        . "/form/index/list-response/formID/%ID%/",
//                                'findReplace' => array(
//                                    'search' => '%ID%',
//                                    'replace' => 'F_ID'
//                                )
//                            ),
                            'delete' => array(
                                'label' => $this->view->getCibleText('button_delete'),
                                'url' => $this->view->baseUrl()
                                        . "/form/index/delete/formID/%ID%/"
                                        . $pageID,
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'F_ID'
                                )
                            )
                        )
                    )
            );
            // build the page content with the paginator.
            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
            // Set data into the view to be displayed
            $this->view->assign('mylist', $mylist);
        }

    }

    public function listResponseAction()
    {
        if ($this->view->aclIsAllowed($this->_name,'manage',true))
        {
            $tables     = array();
            $field_list = array();

            $this->view->params = $this->_getAllParams();
            $formID = $this->_getParam( 'formID' );



            //$mylist = New Cible_Paginator($select, $tables, $field_list, $options);
            $this->view->assign('mylist', $mylist);
        }
    }

    public function addAction()
    {
        // web page title
        $this->view->title = "Ajout d'un formulaire";

        // variables
        $returnAction = $this->_getParam('return');
        $baseDir      = $this->view->baseUrl();
        $cancelUrl    =  $baseDir . "/form/index/list/";


//        $this->headLink()->appendStylesheet($baseDir
//                . '/themes/default/css/moduleForm.css');

        if($returnAction)
            $returnUrl = "/form/index/$returnAction";
        else
            $returnUrl = "/form/index/edit/formID/";

        if ($this->view->aclIsAllowed($this->_name,'edit',true))
        {
            // generate the form
            $form = new FormForm(
                    array(
                        'baseDir'   => $baseDir,
                        'cancelUrl' => $cancelUrl
                        )
                    );
            $saveButton = $form->getElement('submitSave');
            $saveButton->setLabel('Etape suivante');

            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData))
                {
                    $tmp = $formData['FN_Email'];
                    unset($formData['FN_Email']);

                    $formObject = new FormObject();

                    $formID = $formObject->insert(
                            $formData,
                            $this->_defaultEditLanguage
                            );
                    $languages = Cible_FunctionsGeneral::getAllLanguage();
                    foreach ($languages as $key => $lang)
                    {
                        if ($lang['L_ID'] != $this->_defaultEditLanguage)
                        {
                            $formObject->save($formID, $formData, $lang['L_ID']);
                        }
                    }
                    $oNotification = new FormNotificationObject();
                    $oNotification->save($formID, array('FN_Email' => $tmp), 1);

                    $this->_redirect($returnUrl . $formID);
                }
                else
                {
                    $form->populate($formData);
                }
            }
        }
    }

    public function editAction()
    {
        // web page title
        $this->view->title = "Edition de formulaire";
        // Tests if the user has permissions
        if ($this->view->aclIsAllowed($this->_name, 'manage', true))
        {
            // set variables needed for the process
            $formID       = $this->_getParam('formID');
            $returnAction = $this->_getParam('return');
            $pageID       = $this->_getParam('pageID');
            $blockID      = $this->_getParam('blockID');
            $baseDir      = $this->view->baseUrl();

            if (empty ($formID) && !empty($blockID))
            {
                $formID = Cible_FunctionsBlocks::getBlockParameter($blockID, '1');
            }
            // Define the url where to return
            if($returnAction)
                $returnUrl = "/form/index/$returnAction";
            elseif($pageID)
                    $returnUrl = "/page/index/index/ID/$pageID";
            else
                $returnUrl = "/form/index/list/";

            //Add specific js file
            $this->view->headScript()->appendFile(
                    $this->view->locateFile('headerFormActions.js')
                );


            $this->view->headScript()->appendFile(
                    $this->view->locateFile('tinymce/tinymce.min.js')
                );

            $oNotifications = new FormNotificationObject();
            $recipients = $oNotifications->getAll(null, true, $formID);

            // Get data for the current form
            $oForm = new FormObject();
            $data  = $oForm->populate($formID, $this->getCurrentEditLanguage());

            //Generate the form for the back button
            $formBack = new FormBackButton(array(
                'baseDir'   => $baseDir,
                'cancelUrl' => "$baseDir$returnUrl",
                'disableAction' => false,
            ));
            $formBack->removeElement('submitSave');
            $formBack->getElement('cancel')
                ->setDecorators(array(
                    'ViewHelper',
                    array(array('data'=>'HtmlTag'),array('tag'=>'li')),
                    array(array('row'=>'HtmlTag'),array('tag'=>'ul', 'openOnly'=>true, 'class' => 'actions-buttons'))
                ))
                ->setOrder(1);
            $this->view->formBack = $formBack;

            //Generate the form
            $form = new FormForm(array(
                'baseDir'       => $baseDir,
                'cancelUrl'     => "$baseDir$returnUrl",
                'formID'        => $formID,
                'disableAction' => true,
                'recipients'    => $recipients
            ));

            $this->view->form = $form;

            //Add question type data
            $oQuestion = new FormQuestionTypeObject();
            $questionData = $oQuestion->getAll($this->getCurrentEditLanguage());
            $this->view->questionType = $questionData;

             // action: If not post action (send modification)
            if(!$this->_request->isPost())
            {
                //Populate the for with data from db
                $form->populate($data);
            }
            else
            {
                // Get data sent
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $languages = Cible_FunctionsGeneral::getAllLanguage();
                    // and save value in db
                    $oForm->save($formID, $formData, $this->getCurrentEditLanguage());
                    $this->_redirect($returnUrl);
                }
            }
        }

    }

    public function toExcelAction()
    {

    }

    /**
     * Manage the update of the form parameters sent via ajax (jQuery)
     *
     * @return void
     */
    public function updateformparamAction()
    {
        $saved = false;
        $data = $this->_getAllParams();

        foreach ($data as $key => $value)
        {
            $data[$key] = urldecode($data[$key]);
        }

        if ($data['model'] != 'Form')
            $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
        else
            $dataObject = ucfirst($data['model']) . 'Object';

        if (strpos($dataObject, 'Validation') > 0)
        {
            switch($data['FQVT_Category'])
            {
                case 'VAL':
                    $oValidation = new FormQuestionValidationObject($data);
                    $oValidation->update();
                    break;
                case 'MIX':
                    $oValidation = new FormQuestionValidationObject($data);
                    $oOption     = new FormQuestionOptionObject($data);
                    $saved2 = $oValidation->update();
                    $saved1 =$oOption->update();

                    break;
                case 'OPT':
                    $oOption     = new FormQuestionOptionObject($data);
                    $oOption->update();
                    break;
                default:
            }
        }
        else
        {
            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            $lang = $this->_defaultInterfaceLanguage;

            if (isset($initData['lang']))
                $lang = $initData['lang'];

            $data[$oForm->getDataId()] = $initData['id'];
            if ($oForm->recordExists($data, $lang))
                $saved = $oForm->save($initData['id'], $data, $lang);

            echo $saved;
        }
        exit;
    }

    /**
     * Get langages list
     *
     * @return void
     */
    public function getlanguagesAction()
    {
        $languagesList = Cible_FunctionsGeneral::getAllLanguage();
        foreach ($languagesList as $arg => $lang)
        {
        	foreach ($lang as $_key => $data)
        	{
                $tmpData[$_key] = $data;
        	}

        	$tmp[$arg] = $tmpData;

        }
        echo json_encode($tmp);
        exit;
    }

    /**
     * Get form title to reload for language switching
     *
     * @return void
     */
    public function reloadAction()
    {
    	if ($this->_request->isGet())
    	{
            $tmp  = 0;
            $id   = $this->_request->getParam('FI_FormID');
            $lang = $this->_request->getParam('FI_LanguageID');

	    $oForm = new Form();
            $form  = $oForm->getFormList($id, $lang);
            $row   = $oForm->fetchRow($form);

            if (count($row))
            {
                $data  = $row->toArray();
                $tmp   = $data['FI_Title'];

            }

            echo json_encode($tmp);
        }

        exit;
    }

}