<?php
/**
 * Cible Solutions - Vêtements SP
 * Product management. Data import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Default
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: ImportExportObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data in database for the files to import.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Default
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class ImportExportObject extends DataObject
{
    protected $_dataClass   = 'FilesImportData';
    protected $_dataId      = 'FI_ID';
    protected $_dataColumns = array(
        'FI_ID'      => 'FI_ID',
        'type'       => 'FI_Type',
        'path'       => 'FI_FileName',
        'lastModif'  => 'FI_LastModif',
        'lastAccess' => 'FI_LastAccess'
    );


    /**
     * Fetches data of the files to import and filter according to the file type
     *
     * @param int    $langId Language id
     * @param string $type   Value defining the section where files are used.
     *
     * @return array
     */
    public function getAllByType($langId, $type = null)
    {
        $select = parent::getAll($langId, false);

        if (!is_null($type))
            $select->where ('FI_Type = ?', $type);

        return $select;
    }
}