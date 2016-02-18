<?php

class TextObject extends DataObject
{
    protected $_dataClass   = 'Text';
    protected $_indexClass = '';
    protected $_indexLanguageId = 'TD_LanguageID';

    public function getIndexationData(array $result)
    {
        $searchResults = array();
        $pageSelect = new PagesIndex();
        $select = $pageSelect->select()
            ->where('PI_PageID = ?', $result['pageID'])
            ->where('PI_LanguageID = ?', $result['languageID'])
            ->where('PI_Status = 1');

        $pageData = $pageSelect->fetchRow($select);

        $pageIDArray = Zend_Registry::get('pageIDArray');
        if ($pageData && !in_array($result['pageID'], $pageIDArray))
        {
            $pageIDArray[] = $result['pageID'];
            Zend_Registry::set('pageIDArray', $pageIDArray);
            $searchResults['moduleID'] = $result['moduleID'];
            $searchResults['pageID'] = $result['pageID'];
            $searchResults['contentID'] = $result['contentID'];
            $searchResults['languageID'] = $result['languageID'];
            $searchResults['title'] = $result['title'];
            $searchResults['text'] = $result['text'];
            $searchResults['link'] = '/' . $pageData['PI_PageIndex'];
            $searchResults['excerpt'] = $result['excerpt'];
            $searchResults['pageTitle'] = $pageData['PI_PageTitle'];

        }

        return $searchResults;
    }
}