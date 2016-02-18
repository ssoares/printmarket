<?php
/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Hidden.php';

class Cible_Form_Element_FrontImagePicker extends Zend_Form_Element_Hidden
{
    private $associatedElement;
    private $pathTmp;
    private $contentID;
    public function __construct($spec, $options = null)
    {
        parent::__construct($spec, $options);
        $this->associatedElement = $options['associatedElement'];
        $this->pathTmp           = $options['pathTmp'];
        $this->contentID         = $options['contentID'];

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
        $defaultImage = $_baseUrl . "/icons/image_non_ disponible.jpg";

        $this->_view->headScript()->appendFile($this->getView()->locateFile('moxman.loader.min.js', 'tinymce/plugins/moxiemanager/js'));
        if (null !== $view) {
            $this->setView($view);
        }

        $session = new Zend_Session_Namespace(SESSIONNAME);
        $_SESSION["moxiemanager.filesystem.rootpath"] = "../../../../../" . $session->currentSite . '/data';

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
            path : '".$this->pathTmp."',
            language:'{$_lang}',
            onupload :  function(info) {
                document.getElementById('".$this->getId()."_tmp').value = info.files[0].meta.thumb_url;
                document.getElementById('".$this->getId()."_original').value = info.files[0].url;
                document.getElementById('".$this->getId()."_preview').src = info.files[0].meta.thumb_url;
                document.getElementById('".$this->getId()."').value = info.files[0].name;

            }});\">[Parcourir]</a>";

        //$content .= "<a href=\"javascript:mcImageManager.browse({fields : '".$this->getId()."', no_host : true});\">[Parcourir]</a>";
        $content .=  "&nbsp;&nbsp;<img class='action_icon' alt='Supprimer' src='".$_baseUrl."/icons/icon-close-16x16.png' onclick='separateImage(\"".$this->associatedElement."\",\"".$defaultImage."\",\"".$this->getId()."\")' />";

        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}