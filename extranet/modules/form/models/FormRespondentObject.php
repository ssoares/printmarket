<?php
/**
 * Class FormRespondentObject -
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * Class FormRespondentObject - Manage Respondent data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class FormRespondentObject extends FormRespondent
{
    protected $_dataClass   = 'FormRespondent';
    protected $_dataId      = 'FR_ID';
    protected $_dataColumns = array(
            'FR_FormID'        => 'FR_FormID',
            'FR_ProfilID'      => 'FR_ProfilID',
            'FR_StartDataTime' => 'FR_StartDataTime',
            'FR_EndDataTime'   => 'FR_EndDataTime',
            'FR_Complete'      => 'FR_Complete'
        );

    
}