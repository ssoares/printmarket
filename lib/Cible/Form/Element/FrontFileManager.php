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
 * @version   $Id: FrontFileManager.php 59 2012-10-29 22:36:37Z ssoares $id
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
 * @version   $Id: FrontFileManager.php 59 2012-10-29 22:36:37Z ssoares $id
 */
class Cible_Form_Element_FrontFileManager extends Zend_Form_Element_Hidden
{
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
        parent::__construct($spec, $options);

        if(isset($options['associatedElement']))
            $this->associatedElement = $options['associatedElement'] . "-";
        else
            $this->associatedElement = "";
        $this->displayElement    = $options['displayElement'];
        $this->pathTmp           = $options['pathTmp'];
        $this->contentID         = $options['contentID'];
        $this->setInit           = $options['setInit'];
    }
    /**
     * Render form element
     *
     * @param  Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
    {

        //$_id = $this->getId();
        $_lang = Zend_Registry::get('languageID') == 1 ? 'fr' : 'en';

        $_baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();

        $this->_view->headScript()->appendFile($this->_view->locateFile('tiny_mce/plugins/filemanager/js/mcfilemanager.js'));
        if (null !== $view) {
            $this->setView($view);
        }

        $pathV = "";
        if($this->pathTmp!=""){
            $pathV = "path : '{0}/videos',";
        }

        $content = '';

        $content .= "<script type='text/javascript'>\n";

        $name = $this->displayElement;
        if (!empty($this->associatedElement))
            $name = $this->associatedElement . "[" . $this->displayElement . "]";
         //'
        if ($this->setInit)
        {
            $content .= "mcFileManager.init({\n";
            $content .= "fields : '" . $this->getId() . "',
                            path : '" . $this->pathTmp . "',
                            no_host: true,
                            relative_urls : true,
                            remove_script_host : true,
                            document_base_url : 'http://{$_SERVER['HTTP_HOST']}/',
                            disabled_tools : 'createdir,createdoc,cut,copy'";
            $content .= "});\n";
        }
        $content .= "function separateFile_" . $this->displayElement . "(){\n";
        $content .= "document.getElementById('" . $this->getId() . "').value = '';\n";
        $content .= "$('#" . $this->associatedElement . $this->displayElement . "').each(function(){\n$(this).val('')\n});\n";
        $content .= "}\n";
        $content .= "function customInsert_" . $this->displayElement . " (data) {\n";
        $content .= "console.log(data)\n";
        $content .= "document.getElementById('" . $this->getId() . "').value = data.files[0].url;\n";
        $content .= "document.getElementById('" . $this->associatedElement . $this->displayElement . "').value = data.files[0].name;\n";
        $content .= "}\n";
        $content .= "</script>\n";
        $content .= "<input id='" . $this->associatedElement . $this->displayElement . "' class='stdTextInput' name='" . $name . "' value='' />";
        $content .= "<a href=\"javascript:;\"
            onclick=\"mcFileManager.upload({
                path: '{0}/',
                document_base_url : 'http://{$_SERVER['HTTP_HOST']}/',
                relative_urls : true,
                onupload: customInsert_" . $this->displayElement . "});\">
                {$this->_view->getCibleText('Parcourir')}
                    </a>";
//        $content .=  "&nbsp;&nbsp;<img class='action_icon' title='Supprimer' src='".$_baseUrl."/icons/button_cancel_16x16.png'
//            onclick='separateFile_". $this->displayElement ."()'' />";

        foreach ($this->getDecorators() as $decorator) {
            $decorator->setElement($this);
            $content = $decorator->render($content);
        }

        return $content;
    }
}
?>
