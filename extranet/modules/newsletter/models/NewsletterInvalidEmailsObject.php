<?php
/**
 * Module Catalog
 * Management of the Items.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterInvalidEmailsObject.php 949 2012-05-23 14:50:06Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterInvalidEmailsObject.php 949 2012-05-23 14:50:06Z ssoares $id
 */
class NewsletterInvalidEmailsObject extends DataObject
{
    protected $_dataClass   = 'NewsletterInvalidEmails';
    protected $_dataId      = 'NIE_ID';
    protected $_dataColumns = array(
        'fname' => 'NIE_FirstName',
        'lname' => 'NIE_LastName',
        'email' => 'NIE_Email',
        'releaseId' => 'NIE_ReleaseId',
        'message' => 'NIE_Message'
    );

    protected $_indexClass      = '';
//    protected $_indexId         = '';
    protected $_indexLanguageId = '';
//    protected $_indexColumns    = array();
    protected $_constraint      = '';
    protected $_foreignKey      = '';



    /**
     * Fetch items data for the product and build the rendering.
     *
     * @param int $id     Product id
     * @param int $langId
     *
     * @return string
     */
    public function insertInvalidEmails($data, $releaseID)
    {
        $data['releaseId'] = $releaseID;

        $this->insert($data, 1);
    }
}