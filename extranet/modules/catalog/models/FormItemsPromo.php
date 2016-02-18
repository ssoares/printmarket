<?php
/**
 * Module Catalog
 * Management of the discount items.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormItemsPromo.php 435 2011-03-28 03:57:25Z ssoares $
 */

/**
 * Form to add a new product.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormItemsPromo.php 435 2011-03-28 03:57:25Z ssoares $
 */
class FormItemsPromo extends Cible_Form
{
    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct($options)
    {
        parent::__construct($options);

        $imageSrc   = $options['imageSrc'];
        $dataId     = $options['dataId'];
        $imgField   = $options['imgField'];
        $isNewImage = $options['isNewImage'];
        $moduleName = $options['moduleName'];

        // List of sub categories
        $oItems    = new ItemsObject();
        $listItems = $oItems->itemsCollection(Zend_Registry::get('currentEditLanguage'));

        $items = new Zend_Form_Element_Select('IP_ItemId');
        $items->setLabel($this->getView()->getCibleText('form_gift_item_label'))
                ->setAttrib('class', 'largeSelect')
                ->setRequired(true)
                ->addValidator(
                    'NotEmpty',
                    true,
                    array(
                        'messages' => array(
                            'isEmpty' => $this->getView()->getCibleText(
                                    'validation_message_empty_field')
                         )
                    )
                );

        $items->addMultiOption('', $this->getView()->getCibleText('form_select_default_label'));
        $items->addMultiOptions($listItems);

        $this->addElement($items);

        // discount item price
        $price = new Zend_Form_Element_Text('IP_Price');
        $price->setLabel(
                $this->getView()->getCibleText('form_item_specialPrice_label'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
        ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        'Errors',
                        array('placement' => 'append')
                    ),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'title')
                        ),
                    )
                )
        ->setAttrib('class','smallTextInput');

        $this->addElement($price);

        // List of sub categories
        $conditionItems = new Zend_Form_Element_Select('IP_ConditionItemId');
        $conditionItems->setLabel($this->getView()->getCibleText('form_condition_item_label'))
                ->setAttrib('class', 'largeSelect')
                ->setRequired(true)
                ->addValidator(
                    'NotEmpty',
                    true,
                    array(
                        'messages' => array(
                            'isEmpty' => $this->getView()->getCibleText(
                                    'validation_message_empty_field')
                         )
                    )
                );

        $conditionItems->addMultiOption('', $this->getView()->getCibleText('form_select_default_label'));
        $conditionItems->addMultiOptions($listItems);

        $this->addElement($conditionItems);

        // number of items to add items dicount
        $nbItem = new Zend_Form_Element_Text('IP_NbItem');
        $nbItem->setLabel(
                $this->getView()->getCibleText('form_number_items_label'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
        ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        'Errors',
                        array('placement' => 'append')
                    ),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'title')
                        ),
                    )
                )
        ->setAttrib('class','smallTextInput');

        $this->addElement($nbItem);

        // number of items to add items dicount
        $conditionAmount = new Zend_Form_Element_Text('IP_ConditionAmount');
        $conditionAmount->setLabel(
                $this->getView()->getCibleText('form_condition_amount_label'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator(
                'NotEmpty',
                true,
                array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText(
                                'validation_message_empty_field')
                     )
                )
            )
        ->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        'Errors',
                        array('placement' => 'append')
                    ),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag'   => 'dd',
                            'class' => 'form_title_inline',
                            'id'    => 'title')
                        ),
                    )
                )
        ->setAttrib('class','smallTextInput');

        $this->addElement($conditionAmount);
    }
}
