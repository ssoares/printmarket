<?php
/**
 * Module Template
 * Management of the products for Template.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */

/**
 * Manage data from Template table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class TemplateObject extends DataObject
{
    protected $_dataClass       = 'TemplateData';
    protected $_indexClass      = 'TemplateIndex';
    protected $_indexLanguageId = 'XXI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'XX_XXXXX';
    protected $_titleField      = 'XXI_Name';
    protected $_valurlField     = 'XXI_ValUrl';
    protected $_orderBy         = array('');
    protected $_query;
    protected $_addSubFolder    = false;
    protected $_name            = 'template';

    public function getName()
    {
        return $this->_name;
    }

    public function getTitleField()
    {
        return $this->_titleField;
    }

    public function insert($data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::insert($data, $langId);
    }

    public function save($id, $data, $langId)
    {
        $data = parent::_formatInputData($data);
        return parent::save($id, $data, $langId);
    }


    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'XXXX':
                    $data[$field] = explode(',', $values);

                    break;

                default:
                    break;
            }
        }

        return $data;
    }

    public function populate($id, $langId)
    {
        $data = $this->_formatOutputData(parent::populate($id, $langId));

        return $data;
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
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($path . '/data/files/' . $module)){
            mkdir($path . '/data/files/' .$module);
        }
        if (!is_dir($imgPath)){
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }

    public function setIndexationData()
    {
        $indexData = array();
        if (is_null($id))
            $data = $this->getAll();
        else
            $data = $this->getAll($langId, true, $id);

        foreach ($data as $values)
        {
            $tmp['action'] = "add";
            if (!is_null($id))
                $tmp['object'] = get_class();
            if (!empty($this->_indexationAction))
                $tmp['action'] = $this->_indexationAction;

            $tmp['pageID'] = $values[$this->_dataId];
            $tmp['moduleID'] = 30;
            $tmp['contentID'] = $values[$this->_dataId] . '-' . $values[$this->_valurlField];
            $tmp['languageID'] = $values[$this->_indexLanguageId];
            $tmp['title'] = $values[$this->_titleField];
            $tmp['text'] = $values[$this->_dataId] . '-' . $values[$this->_valurlField];
            $tmp['link'] = '';
            $tmp['contents'] = $values[$this->_titleField]
                . " " . $values[$this->_valurlField];

            array_push($indexData, $tmp);
        }

        return $indexData;
    }
}