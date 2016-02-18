<?php

class Cible_Form_GenerateForm extends Cible_Form_Multilingual
{

    protected $_elemNameId = '';
    protected $_srcData = array();
    protected $_decoParams = array('labelPos' => 'prepend');
    protected $_hasLang = false;
    protected $_groups = array();
    protected $_grpElements = array();
    protected $_groupName = '';
    protected $_script = '';
    protected $_seq = 0;
    protected $_isXmlHttpRequest = false;
    protected $_options = array();
    protected $_addDesc = false;
    protected $_addShortCutPartial;
    protected $_addManageDataPartial;
    protected $_desc = "";
    protected $_addDefault = true;
    protected $_forceNoScript = false;
    protected $_decoratorTag = 'dd';

    public function __construct($options = null)
    {
        $this->_options = $options;
        parent::__construct($options);
    }

    public function autoGenerate()
    {
        $metaData = array();
        if (Zend_Registry::isRegistered('isXmlHttpRequest'))
            $this->_isXmlHttpRequest = Zend_Registry::get('isXmlHttpRequest');

        $object = $this->_object;
        $metaData = $object->getColsData();

        foreach ($metaData as $key => $meta){
            $this->setFormFields($meta, $key);
        }
        $indexTable = $object->getIndexTableName();

        if (!empty($indexTable))
        {
            $this->_hasLang = true;
            $metaIndex = $object->getColsIndex();
            foreach ($metaIndex as $key => $meta)
            {
                $this->_addDefault = true;
                $this->setFormFields($meta, $key);
            }
        }

        if (!empty($this->_grpElements))
        {
            foreach ($this->_grpElements as $key => $group)
            {
                $order = array_shift($group);
                $class = array_shift($group);
                $this->addDisplayGroup($group, $key);
                $tmp = $this->getDisplayGroup($key);
                $tmp->setOrder($order);
                $tmp->setAttrib('class', $class);
                $tmp->removeDecorator('DtDdWrapper');
                $tmp->setLegend($this->getView()->getCibleText('form_group_legend_' . $key));
            }
        }
        $script = <<<EOS
                $(document).ready(function(){
                    {$this->_script}
                });
EOS;
        $this->getView()->headScript()->appendScript($script);

        if ($this->_addShortCutPartial){
            echo $this->getView()->partial("partials/jsManageValuesList.phtml");
        }
        if ($this->_addManageDataPartial){
            echo $this->getView()->partial("partials/jsManageData.phtml");
        }
    }

    public function setFormFields($meta, $key)
    {
        $params = Cible_FunctionsGeneral::fetchParams($meta['COMMENT']);
        $this->_decoParams['class'] = '';
        if (!empty($params['class'])){
            $this->_decoParams['class'] .= $params['class'] . ' ';
        }

        $this->_decoParams['labelPos'] = 'prepend';
        $this->_addDesc = false;

        if (!isset($params['exclude']) || false == (bool) $params['exclude'])
        {
            $this->_elemNameId = $meta['COLUMN_NAME'];
            if (isset($params['group']))
            {
                $this->_setDisplayGroup($params);
            }
            if (isset($params['desc']) || isset($params['descST']))
            {
                $this->_addDesc = true;
                if (isset($params['descST']))
                    $this->_desc = Cible_Translation::getCibleText($params['descST']);
                else
                    $this->_desc = $params['desc'];

            }

            switch ($meta['DATA_TYPE'])
            {
                case 'decimal':
                case 'float':
                case 'double':
                case 'tinyint':
                case 'int':
                    if (!$meta['PRIMARY'] || !empty($params['elem'])){
                        $this->setElementInput($meta, $params);
                    }
                    break;

                case 'char':
                case 'varchar':
                    $this->setElementTextField($meta, $params);
                    break;
                case 'tinytext':
                case 'longtext':
                case 'text':
                    $this->setElementText($meta, $params);
                    break;

                case 'time':
                    $params['elem'] = "time";
                    $this->setElementDatepicker($meta, $params);
                    break;

                case 'date':
                    $params['elem'] = ""; // Équivalent à Date (par défaut)
                    $this->setElementDatepicker($meta, $params);
                    break;
                case 'timestamp':
                case 'datetime':
                    if(!isset($params['elem']))
                        $params['elem'] = "datetime";

                    $this->setElementDatepicker($meta, $params);
                    break;
                case 'time':
                    if (empty($params['elem']))
                        $params['elem'] = 'time';

                    $this->setElementDatepicker($meta, $params);
                    break;

                default:
                    if (preg_match('/^enum/', $meta['DATA_TYPE']))
                    {
                        $params['elem'] = 'select';
                        $params['src'] = 'enum';
                        $this->setElementInput($meta, $params);
                    }
                    break;
            }
        }
    }

