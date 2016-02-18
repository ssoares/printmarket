<?php

/**
 * Edith
 *
 * @category  Cible
 * @package   Cible_Form
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: Form.php 1818 2015-10-06 21:11:57Z ssoares $
 */

/**
 * Defines default elements and methods for forms.
 *
 * @category  Cible
 * @package   Cible_Form
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: Form.php 1818 2015-10-06 21:11:57Z ssoares $
 */
class Cible_Form extends Zend_Form {

    protected $_db;
    protected $_params;
    protected $_disabledDefaultActions = false;
    protected $_disabledSaveAction = false;
    protected $_disabledFieldsStatus = false;
    protected $_addRequiredAsterisks = true;
    protected $_addSubmitSaveClose = false;
    protected $_view;
    protected $_config;
    protected $_object;
    protected $_imagesFolder = '/';
    protected $_filesFolder = '/';
    protected $_requiredValidator = null;

    /**
     * Constructor
     *
     * Registers form view helper as decorator
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null) {
        parent::__construct($options);
        //$token   = new Zend_Form_Element_Hash('token',array('salt' => srand()));
        //$this->addElement($token);
        $this->setMethod('post');
        $this->_db = Zend_Registry::get('db');
        $this->_view = $this->getView();
        $this->_config = Zend_Registry::get('config');

//        $this->_view->headLink()->offsetSetStylesheet(99999, $this->_view->locateFile('form.css'), 'all');
        $this->_view->headLink()->appendStylesheet($this->_view->locateFile('forms.css'));
        $this->_imagesFolder .= 'data/images/' . $this->_view->current_module;
        $this->_filesFolder .= 'data/files';

        if (!empty($this->_object) && $this->_object->getAddSubFolder()) {
            $this->_imagesFolder .= '/' . $this->_object->getName();
            $this->_filesFolder .= '/' . $this->_view->current_module . '/' . $this->_object->getName();
        }

        if (isset($options['disabledDefaultActions']) && $options['disabledDefaultActions'] == true)
            $this->_disabledDefaultActions = true;
        if (isset($options['disabledSaveAction']) && $options['disabledSaveAction'] == true)
            $this->_disabledSaveAction = true;
        if (isset($options['disabledFieldsStatus']) && $options['disabledFieldsStatus'] == true)
            $this->_disabledFieldsStatus = true;

        Zend_Registry::set('disabledFieldStatus', $this->_disabledFieldsStatus);
        $this->_requiredValidator = new Zend_Validate_NotEmpty();
        $this->_requiredValidator->setMessage($this->getView()->getCibleText('validation_message_empty_field'), 'isEmpty');

        $_request = Zend_Controller_Front::getInstance()->getRequest();

        $this->_params = $_request->getParams();

        $buttons = array();
        if ($this->_disabledDefaultActions == false) {

            $cancel_url = isset($options['cancelUrl']) ? $options['cancelUrl'] : '';
            // submit button  (save)
            $submitSave = new Zend_Form_Element_Submit('submit');
            $submitSave->setLabel($this->getView()->getCibleText('button_save'))
                    ->setName('submitSave')
                    ->setAttrib('id', 'submitSave')
                    ->setAttrib('class', 'stdButton')
                    ->setDecorators(array(
                        'ViewHelper',
                        array(array('data' => 'HtmlTag'), array('tag' => 'li')),
                        array(array('row' => 'HtmlTag'), array('tag' => 'ul', 'openOnly' => true, 'class' => 'actions-buttons'))
                    ))
                    ->setOrder(1);
            $rowOptions = array('tag' => 'ul');
            if (!$this->_disabledSaveAction){
                $this->addElement($submitSave);
                $buttons[] = $submitSave->getName();
                $rowOptions = array('tag' => 'ul', 'closeOnly' => true);
            }
            // submit and close button: save and go to the return url
            $submitSaveClose = new Zend_Form_Element_Submit('submitClose');
            $submitSaveClose->setLabel($this->getView()->getCibleText('button_save_close'))
                    ->setName('submitSaveClose')
                    ->setAttrib('id', 'submitSaveClose')
                    ->setAttrib('class', 'stdButton')
                    ->setDecorators(
                            array(
                                'ViewHelper',
                                array(array('data' => 'HtmlTag'), array('tag' => 'li')),
                    ))
                    ->setOrder(2);

            if ($this->_addSubmitSaveClose){
                $this->addElement($submitSaveClose);
                $buttons[] = $submitSaveClose->getName();
            }
            // cancel button (don't save and return to the main page)

            $cancel_params = empty($cancel_url) ? array() : array('onclick' => "document.location.href='$cancel_url'");
            $cancel = new Zend_Form_Element_Button('cancel', $cancel_params);
            $cancel->setLabel($this->getView()->getCibleText('button_preview_close'))
                    ->setAttrib('class', 'stdButton')
                    ->setDecorators(array(
                        'ViewHelper',
                        array(array('data' => 'HtmlTag'), array('tag' => 'li')),
                        array(array('row' => 'HtmlTag'), $rowOptions)
                    ))
                    ->setOrder(10);

            $this->addElement($cancel);
            $buttons[] = $cancel->getName();

            // create an action display group with element name previously added to the form
            $this->addDisplayGroup($buttons, 'actions');

            $actions = $this->getDisplayGroup('actions');
            $this->setDisplayGroupDecorators(array(
                'formElements',
                array(array('innerHtmlTag' => 'HtmlTag'), array('tag' => 'div')),
                'fieldset',
                array(array('outerHtmlTag' => 'HtmlTag'), array('tag' => 'dd'))
            ));
        }
        if (!$this->_disabledFieldsStatus) {
            $script = "\n\r" . '<script type="text/javascript">$(document).ready(function(){if(jQuery().fieldStatus) {$(document).fieldStatus();};});</script>';
            $this->getView()->placeholder('footerScript')->append($script);
        }
        if (!empty($this->_object))
            $this->autoGenerate();
    }

    /**
     * Render form
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null) {
        $firstElementID = null;

        foreach ($this->getElements() as $_element) {
            if ($firstElementID == null && ($_element->getType() == 'Zend_Form_Element_Text' || $_element->getType() == 'Cible_Form_Element_Editor' || $_element->getType() == 'Zend_Form_Element_TextArea' ))
                $firstElementID = $_element->getID();

            if ($_element->getType() == 'Cible_Form_Element_Editor') {
                $this->getView()->headScript()->appendFile($this->getView()->baseUrl() . '/js/tinymce/tinymce.min.js');
                break;
            }

            if ($_element->isRequired() && $this->_addRequiredAsterisks) {
                $_element->setLabel("{$_element->getLabel()} <span class='field_required'>*</span>");
            }
        }

        if ($firstElementID != null) {
            $this->getView()->jQuery()->enable();
            $script = <<< EOS


EOS;
            $this->getView()->jQuery()->addOnLoad($script);
        }

        // render parent
        return parent::render($view);
    }

    public function getFirstElement() {
        foreach ($this->getElements() as $element) {
            return $element;
        }
        return null;
    }

    public function addActionButton($element) {
        if ($element->getType() != 'Cible_Form_Element_LanguageSelector') {
            $element->setDecorators(array(
                'ViewHelper',
                array(array('data' => 'HtmlTag'), array('tag' => 'li')),
            ));
        }
        $actions = $this->getDisplayGroup('actions');
        $actions->addElement($element);
    }

    public function disableElementValidation($elements) {
        $elems = array();

        if (!is_array($elements))
            array_push($elems, $elements);
        else
            $elems = $elements;

        foreach ($elems as $el) {
            $this->getElement($el)->clearValidators()
                    ->setRequired(false);
        }
    }

    protected function _addSubFormAsteriks($subForms) {
        foreach ($subForms->getElements() as $_element) {
            if ($_element->getType() == 'Cible_Form_Element_Editor') {
                $this->getView()->headScript()->appendFile($this->getView()->baseUrl() . '/js/tinymce/tinymce.min.js');
                break;
            }

            if ($_element->isRequired() && $this->_addRequiredAsterisks) {
                $_element->setLabel("{$_element->getLabel()} <span class='field_required'>*</span>");
            }
        }

        $tmpForm = $subForms->getSubForms();
        if (is_array($tmpForm))
            $tmpForm = current($tmpForm);

        if (count($tmpForm) && $tmpForm instanceof Zend_Form_SubForm) {
            $this->_addSubFormAsteriks($tmpForm);
        }
    }

    public function formatDivDecorators($options = null) {

        $label = isset($options['label']) ? $options['label'] : true;
        $classDiv = isset($options['classDiv']) ? $options['classDiv'] : "form-div";
        $classLabel = isset($options['classLabel']) ? $options['classLabel'] : "form-label";

        $formsToFormat = array(array('object' => $this, 'type' => 'Form'));
        //decorators
        if ($this->getSubForms()) {
            foreach ($this->getSubForms() as $form) {
                $formsToFormat[] = array('object' => $form, 'type' => 'Fieldset');
            }
        }
        foreach ($formsToFormat as $form) {
            foreach ($form['object']->getElements() as $key => $element) {
                $currentClass = " " . $element->getAttrib('class');
                if ($label) {
                    $placement = '';
                    if ($element->getDecorator('row')){
                        $currentClass .= ' ' . $element->getDecorator('row')->getOption('class');
                    }
                    if ($element->getDecorator('Label')){
                        $placement = $element->getDecorator('Label')->getOption('placement');
                    }
                    $element->setDecorators(array(
                        'ViewHelper',
                        'Errors',
                        array('Description', array('tag' => 'p', 'class' => 'description')),
                        array('Label', array('class' => $classLabel, 'placement' => $placement)),
                        array('HtmlTag', array('class' => $classDiv . $currentClass)),
                    ));
                } else {
                    $element->setDecorators(array(
                        'ViewHelper',
                        'Errors',
                        array('Description', array('tag' => 'p', 'class' => 'description')),
                        array('HtmlTag', array('class' => $classDiv . $currentClass)),
                    ));
                }
                if ($key == 'captcha') {
                    $element->removeDecorator("viewhelper");
                }
            }
            $form['object']->setDecorators(array(
                'FormElements',
                $form['type']
            ));
        }
    }

    public function setRowDecorator($elements, $name, $options = null )
    {
        $class = "row";
        $legend = null;
        if(isset($options['legend'])){
            $legend = $options['legend'];
        }
        if(isset($options['class'])){
            $class = $options['class'];
        }
        $fieldset = 'Fieldset';
        if(isset($options['fieldset_class'])){
            $fielsetclass = $options['fieldset_class'];
            $fieldset = array('Fieldset', array('class' => $fielsetclass));
        }
        $this->addDisplayGroup(
            $elements, $name,
            array(
                'legend' => $legend,
                'decorators' => array('FormElements', $fieldset,
                    array('HtmlTag',array('tag'=>'div', 'class' => $class))
                    )
            )
        );

    }

//    public function autoGenerate()
//    {
//        $metaData = array();
//        $object = $this->_object;
//
//        $metaData = $object->getColsData();
//
//        foreach ($metaData as $key => $meta)
//            $this->setFormFields($meta, $key);
//
//        $indexTable = $object->getIndexTableName();
//        if (!empty ($indexTable))
//        {
//            $metaIndex = $object->getColsIndex();
//            foreach ($metaIndex as $key => $meta)
//                $this->setFormFields($meta, $key);
//        }
//    }
}
