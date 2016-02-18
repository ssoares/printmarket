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
class AdministratorGroupController extends Cible_Extranet_Controller_Action
{
    function indexAction()
    {
        $tables = array(
            'Extranet_Groups' => array('EG_ID'),
            'Extranet_GroupsIndex' => array(
            'EGI_GroupID','EGI_LanguageID','EGI_Name','EGI_Description')
        );

        $field_list = array(
            'EGI_Name' => array('width' => '450px'),
            'EGI_Description' => array('width' => '450px')
        );

        $select = $this->_db->select()
            ->from('Extranet_Groups', $tables['Extranet_Groups'])
            ->joinInner('Extranet_GroupsIndex', 'Extranet_Groups.EG_ID = Extranet_GroupsIndex.EGI_GroupID', $tables['Extranet_GroupsIndex'])
            ->where('Extranet_GroupsIndex.EGI_LanguageID = ?', $this->_defaultInterfaceLanguage);
        if ($this->view->isAdministrator() == 1){
            $select->where('Extranet_Groups.EG_ID > 1');
        }else{
            $select->where('Extranet_Groups.EG_ID > 2');
        }
        $options = array(
            'commands' => array(
                $this->view->link($this->view->url(array('controller'=>'administrator-group','action'=>'add')),$this->view->getCibleText('button_add_administrators_group'), array('class'=>'action_submit add') )
            ),
            //'disable-export-to-excel' => 'true',
            'action_panel' => array(
                'width' => '50',
                'actions' => array(
                    'edit' => array(
                        'label' => $this->view->getCibleText('button_edit'),
                        'url' => "{$this->view->baseUrl()}/default/administrator-group/edit/administratorGroupID/%ID%",
                        'findReplace' => array(
                            'search' => '%ID%',
                            'replace' => 'EG_ID'
                        )
                    ),
                    'delete' => array(
                        'label' => $this->view->getCibleText('button_delete'),
                        'url' => "{$this->view->baseUrl()}/default/administrator-group/delete/administratorGroupID/%ID%",
                        'findReplace' => array(
                            'search' => '%ID%',
                            'replace' => 'EG_ID'
                        )
                    )
                )
            )
        );

        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

        $this->view->assign('mylist', $mylist);
    }

