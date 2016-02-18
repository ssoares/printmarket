<?php
/**
 * Module Text
 * Management of the texts.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Text
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormText.php 1832 2015-12-15 18:28:45Z freynolds $
 */

/**
 * Form to manage texts.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Text
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormText.php 1832 2015-12-15 18:28:45Z freynolds $
 */
class FormText extends Cible_Form_Multilingual
{
    public function __construct($options = null)
    {
        $mode = '';
        if (isset($options['mode']))
        {
            $mode = $options['mode'];
            unset($options['mode']);
        }
        parent::__construct($options);
        $baseDir   = $options['baseDir'];
        $pageID    = $options['pageID'];
        $toApprove = $options['toApprove'];

        $this->removeElement('submitSaveClose');
        $this->getElement('submitSave')
            ->setLabel($this->getView()->getCibleText('button_save_draft'));

        // input text for the title of the text online
        $pageTitle = new Zend_Form_Element_Text('PI_PageTitle');
        $pageTitle->setLabel($this->getView()->getCibleText('label_titre_page'))
            ->setRequired(true)
            ->addFilter('StripTags')
            ->addFilter('StringTrim')
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))))
            ->setAttrib('class','stdTextInput');

        $label = $pageTitle->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        $this->addElement($pageTitle);

        $this->addDisplayGroup(
            array('PI_PageTitle'),
            'infoPage');
        $this->getDisplayGroup('infoPage')
            ->setLegend($this->getView()->getCibleText('form_legend_infoPage'))
            ->setAttrib('class', 'infoData bottom-separator');

        $draftTitle = new Zend_Form_Element_Hidden('TD_DraftTitle');
        $draftTitle->removeDecorator('label');

        // tinymce editor for the text of the text online
        $draftText = new Cible_Form_Element_Editor('TD_DraftText', array('mode'=>Cible_Form_Element_Editor::ADVANCED, 'showBgColor' => true));
        $draftText->setLabel($this->getView()->getCibleText('form_label_text_draft'))
            //->setAttrib('class','largeEditor');
            ->setAttrib('class','mediumEditor');
        if ($mode !== 'edit')
            $draftText->setRequired(true)
            ->addValidator('NotEmpty', true, array('messages' => array('isEmpty' => $this->getView()->getCibleText('validation_message_empty_field'))));
        $label = $draftText->getDecorator('Label');
        $label->setOption('class', $this->_labelCSS);

        if ($this->getView()->aclIsAllowed('text','publish',false)){
            if ($toApprove == 1){
                // submit button  (save and return to writing)
                $submitSaveReturnWriting = new Zend_Form_Element_Submit('submit');
                $submitSaveReturnWriting->setLabel($this->getView()->getCibleText('button_save_return_writing'))
                    ->setName('submitSaveReturnWriting')
                    ->setAttrib('id', 'submitSaveReturnWriting')
                    ->setAttrib('class','stdButton')
                    ->removeDecorator('DtDdWrapper')
                    ->setOrder(2);

                $this->addActionButton($submitSaveReturnWriting);
            }
            // submit button  (save and put online)
            $submitSaveOnline = new Zend_Form_Element_Submit('submit');
            $submitSaveOnline->setLabel($this->getView()->getCibleText('button_save_publish'))
                ->setName('submitSaveOnline')
                ->setAttrib('id', 'submitSaveOnline')
                ->setAttrib('class','stdButton')
                ->removeDecorator('DtDdWrapper')
                ->setOrder(3);

            $this->addActionButton($submitSaveOnline);
        }
        else{
            if ($toApprove == 1){
                $this->getElement('submitSave')->setAttrib('disabled','disabled');
            }
            else{
                // submit button  (save and submit text to the reviser)
                $submitSaveSubmit = new Zend_Form_Element_Submit('submit');
                $submitSaveSubmit->setLabel($this->getView()->getCibleText('button_save_submit_auditor'))
                    ->setName('submitSaveSubmit')
                    ->setAttrib('id', 'submitSaveSubmit')
                    ->setAttrib('class','stdButton')
                    ->removeDecorator('DtDdWrapper')
                    ->setOrder(2);

                $this->addActionButton($submitSaveSubmit);
            }

        }

        // Adds all elements to the form
        $this->addElements( array($draftText, $draftTitle));

        $this->addDisplayGroup(
            array('TD_DraftText', 'TD_DraftTitle'),
            'text');
        $this->getDisplayGroup('text')
            ->setLegend($this->getView()->getCibleText('form_legend_blockData'))
            ->setAttrib('class', 'infoData');


        $previewButton = new Zend_Form_Element_Button('PreviewButton');
        $previewButton->setAttrib('onclick', "showPreview('TD_DraftText');");
        $previewButton->setAttrib('class','previewButton');
        $previewButton->setLabel($this->getView()->getCibleText('button_preview_text'));
        $previewButton->setAttrib('onmouseover','this.className=\'previewButtonOver\';');
        $previewButton->setAttrib('onmouseout','this.className=\'previewButton\';');

        $previewButton->setDecorators(array(
            'ViewHelper',
            array(array('row'=>'HtmlTag'),array('tag'=>'dd', 'openOnly'=>true,'class'=>'text-align-right'))
        ));

        $this->addElement($previewButton);

        $compareButton = new Zend_Form_Element_Button('CompareButton');
        $compareButton->setAttrib('onclick', "showCompare('TD_DraftText');");
        $compareButton->setAttrib('class','compareButton');
        $compareButton->setLabel($this->getView()->getCibleText('button_compare_text'));
        $compareButton->setAttrib('onmouseover','this.className=\'compareButtonOver\';');
        $compareButton->setAttrib('onmouseout','this.className=\'compareButton\';');
        $compareButton->setDecorators(array(
            'ViewHelper',
            array(array('row'=>'HtmlTag'),array('tag'=>'dd', 'closeOnly'=>true))
        ));

        $this->addElement($compareButton);
    }
}