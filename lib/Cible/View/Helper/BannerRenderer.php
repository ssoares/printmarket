<?php
/**
 * Cible
 *
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaires
 *             (http://www.ciblesolutions.com)
 * @version    $Id: BannerRenderer.php 1221 2013-05-31 15:08:07Z ssoares $
 */

/**
 * Get banner data to display.
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_BannerRenderer extends Zend_View_Helper_Abstract
{
    /**
     * Fecth data to set banner values and render it.
     *
     * @return string
     */
    public function bannerRenderer($groupId = null,$autoPlay = null,$delais = null,$transition = null,$navi = null,$effect = null)
    {
        $imageGroupIndex = 0;
        $currentLang     = Zend_Registry::get('languageID');
        $cat_image       = "";
        $sousCat_image   = "";
        $page_image      = "";
        $imageToShow     = "";
        $textToShow      = "";

        $this->view->assign('autoPlay', $autoPlay);
        $this->view->assign('delais', $delais);
        $this->view->assign('transition', $transition);
        $this->view->assign('navi', $navi);
        $this->view->assign('effect', $effect);

        if (Zend_Registry::isRegistered('subCatId_'))
        {
            $sousCat_ID = Zend_Registry::get('subCatId_');
            $object = new SubCategoriesObject();
            $details = $object->populate($sousCat_ID, $currentLang);
            if (isset($details['SC_BannerGroupID']))
            {
                $sousCat_image = $details['SC_BannerGroupID'];
            }
        }
        if (Zend_Registry::isRegistered('catId_')
            && Zend_Registry::get('catId_') > 0)
        {
            $cat_ID = Zend_Registry::get('catId_');
            $object = new CatalogCategoriesObject();
            $details = $object->populate($cat_ID, $currentLang);

            if (isset($details['C_BannerGroupID']))
                $cat_image = $details['C_BannerGroupID'];
        }
        if (Zend_Registry::isRegistered('bannerGroupImage'))
            $page_image = Zend_Registry::get('bannerGroupImage');

        if ($sousCat_image != "")
            $imageGroupIndex = $sousCat_image;
        else if ($cat_image != "")
            $imageGroupIndex = $cat_image;
        else if ($page_image)
            $imageGroupIndex = $page_image;
        elseif ($groupId)
            $imageGroupIndex = $groupId;

        $images = array();

        if ($imageGroupIndex != 0)
        {
            $obj    = new BannerImageObject();
            $images = $obj->getImageToShow($imageGroupIndex);
        }

        if (count($images))
        {
            $imageToShow = $images;
        }
        else
        {
            return $imageToShow;//[0]['text'] = $this->view->getClientText('banner_default_text');
            //$imageToShow[0]['img']  = $this->view->locateFile("bg_home_header.jpg");
        }
        $this->view->assign('imageToShow', $imageToShow);
        $this->view->assign('textToShow', $textToShow);
    }
}