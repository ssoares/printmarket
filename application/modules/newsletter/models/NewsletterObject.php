<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2012 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Manage newsletters data.
 *
 * @category
 * @package
 * @copyright Copyright (c)2012 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: NewsletterObject.php 1818 2015-10-06 21:11:57Z ssoares $
 */
class NewsletterObject extends Cible_Newsletters
{
    protected $_dataClass = 'NewsletterReleases';
    protected $_id;


    /**
     * Retrieve the model path to render the current release
     * @return array
     */
    public function getModel($modelId)
    {
        //echo $modelId;
        //exit;
        $data = array();
        $oModels = new NewsletterModelsObject();

        $lang = Zend_Registry::get('languageID');
        if (empty($modelId))
            $model = $oModels->getDefault();
        else
            $model = $oModels->getAll($lang, true, $modelId);

        $data = explode('/', $model[0]['NM_Directory']);
        array_pop($data);
        $data = implode('/', $data);

        return $data;
    }

    public function getIndexationData(array $result)
    {
        $newsData = parent::populate($result['contentID'], $result['languageID']);
        $linkToRelease = count(explode('/', $result['link'])) == 2 ? true : false;
        if ($linkToRelease)
        {
            $link = '/' . Cible_FunctionsCategories::getPagePerCategoryView($result['pageID'], 'details_release');
        }
        else
        {
            $link = '/' . Cible_FunctionsCategories::getPagePerCategoryView($result['pageID'], 'details_article');

        }

        if ($newsData['NR_Date'] <= date('Y-m-d') && $newsData['NR_Online'])
        {
            $link .= '/' . $result['link'];
            $result['link'] = $link;
            if (!$linkToRelease)
                $result['title'] = $newsData['NR_Title'];

        }

        return $result;
    }

}