<?php
/**
 * Module Requests
 * Management of the products.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormRequests.php 1295 2013-10-17 17:34:00Z ssoares $id
 */

/**
 * Form to add a new product.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormRequests.php 1295 2013-10-17 17:34:00Z ssoares $id
 */
class FormRequests extends Cible_Form_GenerateForm
{
    protected $_filterTemplate;
    public function setFilterTemplate($filterTemplate){
        $this->_filterTemplate = $filterTemplate;
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
        $add = isset($options['addAction']) ? $options['addAction'] : false;

        parent::__construct($options);
        $this->getDisplayGroup('columnLeft')->setLegend(null);
//        $this->getDisplayGroup('columnRight')->setLegend(null);
//        $this->getDisplayGroup('columnBottom')->setLegend(null);

        $grpAction = $this->getDisplayGroup('actions');
        foreach($grpAction as $key => $btn){
            $buttons[] = $key;
        }
        $html = new Cible_Form_Element_Html('filters');
        $html->removeDecorator('Label')
            ->removeDecorator('HtmlTag')
            ->removeDecorator('DtDdWrapper')->setOrder(15);
        $this->addElement($html);
        // submit and close button: save and go to the return url
        if (!$add){
            $exportReport = new Zend_Form_Element_Submit('exportReport');
            $exportReport->setLabel($this->getView()->getCibleText('button_export'))
                ->setName('exportReport')
                ->setAttrib('id', 'exportReport')
                ->setAttrib('class', 'stdButton actionButtons')
                ->setDecorators(array(
                    'ViewHelper',
                    array(array('data' => 'HtmlTag'), array('tag' => 'li')),
                ))
                ->setOrder(4);
            $this->addElement($exportReport);
            $buttons[] = $exportReport->getName();
            $this->removeDisplayGroup('actions');
            $this->addDisplayGroup($buttons, 'actions');
            $actions = $this->getDisplayGroup('actions');
            $actions->removeDecorator('DtDdWrapper');
        }
    }

    public function render(Zend_View_Interface $view = null)
    {
        $html = $this->getElement('filters');
        $html->setValue($this->_filterTemplate);
        return parent::render($view);
    }
}