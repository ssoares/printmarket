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
 * @version   $Id: AddressObject.php 832 2012-02-06 21:25:03Z ssoares $id
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: AddressObject.php 832 2012-02-06 21:25:03Z ssoares $id
 */
class AddressObject extends DataObject
{

    protected $_dataClass = 'AddressData';
    protected $_indexClass = 'AddressIndex';
    protected $_indexLanguageId = 'AI_LanguageID';

//    protected $_constraint      = '';

    /*
      public function addressCollection($addID,$langId){
      $select = $this->getAll($langId, false);
      $select->where('AI_AddressId = ?', $addID);
      $addresses =$this->_db->fetchAll($select);
      return $addresses;
      } */

    public function save($id, $data, $langId)
    {
        $addrId = $id;
        if (empty($id))
            $addrId = parent::insert($data, $langId);
        else
            parent::save($id, $data, $langId);

        return $addrId;
    }

    /**
     * Return the state id according of the selected address
     * @param int $id
     *
     * @return int
     */
    public function getStateId($id)
    {
        $stateId = 0;

        $data = $this->getAll(null, true, $id);

        $stateId = $data[0]['A_StateId'];

        Return $stateId;
    }

}