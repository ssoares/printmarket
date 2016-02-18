<?php
/**
 * Cible Solutions - Vêtements SP
 * Messages Alert management.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Messages
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: MessagesObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data for featured products - Table SP_ProduitsVedette.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_FeaturedProducts
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class MessagesObject extends DataObject
{
    protected $_dataClass   = 'MessagesData';
    protected $_dataId      = 'MA_ID';
    protected $_dataColumns = array(
        'MA_Online'  => 'MA_Online',
        'MA_Timeout' => 'MA_Timeout'
    );

    protected $_indexClass      = 'MessagesIndex';
    protected $_indexId         = 'MAI_MessageAlertID';
    protected $_indexLanguageId = 'MAI_LanguageID';
    protected $_indexColumns    = array(
        'MAI_Title' => 'MAI_Title',
        'MAI_Text' => 'MAI_Text'
    );

    protected $_indexSelectColumns = array();
}