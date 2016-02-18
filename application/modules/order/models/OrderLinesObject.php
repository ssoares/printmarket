<?php
/**
 * Cible Solutions -
 * Orders management.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: OrderLinesObject.php 422 2011-03-24 03:25:10Z ssoares $
 */

/**
 * Manage data in database for the orders lines.
 *
 * @category  Application_Modules
 * @package   Application_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class OrderLinesObject extends DataObject
{
    protected $_dataClass   = 'OrderLinesData';
    protected $_dataId      = '';
//    protected $_dataColumns = array();
    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();

    protected $_query;

    /**
     * Fetch data of orders lines to generate csv file.
     *
     * @param string $orderColumns The list of colums to set in the select statement.
     * @param string $status       The order status to export only available orders.
     * @param int    $id           Order id, allows to fetch data for an other part of the file.
     *
     * @return array
     */
    public function getDataForExport($orderId, $columns = '*', $id = null)
    {
        $select = $this->_db->select()
                ->from($this->_oDataTableName,
                        $columns)
                ->where('OL_OrderId = ?', $orderId);

        if ($id)
            $select->where('O_ID = ?', $id);

        $data = $this->_db->fetchAll($select);

        return $data;
    }
    
}