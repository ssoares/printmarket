<?php

class EventsObject extends DataObject
{
    protected $_dataClass = 'EventsData';
    protected $_dataId = 'ED_ID';
    protected $_dataColumns = array(
        'CategoryID' => 'ED_CategoryID',
        'ImageSrc' => 'ED_ImageSrc',
        'ED_Allstar' => 'ED_Allstar'
    );

    protected $_indexClass = 'Events';
    protected $_indexId = 'EI_EventsDataID';
    protected $_indexLanguageId = 'EI_LanguageID';
    protected $_indexColumns = array(
        'Title' => 'EI_Title',
        'Brief' => 'EI_BriefText',
        'Location' => 'EI_Location',
        'Text' => 'EI_Text',
        'ImageAlt' => 'EI_ImageAlt',
        'Status' => 'EI_Status',
        'ValUrl' => 'EI_ValUrl'
    );

    public function getIndexationData(array $result)
    {
        $link = '/' . Cible_FunctionsCategories::getPagePerCategoryView($result['pageID'], 'details');
        if ($link <> '')
            $result['link'] = $link . '/' . $result['link'];

        return $result;
    }
}
