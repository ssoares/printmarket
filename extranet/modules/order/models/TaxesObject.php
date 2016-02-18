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
 * @version   $Id: TaxesObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */

/**
 * Manage data from references table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_References
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: TaxesObject.php 435 2011-03-28 03:57:25Z ssoares $id
 */
class TaxesObject extends DataObject
{
    protected $_dataClass   = 'TaxesData';

    protected $_indexClass      = '';
    protected $_indexLanguageId = '';

    /**
     * Return the taxe value of the selected state.
     *
     * @param int $stateId
     *
     * @return float
     */
    public function getTaxData($stateId)
    {
        $value = 0;

        $select = $this->_db->select()
            ->from($this->_oDataTableName, array('TP_Rate', 'TP_Code'))
            ->join('Catalog_TaxeZone',
                    'TZ_ID = TP_StateId',
                    array('TZ_GroupName')
                    )
            ->where('TP_StateId = ?', $stateId);

        $value = $this->_db->fetchRow($select);
        
        return $value;
    }

    public function importTaxes()
    {
        $oTax      = new TaxeZoneObject();
        $taxesZone = $oTax->getAll();

        foreach ($taxesZone as $data)
        {
            if ($data['TZ_ProvCode'] != 'QC')
                $tmp['TP_Rate'] = $data['TZ_TaxValue1'] * 100;
            else
                $tmp['TP_Rate'] = $data['TZ_TaxValue2'] * 100;

            $tmp['TP_Code']    = $data['TZ_GroupName'];
            $tmp['TP_StateId'] = $data['TZ_ID'];


            $this->_constraint = 'TP_StateId';
            $found = $this->recordExists($tmp);
            $this->_constraint = '';
            if ($found)
            {
                $where = $this->_db->quoteInto("TP_StateId = ?", $data['TZ_ID']);
                $this->_db->update($this->_oDataTableName, $tmp, $where);
            }
            else
            {
                $this->_db->insert($this->_oDataTableName, $tmp);
            }
        }
    }
}