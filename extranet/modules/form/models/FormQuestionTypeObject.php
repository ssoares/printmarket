<?php
/**
 * Class FromTextObject - Manage texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormTextObject - Manage Texts data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormQuestionTypeObject extends DataObject
{
    protected $_dataClass   = 'FormQuestionType';
    protected $_dataId      = 'FQT_ID';
    protected $_dataColumns = array(
            'FQT_TypeName'  => 'FQT_TypeName',
            'FQT_ImageLink' => 'FQT_ImageLink'
        );

    protected $_indexClass      = 'FormQuestionTypeIndex';
    protected $_indexId         = 'FQTI_QuestionTypeID';
    protected $_indexLanguageId = 'FQTI_LanguageID';
    protected $_indexColumns    = array(
            'FQTI_Title'       => 'FQTI_Title',
            'FQTI_Description' => 'FQTI_Description'
    );

}