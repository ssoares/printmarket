<?php
/**
* Build the main menu to display
*
* The system finds pages and each of these child pages to display in the main menu order by position
*
* PHP versions 5
*
* LICENSE: 
*
* @category   Views Helpers
* @package    Default
* @author     Alexandre Beaudet <alexandre.beaudet@ciblesolutions.com>
* @copyright  2009 CIBLE Solutions d'Affaires
* @license    http://www.ciblesolutions.com
* @version    CVS: <?php $ ?> Id:$
*/

class Zend_View_Helper_MenuGallery
{
    /**
     * Construct the menu for a category of gallery.
     *
     * @param id $string The id of the gallery
     * @param galleryID The id of a specific gallery to selected its li (-1 means there is no selected li)
     * @param menuClass The class to add to the menu
     *
     * @return string of list of gallery
     */
    public function MenuGallery($id)
    {
        $returnString = "";
        if (!empty($id))
        {
            $galery = new Galleries();
            $select = $galery->select()->setIntegrityCheck(false)
                        ->from('Galleries')
                        ->join('GalleriesIndex', 'GI_GalleryID = G_ID')
                        ->where('G_CategoryID = ?', $id)                        
                        ->where('GI_LanguageID = ?', Zend_Registry::get('languageID'));           
            return $galery->fetchAll($select);            
        }
        return $returnString;
    }
}