    /**
     * Defines and build an input field which is not a text field.
     * According to the parameter elem, it will set the element.
     *
     * $params['exclude']   => boolean; The column will not be built.<br />
     * $params['required '] => boolean;<br />
     * $params['elem']      => select, checkbox, radio, editor;<br />
     *                         If $params['elem'] = select, then the $params['src']<br />
     *                         parameter must be defined.
     * $params['src']       => string; name of the source for the element.<br />
     *
     * @param array $meta
     * @param array $params
     *
     * @return void
     */
    public function setElementInput(Array $meta, Array $params)
    {
        if (!empty($params))
        {
            if (!isset($params['elem']))
                $params['elem'] = '';

            $classForNumericFormat = '';
            $fieldId = $meta['COLUMN_NAME'];
            if (!empty($params['placeholder'])){
                $this->_decoParams['placeholder'] = $params['placeholder'];
            }
            switch ($params['elem'])
            {
                case 'select':
                    $this->_addDefault = true;
                    if (empty($params['src']))
                        throw new Exception('Trying to build an element but no data source given');

                    $this->_defineSrc($params, $meta);

                    $element = new Zend_Form_Element_Select($fieldId);
                    $element->setLabel($this->getView()->getCibleText('form_label_' . $fieldId))
                        ->setAttrib('class', 'largeSelect')
                        ->addMultiOptions($this->_srcData);
                    $element = $this->_setBasicDecorator($element);
                    break;
                case 'checkbox':
                    $element = new Zend_Form_Element_Checkbox($fieldId);
                    $element->setLabel($this->getView()->getCibleText('form_label_' . $fieldId));
                    $this->_decoParams['class'] .= 'label_after_checkbox';
                    $this->_decoParams['labelPos'] = 'append';
                    $element = $this->_setBasicDecorator($element);
                    break;
                case 'radio':
                    $this->_addDefault = false;
                    $this->_defineSrc($params, $meta);

                    $element = new Zend_Form_Element_Radio($fieldId);
                    $element->setLabel($this->getView()->getCibleText('form_label_' . $fieldId));
                    $element->setSeparator('')
                        ->addMultiOptions($this->_srcData);
                    $this->_decoParams['class'] .= 'radio radioInline';
                    $element = $this->_setBasicDecorator($element);
                    break;
                case 'hidden':
                    $element = new Zend_Form_Element_Hidden($fieldId);
                    $element->setDecorators(array('ViewHelper'));
                    break;

                default:
                    $classForNumericFormat = 'numeric';
                    $element = new Zend_Form_Element_Text($fieldId);
                    $element->setLabel(
                            $this->getView()->getCibleText('form_label_' . $fieldId))
                        ->addFilter('StringTrim');

                    switch ($meta['DATA_TYPE'])
                    {
                        case 'decimal':
                        case 'double':
                        case 'float':
                            $classForNumericFormat .= ' decimal';
                            break;

                        default:
                            $classForNumericFormat .= ' integer';
                            break;
                    }
                   $this->_decoParams['class'] .= 'smallTextInput';
                    $element = $this->_setBasicDecorator($element);
                    $element->setAttrib('maxlength', $meta['LENGTH']);
                    break;
            }

            if (!$meta['NULLABLE'])
            {
                $element->setRequired(true);
                $element->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
            }
            if (!empty($params['disabled']))
                $element->setAttrib('disabled', (bool) $params['disabled']);

            if (!empty($params['seq']))
                $element->setOrder($params['seq']);
            if ($this->_addDesc)
                $element->setDescription($this->_desc);
            if (!empty($this->_groupName))
                array_push($this->_grpElements[$this->_groupName], $fieldId);

            $element->setAttrib('class', $classForNumericFormat);

            $this->addElement($element);

            if (isset($params['shortCut']) && SESSIONNAME != 'application')
                $this->_addShortcut($params, $meta);
            if (isset($params['manageData']) && SESSIONNAME != 'application')
                $this->_addManageData($params, $meta);

        }
    }

