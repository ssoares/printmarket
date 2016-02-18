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


    public function getIndexationData(array $result)
    {
        $newsSelect = new NewsData();
        $select = $newsSelect->select()
                ->where('ND_ID = ?', $result['contentID']);
        $newsData = $newsSelect->fetchRow($select);

        $link = '/' . Cible_FunctionsCategories::getPagePerCategoryView($result['pageID'], 'details');
        if ($newsData['ND_ReleaseDate'] <= date('Y-m-d') && $link != '')
        {
            $link .= '/' . $result['link'];
            $result['link'] = $link;

        }

        return $result;
    }

}
