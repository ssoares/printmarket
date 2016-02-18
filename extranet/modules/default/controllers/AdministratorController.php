<?php
/**
* Make the management of the directors of the extranet
*
* The system can view, add, edit and remove directors of the extranet. It also allows an administrator to associate one or several groups to provide access rights.
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
class AdministratorController extends Cible_Extranet_Controller_Module_Action
{
    protected $_paramId = 'administratorID';
    public function indexAction()
    {
        // NEW LIST GENERATOR CODE //
        $tables = array(
                'Extranet_Users' => array('EU_ID','EU_LName','EU_FName','EU_Email', 'EU_Disabled')
        );

        $field_list = array(
            'EU_FName' => array('width' => '300px'),
            'EU_LName' => array('width' => '300px'),
            'EU_Email' => array('width' => '300px'),
            'EU_Disabled' => array(
                'width' => '300px',
                'useFormLabel' => true,
                'postProcess' => array('type' => 'yesNo')
                )
            );
        $administratorData = new ExtranetUsers();
        $adminGroup = $this->view->isAdministrator();
        if ($adminGroup <= 2){
            $administratorData->setIncludeAll(true);
        }
        $select = $administratorData->getAdminEqualOrOver($adminGroup);
        $adminData = $administratorData->getAdminEqualOrOver($adminGroup, true);
        $dbs = Zend_Registry::get('dbs');
        $db = $dbs->getDb('admins');
        $adminCible = new ExtranetUsers(array('db' => $db));
        $adminsCible = $adminCible->getAdminEqualOrOver($adminGroup, true);
        foreach ($adminsCible as $key => $value)
        {
            $admins[$value['EU_ID']] = $value;
            $data = $administratorData->setId($value['EU_ID'])->populate();
            if (empty($data)){
                $admins[$value['EU_ID']]['disabled'] = true;
            }
        }

        $defaultSite = $this->view->user['EU_DefaultSite'];
        $adminLevel = $this->_config->levels->$defaultSite;
        foreach ($adminData as $key => $value)
        {
            $site = $value['EU_DefaultSite'];
            $level = $this->_config->levels->$site;
            if ($level > $adminLevel && $adminGroup > 1){
                $notIn[] = $value['EU_ID'];
            }
        }
        if (!empty($notIn)){
            $select->where('EU_ID not in (?)', implode(',', $notIn));
        }

        $options = array(
            'commands' => array(
                $this->view->link($this->view->url(array('controller'=>'administrator','action'=>'add')),$this->view->getCibleText('button_add_administrators'), array('class'=>'action_submit add') )
            ),
            //'disable-export-to-excel' => 'true',
            'action_panel' => array(
                'width' => '50',
                'actions' => array(
                    'edit' => array(
                        'label' => $this->view->getCibleText('button_edit'),
                        'url' => "{$this->view->baseUrl()}/default/administrator/edit/administratorID/%ID%",
                        'findReplace' => array(
                            'search' => '%ID%',
                            'replace' => 'EU_ID'
                        )
                    ),
                    'delete' => array(
                        'label' => $this->view->getCibleText('button_delete'),
                        'url' => "{$this->view->baseUrl()}/default/administrator/delete/administratorID/%ID%",
                        'findReplace' => array(
                            'search' => '%ID%',
                            'replace' => 'EU_ID'
                        )
                    )
                )
            )
        );

        $options['adapter'] = 'arrayAdmin';
        $options['adapterData'] = $admins;
        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

        $this->view->assign('mylist', $mylist);
        $this->view->assign('adminCible', $admins);
    }

    public function editAction()
    {
        // page title
        $this->view->title = "Profil de l'administrateur";

        // get param
        $administratorID = $this->_getParam($this->_paramId);
        $order           = $this->_getParam('order');
        $tablePage       = $this->_getParam('tablePage');
        $search          = $this->_getParam('search');

        $paramsArray = array("order" => $order, "tablePage" => $tablePage, "search" => $search);
        $sites = $this->view->siteList(array('getValues' => true));
        $dbs = Zend_Registry::get('dbs');
        $returnLink = $this->view->url(array('module' => 'default',
                'controller' => 'administrator',
                'action' => 'index',
                $this->_paramId => null));
        // get user data
        $userData = Cible_FunctionsAdministrators::getAdministratorData($administratorID, null, $this->view->isAdministrator());
        if (!$userData){
            $this->_redirect(str_replace($this->view->baseUrl(), '',$returnLink));
        }
        /********** ACTIONS ***********/
        $this->view->joinAssociation = true;
        $this->view->headScript()->appendFile($this->view->locateFile('associationSet.js'));
        $this->view->data = array('entities' =>$sites);
        if ($this->_isXmlHttpRequest){
            $this->_associationIds = array();
            $this->ajaxAction();
            exit;
        }
        $form = new FormExtranetUser(array(
            'baseDir'   => $this->view->baseUrl(),
            'cancelUrl' => "$returnLink",
            'userId' =>  $administratorID
            ),
            $this->view->isAdministrator()
        );

        $this->view->assign($this->_paramId, $administratorID);
        $this->view->assign('form', $form);

        $optionsList = array();
        $tmpList = explode('|', $userData->EU_SiteAccess);
        foreach ($tmpList as $value)
            if (isset($sites[$value]))
                $optionsList[$value] = $value;

        $this->view->related = $optionsList;
        $this->view->default = $userData->EU_DefaultSite;
        if ( !$this->_request->isPost() ){

            $form->populate($userData->toArray());
        }
        else {
            $formData = $this->_request->getPost();
            $sitesAccess = $this->_getSitesAccess($formData['entitiesSet']);
            $formData['testAccess'] = $sitesAccess['access'];
            $formData['testDefault'] = $sitesAccess['default'];
            if ($form->isValid($formData)) {
                // validate username is unique
                // save user information
                $options = array('type' => $this->_config->type,
                        'initialDb' => $this->_config->db);
                $this->_helper->switchDB->setOptions($options);
                $currentPwd = $userData['EU_Password'];
                foreach ($formData['entitiesSet'] as $assocations)
                {
                    $site = $assocations['entities'];
                    $db = $this->_helper->switchDB->setEntity($site)
                        ->loadDbConfig()
                        ->getCurrentDb();
                    unset($optionsList[$site]);
                    $userDB = new ExtranetUsers(array('db' => $db));
                    // get user data
                    $userData = Cible_FunctionsAdministrators::getAdministratorData($administratorID, $db);
                    $exists = $userDB->recordExists($form->getValue('EU_Email'));
                    if (!$userData && !$exists){
                        $userData = $userDB->createRow(array('EU_ID' => $administratorID));
                        $userData->EU_Password = $currentPwd;
                    }elseif($exists){
                        $userData = Cible_FunctionsAdministrators::getAdministratorData($exists, $db);
                        $userData->EU_ID = $administratorID;
                    }
                    $userData['EU_LName']       = $form->getValue('EU_LName');
                    $userData['EU_FName']       = $form->getValue('EU_FName');
                    $userData['EU_Email']       = $form->getValue('EU_Email');
                    $userData['EU_Username']    = $form->getValue('EU_Username');
                    $userData['EU_ShowError']   = $form->getValue('EU_ShowError');
                    $userData['EU_DefaultSite'] = $sitesAccess['default'];
                    $userData['EU_SiteAccess']  = $sitesAccess['access'];
                    $userData['EU_Disabled']    = $form->getValue('EU_Disabled');
                    $userData->EU_ModifDate = date('Y-m-d H:i:s');

                    if ($form->getValue('EU_Password') <> ""){
                        $userData['EU_Password']  = md5($form->getValue('EU_Password'));
                    }

                    $userData->save();

                    // delete all user and group association for that user
                    if ($exists){
                        $where = 'EUG_UserID = ' . $exists;
                    }else{
                        $where = 'EUG_UserID = ' . $administratorID;
                    }
                    $userGroups = new ExtranetUsersGroups(array('db' => $db));
                    $userGroups->delete($where);

                    // insert all user and group association for that user
                    if ($assocations['groups']){
                        foreach ($assocations['groups'] as $group){
                            $userGroupAssociationData = new ExtranetUsersGroups(array('db' => $db));
                            $row = $userGroupAssociationData->createRow();
                            $row->EUG_UserID    =   $administratorID;
                            $row->EUG_GroupID   =   $group;
                            $row->save();
                        }
                    }
                }
                //Delete from entities no more avalaible
                foreach($optionsList as $key => $site){
                    $db = $this->_helper->switchDB->setEntity($site)
                    ->loadDbConfig()
                    ->getCurrentDb();
                    $userData = Cible_FunctionsAdministrators::getAdministratorData($administratorID, $db);
                    $userData->EU_DefaultSite = $sitesAccess['default'];;
                    $userData->EU_SiteAccess = $sitesAccess['access'];;
                    $userData->EU_Disabled = 1;
                    $userData->save();
                }
                if (!isset($formData['submitSaveClose'])){
                    $returnLink = $this->view->url(array($this->_paramId => $administratorID));
                }
                header("location:".$returnLink);
            }
            else
            {
                $optionsList = array();
                $tmpList = explode('|', $sitesAccess['access']);
                foreach ($tmpList as $value)
                    $optionsList[$value] = $value;
                $tmp = array();
                foreach($formData['entitiesSet'] as $key => $entity){
                    $tmp[] = $entity['groups'];
                }
                $this->view->related = $optionsList;
                $this->view->default = $sitesAccess['default'];
                $this->view->groups = json_encode($tmp);

            }
        }
    }

    public function addAction()
    {
        // page title
        $this->view->title = "Ajout d'un administrateur";

        /********** ACTIONS ***********/
        $returnLink =
            $this->view->url(array('controller' => 'administrator',
            'action' => 'index', $this->_paramId => null));
        $related = $this->_config->relatedEntities->toArray();
        $sites = $this->view->siteList(array('getValues' => true));
        if (!empty($related) && count($sites) > count($related)){
            $sites = $related;
        }else{
            $related = $sites;
        }
        $this->view->joinAssociation = true;
        $this->view->headScript()->appendFile($this->view->locateFile('associationSet.js'));
        $this->view->data = array('entities' =>$sites);
        if ($this->_isXmlHttpRequest){
            $this->_associationIds = array();
            $this->ajaxAction();
            exit;
        }
        $form = new FormExtranetUser(array(
            'baseDir'   => $this->view->baseUrl(),
            'cancelUrl' => "$returnLink",
            'sites' => $sites,
            ),
            $this->view->isAdministrator()
        );

        $form->getElement('cancel')->setAttrib('onclick', 'document.location.href="'.$returnLink.'"');
        $form->getElement("EU_Password")->setRequired(true);
        $form->getElement("EU_Password")->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => "Veuillez entrer un mot de passe")));
        $form->getElement("PasswordConfirmation")->setRequired(true);
        $form->getElement("PasswordConfirmation")->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->view->getCibleText('validation_message_empty_field'))));
        $this->view->form = $form;
        $dbs = Zend_Registry::get('dbs');

        if ($this->_request->isPost() ){
            $formData = $this->_request->getPost();
            $sitesAccess = $this->_getSitesAccess($formData['entitiesSet']);
            $formData['testAccess'] = $sitesAccess['access'];
            $formData['testDefault'] = $sitesAccess['default'];
            if ($form->isValid($formData)) {
                $name = 'Extranet_Users';
                $options = array('type' => $this->_config->type,
                    'initialDb' => $this->_config->db,
                    'tableName' => $name);
                $this->_helper->switchDB->setOptions($options);
                $autoincrement = 0;
                foreach($related as $site => $siteName)
                {
                    $ai = $this->_helper->switchDB->setEntity($site)
                        ->loadDbConfig()
                        ->getLastAutoIncrement();
                    $autoincrement = $ai > $autoincrement ? $ai : $autoincrement;
                }
                foreach ($formData['entitiesSet'] as $assocations)
                {
                    $site = $assocations['entities'];
                    $db = $this->_helper->switchDB->setEntity($site)
                        ->loadDbConfig()
                        ->getCurrentDb();
                    unset($sites[$site]);
                    $userDB = new ExtranetUsers(array('db' => $db));
                    $userData = Cible_FunctionsAdministrators::getAdministratorData($autoincrement, $db);
                    $exists = $userDB->recordExists($form->getValue('EU_Email'));
                    if (!$userData && !$exists){
                        $userData = $userDB->createRow(array('EU_ID' => $autoincrement));
                        $userData->EU_CreaDate = date('Y-m-d H:i:s');
                    }elseif($exists){
                        $userData = Cible_FunctionsAdministrators::getAdministratorData($exists, $db);
                        $userData->EU_ID = $autoincrement;
                    }
                    $userData->EU_LName      = $form->getValue('EU_LName');
                    $userData->EU_FName      = $form->getValue('EU_FName');
                    $userData->EU_Email      = $form->getValue('EU_Email');
                    $userData->EU_Username   = $form->getValue('EU_Username');
                    $userData->EU_Password   = md5($form->getValue('EU_Password'));
                    $userData->EU_DefaultSite  = $sitesAccess['default'];;
                    $userData->EU_SiteAccess  = $sitesAccess['access'];;
                    $userData->EU_ShowError  = $form->getValue('EU_ShowError');
                    $newInsertID = $userData->save();

                    if ($exists){
                        // delete all user and group association for that user
                        $userGroups = new ExtranetUsersGroups(array('db' => $db));
                        $where = 'EUG_UserID = ' . $newInsertID;
                        $userGroups->delete($where);
                    }
                    // insert all user and group association for that user
                    if ($assocations['groups']){
                        foreach ($assocations['groups'] as $group){
                            $userGroupAssociationData = new ExtranetUsersGroups(array('db' => $db));

                            $rowGroup = $userGroupAssociationData->createRow();
                            $rowGroup->EUG_UserID    =   $newInsertID;
                            $rowGroup->EUG_GroupID   =   $group;

                            $rowGroup->save();
                        }
                    }
                }
                $autoincrement = $this->_helper->switchDB
                    ->getLastAutoIncrement();
                foreach($related as $site => $siteName)
                {
                    $db = $this->_helper->switchDB->setEntity($site)
                        ->loadDbConfig()
                        ->getCurrentDb();
                    $db->getConnection()->exec('Alter Table ' . $name
                        . ' AUTO_INCREMENT=' . $autoincrement);
                }
                if (!isset($formData['submitSaveClose'])){
                    $returnLink = $this->view->url(array('action' => 'edit',
                        $this->_paramId => $newInsertID));
                }
                header("location:".$returnLink);
            }
            else
            {
                $optionsList = array();
                $tmpList = explode('|', $sitesAccess['access']);
                foreach ($tmpList as $value)
                    $optionsList[$value] = $value;
                foreach ($tmpList as $value)
                    $optionsList[$value] = $value;

                $this->view->related = $optionsList;
                $this->view->default = $sitesAccess['default'];
                $tmp = array();
                foreach($formData['entitiesSet'] as $key => $entity){
                    $tmp[] = $entity['groups'];
                }
                $this->view->groups = $tmp;
                $form->populate($formData);
            }
        }
    }

    public function deleteAction()
    {
        // set page title
        $this->view->title = "Supprimer un administrateur";

        // get params
        $administratorID = (int)$this->_getParam( 'administratorID' );

        if ($this->_request->isPost()) {
            // if is set delete, then delete
            $delete = isset($_POST['delete']);
            $returnLink = $this->view->url(array('controller' => 'administrator', 'action' => 'index', 'administratorID' => null));
            if ($delete && $administratorID > 0) {
                $sites = $this->view->siteList(array('getValues' => true));
                $options = array('type' => $this->_config->type,
                        'initialDb' => $this->_config->db);
                $this->_helper->switchDB->setOptions($options);
                foreach ($sites as $site => $siteName)
                {
                    $db = $this->_helper->switchDB->setEntity($site)
                        ->loadDbConfig()
                        ->getCurrentDb();
                    $user = Cible_FunctionsAdministrators::getAdministratorData($administratorID, $db);
                    if (!empty($user)){
                        $user->EU_Disabled = 1;
                        $user->save();
                        // delete all user and group association for that user
                        $userGroups = new ExtranetUsersGroups(array('db' => $db));
                        $where = 'EUG_UserID = ' . $administratorID;
                        $userGroups->delete($where);
                    }
                }

            }
            //$this->_redirect($returnLink);
            header("location:".$returnLink);
        }
        else
        {
            if ($administratorID > 0) {
                $administrator = new ExtranetUsers();
                $this->view->administrator = $administrator->fetchRow('EU_ID='.$administratorID);
            }
        }
    }

    public function toExcelAction(){
        $this->filename = 'Administrators.xlsx';

        $this->tables = array(
                'Extranet_Users' => array('EU_ID','EU_LName','EU_FName','EU_Email')
        );

        $this->fields = array(
            'EU_FName' => array(
                'width' => '',
                'label' => ''
            ),
            'EU_LName' => array(
                'width' => '',
                'label' => ''
            ),
            'EU_Email' => array(
                'width' => '',
                'label' => ''
            )
        );

        $this->filters = array(

        );

        $administratorData = new ExtranetUsers();
        $this->select = $administratorData->select();

        parent::toExcelAction();
    }

    public function profileAction(){
        // page title
        $this->view->title = "Votre profil";
        $sites = $this->view->siteList(array('getValues' => true));
        $dbs = Zend_Registry::get('dbs');
        // get user data
        $authData = $this->view->user;
        $authID     = $authData['EU_ID'];

        $users = new ExtranetUsers();
        $select = $users->select()
        ->where("EU_ID = ?", $authID);


        $userData = $users->fetchRow($select);

        if($userData==""){
            echo "<br /><font style='font-size:18px;'> Vous êtes un super utilisateur.<br />Vous ne pouvez gérer vos informations ici.<br/>"
            . "Vous pouvez modifier vos informations dans la base 'cible_admin'</font> ";

        }
        else{
            /********** ACTIONS ***********/
            $form = new FormExtranetUser(array(
                'baseDir'   => $this->view->baseUrl(),
                'cancelUrl' => $this->getFrontController()->getBaseUrl(),
                'userId' => $authID,
                'profile' => true
            ),array(), $this->view->isAdministrator());
            $this->view->form = $form;

            if ( !$this->_request->isPost() ){
                $form->populate($userData->toArray());
            }
            else {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData)) {
                    // save user information
                    foreach ($sites as $site => $siteName)
                    {
                        $db = $dbs->getDb($site);
                        // get user data
                        $userData = Cible_FunctionsAdministrators::getAdministratorData($authID, $db);

                        $userData['EU_LName']     = $form->getValue('EU_LName');
                        $userData['EU_FName']     = $form->getValue('EU_FName');
                        $userData['EU_Email']     = $form->getValue('EU_Email');
                        $userData['EU_Username']  = $form->getValue('EU_Username');
                        $userData['EU_ShowError'] = $form->getValue('EU_ShowError');

                        if ($form->getValue('EU_Password') <> ""){
                            $userData['EU_Password']  = md5($form->getValue('EU_Password'));
                        }

                        $userData->save();
                    }
                    $this->_redirect('');
                }
            }
        }
    }

    /**
     * Create a dorpdown list for the association to do
     * Retrieve parameters from url parameters sent via ajax.
     *
     * @return void
     */
    public function ajaxAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $associationAction = $this->_getParam('associationAction');
        $associationId = $this->_getParam('associationID');
        $associationSetId = $this->_getParam('associationSetID');
        $special = false;
        if ($associationAction == "new")
        {
            $optionsData = array();
            if (in_array($associationSetId, $this->_associationIds))
            {
                $nameAssoc = $this->_objectList[$associationSetId];
                $oData = new $nameAssoc();
                $optionsData = $oData->getList(true, null, true);
            }

            switch ($associationSetId)
            {
                case 'entities':
                    $ID = '';
                    $Name = '';
                    $labelAssociated = '';
                    $newElement = Cible_FunctionsAssociationElements::getNewAssociationEntities(
                        $associationSetId,
                        $ID,
                        $Name,
                        $associationId,
                        $labelAssociated,
                        $this->view->data,
                        array(),
                        true);
                    $special = true;
                    break;
                default:
                    break;
            }
            if (!$special){
                $newElement = Cible_FunctionsAssociationElements::getNewAssociationSetBox(
                    $associationSetId,
                    $ID,
                    $Name,
                    $associationId,
                    $labelAssociated,
                    $optionsData,
                    array(),
                    true);
            }

            echo(Zend_Json::encode(array('newElement' => $newElement)));
        }
        elseif($associationAction == 'getGroups')
        {
            $adminId = $this->_getParam('administratorID');
            $site = $this->_getParam('entity');
            $options = array('type' => $this->_config->type,
                        'initialDb' => $this->_config->db);
            $this->_helper->switchDB->setOptions($options);
            $db = $this->_helper->switchDB->setEntity($site)
                ->loadDbConfig()
                ->getCurrentDb();
            // get group data
            $obj = new ExtranetGroups(array('db' => $db));
            if ($this->view->isAdministrator()<=2){
                $limitId = $this->view->isAdministrator() == 1 ? 0 : 1;
                $obj->setLimitId($limitId);
            }
            $grps = $obj->setLangId($this->_defaultInterfaceLanguage)->getList();

            $groups = array();
            foreach ($grps as $group){
                $groups[$group['EG_ID']]['label'] = $group['EGI_Name']
                    . "||" . $group['EGI_Description'];
                $groups[$group['EG_ID']]['active'] = false;
            }
            if ($adminId > 0){
                $usrGrp = new ExtranetUsersGroups(array('db' => $db));
                $grpUser = $usrGrp->setAdminId($adminId)
                    ->getGroups();
                foreach($grpUser as $active){
                    if (array_key_exists($active['EUG_GroupID'], $groups)){
                        $groups[$active['EUG_GroupID']]['active']=true;
                    }
                }
            }
            echo(Zend_Json::encode(array('groups' => $groups)));
        }
    }
    private function _getSitesAccess($entitiesSet)
    {
        $access = array();
        $default = '';
        foreach($entitiesSet as $entity){
            if (!is_numeric($entity['entities']) && !empty($entity['entities'])){
                $access[] = $entity['entities'];
            }
            if (!empty($entity['default']) && empty($default)){
                $default = $entity['default'];
            }
        }

        $accessList = implode('|', $access);
        return array('access' => $accessList, 'default' => $default);
    }
}
