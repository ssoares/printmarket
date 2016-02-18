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

    protected $_indexClass      = 'MessagesIndex';
    protected $_indexLanguageId = 'MAI_LanguageID';

    protected $_indexSelectColumns = array();

    /**
     * Build an array to feed dropdown list element;
     *
     * @return array
     */
    public function getMessagesList()
    {
        $list = array();
        $data = parent::getAll(Cible_Controller_Action::getDefaultEditLanguage());

        foreach ($data as $message)
            $list[$message[$this->_dataId]] = $message['MAI_Title'];

        return $list;
    }

    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
    }
}