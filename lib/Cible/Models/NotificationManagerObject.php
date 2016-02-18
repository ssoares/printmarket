<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of NotificationManagerObject
 *
 * @category
 * @package
 * @copyright Copyright (c)2011 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NotificationManagerObject.php 726 2011-12-06 22:18:46Z ssoares $
 */
class NotificationManagerObject extends DataObject
{
    protected $_dataClass   = 'NotificationManagerData';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = '';

    /**
     * Fetch data to build the email for notification
     *
     * @param int    $moduleId  The module id generating the notification.
     * @param string $event     The event name (i.e newAccount or editAccount).
     * @param string $recipient The recipient type : client or administrator.
     *
     * @return array
     */
    public function fetchData($moduleId, $event, $recipient)
    {
        $result = array();
        $select = $this->getAll(null, false);

        $select->where('NM_ModuleId = ?', $moduleId);
        $select->where('NM_Event = ?', $event);
        $select->where('NM_Recipient = ?', $recipient);

        $result = $this->_db->fetchRow($select);

        return $result;
    }
}