<?php
class Cible_View_Helper_GetProductPath extends Zend_View_Helper_Abstract
{
    public function getProductPath($data){
        $uri = explode('/', $this->view->request->getRequestUri());
        $oCategory = new CatalogCategoriesObject();
        $categories = $oCategory->getParents((int)$data['CC_ID'], $this->view->languageId);
        $urlValues = array_merge($this->cleanupUri($uri), $categories);
        array_push($urlValues, $data['PI_ValUrl']);
        return array_unique($urlValues);
    }

    private function cleanupUri(array $uri)
    {
        foreach($uri as $key => $value){
            if (in_array($value, $this->view->filterFields)){
                unset($uri[$key], $uri[$key+1]);
            }
        }

        return $uri;
    }
}