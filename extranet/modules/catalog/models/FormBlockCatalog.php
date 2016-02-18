<?php
/**
 * Module Catalog
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockCatalog.php 1856 2016-01-26 11:20:40Z ssoares $id
 */

/**
 * Form to add a new catalog block.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockCatalog.php 1856 2016-01-26 11:20:40Z ssoares $id
 */
class FormBlockCatalog extends Cible_Form_Block
{
    protected $_moduleName = 'catalog';
    protected $_jsScript = 'catalogBlock.js';

    public function getJsScript(){
        return $this->_jsScript;
    }

    public function __construct($options = null)
    {
        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        parent::__construct($options);


        /****************************************/
        // PARAMETERS
        /****************************************/

        // select box category (Parameter #1)
        $blockCategory = new Zend_Form_Element_Select('Param1');
        $blockCategory->setLabel(Cible_Translation::getCibleText('catalog_category_block_page'))
        ->setAttrib('class','largeSelect')
        ->setOrder(2)->setDecorators(array(
            'ViewHelper',
            'Label',
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'list-hidden')),
        ));
        $langId = $this->getView()->_defaultEditLanguage;
        $oCategory  = new CatalogCategoriesObject();
        $blockCategory->addMultiOption(0,Cible_Translation::getCibleText('form_select_default_label'));
        $categories = $oCategory->getAll($langId);
        foreach ($categories as $category){
            $blockCategory->addMultiOption($category['CC_ID'],$category['CCI_Name']);
        }

        $listType = new Zend_Form_Element_Radio('Param2');
        $listType->setLabel(Cible_Translation::getCibleText('block_catalog_listType'))
            ->setAttrib('class','largeSelect')
            ->setOrder(20)
            ->setSeparator(' ')
            ->addMultiOptions(array(1 => Cible_Translation::getCibleText('block_catalog_option_1_list'),
                2 => Cible_Translation::getCibleText('block_catalog_option_2_list')))
            ->setValue(1)
            ->setDecorators(array(
                'ViewHelper',
                'Label',
//                array('label', array('placement' => 'append')),
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'radioInline list-hidden')),
            ));

        $listSubtype = new Zend_Form_Element_Radio('Param3');
        $listSubtype->setLabel(Cible_Translation::getCibleText('block_catalog_listSubtype'))
            ->setAttrib('class','largeSelect')
            ->setOrder(30)
            ->setSeparator(' ')
            ->addMultiOptions(array(1 => Cible_Translation::getCibleText('block_catalog_option_1_sublist'),
                2 => Cible_Translation::getCibleText('block_catalog_option_2_sublist')))
            ->setValue(1)
            ->setDecorators(array(
                'ViewHelper',
                'Label',
                array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'radioInline list-hidden sub-list')),
            ));

        $onlyNew = new Zend_Form_Element_Checkbox('Param4');
        $onlyNew->setLabel(Cible_Translation::getCibleText('block_catalog_list_onlyNew'))
            ->setAttrib('class','largeSelect')
            ->setOrder(40)
            ->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox list-hidden opt-prod')),
        ));

        $onlySolde = new Zend_Form_Element_Checkbox('Param5');
        $onlySolde->setLabel(Cible_Translation::getCibleText('block_catalog_list_onlySolde'))
            ->setAttrib('class','largeSelect')
            ->setOrder(50)
            ->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox list-hidden opt-prod')),
        ));

        $onlyCloseout = new Zend_Form_Element_Checkbox('Param6');
        $onlyCloseout->setLabel(Cible_Translation::getCibleText('block_catalog_onlyCloseout'))
            ->setAttrib('class','largeSelect')
            ->setOrder(60)
            ->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox list-hidden opt-prod')),
        ));
        // number of data to display in the list
        $blockNewsMax = new Zend_Form_Element_Text('Param7');
        $blockNewsMax->setLabel($this->getView()->getCibleText('form_list_items_per_page_end'))
            ->setAttrib('class', 'numeric integer')
            ->setOrder(70);



        $this->addElement($blockCategory);
        $this->addElement($listType);
        $this->addElement($listSubtype);
        $this->addElement($onlyNew);
        $this->addElement($onlySolde);
        $this->addElement($onlyCloseout);
        $this->addElement($blockNewsMax);

        $this->removeDisplayGroup('parameters');
        $this->addDisplayGroup(array('Param2','Param3', 'Param4', 'Param5', 'Param6', 'Param1', 'Param999'),'parameters');

    }

}
