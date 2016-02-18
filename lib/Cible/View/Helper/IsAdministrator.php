<?php
    class Cible_View_Helper_IsAdministrator extends Zend_View_Helper_Abstract
    {
        public function IsAdministrator(){
            $auth = Zend_Auth::getInstance();
            $data = (array)$auth->getStorage()->read();

            if(empty($data))
                return false;

            $authID = $data['EU_ID'];
            $db = null;
            if ($data['isAdminCible'])
            {
                $dbs = Zend_Registry::get('dbs');
                $db = $dbs->getDb('admins');
            }
            $administrator = new ExtranetUsersGroups(array('db' => $db));
            $row = $administrator->setAdminId($authID)
                ->getFirstLevelsAdmin();

            if (count($row) == 0){
                return 0;
            }else{
                return $row['EUG_GroupID'];
            }
        }
    }