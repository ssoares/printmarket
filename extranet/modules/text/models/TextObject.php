<?php

class TextObject extends DataObject
{
    protected $_dataClass   = 'Text';
    protected $_indexClass = '';
    protected $_indexLanguageId = 'TD_LanguageID';

    public function setIndexationData()
    {
        $textSelect = new Text();
        $select = $textSelect->select()->setIntegrityCheck(false)
                ->from('TextData', array('ID' => 'TD_ID', 'LanguageID' => 'TD_LanguageID', 'Text' => 'TD_OnlineText'))
                ->join('Blocks', 'B_ID = TD_BlockID', array('BlockID' => 'B_ID', 'ModuleID' => 'B_ModuleID'))
                ->where('B_Online = 1')
                ->join('PagesIndex', 'PI_PageID = B_PageID', array('PageID' => 'PI_PageID', 'Title' => 'PI_PageTitle'))
                ->where('PI_Status = 1')
                ->where('PI_LanguageID = TD_LanguageID');

        $textData = $textSelect->fetchAll($select)->toArray();

        $cpt = count($textData);
        for ($i = 0; $i < $cpt; $i++)
        {
            $indexData['action'] = "add";
            $indexData['pageID'] = $textData[$i]['PageID'];
            $indexData['moduleID'] = $textData[$i]['ModuleID'];
            $indexData['contentID'] = $textData[$i]['ID'];
            $indexData['languageID'] = $textData[$i]['LanguageID'];
            $indexData['title'] = $textData[$i]['Title'];
            $indexData['text'] = '';
            $indexData['link'] = '';
            $indexData['object'] = get_class();
            $indexData['contents'] = $textData[$i]['Title'] . " " . $textData[$i]['Text'];
            Cible_FunctionsIndexation::indexation($indexData);
        }

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
        if (!is_dir($path . '/data/images/content'))
            mkdir($path . '/data/images/content');
    }
}