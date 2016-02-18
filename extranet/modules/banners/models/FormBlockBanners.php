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
 * @version   $Id: FormBlockBanners.php 214 2011-07-07 14:40:35Z freynolds $id
 */

/**
 * Form to add a new catalog block.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Catalog
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockBanners.php 214 2011-07-07 14:40:35Z freynolds $id
 */
class FormBlockBanners extends Cible_Form_Block
{
    protected $_moduleName = 'banners';

    public function __construct($options = null)
    {
        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        parent::__construct($options);


        /****************************************/
        // PARAMETERS
        /****************************************/

        // select box category (Parameter #1)
        $blockBanners = new Zend_Form_Element_Select('Param1');
        $blockBanners->setLabel(Cible_Translation::getCibleText('banners_image_group_block_page'))
        ->setAttrib('class','largeSelect');

        $langId = $this->getView()->_defaultEditLanguage;

        $oGroup  = new GroupObject();
        $groups  = $oGroup->getAll($langId);

        foreach ($groups as $group){
            $blockBanners->addMultiOption($group['BG_ID'],$group['BG_Name']);
        }

        $oBanner = new BannerFeaturedObject();
        $banners = $oBanner->getAll($langId);
        foreach ($banners as $banner)
        {
            $id = $banner['BF_ID'] . '_f';
            $blockBanners->addMultiOption($id, $banner['BF_Name']);
        }

        // Status
        $autoPlay = new Zend_Form_Element_Checkbox('Param2');
        $autoPlay->setLabel(Cible_Translation::getCibleText('banners_autoPlay_block_page'));
        $autoPlay->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => 'label_checkbox_banniere check_auto_box')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'checkbox_banniere')),
        ));

        // select box category (Parameter #3)
        $blockDelais = new Zend_Form_Element_Text('Param3');
        $blockDelais->setLabel(Cible_Translation::getCibleText('banners_delais_block_page'))
        ->setAttrib('class','largeSelect')->setValue(3);

        // select box category (Parameter #4)
        $blockTransition = new Zend_Form_Element_Text('Param4');
        $blockTransition->setLabel(Cible_Translation::getCibleText('banners_transition_block_page'))
            ->setAttrib('class','largeSelect')
            ->setValue('1000');

        // Status
        $navi = new Zend_Form_Element_Checkbox('Param5');
        $navi->setLabel(Cible_Translation::getCibleText('banners_navigation_block_page'));
        $navi->setDecorators(array(
            'ViewHelper',
            array('label', array('placement' => 'append','class' => 'label_checkbox_banniere')),
            array(array('row' => 'HtmlTag'), array('tag' => 'dd', 'class' => 'checkbox_banniere')),
        ));

        $blockEffect = new Zend_Form_Element_Select('Param6');
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
//            'uncover' => 'uncover',
//            'turnLeft' => 'Tourner à gauche',
//            'turnRight' => 'Tourner à droite'
        );
        $blockEffect->addMultiOptions($effects);

        $this->addElement($blockBanners);
        $this->addElement($blockDelais);
        $this->addElement($blockTransition);
        $this->addElement($navi);
        $this->addElement($autoPlay);
        $this->addElement($blockEffect);

        $this->addDisplayGroup(array('Param1','Param2','Param3','Param4','Param5','Param6','Param999'),'parameters');
        $parameters = $this->getDisplayGroup('parameters');

        $script =<<< EOS
        $('#Param999').change(function(){
            if ($(this).val() == 'index')
            {
                $('#Param2').show();
                $('label[for=Param2]').show();
                $('#Param3').show();
                $('label[for=Param3]').show();
                $('#Param4').show();
                $('label[for=Param4]').show();
                $('#Param5').show();
                $('label[for=Param5]').show();
                $('#Param6').show();
                $('label[for=Param6]').show();
            }
            else
            {
                $('#Param2').hide();
                $('label[for=Param2]').hide();
                $('#Param3').hide();
                $('label[for=Param3]').hide();
                $('#Param4').hide();
                $('label[for=Param4]').hide();
                $('#Param5').hide();
                $('label[for=Param5]').hide();
                $('#Param6').hide();
                $('label[for=Param6]').hide();
            }
        }).change();
EOS;
        $this->getView()->inlineScript()->appendScript($script);

    }
}