    function editAction()
    {
        // page title
        $this->view->title = "Information sur le groupe d'administrateur";

        // js import
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/administrator.js');

        // get param
        $administratorGroupID = $this->_getParam('administratorGroupID');
        $order           = $this->_getParam('order');
        $tablePage       = $this->_getParam('tablePage');
        $search          = $this->_getParam('search');

        $paramsArray = array("order" => $order, "tablePage" => $tablePage, "search" => $search);


        // get group data
        $groupData = Cible_FunctionsAdministrators::getAdministratorGroupData($administratorGroupID);

        $returnLink = $this->view->url(array('controller' => 'administrator-group', 'action' => 'index', 'administratorGroupID' => null));
        /********** ACTIONS ***********/
        $form = new FormExtranetGroup(array(
            'baseDir'   => $this->view->baseUrl(),
            'cancelUrl' => "$returnLink",
            'groupID' => $administratorGroupID
        ));
        $form->setDecorators(array(
            'PrepareElements',
            array('ViewScript', array('viewScript' => 'administrator-group/formGroup.phtml'))
        ));

        $this->view->form = $form;

        if ( !$this->_request->isPost() ){
            $form->populate($groupData->toArray());
        }
        else {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                // validate name is unique
                $findGroup = new ExtranetGroupsIndex();
                if ($administratorGroupID > 1){
                    $findGroup->setGroupId($administratorGroupID);
                }
                $findGroupData = $findGroup->setGroupName($formData['EGI_Name'])
                    ->recordExists();

                // name is allready use
                if ($findGroupData > 0){
                    $form->getElement('EGI_Name')->addError('Un autre groupe possède déjà ce nom');
                }
                // save group information
                else{
                    $db = $this->_db;

                    // update group data
                    $where = ("EG_ID = $administratorGroupID");
                    $db->update('Extranet_Groups', array('EG_Status' => $form->getValue('EG_Status')), $where);

                    // update group index data
                    $where = ("EGI_GroupID = $administratorGroupID AND EGI_LanguageID = " . Zend_Registry::get("languageID"));
                    $db->update('Extranet_GroupsIndex', array('EGI_Name' => $form->getValue('EGI_Name'), 'EGI_Description' => $form->getValue('EGI_Description')), $where);


                    // get pages data
                    $pagesArray = Cible_FunctionsPages::getAllPagesDetailsArray();

                    // save group pages permissions
                    // save pages access
                    $this->deleteGroupPagesPermissions($administratorGroupID);
                    $this->saveGroupPagesPermissions($administratorGroupID, $pagesArray, $_POST, "structure");
                    $this->saveGroupPagesPermissions($administratorGroupID, $pagesArray, $_POST, "data");

                    // save roles
                    $this->deleteGroupRoles($administratorGroupID);
                    $this->saveGroupRoles($administratorGroupID, $_POST);

                    header("location:".$returnLink);
                }
            }
        }
    }

    function addAction()
    {
        // page title
        $this->view->title = "Ajout d'un groupe d'administrateur";

        // js import
        $this->view->headScript()->appendFile($this->view->baseUrl().'/js/administrator.js');

        /********** ACTIONS ***********/
        $form = new FormExtranetGroup(array(
            'baseDir'   => $this->view->baseUrl()
        ));

        $form->setDecorators(array(
            'PrepareElements',
            array('ViewScript', array('viewScript' => 'administrator-group/formGroup.phtml'))
        ));
        $returnLink = $this->view->url(array('controller' => 'administrator-group', 'action' => 'index'));
        $form->getElement('cancel')->setAttrib('onclick', 'document.location.href="'.$returnLink.'"');
        $this->view->form = $form;



        if ($this->_request->isPost() ){
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)) {
                // validate name is unique
                $findGroup = new ExtranetGroupsIndex();
                $select = $findGroup->select()
                ->where('EGI_Name = ?', $form->getValue('EGI_Name'));

                $findGroupData = $findGroup->fetchAll($select);

                // name is allready use
                if ($findGroupData->count() > 0){
                    $form->getElement('EGI_Name')->addError('Un autre groupe possède déjà ce nom');
                }
                else{
                    // create group
                    $groupData = new ExtranetGroups();
                    $row = $groupData->createRow();
                    $row->EG_Status = $form->getValue('EG_Status');
                    $row->save();
                    $groupID = $row->EG_ID;

                    // create index group as many language in the system
                    $languages = Cible_FunctionsGeneral::getAllLanguage();
                    foreach ($languages as $language){
                        $groupData = new ExtranetGroupsIndex();
                        $row = $groupData->createRow();
                        $row->EGI_GroupID       = $groupID;
                        $row->EGI_LanguageID    = $language["L_ID"];
                        $row->EGI_Name          = $form->getValue('EGI_Name');
                        $row->EGI_Description   = $form->getValue('EGI_Description');

                        $row->save();
                    }


                    // get pages data
                    $pagesArray = Cible_FunctionsPages::getAllPagesDetailsArray();

                    // save group pages permissions
                    if(!empty($_POST["submitSave"])){
                        // save pages access
                        //$this->deleteGroupPagesPermissions($newInsertID);
                        $this->saveGroupPagesPermissions($groupID, $pagesArray, $_POST, "structure");
                        $this->saveGroupPagesPermissions($groupID, $pagesArray, $_POST, "data");

                        // save roles
                        //$this->deleteGroupRoles($newInsertID);
                        $this->saveGroupRoles($groupID, $_POST);

                    }

                    header("location:".$returnLink);
                }

            }
        }
    }

    function deleteAction()
    {
        // set page title
        $this->view->title = "Supprimer un groupe d'administrateur";

        // get params
        $administratorGroupID = (int)$this->_getParam( 'administratorGroupID' );

        if ($this->_request->isPost()) {
            // if is set delete, then delete
            $delete = isset($_POST['delete']);
            $returnLink = $this->view->url(array('controller' => 'administrator-group', 'action' => 'index', 'administratorGroupID' => null));
            if ($delete && $administratorGroupID > 0) {
                // delete group
                $group = new ExtranetGroups();
                $where = 'EG_ID = ' . $administratorGroupID;
                $group->delete($where);

                // delete group index
                $groupIndex = new ExtranetGroupsIndex();
                $where = 'EGI_GroupID = ' . $administratorGroupID;
                $groupIndex->delete($where);

                // delete Extranet_Groups_Pages_Permissions
                $groupPagesPermissions = new ExtranetGroupsPagesPermissions();
                $where = 'EGPP_GroupID = ' . $administratorGroupID;
                $groupPagesPermissions->delete($where);

                // delete Extranet_UsersGroups
                $groupUsers = new ExtranetUsersGroups();
                $where = 'EUG_GroupID = ' . $administratorGroupID;
                $groupUsers->delete($where);
            }
            header("location:".$returnLink);
        }
        else
        {
            if ($administratorGroupID > 0) {
                $administratorGroup = new ExtranetGroupsIndex();
                $this->view->group = $administratorGroup->fetchRow('EGI_GroupID='.$administratorGroupID.' AND EGI_LanguageID = ' . Zend_Registry::get("languageID"));
            }
        }
    }

    function saveGroupPagesPermissions($groupID, $pagesArray, $checkboxArray, $permission)
    {
        //$pageAssociate = "";
        foreach ($pagesArray as $page){
            if (count($page['child'] > 0)){
                //$pageAssociate .= $this->checkboxVerify($page['child'], $checkboxArray, $name);
                $this->saveGroupPagesPermissions($groupID, $page['child'], $checkboxArray, $permission);
            }

            if(!empty($_POST["checkbox_".$permission."_".$page['P_ID']])){
                $groupPagePermission = new ExtranetGroupsPagesPermissions();
                $select = $groupPagePermission->select()->setIntegrityCheck(false);
                $select->from('Extranet_Groups_Pages_Permissions')
                        ->where('EGPP_GroupID = ?', $groupID)
                        ->where('EGPP_PageID = ?', $page['P_ID']);

                $row = $groupPagePermission->fetchRow($select);

                if (count($row) == 0){
                    $createPagePermission = new ExtranetGroupsPagesPermissions();
                    $GPP = $createPagePermission->createRow();
                    $GPP->EGPP_GroupID = $groupID;
                    $GPP->EGPP_PageID = $page['P_ID'];


                    if ($permission == "structure")
                        $GPP->EGPP_Structure = 'Y';
                    elseif ($permission == "data")
                        $GPP->EGPP_Data = 'Y';

                    $GPP->save();
                }
                else{
                    if ($permission == "structure")
                        $row->EGPP_Structure = 'Y';
                    elseif ($permission == "data")
                        $row->EGPP_Data = 'Y';
                    $row->save();
                }

            }
        }
        //return $pageAssociate;
    }

    function deleteGroupPagesPermissions($groupID)
    {
        if($groupID <> "")
        {
            $groupPagesPermissionsData = new ExtranetGroupsPagesPermissions();
            $where      = 'EGPP_GroupID = ' . $groupID;
            $groupPagesPermissionsData->delete($where);
        }
    }

    function deleteGroupRoles($groupID)
    {
        if ($groupID <> "")
        {
            $groupsRolesResourcesData = new ExtranetGroupsRolesResources();
            $where = 'EGRRP_GroupID = ' . $groupID;
            $groupsRolesResourcesData->delete($where);
        }
    }

    function showRolesResourcesPermissions($groupID)
    {
        $resourcesData = $this->getAllResources();

        foreach ($resourcesData as $resources){
            echo ("<div style='width:100%;font-weight:bolder;'>".$resources["ERI_Name"]."</div>");
            // get all roles associated to the resources
            $rolesResourcesSelect = new ExtranetRolesResources();
            $select = $rolesResourcesSelect->select()->setIntegrityCheck(false);
            $select->from('Extranet_RolesResources')
            ->join('Extranet_RolesResourcesIndex', 'ERRI_RoleResourceID = ERR_ID')
            ->where('ERR_ResourceID = ?', $resources["ERI_ResourceID"])
            ->where('ERRI_LanguageID = ?',Zend_Registry::get("languageID"));

            $rolesData = $rolesResourcesSelect->fetchAll($select)->toArray();

            echo ("<ul>");
            echo("<li>");
                echo("<input type='radio' id='radio_role_0' name='radio_resource_".$resources["ERI_ResourceID"]."' value='0' checked='checked'>");
                echo($this->view->getCibleText('form_label_noRight'));
            echo("</li>");
            foreach ($rolesData as $role){
               echo("<li>");
               if ($this->checkGroupRoleResource($groupID,$role["ERRI_RoleResourceID"])){
                echo("<input type='radio' id='radio_role_".$role["ERRI_RoleResourceID"]."' name='radio_resource_".$resources["ERI_ResourceID"]."' value='".$role["ERRI_RoleResourceID"]."' checked='checked'>");
               }
               else{
                   echo("<input type='radio' id='radio_role_".$role["ERRI_RoleResourceID"]."' name='radio_resource_".$resources["ERI_ResourceID"]."' value='".$role["ERRI_RoleResourceID"]."'>");
               }

               echo($role["ERRI_Name"] . " (" .$role["ERRI_Description"]. ")");
               echo("</li>");
            }
            echo ("</ul>");

        }
    }

    function saveGroupRoles($groupID, $roles)
    {
        $resourcesData = $this->getAllResources();
        foreach($resourcesData as $resource)
        {
            if(!empty($roles["radio_resource_".$resource['ERI_ResourceID']])){
                $groupRolesResourcesCreate = new ExtranetGroupsRolesResources();
                $data = $groupRolesResourcesCreate->createRow();
                $data->EGRRP_GroupID = $groupID;
                $data->EGRRP_RoleResourceID = $roles["radio_resource_".$resource['ERI_ResourceID']];
                $data->EGRRP_Access = 'allow';

                $data->save();
            }
        }
    }

    function getAllResources()
    {
        // get all resources
        $resourcesSelect = new ExtranetResources();
        $select = $resourcesSelect->select()->setIntegrityCheck(false);
        $select->join('Extranet_ResourcesIndex','ERI_ResourceID = ER_ID')
        ->where('ERI_LanguageID = ?',Zend_Registry::get("languageID"))
        ->order('ERI_Name');
        return $resourcesSelect->fetchAll($select)->toArray();
    }

    function checkGroupRoleResource($groupID, $roleResourceID)
    {
        $groupRoleResourceSelect = new ExtranetGroupsRolesResources();
        $select = $groupRoleResourceSelect->select();
        $select->where('EGRRP_GroupID = ?', $groupID)
        ->where('EGRRP_RoleResourceID = ?', $roleResourceID);

        $groupRoleResourceData = $groupRoleResourceSelect->fetchRow($select);
        if (count($groupRoleResourceData) == 0)
            return false;
        else
            return true;
    }

    public function toExcelAction(){
        $this->filename = 'AdministratorsGroups.xlsx';

        $this->tables = array(
                'Extranet_Groups' => array('EG_ID'),
                'Extranet_GroupsIndex' => array('EGI_GroupID','EGI_LanguageID','EGI_Name','EGI_Description')
        );

        $this->fields = array(
            'EGI_Name' => array(
                'width' => ''
            ),
            'EGI_Description' => array(
                'width' => ''
            )
        );

        $this->filters = array(

        );

        $this->select = $this->_db->select()
            ->from('Extranet_Groups', $this->tables['Extranet_Groups'])
            ->joinInner('Extranet_GroupsIndex', 'Extranet_Groups.EG_ID = Extranet_GroupsIndex.EGI_GroupID', $this->tables['Extranet_GroupsIndex'])
            ->where('Extranet_GroupsIndex.EGI_LanguageID = ?', Zend_Registry::get('languageID'))
            ->where('Extranet_Groups.EG_ID > 1');

        parent::toExcelAction();
    }
}
