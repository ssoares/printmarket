<?php
/**
 * Module Template
 * Management of the Template.
 *
 * @category  Extranet_Module
 * @package   Extranet_Modulem_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormTemplate.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Form to add a new product.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Template
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormTemplate.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class FormTemplate extends Cible_Form_GenerateForm
{
    protected $_imagesLst     = array();
    protected $_imagePaths    = array();

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
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }

        parent::__construct($options);
//        $this->getDisplayGroup('columnLeft')->setLegend(null);
//        $this->getDisplayGroup('columnRight')->setLegend(null);
//        $this->getDisplayGroup('columnBottom')->setLegend(null);

        //$this->getDisplayGroup('columnRight')->setLegend(null);
//        $moreImg = new Cible_Form_Element_Html('moreImg',
//            array('value' => '<a href="#" id="moreImg">[Ajouter une image]</a>'));
//        $moreImg->setOrder(21)
//            ->setDecorators(array(
//            'ViewHelper',
//            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'id' => 'additionalImg'))
//
//        ));
//        $this->addElement($moreImg);

//        $html = new Cible_Form_Element_Html('associateTemplate');
//        $this->addElement($html);

    }

    public function populate(array $values)
    {
        foreach ($values as $key => $value)
        {
            $tmpKeys = explode('_', $key);
            if (in_array('tmp', $tmpKeys))
            {
                $tmp = str_replace('_tmp', '_preview', $key);
                if (!empty($value))
                    $this->getElement($tmp)->setImage($value);
                else
                    $this->getElement($tmp)->setImage($this->getView()->baseUrl() . "/icons/image_non_ disponible.jpg");

                unset($values[$key]);
            }
            elseif (in_array($key, $this->_imagesLst))
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

        return parent::populate($values);
    }

    public function render(Zend_View_Interface $view = null)
    {
        if ($this->getView()->joinAssociation)
        {
            $html = new Cible_Form_Element_Html('associateCampaignsProposals');
            $html->setValue(Cible_FunctionsAssociationElements::getNewAssociationSetInput(
                    'xxxxx',
                    'XX_',
                    'XXI_Name',
                    '1',
                    $this->getView()->getCibleText('form_label_xxxxx'),
                    $this->getView()->data['xxxxx'],
                    $this->getView()->related['xxxxx']))
                ->setOrder(0)
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');
            $this->addElement($html);
        }

        return parent::render($view);
    }
}