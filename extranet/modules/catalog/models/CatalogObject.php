<?php
/**
 * Module Catalog
 * Management of the Items.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogObject.php 1859 2016-01-26 17:35:28Z ssoares $id
 */

/**
 * Manage data from items table.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: CatalogObject.php 1859 2016-01-26 17:35:28Z ssoares $id
 */
class CatalogObject
{
    protected $_oCC = null;
    protected $_oP = null;
    protected $_oPC = null;
     protected $_oPK = null;
    protected $_oPT = null;
    protected $_buildSubMenuOn = "CatalogCategoriesObject";

    public function setOCC()
    {
        $this->_oCC = new CatalogCategoriesObject();
        return $this;
    }

    public function setOP()
    {
        $this->_oP = new ProductsObject();
        return $this;
    }

    public function setOPC()
    {
        $this->_oPC = new ProductsCategoriesObject();
        return $this;
    }

    public function setOPK()
    {
        $this->_oPK = new ProductsKeywordsObject();
        return $this;
    }

    public function setOPT()
    {
        $this->_oPT = new ProductsTabsObject();
        return $this;
    }

    public function getBuildSubMenuOn()
    {
        return $this->_buildSubMenuOn;
    }


    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($path . '/data/files/order'))
            mkdir($path . '/data/files/order');
        if (!is_dir($path . '/data/files/order/export'))
            mkdir($path . '/data/files/order/export');
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
            mkdir ($imgPath . '/categories' );
            mkdir ($imgPath . '/categories/tmp' );
            mkdir ($imgPath . '/products' );
            mkdir ($imgPath . '/products/tmp' );
            mkdir ($imgPath . '/items' );
            mkdir ($imgPath . '/items/tmp' );
        }
    }

    public function setIndexationData()
    {
        $this->setOCC();
        $this->setOPC();
        $this->setOP();
        $this->setOPK();
        $this->setOPT();

        $cfk = $this->_oPC->getForeignKey();
        $qry = $this->_oPC->setQuery($this->_oP->getAll(null,false))
            ->setForeignKey($this->_oP->getDataId())
            ->joinFetchData();
        $fk = $this->_oCC->getForeignKey();
        $products = $this->_oCC->setQuery($qry)
            ->setForeignKey($cfk)
            ->joinFetchData(true);

        $this->_oCC->setForeignKey($fk);
        $page = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list', 'catalog', null, true);
        foreach ($products as $data)
        {
            $indexData['action'] = "add";
            $indexData['pageID'] = $data[$this->_oP->getDataId()];
            $indexData['moduleID'] = 14;
            $indexData['contentID'] = $data[$this->_oP->getDataId()];
            $indexData['languageID'] = $data[$this->_oP->getIndexLanguageId()];
            $indexData['title'] = $data[$this->_oP->getTitleField()];
            $indexData['text'] = '';
            $indexData['link'] = $page . '/' . $this->getLink($data);
            $indexData['object'] = get_class();
            $indexData['contents'] = implode(' ', $this->getContentData($data));

            Cible_FunctionsIndexation::indexation($indexData);
        }

        return $this;
    }

    private function getContentData($data)
    {
        $keywords = $this->getKeywordsLabel($data);
        $tabs = $this->getTabsContent($data);
        $content = array_merge(
            array(
                $data[$this->_oP->getTitleField()],
                $data['PI_Description'],
                $data['CCI_Description'],
                $data['CCI_Name']
            ),
            $keywords,
            $tabs);

        return $content;
    }
    private function getLink($data)
    {
        $id = $data[$this->_oCC->getDataId()];
        $langId = $data[$this->_oP->getIndexLanguageId()];
        $values = $this->_oCC->getParentsForLink($id, $langId);
        if (empty($values)){
            $values[] = $data[$this->_oCC->getValUrlField()];
        }
        $values[] = $data[$this->_oP->getValUrlField()];
        $link = implode('/', $values);
        return $link;
    }

    private function getKeywordsLabel($data)
    {
        $keywords = array();
        $oV = new ReferencesObject();
        $id = $data[$this->_oP->getDataId()];
        $langId = $data[$this->_oP->getIndexLanguageId()];
        $k = $this->_oPK->getAssociationData($id, $langId);
        foreach($k as $value){
            $ref = $oV->getValueById($value);
            $keywords[$value] = $ref['value'];
        }

        return $keywords;
    }

    private function getTabsContent($data)
    {
        $content = array();
        $id = $data[$this->_oP->getDataId()];
        $langId = $data[$this->_oP->getIndexLanguageId()];
        $tabs = $this->_oPT->setLanguageId($langId)
            ->getAssociationData($id);

        foreach($tabs as $key => $value){
            if (strstr($key, 'CPT_TabText')){
                $content[] = $value;
            }
        }

        return $content;
    }

}