<?php
/**
 * Module Requests
 * Management of the products for Logiflex.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockRequests.php 1303 2013-10-25 20:37:48Z ssoares $id
 */

/**
 * Form to add a new catalog block.
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Requests
 * @copyright Copyright (c)2010 Cibles solutions d'affaires
 *            http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: FormBlockRequests.php 1303 2013-10-25 20:37:48Z ssoares $id
 */
class FormBlockRequests extends Cible_Form_Block
{
    protected $_moduleName = 'template';

    public function __construct($options = null)
    {
        $baseDir = $options['baseDir'];
        $pageID = $options['pageID'];

        parent::__construct($options);


        /****************************************/
        // PARAMETERS
        /****************************************/

        // select box category (Parameter #1)
//        $blockCategory = new Zend_Form_Element_Select('Param1');
//        $blockCategory->setLabel(Cible_Translation::getCibleText('catalog_category_block_page'))
//        ->setAttrib('class','largeSelect')
//        ->setOrder(2);
//
//        $this->removeDisplayGroup('parameters');
//
//        $this->addDisplayGroup(array('Param1','Param2','Param999'),'parameters');
//        $parameters = $this->getDisplayGroup('parameters');

         $script =<<< EOS
        $('#Param999').change(function(){
//            if ($(this).val() == 'commandes'){
//                $('#Param1').hide();
//                $('label[for=Param1]').hide();
//                $('#Param2').show();
//                $('label[for=Param2]').show();
//            }
//            else if($(this).val() == 'list'){
//                $('#Param1').show();
//                $('label[for=Param1]').show();
//                $('#Param2').hide();
//                $('label[for=Param2]').hide();
//            }
//            else{
//                $('#Param1').hide();
//                $('label[for=Param1]').hide();
//                $('#Param2').hide();
//                $('label[for=Param2]').hide();
//            }
        }).change();
EOS;
        $this->getView()->inlineScript()->appendScript($script);
    }
}
