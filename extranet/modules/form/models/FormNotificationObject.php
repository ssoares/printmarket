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
class FormNotificationObject extends DataObject
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

    /**
     * Overrides the save method.
     * Delete and replace the list of recipients for email notification of the
     * current form.
     *
     * @param int   $id     Id of the form.
     * @param array $data   Array with various data to be processed.
     * @param int   $langId Language Id. Set by default. Only used to be
     *                      compliant with overrided method.
     * 
     * @return int $saved Id of the form referencing the notification emails.
     */
    public function save($id, $data, $langId)
    {
        $notificationData = array();

        $tmp = $data['FN_Email'];
        
        // Start to delete data. It will be replaced.
        $this->delete($id);

        // set the position of the last separator
        $lastSeparatorPos = strrpos($data['FN_Email'], ";");
        // set the string length
        $stringlength     = strlen($data['FN_Email']);

        // Test if the value ends with a separator in order to avoid empty value
        if ($lastSeparatorPos == $stringlength - 1)
        {
            $tmp = substr_replace(
                $data['FN_Email'],
                "",
                $lastSeparatorPos
            );
        }
        // set the email list to be inserted
        $emailList = explode(';', trim($tmp));
        
        $notificationData['FN_FormID'] = $id;
        $notificationData['FN_Type']   = 1;

        // replace data
        foreach ($emailList as $email)
        {
            $notificationData['FN_Email'] = trim($email);
            $saved = parent::insert($notificationData, 1);
        }

        return $saved;
    }
}