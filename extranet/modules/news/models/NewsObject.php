<?php

class NewsObject extends DataObject
{
    protected $_dataClass = 'NewsData';
    protected $_dataId = 'ND_ID';
    protected $_dataColumns = array(
        'CategoryID' => 'ND_CategoryID',
        'ReleaseDate' => 'ND_ReleaseDate',
        'ReleaseDateEnd' => 'ND_ReleaseDateEnd',
        'Date' => 'ND_Date',
        'ND_AuthorID' => 'ND_AuthorID'
    );


    protected $_indexClass = 'NewsIndex';
    protected $_indexId = 'NI_NewsDataID';
    protected $_indexLanguageId = 'NI_LanguageID';
    protected $_indexColumns = array(
        'Title' => 'NI_Title',
        'NI_LanguageID' => 'NI_LanguageID',
        'Brief' => 'NI_Brief',
        'Text' => 'NI_Text',
        'ImageSrc' => 'NI_ImageSrc',
        'ImageAlt' => 'NI_ImageAlt',
        'Status' => 'NI_Status',
        'ValUrl' => 'NI_ValUrl'

    );

    public function getListForCategory($catId)
    {
        $langId = Cible_Controller_Action::getDefaultEditLanguage();
        $this->_query = $this->getAll($langId, false);
        $this->_query->where('ND_CategoryID = ?', $catId);
        $data = $this->findData(array(), true);

        return $data;
    }
    public function deleteOld()
    {
        $wheres = array(
            ' C_ID = 1 AND YEAR(ND_Date) NOT IN (2011,2012,2013)	',
            ' C_ID = 2 AND YEAR(ND_Date) NOT IN (2011,2012,2013)	',
            ' C_ID = 3 AND YEAR(ND_Date) NOT IN (2011,2012,2013)	',
            ' C_ID = 4 AND YEAR(ND_Date) NOT IN (2011,2012,2013)	',
            ' C_ID = 42 AND YEAR(ND_Date) NOT IN (2011,2012,2013)	',
//            ' C_ID = 32	',
//            ' C_ID = 13 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 22 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 20 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 21 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 17 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 18 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 19 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 15 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 14 AND (YEAR(ND_ReleaseDate) < 2013 AND YEAR(ND_ReleaseDateEnd) <= 2016)	',
//            ' C_ID = 49 '

            );

        foreach ($wheres as $key => $value)
        {
            $select = parent::getAll(null, false);
            $select->joinLeft('Categories', 'C_ID = ND_CategoryID', array());
            $select->where($value);

            $data = $this->_db->fetchAll($select);

            foreach ($data as $news)
                $this->delete($news['ND_ID']);

            echo "<pre>";
            print_r($select->assemble());
            echo "</pre>";
            var_dump(count($data));
        }
            exit;
    }

    public function setIndexationData()
    {
        $newsSelect = new NewsData();
        $select = $newsSelect->select()->setIntegrityCheck(false)
                ->from('NewsData', array('NewsID' => 'ND_ID', 'CategoryID' => 'ND_CategoryID', 'Date' => 'ND_Date'))
                ->join('NewsIndex', 'NI_NewsDataID = ND_ID', array('LanguageID' => 'NI_LanguageID', 'NewsTitle' => 'NI_Title', 'NewsBrief' => 'NI_Brief', 'NewsText' => 'NI_Text', 'NewsImageAlt' => 'NI_ImageAlt', 'ValUrl' => 'NI_ValUrl'))
                ->where('NI_Status = 1')
                ->where('ND_ReleaseDate <= ?', date('Y-m-d', time()));

        $newsData = $newsSelect->fetchAll($select);

        foreach ($newsData as $data)
        {
            $indexData['action'] = "add";
            $indexData['pageID'] = $data['CategoryID'];
            $indexData['moduleID'] = 2;
            $indexData['contentID'] = $data['NewsID'];
            $indexData['languageID'] = $data['LanguageID'];
            $indexData['title'] = $data['NewsTitle'];
            $indexData['text'] = '';
            $indexData['link'] = $data['Date'] . '/' . $data['ValUrl'];
            $indexData['object'] = get_class();
            $indexData['contents'] = $data['NewsTitle'] . " " . $data['NewsBrief'] . " " . $data['NewsText'] . " " . $data['NewsImageAlt'];

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
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
}
