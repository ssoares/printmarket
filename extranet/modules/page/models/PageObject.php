<?php
/**
 * Pages
 * Management of the Items.
 *
 * @category  Cible
 * @package   Cible_Pages
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: PageObject.php 1276 2013-10-07 19:19:07Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Cible
 * @package   Cible_Pages
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: PageObject.php 1276 2013-10-07 19:19:07Z ssoares $id
 */
class PageObject extends DataObject
{

    protected $_dataClass   = 'Pages';
//    protected $_dataId      = '';
//    protected $_dataColumns = array();

    protected $_indexClass      = 'PagesIndex';
//    protected $_indexId         = '';
    protected $_indexLanguageId = 'PI_LanguageID';
//    protected $_indexColumns    = array();
    protected $_constraint      = 'PI_PageIndex';
    protected $_foreignKey      = '';

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
        $imgPath = $path . '/data/images/' . $module ;

        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/background' );
            mkdir ($imgPath . '/background/tmp' );
            mkdir ($imgPath . '/header' );
            mkdir ($imgPath . '/header/tmp' );
        }
    }

    public function completeQuery($langId = null, $array = true)
    {
        parent::completeQuery($langId, false);

        if ($array)
            $result = $this->_db->fetchAll($this->_query);
        else
            $result = $this->_query;

        return $result;

    }
}