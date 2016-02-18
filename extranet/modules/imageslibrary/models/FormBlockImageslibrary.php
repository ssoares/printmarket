<?php
/**
 * Module Banner
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Banner
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockImageslibrary.php 428 2013-02-10 05:35:21Z ssoares $id
 */

/**
 * Form to add a new catalog block.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockImageslibrary.php 428 2013-02-10 05:35:21Z ssoares $id
 */
class FormBlockImageslibrary extends Cible_Form_Block
{
    protected $_moduleName = 'imageslibrary';

    public function __construct($options = null)
    {
        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        parent::__construct($options);

        // autoplay (Parameter #1)
        $autoPlay = new Zend_Form_Element_Checkbox('Param1');
        $autoPlay->setLabel(Cible_Translation::getCibleText('banners_autoPlay_block_page'));
        $autoPlay->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => 'label_checkbox_banniere check_auto_box')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));

        // select box category (Parameter #3)
        $blockDelais = new Zend_Form_Element_Text('Param2');
        $blockDelais->setLabel(Cible_Translation::getCibleText('banners_delais_block_page'))
        ->setAttrib('class','largeSelect')->setValue(3);

        // select box category (Parameter #4)
        $blockTransition = new Zend_Form_Element_Text('Param3');
        $blockTransition->setLabel(Cible_Translation::getCibleText('banners_transition_block_page'))
            ->setAttrib('class','largeSelect')
            ->setValue('1000');

        // Status
        $navi = new Zend_Form_Element_Checkbox('Param4');
        $navi->setLabel(Cible_Translation::getCibleText('banners_navigation_block_page'));
        $navi->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => '')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        
        // direction
        $direction = new Zend_Form_Element_Checkbox('Param8');
        $direction->setLabel(Cible_Translation::getCibleText('banners_direction_block_page'));
        $direction->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => '')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        
        // prettyphoto
        $prettyphoto = new Zend_Form_Element_Checkbox('Param9');
        $prettyphoto->setLabel(Cible_Translation::getCibleText('banners_prettyphoto_block_page'));
        $prettyphoto->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => '')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'label_after_checkbox')),
        ));
        
        $blockEffect = new Zend_Form_Element_Select('Param5');
        $blockEffect->setLabel(Cible_Translation::getCibleText('banners_effect_block_page'))
        ->setAttrib('class','largeSelect');

        $effects = array(
            'none' =>'Aucun',
            'fade' =>'fading',
            'fadeout' =>'fading out',
            'scrollHorz' =>'slide horizontal',
            'scrollVert' =>'slide vertical',
            'shuffle' =>'shuffle', 
            'tileSlide' =>'tile slide',
            'tileBlind' =>'tile blind',
            'flipHorz' =>'flip horizontal',
            'flipVert' =>'flip vertical',
        );
        $blockEffect->addMultiOptions($effects);

        $this->addElement($blockDelais);
        $this->addElement($blockTransition);
        $this->addElement($navi);
        $this->addElement($autoPlay);
        $this->addElement($blockEffect);
        $this->addElement($direction);
        $this->addElement($prettyphoto);

        // List of keywords
        $keywordsIds = new Zend_Form_Element_Hidden('Param7');
        $keywordsIds->removeDecorator('Label');
        $this->addElement($keywordsIds);

        $field = 'IFK_Param7_album';
        $fieldId = 'IFK_Param7-album';
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
                        'id' => 'Param7_Labels',
                        'class' => 'clearRight left')
                ),
            )
        );
        $this->addElement($listKeywords);
        $this->addDisplayGroup(array('Param1','Param2','Param3','Param4','Param8', 'Param5','Param7','Param9','Param999'),'parameters');
        $parameters = $this->getDisplayGroup('parameters');
        
        $script =<<< EOS
            $('#Param999').change(function(){                
                $('#Param1').show();
                $('label[for=Param1]').show();
                $('label[for=Param1]').css("visibility", "visible");
                $('#Param2').show();
                $('label[for=Param2]').show();
                $('label[for=Param2]').css("visibility", "visible");
                $('#Param3').show();
                $('label[for=Param3]').show();
                $('label[for=Param3]').css("visibility", "visible");
                $('#Param4').show();
                $('label[for=Param4]').show();
                $('label[for=Param4]').css("visibility", "visible");
                $('#Param5').show();
                $('label[for=Param5]').show();
                $('label[for=Param5]').css("visibility", "visible");
                $('#Param6').show();
                $('label[for=Param6]').show();
                $('label[for=Param6]').css("visibility", "visible");                
                $('#Param7').show();
                $('label[for=Param7]').show();
                $('label[for=Param7]').css("visibility", "visible");
                $('#Param8').show();
                $('label[for=Param8]').show();
                $('label[for=Param8]').css("visibility", "visible");
                $('#Param9').show();
                $('label[for=Param9]').show();
                $('label[for=Param9]').css("visibility", "visible");
                $(".shortcut").show();
               
            if ($(this).val() == 'gestion')
            {  
               
                    $('#Param1').hide();
                    $('label[for=Param1]').hide();
                    $('label[for=Param1]').css("visibility", "hidden");
                    $('#Param2').hide();
                    $('label[for=Param2]').hide();
                    $('label[for=Param2]').css("visibility", "hidden");
                    $('#Param3').hide();
                    $('label[for=Param3]').hide();
                    $('label[for=Param3]').css("visibility", "hidden");
                    $('#Param4').hide();
                    $('label[for=Param4]').hide();
                    $('label[for=Param4]').css("visibility", "hidden");
                    $('#Param5').hide();
                    $('label[for=Param5]').hide();
                    $('label[for=Param5]').css("visibility", "hidden");
                    $('#Param6').hide();
                    $('label[for=Param6]').hide();
                    $('label[for=Param6]').css("visibility", "hidden");
                    $('#Param7').hide();
                    $('label[for=Param7]').hide();
                    $('label[for=Param7]').css("visibility", "hidden");
                    $('#Param8').hide();
                    $('label[for=Param8]').hide();
                    $('label[for=Param8]').css("visibility", "hidden");     
                    $('#Param9').hide();
                    $('label[for=Param9]').hide();
                    $('label[for=Param9]').css("visibility", "hidden");  
                    $(".shortcut").hide();
                
            }
        }).change();
EOS;
        $this->getView()->inlineScript()->appendScript($script);

    }

    public function render(\Zend_View_Interface $view = null)
    {
        $keywords = explode(',', $this->getElement('Param7')->getValue());
        $oRef = new ReferencesObject();

        foreach ($keywords as $value)
        {
            $tmpK= $oRef->getValueById($value, Cible_Controller_Action::getDefaultEditLanguage());
            $kData[] = $tmpK['value'];
        }
        $this->getElement('listKeywords')->setValue(implode(', ', $kData));
        echo $this->getView()->partial('partials/activateRefValues.phtml');

        return parent::render($view);
    }
}
