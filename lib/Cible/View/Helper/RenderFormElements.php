<?php

class Cible_View_Helper_RenderFormElements extends Zend_View_Helper_Abstract
{

    public function renderFormElements($container, $addMapLink = false)
    {
        $html = '<table>';
        $noZeroArray = array();
        foreach ($container as $key => $elem)
        {

            if ($elem instanceof Zend_Form_DisplayGroup || $elem instanceof Zend_Form_SubForm)
            {
                if ($elem instanceof Zend_Form_DisplayGroup)
                {
                    $html .= "<tr><td><font color='#000'>";
                    $html .= '<strong>' .$elem->getLegend () . '</strong>';
                    $html .= "</td></tr>";
                }
                foreach ($elem->getElements() as $key => $children)
                    $html .= $this->render($children, $key);
            }
            else
                $html .= $this->render($elem, $key);
        }

        $html .= "</table>";
        return $html;
    }

    public function render($elem, $key)
    {
        $html = '';
        $locale = new Zend_Locale(Zend_Registry::get('languageSuffix'));
        $value = $elem->getValue();

        $label = $elem->getLabel();

        $fieldName = $elem->getName();
        $echo = true;
        $isParagraph = false;
        $type = $elem->getType();
        $description = $elem->getDescription();
        $displayLogged = false;
        $user = Zend_Registry::get('user');
        if (!empty($user))
            $displayLogged = true;

        switch ($type)
        {
            case 'Zend_Form_Element_Hidden':
            case 'Zend_Form_Element_Submit':
            case 'Zend_Form_Element_Image':
            case 'Zend_Form_Element_ImagePicker':
            case 'Zend_Form_Element_Captcha':
            case 'Cible_Form_Element_Html':
            case 'Zend_Form_Element_Button':
            case 'Zend_Form_Element_Legend':
                $echo = false;
                break;
            case 'Zend_Form_Element_MultiCheckbox':
                $value = $this->view->getValuesFromId($value, $key);

                break;
            case 'Zend_Form_Element_Textarea':
                $isParagraph = true;
                break;

            case 'Zend_Form_Element_Radio':
                if($value == 1)
                    $value = Cible_Translation::getCibleText ('button_yes');
                elseif($value == 0)
                    $value = Cible_Translation::getCibleText ('button_no');
                else
                    $value = $this->view->getValuesFromId(array($value), $key);
                break;
            default:

                $varRow = $elem->getDecorator('row');
                if(!$varRow)
                {
                    $hasShortcut = preg_match('/hasShortcut/', $elem->getDecorator('row')->getOption('class'));
                    if ($hasShortcut)
                        $key = 'shortcut';
                    if (preg_match('/^[0-9]+$|^-[0-9]+$/', $value))
                        $value = $this->view->getValuesFromId($value, $key);
                }
                break;
        }

        if ($echo)
        {
            if (!empty($label) || $label != '&nbsp;')
            {
                $html .= "<tr><td><font color='#454545'>";
                $html .= $label;
                $html .= "</td></tr>";
            }
            $html .= "<tr><td><font color='#000000'>";
            if (!empty($description))
            $html .= $description . ' : ';
            $html .= $value;
            $html .= "</font></td></tr>";

        }


        return $html;
    }
}