<?php


class Text_IndexController extends Cible_Controller_Action
{
    public function indexAction()
    {
        $this->view->assign('otherData', false);
        $baseDir = $this->view->baseUrl();

        $this->_testDuplicatedContent();
        $id = $this->_blockId;
        if ($this->_duplicateId > 0)
            $id = $this->_duplicateId;
        $Select = $this->_db->select()
            ->from('TextData')
            ->where('TD_BlockID = ?', $id)
            ->where('TD_LanguageID = ?', Zend_Registry::get("languageID"));
        $textToTransform = $this->_db->fetchRow($Select);
//        $this->view->block = $this->view->decorateImage($textToTransform['TD_OnlineText']);
        if(empty($textToTransform['TD_OnlineText']))
        {
            $otherData = (bool)$this->_hasContent();
            if ($otherData)
                $this->view->assign('otherData', true);
        }

        $this->_resetDefaultAdapter();

        $this->view->block = $textToTransform['TD_OnlineText'];
    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        echo $url;
    }

    protected function _hasContent()
    {
        $textToTransform = array();
        $langs = Cible_FunctionsGeneral::getAllLanguage();
        $lang = $this->view->languageId;
        foreach ($langs as $lg)
        {
            if ($lg['L_ID'] != $lang)
            {
                $Select = $this->_db->select()
                    ->from('TextData')
                    ->where('TD_BlockID = ?', $this->_blockId)
                    ->where('TD_LanguageID = ?', $lg['L_ID']);
                $textToTransform = $this->_db->fetchRow($Select);
            }
        }

        $nbValues = count($textToTransform);

        return $nbValues;
    }
}
