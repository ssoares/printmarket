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
 * @version    $Id:
 */

/**
 * Allow to retrieve the path for files such as *.css, *.js
 *
 * @category   Cible
 * @package    cible_View
 * @subpackage Cible_View_Helper
 * @copyright  Copyright (c) 2009 Cible Solutions d'affaire
 *             (http://www.ciblesolutions.com)
 */
class Cible_View_Helper_FormReadOnly extends Zend_View_Helper_Abstract
{
    protected $_openListTag  = "<dl>";
    protected $_closeListTag = "</dl>";
    protected $_openLabelTag  = "<dt>";
    protected $_closeLabelTag = "</dt>";
    protected $_openValueTag  = "<dd>";
    protected $_closeValueTag = "</dd>";

    protected $_listOpened = false;
    protected $_closeList  = false;

    protected $_addSeparator      = false;
    protected $_separatorClass    = false;
    protected $_separatorPosition = array();

    protected $_html = "";

    /**
     * Setter for $_listOpened.
     *
     * @param bool $value Default = false. Allows to add the tag list.
     *
     * @return void
     */
    public function setListOpened($value)
    {
        $this->_listOpened = $value;
    }

    /**
     * Setter for $_addSeparator.
     * Add a decorator between each fieldset.
     *
     * @param bool $value Default = false. Allows to add a line to seprate elements.
     *
     * @return void
     */
    public function setAddSeparator($value)
    {
        $this->_addSeparator = $value;
    }
    /**
     * Setter for $_separatorClass.
     * The class will define CSS style for the separator.
     *
     * @param bool $value The class name for the separator.
     *
     * @return void
     */
    public function setSeparatorClass($value)
    {
        $this->_separatorClass = $value;
    }
    /**
     * Setter for $_separatorPosition.
     * Defines the fielset number which will recieve the sepatator.
     *
     * @param array $value Array containing the positions. If empty, then add
     *                     the separator after each fielset.
     *
     * @return void
     */
    public function setSeparatorPosition($value)
    {
        $this->_separatorPosition = $value;
    }

    /**
     * Create an html code from form elements to render a plain text fom.<br />
     * This function filter subForms and create the read only view.<br />
     * Utilize the elementRender function to do the same with elements.<br />
     *
     * @param Zend_Form  $form  The form to transform into readOnlu view.
     *
     * @return string $this->_html The html code for the elements rendering.
     */
    public function subFormRender(Zend_Form $form, $subFormName = "")
    {
        $subForms = null;
        if(!$this->_listOpened)
        {
            $this->_html       = $this->_openListTag;
            $this->_listOpened = true;
        }


        if (!empty ($subFormName))
        {
            $subForm = $form->getSubForm ($subFormName);
            $this->_html .= $this->elementRender($subForm);
        }
        else
        {
            $subForms = $form->getSubForms();
            $nbSubForms = count($subForms);
            $position = 1;
            foreach ($subForms as $subForm)
            {
                $this->_html .= '<fieldset id = "' . $subForm->getId() . '" class="' . $subForm->getAttrib('class') . '">';
                $this->_html .= '<legend class="readOnly">';
                $this->_html .= $subForm->getLegend();
                $this->_html .= '</legend>';
                // Do not close the list after rendering elemnts of the sub forms.
                $this->_closeList = false;
                // Render sub form elements
                $this->_html .= $this->elementRender($subForm);

                $this->_html .= '</fieldset>';

                if ($this->_addSeparator && in_array($position, $this->_separatorPosition))
                    $this->_html .= '<div class="' . $this->_separatorClass . '"></div>';

                $position++;
            }
            $this->_html .= $this->_closeListTag;
        }

        return $this->_html;
    }

    /**
     * Create an html code from form elements to render a plain text fom.<br />
     * This function filter subForms and create the read only view.<br />
     * Utilize the subFormRender function to do the subform elements rendering.<br />
     *
     * @param Zend_Form  $form  The form to transform into readOnly view.
     *
     * @return string $this->_html The html code for the elements rendering.
     */
    public function elementRender(Zend_Form $form)
    {
        if(!$this->_listOpened || !$form instanceof Zend_Form_SubForm)
        {
            $this->_html       = $this->_openListTag;
            $this->_openedList = true;
            $this->_closeList = true;
        }

        $formElements = $this->_reorderElements($form);

        foreach ($formElements as $element)
        {
            if ($element['value'] != "&nbsp;")
            {
                $this->_html .= "<dt class=\"readOnly\">{$element['label']} : </dt>\n";
                $this->_html .= "<dd class=\"readOnly\">{$element['value']}</dd>\n";
            }

            }

        if($this->_closeList)
        {
            $this->_html .= $this->_openListTag;
            return $this->_html;
        }
    }
    /**
     * Organizes the fieds according its position define in the order attribute.
     * <br /> This is usefull besause of the loop done for the render.
     * It's not the default Zend_Form mechanism and we have to set this order.
     *
     * @param Zend_Form $form The form or subform to render.
     *
     * @return array
     */
    private function _reorderElements(Zend_Form $form)
    {
        $elementsArray = array();

        foreach ($form->getElements() as $key => $element)
        {
            $test = $element instanceof Zend_Form_Element_Hidden;
            if(!$element instanceof Zend_Form_Element_Hidden
                && !$element instanceof Zend_Form_Element_Button
                && !$element instanceof Zend_Form_Element_Submit)
            {
                $value = $element->getValue();
                if(empty($value))
                    $value = '&nbsp;';

                $index = $element->getOrder();
                $elementsArray[$index]['label'] = $element->getLabel();
                $elementsArray[$index]['value'] = $value;
            }

        }

        ksort($elementsArray);

        return $elementsArray;
    }

}