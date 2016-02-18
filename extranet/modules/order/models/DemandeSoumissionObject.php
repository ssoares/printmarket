<?php
/**
 * Cible Solutions - Vêtements SP
 * QuoteRequest management.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: DemandeSoumissionObject.php 1367 2013-12-27 04:19:31Z ssoares $
 */

/**
 * Manage data in database for the quote request.
 *
 * @category  Extranet_Modules
 * @package   Extranet_Modules_Order
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class DemandeSoumissionObject extends DataObject
{
    protected $_dataClass   = 'DemandeSoumissionData';
    protected $_dataId      = 'DS_ID';
    protected $_dataColumns = array(
        'DS_ID'              => 'DS_ID',
        'DS_DateHeure'       => 'DS_DateHeure',
        'DS_Status'          => 'DS_Status',
        'DS_ClientProfileID' => 'DS_ClientProfileID',
        'DS_DetaillantID'    => 'DS_DetaillantID',
        'DS_SalutationID'    => 'DS_SalutationID',
        'DS_Nom'             => 'DS_Nom',
        'DS_Prenom'          => 'DS_Prenom',
        'DS_Courriel'        => 'DS_Courriel',
        'DS_MotDePasse'      => 'DS_MotDePasse',
        'DS_Adresse1'        => 'DS_Adresse1',
        'DS_Adresse2'        => 'DS_Adresse2',
        'DS_Ville'           => 'DS_Ville',
        'DS_Province'        => 'DS_Province',
        'DS_Pays'            => 'DS_Pays',
        'DS_CodePostal'      => 'DS_CodePostal',
        'DS_MotDePasse'      => 'DS_MotDePasse',
        'DS_NoCompteSP'      => 'DS_NoCompteSP',
        'DS_IsDetaillant'    => 'DS_IsDetaillant',
        'DS_Langue'          => 'DS_Langue',
        'DS_Notes'           => 'DS_Notes'
      );

    protected $_indexClass      = '';
    protected $_indexId         = '';
    protected $_indexLanguageId = '';
    protected $_indexColumns    = array();

    protected $_query;

}