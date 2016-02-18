<?php

class Rssreader_IndexController extends Cible_Controller_Action
{

    /**
     * Set some properties to redirect and process actions.
     *
     * @access public
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('rssReader.css'));
        $this->view->headLink()->appendStylesheet($this->view->locateFile('rssReader.css'));
    }

    /**
    * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function homepagelistAction()
    {
        $_blockID = $this->_request->getParam('BlockID');
        $languageID = Zend_Registry::get('languageID');
        $block_info = Cible_FunctionsBlocks::getBlockDetails($_blockID);

        $data = array(
            'block_title' => $block_info["BI_BlockTitle"],
            'linkMax' => '',
            'title' => '',
            'link' => '',
            'dateModified' => '',
            'description' => '',
            'language' => '',
            'image' => '',
            'entries' => array(),
        );

        $link = Cible_FunctionsBlocks::getBlockParameter($_blockID, $languageID);
        $linkMax = Cible_FunctionsBlocks::getBlockParameter($_blockID, 3);

        $client = Zend_Feed_Reader::getHttpClient();
        $client->setUri($link);
        $response = $client->request();
        if ($response->getStatus() === 200)
        {
        $feed = Zend_Feed_Reader::import($link);

        $data = array(
            'block_title' => $block_info["BI_BlockTitle"],
            'linkMax' => $linkMax,
            'title' => $feed->getTitle(),
            'link' => $feed->getLink(),
            'dateModified' => $feed->getDateModified(),
            'description' => $feed->getDescription(),
            'language' => $feed->getLanguage(),
            'image' => $feed->getImage(),
            'entries' => array(),
        );

        foreach ($feed as $entry)
        {
                $imgSrc = '';
                $enclosure = $entry->getEnclosure();
                if (isset($enclosure) && preg_match('/^image\//',$enclosure->type))
                    $imgSrc = $enclosure->url;

            $edata = array(
                    'title' => $entry->getTitle(),
                    'description' => $entry->getDescription(),
                'dateModified' => $entry->getDateModified(),
                    'authors' => $entry->getAuthors(),
                    'link' => $entry->getLink(),
                    'image' => $imgSrc,
                    'content' => $entry->getContent()
            );

            $data['entries'][] = $edata;
        }
        }

        $this->view->data = $data;
    }
    
    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');
        echo $url;
    }

}
