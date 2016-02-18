<?php
/**
 * Class FromNotificationObject - Manage emails list for notification
 * on submitted forms
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FromNotificationObject - Manage emails list for notification
 * on submitted forms
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormNotificationObject extends FormNotification
{
    protected $_dataClass   = 'FormNotification';
    protected $_dataId      = 'FN_FormID';
    protected $_dataColumns = array(
            'FN_FormID' => 'FN_FormID',
            'FN_Email'  => 'FN_Email',
            'FN_Type'   => 'FN_Type'
        );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();
    
    protected $_baseUrl;


    public function  __construct()
    {
        $this->_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        parent::__construct();
    }

    /**
     * Get All the emails to send notification when the form is sended.
     * 
     * @param int $formId Id of the form.
     *
     * @return array List of emails for recipients notification.
     */
    public function getNotificationEmails($formId = 0)
    {
        $emails = array();
        $dataTableName = $this->_name;
        $db = $this->_db;
        $select = $db->select()
                ->from($dataTableName, $this->_dataColumns);

        if ($formId != 0)
        {
            $select->where($this->_dataId . ' = ?', $formId);
        }

        $emails = $db->fetchAll($select);

        return $emails;
    }
}