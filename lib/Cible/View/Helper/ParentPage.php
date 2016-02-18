<?php
class Cible_View_Helper_ParentPage extends Zend_View_Helper_Abstract
{
    public function parentPage($blockId)
    {
        $blockData = Cible_FunctionsBlocks::getBlockDetails($blockId);
        $pageId = $blockData['B_PageID'];
        $page = Cible_FunctionsPages::findParentPageID($pageId);
        return Cible_FunctionsPages::getPageLinkByID($page['P_ParentID']);
    }
}