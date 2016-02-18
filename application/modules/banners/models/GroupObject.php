<?php
/**
 * Module Utilities
 * Management of the references data.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Utilities
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: $id
 */

class GroupObject extends DataObject
{
    protected $_dataClass   = 'GroupData';

  /*  protected $_dataColumns = array(
            'GroupID' => 'BG_ID',
            'GroupName' => 'BG_Name'
     );*/
    
    protected $_indexClass      = '';
    protected $_indexLanguageId = '';

    public function groupCollection($id = 0)
    {
        (array) $array = array();

        if($id>0){
            $groups = $this->getAll(null,true,$id);
        }
        else {
            $groups = $this->getAll();
        }        
        return $groups;
    }
}