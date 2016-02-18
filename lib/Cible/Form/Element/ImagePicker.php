<?php
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Hidden.php';

class Cible_Form_Element_ImagePicker extends Zend_Form_Element_Hidden
{
    private $associatedElement;
    private $pathTmp;
    private $contentID;
    private $moxFileRootPath = '../../../../../';
    private $moxSrc = 'tinymce/plugins/moxiemanager/js';
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->associatedElement = $options['associatedElement'];
        $this->pathTmp           = $options['pathTmp'];
        $this->contentID         = $options['contentID'];
        $this->showCrop          = isset($options['showCrop'])?$options['showCrop']:false;
        if (SESSIONNAME == 'application'){
//            $this->moxFileRootPath = '../../../../';
        }
    }
    /**
     * Render form element
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {
        $_lang = Cible_FunctionsGeneral::getLanguageSuffix(Zend_Registry::get('languageID'));

        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $this->_view->headScript()->appendFile($this->getView()->locateFile('moxman.loader.min.js', $this->moxSrc, 'back'));
        if (null !== $view) {
            $this->setView($view);
        }

        $session = new Zend_Session_Namespace(SESSIONNAME);
        $_SESSION["moxiemanager.filesystem.rootpath"] = $this->moxFileRootPath
                . $session->currentSite . '/data';
        if (SESSIONNAME == 'application'){
            $_SESSION["moxiemanager.upload.maxsize"] = '2M';
            $_baseUrl .= '/extranet';
        }else{
            unset($_SESSION["moxiemanager.upload.maxsize"]);
        }
        $defaultImage = $_baseUrl . "/icons/image_non_ disponible.jpg";

        $content = '';
        $content .= '
            <script>
                function separateImage($associatedElement, $defaultImage, $imagePicker){
                        if (!document.getElementById($associatedElement))
                            $associatedElement = $imagePicker + "_preview";

                    document.getElementById($associatedElement).src = $defaultImage;
                    document.getElementById($imagePicker).value = "";
                }
            </script>';
        $content .= "<a href=\"javascript:moxman.upload({
            fields : '".$this->getId()."',
            relative_urls : true,
            remove_script_host : true,
            insert:false,
            upload_auto_close : true,
            path : '".$this->pathTmp."',
            language:'{$_lang}',
            onupload :  function(info) {
                document.getElementById('".$this->getId()."_tmp').value = info.files[0].meta.thumb_url;
                document.getElementById('".$this->getId()."_original').value = info.files[0].url;
                document.getElementById('".$this->getId()."_preview').src = info.files[0].meta.thumb_url;
                document.getElementById('".$this->getId()."').value = info.files[0].name;

            }});\" class=\"img_action\">[{$this->_view->getCibleText('form_label_parcourir')}]</a>";

        //$content .= "<a href=\"javascript:mcImageManager.browse({fields : '".$this->getId()."', no_host : true});\">{$this->_view->getCibleText('form_label_parcourir')}</a>";
        $content .=  "&nbsp;&nbsp;<img class='img_action' title='{$this->_view->getCibleText('button_delete')}' alt='{$this->_view->getCibleText('button_delete')}' src='".$_baseUrl."/icons/icon-close-16x16.png' onclick='separateImage(\"".$this->associatedElement."\",\"".$defaultImage."\",\"".$this->getId()."\")' />";
        if ($this->showCrop)
        $content .=  "&nbsp;&nbsp;<img class='img_action trigger_crop' title='{$this->_view->getCibleText('crop')}' alt='{$this->_view->getCibleText('crop')}' src='".$_baseUrl."/icons/icon-crop-16x16.png'/>";

        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}