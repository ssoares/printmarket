<?php
/**
 * Cible Solutions - Vêtements SP
 * Product management. Data import.
 *
 * @category  Modules
 * @package   Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: Action.php 1788 2015-06-18 20:07:52Z ssoares $
 */

/**
 * Catalog import controller
 * Manage actions to import data from files.
 *
 * @category  Modules
 * @package   Catalog
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class Cible_Extranet_Controller_Import_Action extends Cible_Controller_Block_Abstract
{
    /**
     * Number of days to indicate if the file could be imported.
     */
    protected  $_interval  = 100;
    /**
     * Define the part of the file name to delete when formating the object name.
     */
    protected  $_prefix    = 'SP_';
    /**
     * Define the separator of the file name to delete when formating the object name.
     */
    protected $_separator = '_';
    protected $_firstAction = "list-files";

    protected $_importFilesFolder;
    protected $_exportFilesFolder;
    protected $_imageFolder;
    protected $_rootImgPath;

    protected $_importType  = null;
    protected $_exportExcel = 'true';
    protected $_exportPdf   = 'true';
    protected $_exportCsv   = 'true';
    protected $_ftpTransfer = false;
    public $addColumnsLabel = false;

    /**
    * List of the table for export. This list is utilized to create csv files
    * from tables.
    *
    * @var array
    */
    protected $_tablesList = array();

    /**
     * Initialize object.
     * Initialize some specific parameters for file management.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $dataPath = Zend_Registry::get("fullDocumentRoot")
                    . "/data/";

        $this->_exportFilesFolder = $dataPath
                                     . "files/"
                                     . $this->_moduleTitle . "/"
                                     . "export/";

        $this->_importFilesFolder = $dataPath
                                     . "files/"
                                     . $this->_moduleTitle . "/"
                                     . "import/";

        $this->_imageFolder = $dataPath
                              . "images/"
                              . $this->_moduleTitle . "/";

        $this->_rootImgPath = Zend_Registry::get("www_root")
                                . "/data/images/"
                                . $this->_moduleTitle . "/";

        $this->view->assign('interval', $this->_interval);
        set_time_limit(180);
    }

    /**
     * Add a new record when file contains a new line.
     */
    public function addAction()
    {
        throw new Exception('Not implemented');
    }

    public function deleteAction()
    {
        throw new Exception('Not implemented');
    }

    /**
     * Update data when found in the input file.
     */
    public function editAction()
    {
        throw new Exception('Not implemented');
    }

    /**
     * For each files found, import data and update the last files access.
     * This date will be displayed as information.
     *
     * @return void
     */
    public function importAction()
    {
        $nbLines = array();
        $fileId  = $this->_getParam('fileName');
        // Create an array with the csv files data (name, last modif...)
        $oFiles    = new ImportExportObject();
        $filesList = $oFiles->getAll(NULL, true, $fileId);

        $this->view->assign('nbFilesTotal', count($filesList));

        // For each file
        foreach ($filesList as $fileData)
        {
            $fileName = $fileData['FI_ID'];
            $prevTime = strtotime($fileData['FI_LastModif']);
            $days     = round((time() - $prevTime)/(60*60*24));

            // If file if less old than 2 days
            if ($days <= $this->_interval || $this->_interval == -1)
            {
                // read the file
                $fileLines = file($fileData['FI_FileName']);

                // Get the name
                $objectName = $this->_formatName($fileName);

                // According to the name
                $oData = new $objectName();
                // call the method to process data.
                $nbLines[$fileName] = $oData->processImport($fileLines);
                // Update last file access

//                touch($fileData['FI_FileName'], date('U', filemtime($fileData['FI_FileName'])), time());
                // Update database
                $this->_findCsvFiles('import');
            }
        }
        $this->view->assign('statLines', $nbLines);
    }

    /**
     * Initiates data of importation files.
     * Scan the folder containing import files and format some data.
     * Save data into database to be used in the import actions.
     *
     * @return array
     */
    protected function _findCsvFiles($action = '')
    {
        $filesList = array();

        if (is_dir($this->_importFilesFolder))
        {
            $dirHandler = opendir($this->_importFilesFolder);

            if ($dirHandler)
            {
                // for each file in the folder
                while (($file = readdir($dirHandler)) !== false)
                {
                    // get name and data (date of modif...)
                    $realPath = realpath($this->_importFilesFolder . $file);
                    $info     = pathinfo($this->_importFilesFolder . $file);
                    $fileName = $info['filename'];
                    // store it in an array
                    if (filetype($realPath) == 'file')
                    {
                        $filesList[$fileName]['FI_ID']      = $fileName;
                        $filesList[$fileName]['path']       = $realPath;
                        $filesList[$fileName]['type']       = $this->_importType;
                        if ($action == 'import')
                        $filesList[$fileName]['lastAccess'] = date('Y-m-d H:i:s');
                        if ($action == 'list')
                        $filesList[$fileName]['lastModif']  = date('Y-m-d H:i:s', filemtime($realPath));
                    }
                }

                closedir($dirHandler);
                //save data into db for control
                $oFiles = new ImportExportObject();
                $langId = $this->_registry->currentEditLanguage;

                foreach ($filesList as $name => $data)
                {
                    $exist = $oFiles->recordExists($data, $langId, true);
                    if ($exist)
                    {
                        $oFiles->save($name, $data, $langId);
                    }
                    else
                    {
                        $oFiles->insert($data, $langId);
                    }
                }
            }
        }

        // return this array
        return $filesList;
    }

    /**
     * Format a file name to be compliant with the object name and replace it.
     *
     * @param string $name The file name
     *
     * @return string
     */
    protected function _formatName($name, $suffix = 'Object')
    {
        $tmp  = str_replace($this->_prefix, '', $name);

        $formatedName = str_replace($this->_separator, '', $tmp) . $suffix;

        return $formatedName;

    }

    /**
     * Display the list of the importation files
     * Initiates actions for import and export of data.
     *
     * @return void
     */
    public function listFilesAction()
    {
        if ($this->view->aclIsAllowed($this->_moduleTitle,'edit',true))
        {
            $this->_findCsvFiles('list');
            $tables    = array(
                'FilesImport' => array(
                    'FI_ID',
                    'FI_FileName',
                    'FI_LastModif',
                    'FI_LastAccess')
            );
            $field_list = array(
                'FI_ID'         => array('width' => '50px'),
                'FI_LastModif'  => array('width' => '150px'),
                'FI_LastAccess' => array('width' => '150px')
            );

            if($this->_importType)
                $field_list['FI_Type'] = array('width' => '50px');

            $this->view->params = $this->_getAllParams();

            $pageID = $this->_getParam( 'pageID' );
            $langId = $this->_registry->languageID;

            $lines  = new ImportExportObject();
            $select = $lines->getAllByType($langId, $this->_importType);

            $oDate = new Zend_Date(time());
            $validDate = $oDate->subDay($this->_interval, Zend_Date::DAY);
            $validDate = $oDate->toString('Y-M-d H:m:s');

            $options = array(
                    'commands' => array(
                        $this->view->link($this->view->url(
                                array(
                                    'controller'=>  $this->_name,
                                    'action'=>  $this->_defaultAction
                                    )
                                ),
                                $this->view->getCibleText('button_import_all'),
                                array('class'=>'action_submit import')
                            )
                    ),
                    'disable-export-to-excel' => $this->_exportExcel,
                    'disable-export-to-pdf'   => $this->_exportPdf,
                    'disable-export-to-csv'   => $this->_exportCsv,
                    'filters' => array(
                        'max_days_interval' => array(
                            'label'         => 'Filtre 1',
                            'default_value' => $validDate,
                            'associatedTo'  => 'FI_LastModif',
                            'whereClause'   => ' > ',
                            'choices'       => array(
                                '' => '',
                                'isvalid' => $this->view->getCibleText('filter_files_to_import')
                            )
                        )
                    ),
                    'action_panel' => array(
                        'width' => '50',
                        'actions' => array(
                            'import' => array(
                                'label' => $this->view->getCibleText('button_import'),
                                'url' => $this->view->baseUrl() . "/"
                                        . $this->_moduleTitle . "/"
                                        . $this->_name . "/"
                                        . $this->_defaultAction . "/"
                                        . $this->_paramId
                                        . "/%ID%/"
                                        . $pageID,
                                'findReplace' => array(
                                    'search' => '%ID%',
                                    'replace' => 'FI_ID'
                                )
                            )
//                            'delete' => array(
//                                'label' => $this->view->getCibleText('button_delete'),
//                                'url' => $this->view->baseUrl() . "/"
//                                        . $this->_moduleTitle . "/"
//                                        . $this->_name
//                                        . "/delete/"
//                                        . $this->_paramId
//                                        . "/%ID%/"
//                                        . $pageID,
//                                'findReplace' => array(
//                                    'search' => '%ID%',
//                                    'replace' => 'P_ID'
//                                )
//                            )
                        )
                    )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
            $this->view->assign('mylist', $mylist);
        }
    }

    /**
     * Manage table list for csv export.
     *
     * @return void
     */
    public function toCsvAction()
    {
        // variables
        $pageID       = $this->_getParam('pageID');
        $returnAction = $this->_getParam('return');
        $returnUrl    = '/';
        $blockID      = $this->_getParam('blockID');
        $baseDir      = $this->view->baseUrl();

        if ($this->view->aclIsAllowed($this->_moduleTitle,'edit',true))
        {
            $returnUrl .= $this->_moduleTitle . "/"
                        . $this->_name . "/"
                        . $this->_firstAction . "/";

            if ($pageID > 0)
                $returnUrl .= "page/" . $pageID . "/";

            // get the list of tables that may be exported
            $tablesList = $this->_tablesList;

            // generate the form to select tables to export
            $form = new FormExportCatalog(array(
                'baseDir'        => $baseDir,
                'cancelUrl'      => "$baseDir$returnUrl",
                'selectedTables' => $tablesList
            ));

            $this->view->form = $form;

            // action
            if ( !$this->_request->isPost() )
            {
                $form->populate($tablesList);
            }
            else
            {
                $formData       = $this->_request->getPost();
                $selectedTables = $formData['tablesList'];

                if ($selectedTables[0] == 'checkAll')
                        unset($selectedTables[0]);

                if ($form->isValid($formData))
                {
                    if ($this->_ftpTransfer && $this->_exportFilesFolder)
                    {
                        $config     = Zend_Registry::get('config');
                        $properties = $config->toArray();

                        $ftp = new Cible_Ftp($properties['ftpTransfer']);
                    }
                    // Process export
                    foreach ($selectedTables as $name)
                    {
//                        if (isset($this->relatedId[$name]))
//                        {
                            $this->type = 'CSV';

                            $options['dataClass'] = $this->_formatName($name);
                            $lines = new ExportObject($options);

                            $dataColumns = $lines->getDataColumnsForExport();

                            $this->tables = array(
                                $name => $dataColumns
                            );

                            $this->fields = $dataColumns;
                            $this->filters = array();

                            $this->select = $lines->getAll(null, false);

                            if (is_array($this->relatedId)
                                && count($this->relatedId) > 0)
                            {
                                $object = new $options['dataClass']();
                                //Get the constraint to find related data/table
                                $field  = $object->getConstraint();
                                //If no constraint it's the first table. We take the key.
                                if (empty($field))
                                    $field = $object->getDataId();
                                // Add where clause to the query

                                if (isset($this->relatedId[$name])
                                    && is_array($this->relatedId[$name])
                                    && count($this->relatedId[$name]) > 0)
                                {
                                    $this->select->where(
                                        $field . " = ?", current($this->relatedId[$name])
                                    );

                                    for ($i = 1; $i < count($this->relatedId[$name]); $i++)
                                    {
                                        $this->select->orWhere(
                                            $field . " = ?", $this->relatedId[$name][$i]
                                        );
                                    }

                                }
                                elseif (isset($this->relatedId[$name]))
                                {
                                    $this->select->where(
                                        $field . " = ?", $this->relatedId[$name]
                                    );
                                }
                                $name .= "_" . $this->_getParam('ID');
                            }

                            $this->filename = $name . ".csv";
                            parent::toCsvAction();

                            if ($this->_ftpTransfer && $this->_exportFilesFolder)
                            {
                                $ftp->setFileName($this->filename);
                                $ftp->setLocalFile($this->_exportFilesFolder . $this->filename);

                                $ftp->transfer();
                            }
//                        }
                    }
                    // return to list
                    $this->_redirect($returnUrl);
                }
            }
        }
    }
}