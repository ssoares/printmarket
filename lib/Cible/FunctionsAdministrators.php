<?php
    abstract class Cible_FunctionsAdministrators
    {
        public static function getAllAdministratorGroups($searchText = "", $listOrder = "")
        {
            $oAdmGroups = new ExtranetGroups();
            $groups = $oAdmGroups->setLangId(Zend_Registry::get("languageID"))
                ->getList($searchText, $listOrder);

            return $groups;
        }

        public static function getAdministratorGroupData($groupID)
        {
            $groupData = new ExtranetGroups();
            $group = $groupData->setLangId(Zend_Registry::get('languageID'))
                ->setId($groupID)
                ->populate();

            return $group;
        }               

        public static function getAdministratorData($administratorID, $db = null, $adminGrp = null)
        {
            if (!is_null($db)){
                $users = new ExtranetUsers(array('db' => $db));
            }else{
                $users = new ExtranetUsers();
            }
            if ($adminGrp == 1){
                $users->setIncludeAll(true);
            }
            $data = $users->setId($administratorID)->populate();

            return $data;
        }

        public static function checkAdministratorPageAccess($adminID, $pageID, $permission, $adminGrp)
        {
            $hasAccess = false;
            $administrator = new ExtranetUsersGroups();
            $row = $administrator->setAdminId($adminID)->getFirstLevelsAdmin('count');
            if ($row == 0 || $adminGrp == 2)
            {
                $permissionPage = new ExtranetUsersGroups();
                $hasAccess = $permissionPage->setAdminId($adminID)
                    ->setPageId($pageID)
                    ->getPermissionsPage($permission);
            }
            else{
                $hasAccess = true;
            }

            return $hasAccess;
        }

        public static function CheckGroupPagesPermissions($groupID, $pageID, $permission)
        {
            $hasAccess = false;
            if($groupID == null)
                return false;

            $groupPagePermission = new ExtranetGroupsPagesPermissions();
            $hasAccess = $groupPagePermission->setGroupId($groupID)
                ->setPageId($pageID)
                ->getPermission($permission);

            return $hasAccess;

        }

        public static function getAllUserGroups($userID, Zend_Db_Adapter_Pdo_Mysql $db = null)
        {
            if (!is_null($db)){
                $usrGrpAssocData = new ExtranetUsersGroups(array('db' => $db));
            }else{
                $usrGrpAssocData = new ExtranetUsersGroups();
            }

            $data = $usrGrpAssocData->setAdminId($userID)->getGroups();

            return $data;
        }

        public static function getACLUser($authID)
        {
            $acl = new Zend_Acl();

            /***************** ADDING ALL RESOURCES ************************/
            $resourcesSelect = new ExtranetResources();
            $resourcesData = $resourcesSelect->populate();

            foreach ($resourcesData as $resource){
                $resource = new Zend_Acl_Resource($resource['ER_ControlName']);
                $acl->add($resource);
            }

            /*************** ADDING ALL ROLES ********************************/
            $oRoles = new ExtranetRoles();
            $rolesData = $oRoles->populate();

            $rolesArray = array();
            foreach($rolesData as $role){
                $rolesArray[$role['ER_ID']]['name'] = $role['ER_ControlName'];
                $rolesArray[$role['ER_ID']]['parent'] = array();

                $rolesParentSelect = new ExtranetRolesResources();
                $rolesParentData = $rolesParentSelect->setRoleId($role['ER_ID'])
                    ->setOrderBy('ERR_InheritedParentID')
                    ->populate();

                $rolesParentArray = array();
                foreach ($rolesParentData as $roleParent){
                    if ($roleParent['ERR_InheritedParentID'] <> 0){
                        $roleData = $rolesParentSelect->setOrderBy('')
                            ->setId($roleParent['ERR_InheritedParentID'])
                            ->getRelatedRole();

                        if (!in_array($roleData['ER_ControlName'],$rolesParentArray))
                            $rolesParentArray[count($rolesParentArray)] = $roleData['ER_ControlName'];
                    }
                }
            }
            $rolesArray[$role['ER_ID']]['parent'] = $rolesParentArray;

            foreach ($rolesArray as $roleArray){
                $role = new Zend_Acl_Role($roleArray['name']);
                $acl->addRole($role,$roleArray['parent']);
            }

            $role = new Zend_Acl_Role($authID);
            $acl->addRole($role);

            // get all groups of the current user
            $groupsData = Cible_FunctionsAdministrators::getAllUserGroups($authID);

            $admin = false;
            foreach($groupsData as $group)
            {
                if ($group['EUG_GroupID'] == 1 || $group['EUG_GroupID'] == 2){
                    $admin = true;
                }
                $oGrpRolRes= new ExtranetGroupsRolesResources();
                $groupRoleResourceData = $oGrpRolRes->setGroupId($group['EUG_GroupID'])
                    ->populate();

                foreach ($groupRoleResourceData as $groupRoleResource){
                    $acl = Cible_FunctionsAdministrators::addAllRolesResourcesPermissionsUser($acl,$authID,$groupRoleResource['EGRRP_RoleResourceID']);
                }

            }
            return $acl;

        }

        public static function addAllRolesResourcesPermissionsUser($acl,$userID,$roleRessourceID)
        {
            $oRoleResource = new  ExtranetRolesResources();
            $roleResourceData = $oRoleResource->setId($roleRessourceID)
                ->getRoleRessources();

            foreach($roleResourceData as $roleResource){
                if ($roleResource['ERR_InheritedParentID'] <> 0){
                    $acl = Cible_FunctionsAdministrators::addAllRolesResourcesPermissionsUser($acl,$userID,$roleResource['ERR_InheritedParentID']);
                }
                // get all permission of a role resources associated
                $oRoleResPerm = new ExtranetRolesResourcesPermissions();
                $roleResourcePermissionsData = $oRoleResPerm->setRoleResourceId($roleResource['ERR_ID'])
                    ->populate();
                foreach ($roleResourcePermissionsData as $permission){
                    $acl->allow($userID, $roleResource['ResourceName'], $permission['EP_ControlName']);
                }
            }

            return $acl;
        }
  }