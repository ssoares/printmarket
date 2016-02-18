<?php
/**
 * Module Campaigns
 * Management of the products.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Campaigns
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormCampaigns.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Form to add a new product.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Campaigns
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormCampaigns.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class FormNews extends Cible_Form_GenerateForm
{
    protected $_imagesLst     = array();
    protected $_imagePaths    = array();
    protected $_decoratorTag = 'div';

    public function getImagePaths()
    {
        return $this->_imagePaths;
    }

    public function setImagePaths($imagePaths)
    {
        $this->_imagePaths = $imagePaths;
        return $this;
    }

    public function getImagesLst()
    {
        return $this->_imagesLst;
    }

    public function setImagesLst($imagesLst)
    {
        $this->_imagesLst = $imagesLst;
        return $this;
    }
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($options)
    {
        $this->_addSubmitSaveClose = true;
        $this->_disabledLangSwitcher = true;
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }
        $validationRequired = $options['validationRequired'];
        unset($options['validationRequired']);

        parent::__construct($options);
        $this->getDisplayGroup('columnLeft')->setLegend(null);
        $this->getDisplayGroup('columnRight')->setLegend(null);
        $this->getDisplayGroup('columnBottom')->setLegend(null);
        
        $class=$this->getElement('NI_Title')->getAttrib('class');
        $this->getElement('NI_Title')->setAttrib('class', $class.' full-width');
        
        $class=$this->getElement('NI_ImageAlt')->getAttrib('class');
        $this->getElement('NI_ImageAlt')->setAttrib('class', $class.' full-width');
        
        if ($validationRequired){
            $this->removeElement('NI_Status');
        }

    }

    public function populate(array $values)
    {
        foreach ($values as $key => $value)
        {
            if (in_array($key, $this->_imagesLst))
            {
                if (empty ($value))
                    $this->getElement($key . '_preview')->setImage($this->getView()->baseUrl() . "/icons/image_non_ disponible.jpg");
                else
                {
                    $value = $this->_imagePaths[$key]['imgBasePath']
                        . $this->_imagePaths[$key]['nameSize'] . $value;
                    $this->getElement($key . '_preview')->setImage($value);
                }
            }
        }
        if (isset($values['NI_Status']) && $values['NI_Status'] == 2){
            $values['NI_Status'] = 0;
        }

        return parent::populate($values);
    }

    public function isValid($data)
    {
        return parent::isValid($data);
    }

    public function render(Zend_View_Interface $view = null)
    {
//        if ($this->getView()->joinAssociation)
//        {
//            $related = array();
//            if (isset($this->getView()->related['campaignsProposals'])){
//                $related = $this->getView()->related['campaignsProposals'];
//            }
//            $html = new Cible_Form_Element_Html('associateCampaignsProposals');
//            $html->setValue(Cible_FunctionsAssociationElements::getNewAssociationSetInput(
//                    'campaignsProposals',
//                    'CA_',
//                    'CAI_Name',
//                    '1',
//                    $this->getView()->getCibleText('form_label_proposals_associate'),
//                    $this->getView()->data['campaignsProposals'],
//                    $related))
//                ->setOrder(0)
//                ->removeDecorator('Label')
//                ->removeDecorator('HtmlTag');
//            $rightElems = $this->getDisplayGroup('columnBottom')->getElements();
//            $rightElems['associateCampaignsProposals'] = $html;
//            $this->getDisplayGroup('columnBottom')->setElements($rightElems);
//        }

        $this->setDecorators(array('FormElements', 'Form'));
        return parent::render($view);
    }
}