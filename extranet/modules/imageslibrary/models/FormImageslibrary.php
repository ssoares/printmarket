<?php

/**
 * Module Imageslibrary
 * Management of the featured elements.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Imageslibrary
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormImageslibrary.php 342 2013-01-29 03:43:06Z ssoares $
 */

/**
 * Form to manage images.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormImageslibrary.php 342 2013-01-29 03:43:06Z ssoares $id
 */
class FormImageslibrary extends Cible_Form
{

    public function __construct($options = null)
    {
        $this->_addSubmitSaveClose = true;
        if (!empty($options['object']))
        {
            $this->_object = $options['object'];
            unset($options['object']);
        }

        if (!isset($options['empty']))
            $this->_disabledDefaultActions= true;

        if (isset($options['source'])){
            $source = $options['source'];
        }
        parent::__construct($options);
        if (!isset($options['empty']) || !$options['empty'])
        {
            $imageSrc = $options['imageSrc'];

            if (isset($options['imgField']))
                $imgField = $options['imgField'];
            $subFormId = '';
            if (isset($options['subFormId']))
            {
                $subFormId = $options['subFormId'];
                unset($options['subFormId']);
            }

            $dataId = $options['dataId'];
            $isNewImage = $options['isNewImage'];
            $moduleName = $options['moduleName'];

            if ($dataId == '')
                $pathTmp = $this->_imagesFolder . "/tmp";
            else
                $pathTmp = $this->_imagesFolder . "/" . $dataId . "/tmp";

            $id = new Zend_Form_Element_Hidden('IL_ID');
            $id->removeDecorator('Label');
            $this->addElement($id);
            $newImage = new Zend_Form_Element_Hidden('isNewImage', array('value' => $isNewImage));
            $newImage->removeDecorator('Label');
            $this->addElement($newImage);

            // Image for the category
            $imageTmp = new Zend_Form_Element_Hidden($imgField . '_tmp');
            $imageTmp->removeDecorator('Label');
            $this->addElement($imageTmp);

            $imageOrg = new Zend_Form_Element_Hidden($imgField . '_original');
            $imageOrg->removeDecorator('Label');
            $this->addElement($imageOrg);


            $imageView = new Zend_Form_Element_Image(
                    $imgField . '_preview',
                    array('onclick' => 'return false;')
            );
            $imageView->setImage($imageSrc);
            $this->addElement($imageView);

            $imagePicker = new Cible_Form_Element_ImagePicker(
                    $imgField,
                    array(
                        'associatedElement' => $imgField . '_preview',
                        'pathTmp' => $pathTmp,
                        'contentID' => $dataId
                ));

            $this->addElement($imagePicker);

            // Sequence
            $sequence = new Zend_Form_Element_Text('IL_Seq');
            $sequence->setLabel(
                    $this->getView()->getCibleText('form_label_IF_Seq'))
                ->setRequired(false)
                ->addFilter('StripTags')
                ->addFilter('StringTrim')
                ->addValidator(
                    'NotEmpty', true, array(
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
                            array('row' => 'HtmlTag'),
                            array(
                                'tag' => 'dd',
                                'class' => 'form_title_inline',
                                'id' => 'title')
                        ),
                    )
                )
                ->setAttrib('class', 'smallTextInput');

            $this->addElement($sequence);

            $left = array(
                'IL_ID',
                'isNewImage',
                $imgField . '_tmp',
                $imgField . '_original',
                $imgField . '_preview',
                $imgField,
                'IL_Seq',
                'IL_Video');
            $this->addDisplayGroup($left, 'groupLeft');


            // Description for each language
            $languages = Cible_FunctionsGeneral::getAllLanguage(true);
            $right = array();

            //Title
            foreach ($languages as $lang){
                $labelCSS = 'formLabelLanguageCssColor_' . $lang['L_ID'];
                $label1 = new Zend_Form_Element_Text('ILI_Label1_' . $lang['L_ID']);
                $label1->setLabel($this->getView()->getCibleText('ImageLibrairyLabel1_' . $lang['L_Suffix']))
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag' => 'dd',
                                    'class' => 'form_title_inline marginTop30',
                                    'id' => 'title')
                            ),
                        )
                    )
                    ->setAttrib('class', 'largeTextInput');

                $label = $label1->getDecorator('Label');
                $label->setOption('class', $labelCSS);

                $this->addElement($label1);
                array_push($right, 'ILI_Label1_' . $lang['L_ID']);

            }

            foreach ($languages as $lang){
                $labelCSS = 'formLabelLanguageCssColor_' . $lang['L_ID'];
                $label2 = new Zend_Form_Element_Textarea('ILI_Label2_' . $lang['L_ID']);
                $label2->setAttrib('class', 'stdTextInput')
                ->setAttrib('cols', '30')
                ->setAttrib('rows', '2')
                ->setAttrib('maxlength', '50');
                $label2->setLabel(
                        $this->getView()->getCibleText('ImageLibrairyLabel2_' . $lang['L_Suffix']))
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag' => 'dd',
                                    'class' => 'form_title_inline marginTop30',
                                    'id' => 'title')
                            ),
                        )
                    )
                    ->setAttrib('class', 'largeTextInput');

                $label = $label2->getDecorator('Label');
                $label->setOption('class', $labelCSS);

                $this->addElement($label2);
                array_push($right, 'ILI_Label2_' . $lang['L_ID']);
            }

            /*
            foreach ($languages as $lang){
                $labelCSS = 'formLabelLanguageCssColor_' . $lang['L_ID'];
                $link = new Zend_Form_Element_Text('ILI_Link_' . $lang['L_ID']);
                $link->setAttrib('class', 'stdTextInput')
                ->setAttrib('cols', '30')
                ->setAttrib('rows', '2')
                ->setAttrib('maxlength', '50');
                $link->setLabel(
                        $this->getView()->getCibleText('ImageLibrairyLink_' . $lang['L_Suffix']))
                    ->addFilter('StripTags')
                    ->addFilter('StringTrim')
                    ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag' => 'dd',
                                    'class' => 'form_title_inline marginTop30',
                                    'id' => 'title')
                            ),
                        )
                    )
                    ->setAttrib('class', 'largeTextInput');

                $label = $link->getDecorator('Label');
                $label->setOption('class', $labelCSS);

                $this->addElement($link);
                array_push($right, 'ILI_Link_' . $lang['L_ID']);
            }*/

            foreach ($languages as $lang)
            {
                $labelCSS = 'formLabelLanguageCssColor_' . $lang['L_ID'];
                $descr = new Cible_Form_Element_Editor('ILI_Description_' . $lang['L_ID'],
                    array('mode' => Cible_Form_Element_Editor::ADVANCED, 'subFormID' => $subFormId)
                    );
                $descr->setLabel(
                        $this->getView()->getCibleText('form_label_IFI_Description_' . $lang['L_Suffix']))
                    ->setAttrib('cols', '80')
                    ->setAttrib('rows', '10')
                    ->setDecorators(
                        array(
                            'ViewHelper',
                            array('label', array('placement' => 'prepend')),
                            array(
                                array('row' => 'HtmlTag'),
                                array(
                                    'tag' => 'dd',
                                    'class' => 'form_title_inline marginTop30',
                                    'id' => 'title')
                            ),
                        )
                    )
                    ->setAttrib('class', 'largeTextInput');

                $label = $descr->getDecorator('Label');
                $label->setOption('class', $labelCSS);

                $this->addElement($descr);
                array_push($right, 'ILI_Description_' . $lang['L_ID']);
            }

            // List of keywords
            $keywordsIds = new Zend_Form_Element_Hidden('ILK_RefId');
            $keywordsIds->removeDecorator('Label');
            $this->addElement($keywordsIds);
            $field = 'ILK_RefId_' . $source;
            $fieldId = 'ILK_RefId-' . $source;
            $shortcut = new Cible_Form_Element_Html($field, array('value' => $this->getView()->getCibleText('form_label_modify_keywordsList')));
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
            $this->addElement($shortcut);
            array_push($right, 'ILK_RefId');
            array_push($right, 'ILK_RefId_' . $source);
            // To display the list
            $listKeywords = new Cible_Form_Element_Html(
                    'listKeywords',
                    array(
                        'label' => $this->getView()->getCibleText('form_label_listKeywords')
                    )
            );
            $listKeywords->setDecorators(
                array(
                    'ViewHelper',
                    array('label', array('placement' => 'prepend')),
                    array(
                        array('row' => 'HtmlTag'),
                        array(
                            'tag' => 'dd',
                            'id' => 'RefId_Labels',
                            'class' => 'clearRight left')
                    ),
                )
            );
            $this->addElement($listKeywords);
            array_push($right, 'listKeywords');
            $meta['TABLE_NAME'] = 'Imageslibrary_Keywords';
            $meta['COLUMN_NAME'] = 'ILK_RefId';
            $oRef = new ReferencesObject();
            $oRef->setUtilization('keywords', $meta);
            $this->addDisplayGroup($right, 'groupRight');

            $this->getDisplayGroup('groupLeft')->removeDecorator('DtDdWrapper');
            $this->getDisplayGroup('groupRight')->removeDecorator('DtDdWrapper');
        }
    }
}