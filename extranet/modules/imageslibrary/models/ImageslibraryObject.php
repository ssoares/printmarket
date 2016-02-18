<?php
/**
 * Module Imageslibrary
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryObject.php 338 2013-01-28 18:02:07Z ssoares $
 */

/**
 * Manage data from Imageslibrary table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: ImageslibraryObject.php 338 2013-01-28 18:02:07Z ssoares $
 */
class ImageslibraryObject extends DataObject
{
    protected $_dataClass       = 'ImageslibraryData';
    protected $_indexClass      = 'ImageslibraryIndex';
    protected $_indexLanguageId = 'ILI_LanguageID';
    protected $_constraint      = '';
    protected $_foreignKey      = 'IL_ID';
    protected $_label1          = 'ILI_Label1';
    protected $_link            = 'ILI_Link';
    protected $_label2          = 'ILI_Label2';
    protected $_description     = 'ILI_Description';
    protected $_position        = 'IL_Seq';
    protected $_query;

    public function save($id, $data, $langId)
    {
        $oRef = new ImageslibraryKeywordsObject();
        $oRef->save($id, $data, $langId);
        $languages = Cible_FunctionsGeneral::getAllLanguage(true);
        parent::save($id, $data, $langId);
        foreach ($languages as $lang)
        {
            $field = $this->_label1 . '_' . $lang['L_ID'];
            $index1[$this->_label1] = $data[$field];
            parent::save($id, $index1, $lang['L_ID']);

            $field = $this->_label2 . '_' . $lang['L_ID'];
            $index2[$this->_label2] = $data[$field];
            parent::save($id, $index2, $lang['L_ID']);

            $field = $this->_description . '_' . $lang['L_ID'];
            $index[$this->_description] = $data[$field];
            parent::save($id, $index, $lang['L_ID']);

            $field = $this->_link . '_' . $lang['L_ID'];
            $index3[$this->_link] = $data[$field];
            parent::save($id, $index3, $lang['L_ID']);
        }
    }

    public function populate($id, $langId)
    {
        $data = array();
        $oRef = new ImageslibraryKeywordsObject();

        $languages = Cible_FunctionsGeneral::getAllLanguage(true);
        foreach ($languages as $lang)
        {
            $field = $this->_description . '_' . $lang['L_ID'];
            $tmp = parent::populate($id, $lang['L_ID']);
            if (empty($data))
                $data = $tmp;
            else
                array_merge($data, $tmp);

            $data[$field] = '';
            if (isset($tmp[$this->_description]))
                $data[$field] = $tmp[$this->_description];
            unset($data[$this->_description]);
        }
        foreach ($languages as $lang)
        {
            $field = $this->_link . '_' . $lang['L_ID'];
            $tmp = parent::populate($id, $lang['L_ID']);
            if (empty($data))
                $data = $tmp;
            else
                array_merge($data, $tmp);

            $data[$field] = '';
            if (isset($tmp[$this->_link]))
                $data[$field] = $tmp[$this->_link];
            unset($data[$this->_link]);
        }
        foreach ($languages as $lang)
        {
            $field = $this->_label1 . '_' . $lang['L_ID'];
            $tmp = parent::populate($id, $lang['L_ID']);
            if (empty($data))
                $data = $tmp;
            else
                array_merge($data, $tmp);

            $data[$field] = '';
            if (isset($tmp[$this->_label1]))
                $data[$field] = $tmp[$this->_label1];
            unset($data[$this->_label1]);
        }
        foreach ($languages as $lang)
        {
            $field = $this->_label2 . '_' . $lang['L_ID'];
            $tmp = parent::populate($id, $lang['L_ID']);
            if (empty($data))
                $data = $tmp;
            else
                array_merge($data, $tmp);

            $data[$field] = '';
            if (isset($tmp[$this->_label2]))
                $data[$field] = $tmp[$this->_label2];
            unset($data[$this->_label2]);
        }
        $refIds = $oRef->getData($id, $langId);
        $data = array_merge($data, $refIds);

        return $data;
    }

    public function delete($id)
    {
        $oRef = new ImageslibraryKeywordsObject();
        $oRef->delete($id);
        parent::delete($id);
    }

    public function setIndexationData()
    {

        return $this;
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
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
}