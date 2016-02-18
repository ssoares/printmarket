<?php
/**
 * Edith: Cible Framework
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2010 Cible solutions (http://www.ciblesolutions.com)
 * @license
 */


/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a full captcha element
 *
 * @category   Cible
 * @package    Cible_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2010 Cible solutions (http://www.ciblesolutions.com)
 * @license
 */
class Cible_View_Helper_FormAddCaptcha extends Zend_View_Helper_FormElement
{
    protected $_captcha;
    protected $_tagCaptcha = 'dd';
    protected $_tagRefresh = 'dd';
    protected $_captchaOptions = array(
        'captcha' => 'Word',
        'wordLen' => 6,
        'height'  => 100,
        'width'   => 200,
        'timeout' => 600,
        'dotNoiseLevel' => 0,
        'lineNoiseLevel' => 0,
        'font'    => "/captcha/fonts/ARIAL.TTF",
        'imgDir'  => "captcha/tmp"
    );

    public function getCaptcha()
    {
        $this->_captcha = new Zend_Form_Element_Captcha('captcha', array(
            'label' => $this->view->getCibleText('captcha_label'),
            'captcha' => 'Image',
            'helper' => null,
            'captchaOptions' => $this->_captchaOptions,
        ));
        return $this->_captcha;
    }

    public function setCaptcha($captcha)
    {
        $this->_captcha = $captcha;
        return $this;
    }

    /**
     * Class cosntructor. Set the form if defined and other properties.
     *
     * @param Zend_Form $form    The form which we will add address fields.
     * @param array     $options An array of properties to set.
     *
     * @return void
     */
    public function formAddCaptcha(Zend_Form $form = null, array $options = array())
    {
        if (!empty($options['tagRefresh'])){
            $this->_tagRefresh = $options['tagRefresh'];
        }
        if (!empty($options['tagCaptcha'])){
            $this->_tagCaptcha = $options['tagCaptcha'];
        }

        $this->_captchaOptions['imgUrl']  = rtrim($this->view->baseUrl(), '/') ."/captcha/tmp";
        if (isset($options['getCaptcha']) && $options['getCaptcha'])
            return $this->_getCaptchaImage();

        $this->getCaptcha();
        $this->_captcha->setAttrib('class','form-captcha')
        ->setAttrib('tabindex', 50);
        $this->_captcha->addDecorators(array(
            'Label',
            array(array('row'=>'HtmlTag'),array('tag'=>  $this->_tagCaptcha,
                'id'=> 'dd_captcha', 'class' => 'form-captcha'))
        ));

        $form->addElement($this->_captcha);

        $french = array(
            'badCaptcha'    => 'Veuillez saisir la chaîne ci-dessus correctement.'
        );

        $english = array(
            'badCaptcha'    => 'Captcha value is wrong'
        );
//        $espagnol = array(
//            'badCaptcha'    => 'Introduzca los caracteres que aparecen en la imagen.'
//        );

        $translate = new Zend_Translate('array', $french, 'fr');
        $translate->addTranslation($english, 'en');
//        $translate->addTranslation($espagnol, 'es');
        $lang = Cible_FunctionsGeneral::getLanguageSuffix($this->view->languageId);
        $translate->setLocale($lang);
        $form->setTranslator($translate);

        $this->view->jQuery()->enable();

        // Refresh button
        $refresh_captcha = new  Zend_Form_Element_Button('refresh_captcha');
        $refresh_captcha->setLabel($this->view->getCibleText('button_captcha_refresh'))
               ->setAttrib('onclick', "refreshCaptcha('captcha-id')")
               ->setAttrib('class','refresh-captcha')
               ->removeDecorator('Label')
                ->setDecorators(array(
                'ViewHelper',
                array(array('row' => 'HtmlTag'), array('tag' => $this->_tagRefresh)),
            ));

        $form->addElement($refresh_captcha);

        if(isset($options['containerDiv'])){

            $form->setRowDecorator(array($form->getElement("captcha")->getName(), $form->getElement("refresh_captcha")->getName()), "zoneC", array("class" => "col-md-12"));

        }

        $this->view->jQuery()->enable();
        $script = <<< EOS

        function refreshCaptcha(id){
            $.getJSON('{$this->view->baseUrl()}/default/index/captcha-reload',
                function(data){
                    $("#captcha-id").prevAll("img").attr({src : data['url']});
                    $("#"+id).attr({value: data['id']});
            });
        }

EOS;

        $this->view->headScript()->appendScript($script);


    }

    private function _getCaptchaImage()
    {
        return new Zend_Captcha_Image($this->_captchaOptions);
    }

}