    /**
     * Defines and build an input text field.
     * According to the parameter elem, it will set the element.
     *
     * $params['exclude']   => boolean; defines if the column is built.
     * $params['required '] => boolean;
     * $params['elem']      => select, checkbox, radio, editor;
     *                         If $params['elem'] = select, then the $params['src']
     *                         parameter must be defined.
     * $params['src']       => string; name of the source for the element.
     *
     * @param array $meta
     * @param array $params
     *
     * @return void
     */
    public function setElementTextField(Array $meta, Array $params)
    {
        $isUnique = '';
        $validators = array();
        $addHidden = false;
        if (!empty($params['placeholder'])){
            $this->_decoParams['placeholder'] = $params['placeholder'];
        }
        if (!isset($params['elem']))
            $params['elem'] = '';

        if (!empty($params))
        {
            if (isset($params['validate']))
            {
                $validateName = '_' . $params['validate'] . 'Validate';
                if (isset($params['unique']))
                    $isUnique = $meta['COLUMN_NAME'];

                $validators = $this->$validateName($isUnique);
            }
        }
        switch ($params['elem'])
        {
            case 'hidden':
                $element = new Zend_Form_Element_Hidden($meta['COLUMN_NAME']);
                $element->setDecorators(array('ViewHelper'))->removeDecorator('DtDdWrapper');
                break;
            case 'image':
                $imageSrc = $this->_options['imageSrc'];
                $dataId = $this->_options['dataId'];
                $imgField = $meta['COLUMN_NAME'];
                $isNewImage = $this->_options['isNewImage'];
                $moduleName = $this->_options['moduleName'];

                if ($dataId == '')
                    $pathTmp = $this->_imagesFolder . "/tmp";
                else
                    $pathTmp = $this->_imagesFolder . "/" . $dataId . "/tmp";

                // hidden specify if new image for the news
                $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value' => $isNewImage));
                $newImage->setDecorators(array('ViewHelper'));

                $this->addElement($newImage);

                $imageTmp = new Zend_Form_Element_Hidden($imgField . '_tmp');
                $imageTmp->setDecorators(array('ViewHelper'));
                $this->addElement($imageTmp);

                $imageOrg = new Zend_Form_Element_Hidden($imgField . '_original');
                $imageOrg->setDecorators(array('ViewHelper'));
                $this->addElement($imageOrg);

                $imageView = new Zend_Form_Element_Image(
                        $imgField . '_preview',
                        array('onclick' => 'return false;')
                );
                $this->_decoParams['class'] .= 'imgPreview';
                $this->_setBasicDecorator($imageView);
                $baseUrl = $this->getView()->BaseUrl();
                if (SESSIONNAME == 'application'){
                    $baseUrl .= '/extranet';
                }
                $imageView->setImage($baseUrl . "/icons/image_non_ disponible.jpg");
                if (!empty($imageSrc) && file_exists(Zend_Registry::get('fullDocumentRoot') . $imageSrc)){
                    $imageView->setImage($imageSrc);
                }
                if (!isset($params['imgLabel'])){
                    $imageView->removeDecorator('Label');
                }else{
                    $imageView->setLabel(
                        $this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
                }
                $this->addElement($imageView);

                if (!empty($this->_groupName))
                {
                    array_push($this->_grpElements[$this->_groupName], 'isNewImage');
                    array_push($this->_grpElements[$this->_groupName], $imgField . '_tmp');
                    array_push($this->_grpElements[$this->_groupName], $imgField . '_original');
                    array_push($this->_grpElements[$this->_groupName], $imgField . '_preview');
                }
                $element = new Cible_Form_Element_ImagePicker(
                    $imgField,
                    array(
                        'onchange' => "document.getElementById('imageView').src = document.getElementById('" . $imgField . "').value",
                        'associatedElement' => $imgField . '_preview',
                        'pathTmp' => $pathTmp, 'contentID' => $dataId));
                $this->_decoParams['class'] .= 'imgButtons';
                $element->removeDecorator('Label');

                break;
            case 'fileManager':
                $urlFile = new Zend_Form_Element_Hidden($meta['COLUMN_NAME']);
                $urlFile->setDecorators(array('ViewHelper'));
                $this->addElement($urlFile);
                $rootPath = $this->_view->currentSite . '/data/files/';
                $pathTmp = $this->_filesFolder;
                $dataId = isset($this->_options['dataId'])?$this->_options['dataId']:null;
                if(isset($params['storage']) && $params['storage'] == 'module')
                {
                    $tmpPath = Zend_Registry::get('fullDocumentRoot');
                    $tmpPath .= Zend_Registry::get('www_root');
                    $tmpPath .= $this->_view->currentSite . '/data/files/';
                    $tmpPath .= $this->_view->current_module;
                    if (!preg_match("/{$this->_view->current_module}/", $this->_filesFolder))
                        $this->_filesFolder .= '/' . $this->_view->current_module;

                    if (!is_dir($tmpPath))
                    {
                        mkdir($tmpPath);
                        mkdir($tmpPath . '/tmp');
                        if ($dataId != '')
                            mkdir($tmpPath. '/' . $dataId);
                    }

                    if (SESSIONNAME == 'application' && !is_dir($tmpPath. '/tmp/' . $dataId))
                    {
                        mkdir($tmpPath. '/tmp/' . $dataId);
                        chmod($tmpPath. '/tmp/' . $dataId, 0777);
                    }

                    $rootPath .= $this->_view->current_module;
                    if (!empty($dataId))
                    {
                        $pathTmp = $this->_filesFolder . "/" . $dataId;
                        if (SESSIONNAME == 'application')
                        {
                            $pathTmp = $this->_filesFolder . "/tmp/" . $dataId;
                            $rootPath .= "/tmp/" . $dataId;
                        }
                    }
                    else
                    {
                        $pathTmp = $this->_filesFolder . "/tmp";
                        if (SESSIONNAME == 'application')
                            $rootPath .= "/tmp" ;
                    }
                }

                $file = new Zend_Form_Element_Hidden($meta['COLUMN_NAME'] . '_name');
                $file->setDecorators(array('ViewHelper'));
                $this->addElement($file);
                $options = array(
                    'associatedElement' => $this->getName(),
                    'displayElement'    => $meta['COLUMN_NAME'] . '_name',
                    'pathTmp'           => $pathTmp,
                    'contentID'         => $this->_dataId,
                    'sizeField'         => 'FI_Size',
                    'storage'           => $rootPath,
//                    'setInit'           => true,
                    'setUpload'         => false
                );
//                if (!isset($params['storage']))
                if (SESSIONNAME == 'application')
                {
                    $options['setUpload'] = true;
                    $_SESSION['isLoggedIn'] = true;
                }
                $element = new Cible_Form_Element_FileManager(
                        $meta['COLUMN_NAME'],
                        $options
                );
                $element->setLabel(
                        $this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']))
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim');

                $this->_script .= "if ($('#{$meta['COLUMN_NAME']}').length){\r\n";
                $this->_script .= "var value = ($('#{$meta['COLUMN_NAME']}').val()).split('/');\r\n";
                $this->_script .= "$('#{$meta['COLUMN_NAME']}_name[type=hidden]').remove();\r\n";
                $this->_script .= "$('#{$meta['COLUMN_NAME']}_name').val(value.pop());\r\n}";
                $this->_setBasicDecorator($element);
                break;
            case 'radio':
                $this->_addDefault = false;
                $this->_defineSrc($params, $meta);

                $element = new Zend_Form_Element_Radio($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
                $element->setSeparator('')
                    ->addMultiOptions($this->_srcData);
                $this->_decoParams['class'] .= 'radio radioInline';
                $element = $this->_setBasicDecorator($element);
                break;
            case 'multiCheckbox':
                if (empty($params['src']))
                    throw new Exception('Trying to build an element but no data source given');
                $this->_addDefault = false;
                $this->_defineSrc($params, $meta);

                $element = new Zend_Form_Element_MultiCheckbox($meta['COLUMN_NAME']);
                unset($this->_srcData[0]);
                $element->addMultiOptions($this->_srcData);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
//                    $element->removeDecorator('Label');
                $element->setSeparator(' ');
                $this->_decoParams['class'] .= 'multicheckbox';
                $element = $this->_setBasicDecorator($element);
                $tmpLabel = $element->getDecorator('Label');
                $tmpLabel->setOption('class', 'noStyle');
                break;
            case 'multiSelect':
                if (empty($params['src']))
                        throw new Exception('Trying to build an element but no data source given');

                $this->_defineSrc($params, $meta);

                $element = new Zend_Form_Element_Multiselect($meta['COLUMN_NAME']);
                unset($this->_srcData[0]);
                $element->addMultiOptions($this->_srcData);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
//                $element->removeDecorator('Label');
                $element->setSeparator(' ');
                $this->_decoParams['class'] .= 'multiselect';
                $element = $this->_setBasicDecorator($element);
                break;
            case 'select':
                $this->_addDefault = true;
                if (empty($params['src']))
                    throw new Exception('Trying to build an element but no data source given');

                $this->_defineSrc($params, $meta);

                $element = new Zend_Form_Element_Select($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']))
                    ->setAttrib('class', 'largeSelect' . $this->_decoParams['class'])
                    ->addMultiOptions($this->_srcData);
                $element = $this->_setBasicDecorator($element);
                break;
            case 'password':
                $element = new Zend_Form_Element_Password($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
                $element = $this->_setBasicDecorator($element);
                break;
            case 'autoComplete':
                $source = $this->getView()->BaseUrl() . '/'
                    . $this->_object->getName() . '/index/ajax/actionAjax/';
                $element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
//                $element = new ZendX_JQuery_Form_Element_AutoComplete($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
//                $fn = new Zend_Json_Expr('function(event,ui) { $("#'.$meta['COLUMN_NAME'] . 'Index'.'").val(ui.item.id) }');
//                $options = array(
//                    'data'=> array(),
//                    'source'=> $source . $params['src'],
//                    "select" => $fn
//                    );
//                $element->setJQueryParams($options);
                $this->_forceNoScript = true;
                $element = $this->_setBasicDecorator($element);
                $addHidden = true;
                break;
            case 'email':
                $element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
                $element->addValidators($this->_emailValidate());
                $element = $this->_setBasicDecorator($element);
                break;

            default:

                $element = new Zend_Form_Element_Text($meta['COLUMN_NAME']);
                $element->setLabel($this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']));
                $element->setAttrib('class', 'stdTextInput ' . $this->_decoParams['class']);

                $this->_setBasicDecorator($element);
                break;

        }

        if(!empty($meta['LENGTH'])){
            $element->setAttrib('maxlength', $meta['LENGTH']);
        }

        if (!$meta['NULLABLE'])
        {
            $element->setRequired(true);
            $element->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
        }
        if (count($validators) > 0)
            $element->addValidators($validators);

        if (!empty($params['disabled']))
        {
            $element->setAttrib('disabled', (bool) $params['disabled']);
            $element->setAttrib('class', 'disabled');
        }

        if (!empty($params['seq']))
            $element->setOrder($params['seq']);
        if ($this->_addDesc)
            $element->setDescription($this->_desc);
        if (!empty($this->_groupName))
            array_push($this->_grpElements[$this->_groupName], $meta['COLUMN_NAME']);
        if ($addHidden){
            $params['elem'] = 'hidden';
            $params['class'] = '';
            $meta['COLUMN_NAME'] = $meta['COLUMN_NAME'] . 'Index';
            $this->setElementInput($meta, $params);
        }
        if ($this->_hasLang)
        {
            $label = $element->getDecorator('Label');
            if ($label)
                $label->setOption('class', $this->_labelCSS);
        }

        if (isset($params['shortCut']) && SESSIONNAME != 'application')
            $this->_addShortcut($params, $meta);
        $this->addElement($element);
    }

    /**
     * Defines and build a textarea.
     * According to the parameter elem, it will set the element.
     *
     * $params['exclude']   => boolean; defines if the column is built.
     * $params['required '] => boolean;
     * $params['elem']      => select, checkbox, radio, editor;
     *                         If $params['elem'] = select, then the $params['src']
     *                         parameter must be defined.
     * $params['src']       => string; name of the source for the element.
     *
     * @param array $meta
     * @param array $params
     *
     * @return void
     */
    public function setElementText(Array $meta, Array $params)
    {
         if (!empty($params['placeholder'])){
            $this->_decoParams['placeholder'] = $params['placeholder'];
        }
        if (!isset($params['elem']))
            $params['elem'] = '';

        switch ($params['elem'])
        {
            case 'tiny':
                $element = new Cible_Form_Element_Editor(
                        $meta['COLUMN_NAME'],
                        array('mode' => Cible_Form_Element_Editor::ADVANCED));
                $element->setDecorators(array(
                    'ViewHelper',
                    array('Errors', array('placement' => 'prepend')),
                    array('label', array('placement' => 'prepend')),
                ));
                break;

            default:
                $element = new Zend_Form_Element_Textarea($meta['COLUMN_NAME']);
                break;
        }
        if (empty($params['elem']))
            $element->setAttrib('class', 'mediumEditor');
        elseif (isset($params['class']))
            $element->setAttrib('class', $params['class']);
        $element = $this->_setBasicDecorator($element);
        $label = $this->getView()->getCibleText('form_label_' . $meta['COLUMN_NAME']);

        if (isset($params['maxLength']))
        {
            $tmp = '<span class="charsLeft">' . $params['maxLength'] . '</span>';
            $label = str_replace('##XX##', $tmp, $label);
            $element->setAttrib('maxlength', $params['maxLength']);
        }
        else if(!empty($meta['LENGTH'])){
            $element->setAttrib('maxlength', $meta['LENGTH']);
        }
        if (!$meta['NULLABLE']){
            $element->setRequired(true)->addValidator('NotEmpty', true,
                    array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
        }

        $element->setLabel($label);
        if ($this->_hasLang)
        {
            $label = $element->getDecorator('Label');
            $label->setOption('class', $this->_labelCSS);
        }
        if ($this->_addDesc)
            $element->setDescription($this->_desc);
        if (!empty($params['seq']))
            $element->setOrder($params['seq']);
        if (!empty($this->_groupName))
            array_push($this->_grpElements[$this->_groupName], $meta['COLUMN_NAME']);

        $this->addElement($element);
    }

    /**
     * Defines and build a date picker.
     * According to the parameter elem, it will set the element.
     *
     * $params['exclude']   => boolean; defines if the column is built.
     * $params['required '] => boolean;
     * $params['elem']      => select, checkbox, radio, editor;
     *                         If $params['elem'] = select, then the $params['src']
     *                         parameter must be defined.
     * $params['src']       => string; name of the source for the element.
     *
     * @param array $meta
     * @param array $params
     *
     * @return void
     */
    public function setElementDatepicker(Array $meta, Array $params)
    {
        $picker = "";

        $validateOptions = array();

        switch($params['elem'])
        {
            case "time":
                $picker = "Cible_Form_Element_DatetimePicker";
                $options = array('timepicker' => true,
                        'jquery.params' => array(
//                        'timeFormat'=> "h:m",
//                        'ampm'=> true,
                        'hourGrid'=> 4,
                        'minuteGrid' => 10,
                        'defaultDate' => "$('" . $this->_elemNameId . "').val())",
                    ));

                $params['validate'] = '';

                break;

            case "datetime":
                $picker = "Cible_Form_Element_DatetimePicker";
                $options = array();

                $params['validate'] = '';
                /*
                 * 'jquery.params' => array(
                        'changeYear' => true,
                        'changeMonth' => true,
                        'yearRange' => 'c-10:c+20',
                        'altField' => '#' . $this->_elemNameId . 'Dt',
                        'altFormat' => 'yy-mm-dd',
                        'dateFormat' => 'dd-mm-yy',
                        'timeFormat'=> 'hh:mm',
                        'defaultDate' => "$('" . $this->_elemNameId . "').val())",
                        'YearOrdering' => 'desc'
                    )
                 */


                break;

            default:
                $picker = "Cible_Form_Element_DatePicker";
                $options = array('jquery.params' => array(
                        'changeYear' => true,
                        'changeMonth' => true,
                        'yearRange' => 'c-10:c+20',
                        'altField' => '#' . $this->_elemNameId . 'Dt',
                        'altFormat' => 'yy-mm-dd',
                        'dateFormat' => 'dd-mm-yy',
                        'defaultDate' => "$('#" . $this->_elemNameId . "').val())",
                        'YearOrdering' => 'desc',
                        'showOtherMonths' => true,
                        'selectOtherMonths' => true
                    ));

                $validateOptions = array(
                'messages' => array(
                    'dateNotYYYY-MM-DD' => $this->getView()->getCibleText('validation_message_invalid_date'),
                    'dateInvalid' => $this->getView()->getCibleText('validation_message_invalid_date'),
                    'dateFalseFormat' => $this->getView()->getCibleText('validation_message_invalid_date')
                ));

                break;

        }

        if ($this->_addDesc)
            $element->setDescription($this->_desc);
        if (!empty($params['yearRange'])){
            $options['jquery.params']['yearRange'] = str_replace('>', ':', $params['yearRange']);
        }
        if (isset($params['minDate'])){
            $options['jquery.params']['minDate'] = $params['minDate'];
        }
//        if (!empty($params['noWE'])){
//            $options['jquery.params']['beforeShowDay'] = '$.datepicker.noWeekends';
//        }
        $date = new $picker($this->_elemNameId, $options);
        $date->setLabel($this->getView()->getCibleText('form_label_' . $this->_elemNameId));

        if (!$meta['NULLABLE'])
            $date->setRequired(true)
                ->addValidator(
                    'NotEmpty', true, array(
                    'messages' => array(
                        'isEmpty' => $this->getView()->getCibleText('validation_message_empty_field')
                    )
                    )
            );

        if (!empty($params['validate']))
            $date->addValidator('Date', true, $validateOptions);

        if (!empty($params['disabled']))
        {
            $date->setAttrib('disabled', (bool) $params['disabled']);
            $date->setAttrib('class', 'disabled');
        }

        $params['elem'] = 'hidden';
        $params['class'] = '';
        if (!empty($this->_groupName))
            array_push($this->_grpElements[$this->_groupName], $meta['COLUMN_NAME']);
        $meta['COLUMN_NAME'] = $meta['COLUMN_NAME'] . 'Dt';
        $this->setElementInput($meta, $params);
        $date = $this->_setBasicDecorator($date);
        if (!empty($params['seq']))
            $date->setOrder($params['seq']);
        $this->addElement($date);
    }

    protected function _enumSrc(Array $meta = array())
    {
        $values = explode(',', str_replace(array('enum(', ')', "'"), '', $meta['DATA_TYPE']));
        foreach ($values as $key => $value)
        {
            $this->_srcData[$key + 1] = $value;
        }
    }

    protected function _salutationsSrc(Array $meta = array())
    {
        $greetings = $this->getView()->getAllSalutation();
        foreach ($greetings as $greeting)
        {
            $this->_srcData[$greeting['S_ID']] = $greeting['ST_Value'];
        }
    }
    protected function _sexeSrc(Array $meta = array())
    {
        if ($this->_addDefault)
            $this->_srcData[0] = $this->getView()->getCibleText('form_select_default_label');

        $this->_srcData[1] = $this->getView()->getCibleText('male');
        $this->_srcData[2] = $this->getView()->getCibleText('female');
    }

    protected function _languagesSrc(Array $meta = array())
    {
        $langs = Cible_FunctionsGeneral::getAllLanguage();

        foreach ($langs as $lang)
        {
            $this->_srcData[$lang['L_ID']] = $lang['L_Title'];
        }
    }

    protected function _yesNoSrc(Array $meta = array())
    {
        if ($this->_addDefault)
            $this->_srcData[-1] = '';
        $this->_srcData[1] = $this->getView()->getCibleText('button_yes');
        $this->_srcData[0] = $this->getView()->getCibleText('button_no');
    }

    protected function _yesMaybeSrc(Array $meta = array())
    {
        if ($this->_addDefault)
            $this->_srcData[-1] = '';
        $this->_srcData[1] = $this->getView()->getCibleText('button_yes');
        $this->_srcData[0] = $this->getView()->getCibleText('button_maybe');
    }

    protected function _modulesSrc(Array $meta = array())
    {
        $modules = Cible_FunctionsModules::getModules();

        foreach ($modules as $data)
        {
            $this->_srcData[$data['M_ID']] = Cible_Translation::getCibleText($data['M_MVCModuleTitle'] . "_module_name");
        }
    }

    protected function _emailValidate($isUnique = '')
    {
        $validators = array();
        $regexValidate = new Cible_Validate_Email();
        $regexValidate->setMessage($this->getView()->getCibleText('validation_message_emailAddressInvalid'), 'regexNotMatch');
        array_push($validators, $regexValidate);

        if (!empty($isUnique))
        {
            $emailNotFoundInDBValidator = new Zend_Validate_Db_NoRecordExists($this->_object->getDataTableName(), $isUnique);
            $emailNotFoundInDBValidator->setMessage($this->getView()->getClientText('validation_message_email_already_exists'), 'recordFound');
            array_push($validators, $emailNotFoundInDBValidator);
        }

        return $validators;
    }

    protected function _setBasicDecorator($element)
    {
        $class = '';
        if (!empty($this->_decoParams['class']))
            $class = $this->_decoParams['class'];
        $opt = array(
            'ViewHelper',
            "Errors",
            array('label', array('placement' => $this->_decoParams['labelPos'])),
        );

        if ($this->_addDesc)
            array_push($opt, array('description', array('tag' => 'span', 'class' => 'description')));

        array_push($opt, array(
            array('row' => 'HtmlTag'),
            array(
                'class' => $class)
        ));

        if ($element instanceof Cible_Form_Element_DatePicker){
            $opt = array(
                "UiWidgetElement",
                "Errors",
                array('label', array('placement' => $this->_decoParams['labelPos'])),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => $this->_decoratorTag,
                        'class' => $class)
                ),
            );
        }elseif ($element instanceof Cible_Form_Element_DatetimePicker){
            $opt = array(
                "UiWidgetElement",
                "Errors",
                array('label', array('placement' => $this->_decoParams['labelPos'])),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => $this->_decoratorTag,
                        'class' => $class)
                ),
            );
        }

        if (!empty($this->_decoParams['placeholder'])){
            $key = $this->_decoParams['placeholder'] == 'true' ?
                "placeholder_" . $element->getName() : "placeholder_" . $this->_decoParams['placeholder'];
            $element->setAttrib('placeholder', $this->getView()->getCibleText($key));
            unset($opt[2]);
        }

        $this->_decoParams['placeholder'] = "";
        $element->setDecorators($opt);
        return $element;
    }

    private function _defineSrc($params, $meta)
    {
        $this->_srcData = array();
        $srcName = $params['src'];
        $srcMethod = '_' . $srcName . 'Src';

        if (!in_array($srcMethod, get_class_methods($this)))
        {
            $oRef = new ReferencesObject();
            $listExists = $oRef->listExists($srcName);
            if ($listExists){
                $this->_srcData = $oRef->getListValues($srcName, null, $this->_addDefault);
            }else{
                $this->_srcData = $this->_object->$srcMethod($this->_addDefault);
            }
            $oRef->setUtilization($srcName, $meta);
        }
        else{
            $this->$srcMethod($meta);
        }
    }

    private function _addShortcut(array $params, array $meta)
    {
        $field = $meta['COLUMN_NAME'] . '_' . $params['src'];
        $fieldId = $meta['COLUMN_NAME'] . '-' . $params['src'];
        $shortcut = new Cible_Form_Element_Html($field, array('value' => '&nbsp;'));
        $shortcut->setDecorators(
            array(
                'ViewHelper',
                array('label', array('placement' => 'prepend')),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => 'dd',
                        'class' => 'shortcut',
                        'id' => $fieldId)
                ),
            )
        );
        if (isset($params['seq']))
            $shortcut->setOrder($params['seq'] + 1);
        if (!$this->_forceNoScript){
            $this->getView()->headScript()->appendFile($this->getView()->locateFile('manageRefValues.js'));
            $this->_addShortCutPartial = true;
        }
//        if (!empty($this->_script))
//            if (!$this->_isXmlHttpRequest)
//        $this->getView()->jQuery()->addOnLoad($this->getView()->partial('partials/jsManageValuesList.phtml'));
//            else
//                $this->getView()->inlineScript()->appendScript($this->getView()->partial('partials/jsManageValuesList.phtml'));

        if (!empty($this->_groupName))
            array_push($this->_grpElements[$this->_groupName], $field);

        $this->addElement($shortcut);
    }

    private function _setDisplayGroup($params)
    {
        if (empty($this->_grpElements[$params['group']]) || empty($this->_groupName))
        {
            $class = '';
            $this->_seq += 10;
            if (isset($params['grpSeq']))
                $seq = $params['grpSeq'] * 10;
            else
                $seq = $this->_seq;

            if (isset($params['grpClass']))
                $class = $params['grpClass'];

            $this->_grpElements[$params['group']] = array(
                'order' => $seq,
                'class' => $class
            );
        }

        $this->_groupName = $params['group'];
    }

    private function _addManageData(array $params, array $meta)
    {
        $field = $meta['COLUMN_NAME'] . '_' . $params['manageData'];
        $fieldId = $meta['COLUMN_NAME'] . '-' . $params['manageData'];
        $shortcut = new Cible_Form_Element_Html($field, array('value' => '&nbsp;'));
        $shortcut->setDecorators(
            array(
                'ViewHelper',
                array('label', array('placement' => 'prepend')),
                array(
                    array('row' => 'HtmlTag'),
                    array(
                        'tag' => $this->_decoratorTag,
                        'class' => 'manageData shortcutC',
                        'id' => $fieldId)
                ),
            )
        );
        if (isset($params['seq']))
            $shortcut->setOrder($params['seq'] + 1);
        if (!$this->_forceNoScript){
            $this->getView()->headScript()->appendFile($this->getView()->locateFile('manageData.js'));
            $this->_addManageDataPartial = true;
        }

        if (!empty($this->_groupName))
            array_push($this->_grpElements[$this->_groupName], $field);

        $this->addElement($shortcut);
    }

}
