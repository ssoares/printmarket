<?php

class Cible_View_Helper_PagePerCategoryViewMultiSite extends Zend_View_Helper_Abstract
{

     /**
     * return the page of the details os an events for the site and the category
     *
     * @param string $site
     * @param string $category
     * @return type string
     */
    public function pagePerCategoryViewMultiSite(array $options = array())
    {
        $categoryId = 1;
        $viewName = "details";
        $module = 7;
        $site = "";
        foreach ($options as $key => $array)
        {
            if(count($array) > 0)
            {
                switch ($key)
                {
                    case 'categoryId':
                        $categoryId = $array;
                        break;
                    case 'viewName':
                        $viewName = $array;
                        break;
                    case 'module':
                        $module = $array;
                        break;
                    case 'site':
                        $site = $array;
                        break;
                    default:
                        break;
                }
            }
        }
        $dbs = Zend_Registry::get('dbs');
        $defaultAdapter = $dbs->getDb();
        $dbAdapter = $dbs->getDb($site);
        Zend_Registry::set('db', $dbAdapter);
        $pagelink = Cible_FunctionsCategories::getPagePerCategoryView($categoryId, $viewName, $module, null, true);
        Zend_Registry::set('db', $defaultAdapter);

        return $pagelink;

    }

}