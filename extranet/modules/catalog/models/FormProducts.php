<?php
/**
 * Module Catalog
 * Management of the products.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormProducts.php 1845 2016-01-15 15:31:03Z ssoares $id
 */

/**
 * Form to add a new product.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormProducts.php 1845 2016-01-15 15:31:03Z ssoares $id
 */
class FormProducts extends Cible_Form_GenerateForm
{
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
        $this->getDisplayGroup('productFormLeft')->setLegend(null);
        $this->getDisplayGroup('productFormLeftBis')->setLegend(null);
        $this->getDisplayGroup('productFormRight')->setLegend(null);
        $this->getDisplayGroup('productFormBottom')->setLegend(null);

        $moreImg = new Cible_Form_Element_Html('moreImg', array('value' => '<a href="#" id="moreImg">[Ajouter une image]</a>'));
        $moreImg->setOrder(21)
            ->setDecorators(array(
            'ViewHelper',
            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'id' => 'additionalImg'))

        ));
        $this->addElement($moreImg);
        $group = array();
        for($i = 1; $i < $this->_config->catalog->nbTabs + 1; $i++)
        {
            $titleOptions = array('label' => $this->getView()->getCibleText('form_label_P_Tabs') . $i,
                'belongsTo' => 'P_Tabs['.$i.']',
                'rows' => 12
                );
            $specFieldId = 'CPT_TabId' . $i;
            $specTitle = 'CPT_TabTitle' . $i;
            $specText = 'CPT_TabText' . $i;
            $tmpFieldId = new Zend_Form_Element_Hidden($specFieldId,
                array('value' => $i,
                    'rows' => 12,
                    'belongsTo' => 'P_Tabs['.$i.']',
                    'decorators' => array('ViewHelper')));
            $tmpTitle = new Zend_Form_Element_Text($specTitle, $titleOptions);

            $textOptions = array('label' => $this->getView()->getCibleText('form_label_P_TabsText') . $i,
                'mode' => Cible_Form_Element_Editor::ADVANCED,
                'rows' => 12,
                'belongsTo' => 'P_Tabs['.$i.']',
                );
            $tmpTxt = new Cible_Form_Element_Editor($specText, $textOptions);
            $tmpTxt->setDecorators(array(
                'ViewHelper',
                array('Errors', array('placement' => 'prepend')),
                array('label', array('placement' => 'prepend')),
            ));
            $group[] = $specFieldId;
            $group[] = $specTitle;
            $group[] = $specText;
            $this->addElement($tmpFieldId);
            $this->addElement( $tmpTitle);
            $this->addElement($tmpTxt);
        }
        $html = new Cible_Form_Element_Html('associateProducts');
        $this->addElement($html);
        $group[] = 'associateProducts';
        $this->addDisplayGroup($group, 'bottom');
        $bottom = $this->getDisplayGroup('bottom');
        $bottom->setLegend(null)->setOrder(900)
            ->removeDecorator('HtmlTag');
        $bottom->removeDecorator('DtDdWrapper');
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
            elseif (in_array($key, array('P_Warranty', 'P_ImgDim')))
            {
                if (empty ($value))
                    $this->getElement($key . '_preview')->setImage($this->getView()->baseUrl() . "/icons/image_non_ disponible.jpg");
                else
                {
                    $value = $this->_options['imgBasePath'] . $this->_options['nameSize'] . $value;
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
            $html = $this->getElement('associateProducts');
            $html->setValue(Cible_FunctionsAssociationElements::getNewAssociationSetBox(
                    'products',
                    'P_',
                    'PI_Name',
                    '1',
                    $this->getView()->getCibleText('form_label_PI_Name_associate'),
                    $this->getView()->data,
                    $this->getView()->related))
                ->removeDecorator('Label')
                ->removeDecorator('HtmlTag');
        }
        return parent::render($view);
    }
}
