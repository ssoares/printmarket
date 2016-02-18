<?php
class Cible_View_Helper_MenuToList extends Zend_View_Helper_Abstract
{

    public function menuToList($menu, $options = array())
    {
        $tree = $this->view->menu($menu, array('getTree' => true));
        $list = $this->_buildList($tree);

        return $list;
    }
    private function _buildList($tree)
    {
        $list = array();
        foreach ($tree as $object)
        {
            $url = $link = empty($object['PageID']) || $object['PageID'] == -1 ? $object['Link'] : Cible_FunctionsPages::getPageNameByID($object['PageID'], Zend_Registry::get('languageID'));
            if ($object['PageID'] > 0 && !empty ($object['Link']) )
                $link = $link . $object['Link'];

            $external = false;
            if (empty($link))
            {
                $link = 'javascript:void(0)';
            }
            else
            {
                if (substr($link, 0, 4) == 'http')
                {
                    $link = "{$link}";
                    $external = true;
                }
                else
                    $link = "{$this->view->baseUrl()}/{$link}";
            }
            $list[$link] = $object['Title'];
        }

        return $list;
    }
}