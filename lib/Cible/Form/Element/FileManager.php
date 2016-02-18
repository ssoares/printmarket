<?php
/**
 * Module Catalog
 * Controller for the backend administration of Logiflex.
 *
 * @category  Lib_Cible
 * @package   Lib_Cible_Form_Element
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FileManager.php 1873 2016-02-10 01:55:52Z ssoares $id
 */

/** Zend_Form_Element_Xhtml */
require_once 'Zend/Form/Element/Text.php';

/**
 * Creates an element for the form to manage files.
 *
 * @category  Lib_Cible
 * @package   Lib_Cible_Form_Element
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FileManager.php 1873 2016-02-10 01:55:52Z ssoares $id
 */
class Cible_Form_Element_FileManager extends Zend_Form_Element_Hidden
{
    private $associatedElement;
    private $displayElement;
    private $pathTmp;
    private $contentID;
    private $setInit;
    private $setUpload;
    private $sizeField;
    private $storage;

    /**
     *
     * @param string $spec    Id of the element for html tag.
     * @param array  $options Options to create the element.<br />
     *                        The following ones are mandatory:<br />
     *                         <p>- associatedElement: Element containing the
     *                          file path. It's the form element to be saved into db.</p>
     *                         <p>- displayElement: The field name to display the file name.</p>
     *                         <p>- pathTmp: Path to initialize the browser.</p>
     *                         <p>- setInit: Set to true for the first element.
     * This is not usefull if there is more than one element.</p>
     *                         <p>- contentID: OPTIONAL</p>
     */
    public function __construct($spec, $options = null)
    {
        if(isset($options['associatedElement']))
            $this->associatedElement = $options['associatedElement'] . "-";
        else
            $this->associatedElement = "";
        $this->displayElement    = $options['displayElement'];
        $this->pathTmp           = $options['pathTmp'];
        $this->contentID         = $options['contentID'];
        $this->setUpload         = $options['setUpload'];
        $this->sizeField         = $options['sizeField'];
        $this->storage           = $options['storage'];
        parent::__construct($spec, null);

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
        $this->_view->headScript()->appendFile($this->getView()->locateFile('moxman.loader.min.js', 'tinymce/plugins/moxiemanager/js', 'back'));
        if (null !== $view) {
            $this->setView($view);
        }

        $fileContentFolder = '';
        $session = new Zend_Session_Namespace(SESSIONNAME);
        if (!empty($session->currentSite))
            $fileContentFolder .= $session->currentSite;

        $fileContentFolder .= '/data/files/';
        $dir = Zend_Registry::get('fullDocumentRoot');
        if (ISDEV){
            $dir .= Zend_Registry::get('www_root');
        }
        if (!empty($this->storage))
        {
            $fileContentFolder = $this->storage;
            if (!is_dir($dir . $fileContentFolder))
                mkdir ($dir . $fileContentFolder);
        }

        $session = new Zend_Session_Namespace(SESSIONNAME);
        $_SESSION["moxiemanager.filesystem.rootpath"] = "../../../../../" . $session->currentSite . '/data';
        $dir .=  $session->currentSite .'/';
        $dir .= trim($this->pathTmp, './../.');
        if (!is_dir($dir)){
            mkdir ($dir);
        }
        $content = '';
        $moxiConfig = "{\n";
        $moxiConfig .= "fields : '" . $this->getId() . "',
                        path : '" . $this->pathTmp . "',
                        language : '{$_lang}',
                        extensions : 'txt, docx, doc, zip, pdf, xls, xlsx',
                        relative_urls : true,
                        remove_script_host : true,
                        document_base_url : 'http://{$_SERVER['HTTP_HOST']}/',
                        disabled_tools : 'createdir,createdoc,cut,copy',";
        $api = 'browse';
        $fonction = 'oninsert';
        if ($this->setUpload)
        {
            $api = 'upload';
            $fonction = 'onupload';
        }
        $moxiConfig .= $fonction . ": function (data) {\n
                        getElementById('" . $this->getId() . "').value = data.files[0].url;\n
                        document.getElementById('" . $this->associatedElement . $this->displayElement . "').value = data.files[0].name;\n
                        document.getElementById('" . $this->associatedElement . $this->sizeField . "').value = data.files[0].size;\n
                        }\n";
        $moxiConfig .= "}\n";
        $content .= "<script type='text/javascript'>\n";
        $content .= "function separateFile_" . $this->displayElement . "(){\n";
        $content .= "document.getElementById('" . $this->getId() . "').value = '';\n";
        $content .= "$('#" . $this->associatedElement . $this->displayElement . "').each(function(){\n$(this).val('')\n});\n";
        $content .= "}\n";
        $content .= "\n";
        $content .= "</script>\n";
        $content .= "<input id='" . $this->associatedElement . $this->displayElement . "' class='stdTextInput' name='" . $this->associatedElement . "[" . $this->displayElement . "]" . "' value='' />";


        $content .= "<a href=\"javascript:;\" onclick=\"moxman.".$api."(" . $moxiConfig . ");\">[".$this->getView()->getCibleText('form_label_parcourir')."]</a>";
        if (SESSIONNAME == 'application'){
            $content .=  "&nbsp;&nbsp;<span class='action_icon' onclick='separateFile_". $this->displayElement ."()''>".$this->getView()->getCibleText('delete')."</span>";
        }else{
            $content .=  "&nbsp;&nbsp;<img class='action_icon' alt='Supprimer' title='Supprimer' src='".$this->getView()->locateFile('icon-close-16x16.png', null, 'back')."'
            onclick='separateFile_". $this->displayElement ."()'' />";
        }

        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}