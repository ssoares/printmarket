<?php

/**
 * LICENSE
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 */

/**
 * Description of GPS
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_ExternalProcess_Gps extends Cible_ExternalProcess
{
    const SPACE = ' ';
//Nom d'usager pour accès à GPS :  5A691HX01
//Mot de passe : 29IWS02D42
//
    /**
     * Request to a credit card management
     */
    const REQUEST = 'request';
    /**
     * Request to a bank account management not credit card
     */
    const REQUESTBA = 'requestba';
    const RESPONSE = 'response';
    const REJECTS = 'rejects';
    const PROCESS = 'gps';
    const CREATE = 'create';
    const UPDATE = 'update';
    const REPORTS = 'reports';

    /**
     * apiId is the company number needed for GPS authentication.
     * It is the <strong>cie<strong/> parameter.
     * @var string
     */
    protected $_apiId = '';
    protected $_apiVersion = '04';
    protected $_data = array();
    protected $_requestType = '';
    protected $_urlPage = '';
    protected $_pages = array(
        'request' =>'inscription.php',
        'requestba' =>'inscription-ca.php',
        'reports' => 'rapports.php',
        'upload' => 'upload.php',
        );
    protected $_token = '';
    protected $_messages = array(
        1 => 'Transaction GPS started. Donation : ##INVOICENUM##'
    );
    protected $_keys = array("cie", 'clientno', 'action', 'redirectUrl',
        'language', 'fingerprint');

    protected $_errors = array(
        'request' => array(
            1 => ' Pas d’erreur, la soumission c’est bien effectuée.',
            0 => ' Pas d’erreur, l’Utilisateur a choisi d’annuler la saisie.',
            -1 => ' Erreur interne, contactez le support E-ACCEPT avec les informations de retour et le numéro d’erreur pour plus d’informations.',
            -20 => ' le champ fingerprint est absent de la Requête',
            -21 => ' le champ fingerprint de la Requête ne correspond pas aux données',
            -30 => ' le champ cie est absent de la Requête',
            -31 => ' le champ cie de la Requête ne correspond pas à un ID de Compagnie existante',
            -32 => ' la compagnie référencée par le champ cie de la Requête n’a pas le droit d’accès à ce formulaire',
            -40 => ' le champ clientno est absent de la Requête',
            -41 => ' le champ clientno est invalide',
            -50 => ' le champ action est absent de la Requête',
            -51 => ' le champ action de la Requête ne contient ni « create » ni « update » (sans guillemets en minuscules non-accentuées)',
            -52 => ' le champ action contient « update » alors que le Client identifié par le champ clientno de la Requête n’existe pas',
            -53 => ' le champ action contient « create » alors que le Client identifié par le champ clientno de la Requête existe déjà',
            -60 => ' l’URL de retour est absente ou vide',
        ),
        'requestba' => array(
            1 => ' Pas d’erreur, la soumission c’est bien effectuée.',
            0 => ' Pas d’erreur, l’Utilisateur a choisi d’annuler la saisie.',
            -1 => ' Erreur interne, contactez le support E-ACCEPT avec les informations de retour et le numéro d’erreur pour plus d’informations.',
            -20 => ' le champ fingerprint est absent de la Requête',
            -21 => ' le champ fingerprint de la Requête ne correspond pas aux données',
            -30 => ' le champ cie est absent de la Requête',
            -31 => ' le champ cie de la Requête ne correspond pas à un ID de Compagnie existante',
            -32 => ' la compagnie référencée par le champ cie de la Requête n’a pas le droit d’accès à ce formulaire',
            -40 => ' le champ clientno est absent de la Requête',
            -41 => ' le champ clientno est invalide',
            -50 => ' le champ action est absent de la Requête',
            -51 => ' le champ action de la Requête ne contient ni « create » ni « update » (sans guillemets en minuscules non-accentuées)',
            -52 => ' le champ action contient « update » alors que le Client identifié par le champ clientno de la Requête n’existe pas',
            -53 => ' le champ action contient « create » alors que le Client identifié par le champ clientno de la Requête existe déjà',
            -60 => ' l’URL de retour est absente ou vide',
        ),
        'upload' => array(
            '1' => 'Succès.',
            '-21' => 'Format de fichier invalide.',
            '-22' => 'Date de création invalide.',
            '-23' => 'La date de création ne peut être dans le futur.',
            '-24' => 'La date de création ne peut être plus ancienne que 7 jours.',
            '-25' => 'Le numéro Perceptech est invalide ou vous n’avez pas les droits d’accéder à cette compagnie.',
            '-26' => 'La version du fichier est inconnue',
            '-43' => "Incapable d'importer le client #",
            '-41' => 'Format de fichier invalide.',
            '-42' => 'Les totaux de la ligne Z ne concordent pas',
            '-44' => 'Connexion perdue',
            '-81' => 'Incapable de sauvegarder le fichier',
            '-90' => 'Impossible de lire le fichier ou fichier non inclus',
            '-100' => 'Fichier déjà traité',
            '-101' => 'Communiquer avec Perceptech inc',
            '-102' => 'Communiquer avec Perceptech inc',
            '-103' => 'Problème avec votre fichier',
        ),
        'reports' => array()
    );

    protected $_totalC = 0;
    protected $_totalD = 0;
    protected $_totalU = 0;
    protected $_totalV = 0;
    protected $_nbLinesC = 0;
    protected $_nbLinesD = 0;
    protected $_nbLinesU = 0;
    protected $_nbLinesV = 0;
    protected $_fileLenght = 3000;
    protected $_fileIndex = 1;

    protected $_user = '';
    protected $_pwd = '';

    public function getUser()
    {
        return $this->_user;
    }

    public function getPwd()
    {
        return $this->_pwd;
    }

    public function setUser($user = '')
    {
        if (empty($user)){
            $action = $this->_action;
            $process = self::PROCESS;
            $this->_user = $this->_config->services->$process->$action->user;
        }else{
            $this->_user = $user;
        }
        return $this;
    }

    public function setPwd($pwd = '')
    {
        if (empty($pwd)){
            $action = $this->_action;
            $process = self::PROCESS;
            $this->_pwd = $this->_config->services->$process->$action->pwd;
        }else{
            $this->_pwd = $pwd;
        }
        return $this;
    }

    public function getCodesErrors(){
        if (!empty($this->_requestType)
            && isset($this->_errors[$this->_requestType])){
            $errors = $this->_errors[$this->_requestType];
        }else{
            $errors = $this->_errors;
        }
        return $errors;
    }

    public function setCodesErrors($errors)    {
        $this->_errors = $errors;
        return $this;
    }

    public function getToken(){
        return $this->_token;
    }

    public function setToken($token){
        $this->_token = $token;
        return $this;
    }

    public function getData(){
        return $this->_data;
    }

    public function setData($data, $action = self::REQUEST)
    {
        if (empty($this->_action)){
            $this->_action = self::CREATE;
        }
        switch($action)
        {
            case self::REJECTS:
//                $this->_keys = array('cie', 'rapport_type', 'format', 'de');
//                $this->_data = array($this->_apiId, 900, 'CSV', date('Y-m-d'));
                $this->_keys = array('cie', 'rapport_type', 'format', 'de', 'a');
                $this->_data = array($this->_apiId, 900, 'CSV', '2015-03-19', '2015-03-23');
//                rapports.php?cie=P123456789&format=CSV&rapport_type=900&de=2015-03-19&a=2015-03-23
                break;
            case self::RESPONSE:
                $oDonTrans = new DonorsAccountObject();
                $cols = $oDonTrans->getCryptoCols();
                foreach($data as $key => $value){
                    if (in_array($key, $cols)){
                        $this->_data[$key] = $value;
                    }
                }
                break;
            default:
                $this->_data = array($this->_apiId, $this->_token,
                    $this->_action, $this->_redirectUrl,$data['language']);
                break;
        }
        return $this;
    }
    public function __construct($config){
        parent::__construct($config);
        $this->setTimestamp();
        $process = self::PROCESS;
        $this->_apiId = $this->_config->services->$process->id;
        $this->_key = $this->sanitizeKey($this->_config->services->$process->key);
        $this->_urlPage = $this->_config->services->$process->url;
    }
    public function getRequestType(){
        return $this->_requestType;
    }

    public function setRequestType($requestType)
    {
        switch($requestType)
        {
            case 18:
                $this->_requestType = self::REQUESTBA;
                break;
            case 17:
                $this->_requestType = self::REQUEST;
                break;

            default:
                $this->_requestType = $requestType;
                break;
        }
        return $this;
    }

    public function getUrlPage(){
        return $this->_urlPage;
    }
    public function resetUrlPage(){
        $process = self::PROCESS;
        $this->_urlPage = $this->_config->services->$process->url;
        return $this;
    }
    public function setUrlPage($urlPage = '')
    {
        if (!empty($urlPage)){
            $this->_urlPage .= $urlPage;
        }else{
            $this->_urlPage .= $this->_pages[$this->_requestType];
        }
        return $this;
    }

    public function getExtReqUrl()
    {
        $this->_urlPage .= '?';
        if (!empty($this->_fingerPrint)){
            $this->_data[] = $this->_fingerPrint;
        }
        if ($this->_supportRequired){
//            $this->_keys[] = 'supportRequired';
//            $this->_data[] = $this->_supportRequired;
        }
        $string = http_build_query(array_combine($this->_keys, $this->_data));

        return $this->_urlPage .= $string;
    }

    /**
     * According to response status get the e-Accept matching code.
    * -1 à -19 : Erreur interne, contactez le support E-ACCEPT avec les informations de retour et le
    * numéro d’erreur pour plus d’informations.
    * -20 : le champ fingerprint est absent de la Requête
    * -21 : le champ fingerprint de la Requête ne correspond pas aux données
    * -30 : le champ cie est absent de la Requête
    * -31 : le champ cie de la Requête ne correspond pas à un ID de Compagnie existante
    * -32 : la compagnie référencée par le champ cie de la Requête n’a pas le droit d’accès à ce
    * formulaire
    * -40 : le champ clientno est absent de la Requête
    * -41 : le champ clientno est invalide
    * -50 : le champ action est absent de la Requête
    * -51 : le champ action de la Requête ne contient ni « create » ni « update » (sans guillemets en
    * minuscules non-accentuées)
    * -52 : le champ action contient « update » alors que le Client identifié par le champ clientno de
    * la Requête n’existe pas
    * -53 : le champ action contient « create » alors que le Client identifié par le champ clientno de
    * la Requête existe déjà
    * -60 : l’URL de retour est absente ou vide
    * @param int $status response satus from GPS to convert.
    * @return string
    */
    public function getMatchingCode($status)
    {
        switch($status)
        {
            case 0 :
                $matchingCode = 'C';
                break;
            case ($status < 0 && $status >-20):
                $matchingCode = 'E';
                break;
            case ($status < -19):
                $matchingCode = 'I';
                break;

            default:
                $matchingCode = 'A';
                break;
        }
        return $matchingCode;
    }

    public function buildLine($lineType = '')
    {
        switch($lineType)
        {
            case 'A':
                $line = $this->_getLineA();
                break;
            case 'Z':
                $line = $this->_getLineZ();
                break;

            default:
                break;
        }
        return $line;
    }

    public function buildTransactionsData(array $transactions)
    {
        $count = 1;
        $tmp[] = $this->buildLine('A');
        foreach($transactions as $key => $values)
        {
            $data = array();
            switch($values['DO_Mode'])
            {
                case 18:
                    $index = 'D';
                    ++$this->_nbLinesD;
                    $this->_totalD += $values['RT_Amount'] * 100;
                    $line = $this->_getLineCD($values);
                    break;
                default:
                    $index= 'V';
                    ++$this->_nbLinesV;
                    $this->_totalV += $values['RT_Amount'] * 100;
                    $line = $this->_getLineUV($values);
                    break;
            }
            $data[] = $index;
            $data[] = $this->_getFirstPart($values);
            $data[] = $line;
            $data[] = $this->_getLastPart($values);
            $transLine = implode(self::SPACE, $data);
            $tmp[] = $transLine;
            unset($transactions[$key]);
            if (($count > 1 || $count == $this->_fileLenght)
                && $count % $this->_fileLenght == 0 && !empty($transactions)){
                $tmp[] = $this->buildLine('Z');
                $lines[$this->_fileIndex] = $tmp;
                $this->_fileIndex++;
                $this->_totalD = $this->_nbLinesD = 0;
                $this->_totalV = $this->_nbLinesV = 0;
                $lines[$this->_fileIndex] = $this->buildTransactionsData($transactions);
                $tmp = array();
            }
            $count++;
        }
        $tmp[] = $this->buildLine('Z');
        if ($this->_fileIndex == 1){
            $lines[$this->_fileIndex] = $tmp;
        }elseif (count($lines) != $this->_fileIndex){
            $lines = $tmp;
        }

        return $lines;
    }

    /**
     * Build the CREDIT or DEBIT transactions string = char 54 - 85.
     * This part is done for bank account.
     * @param array $data Transaction data
     * @return string
     */
    private function _getLineCD($data)
    {
        switch($data['RT_CodeCountry'])
        {
            case 'US':
                $line[] = $this->_repeatChar(9);
                $line[] = $this->_repeatChar(17) . $this->_repeatChar(1)
                    . $this->_repeatChar(1);
                $line[] = $this->_repeatChar(2, $data['RT_CodeCountry']);
                break;

            default: // CA
                $line[] = $this->_repeatChar(5);
                $line[] = $this->_repeatChar(3);
                $line[] = $this->_repeatChar(12);
                $line[] = $this->_repeatChar(3, 'CAD');
                $line[] = $this->_repeatChar(2);
                $line[] = $this->_repeatChar(2, $data['RT_CodeCountry']);
                break;
        }

        return implode(self::SPACE, $line);
    }
    /**
     * Build the CREDIT or DEBIT transactions string = char 54 - 85.
     * This part is done for credit cards.
     * @param array $data Transaction data
     * @return string
     */
    private function _getLineUV($data)
    {
        $line[] = $this->_repeatChar(19);
        $line[] = $this->_repeatChar(2);
        $line[] = $this->_repeatChar(4, $data['DAD_ExpirationDate']);
        $line[] = $this->_repeatChar(4);

        return implode(self::SPACE, $line);
    }
    /**
     * Build the first part of the transaction string = char 3 - 52
     * @param array $data Transaction data
     * @return string
     */
    private function _getFirstPart($data)
    {
        $line[] = $data['DAD_Token'];
        $string = empty($data['DAD_Name']) ?$data['DAD_CardHolderName'] : $data['DAD_Name'];
        $line[] = $this->_repeatChar(30, $string);

        return implode(self::SPACE, $line);
    }
    /**
     * Build the last part of the transaction string = char 87 - 120
     * @param array $data Transaction data
     * @return string
     */
    private function _getLastPart($data)
    {
        $amount = $data['RT_Amount'] * 100;
        $line[] = '';
        $line[] = date('dmy', strtotime($data['RT_PlannedDate']));
        $line[] = $this->_repeatChar(10, $amount, 0);
        $line[] = $this->_repeatChar(15);

        return implode(self::SPACE, $line);
    }

    private function _getLineA()
    {
        $line[] = 'A';
        $line[] = date('dmy');
        $line[] = $this->_apiId;
        $line[] = $this->_apiVersion;
        $line[] = $this->_repeatChar(4, $this->_fileIndex, 0);
        $line[] = $this->_repeatChar(5, 99999, 0);
        $fr = 1;
        $line[] = $this->_repeatChar(30, $this->_config->site->title->$fr);
        $line[] = 'CAD';
        $line[] = $this->_repeatChar(15);
        $line[] = $this->_repeatChar(35);

        return implode(self::SPACE, $line);
    }

    private function _getLineZ()
    {
        $line[] = 'Z'; // 1
        $line[] = $this->_repeatChar(8, $this->_nbLinesC, 0);
        $line[] = $this->_repeatChar(14, $this->_totalC, 0);
        $line[] = $this->_repeatChar(8, $this->_nbLinesD, 0);
        $line[] = $this->_repeatChar(14, $this->_totalD, 0);
        $line[] = $this->_repeatChar(8, $this->_nbLinesU, 0);
        $line[] = $this->_repeatChar(14, $this->_totalU, 0);
        $line[] = $this->_repeatChar(8, $this->_nbLinesV, 0);
        $line[] = $this->_repeatChar(14, $this->_totalV, 0);
        $line[] = $this->_repeatChar(22);

        return implode(self::SPACE, $line);
    }

    private function _repeatChar($max, $value = '', $char = self::SPACE)
    {
        if (strlen($value) > $max){
            $value = substr($value, 0, $max);
        }
        $multiplier = $value === '' ? $max : $max-strlen($value);
        $string = str_repeat($char, $multiplier) . $value;

        return $string;
    }

    public function setUploadOpt()
    {
        $fields = array('userfile' => '@' . $this->_file);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: multipart/form-data', 'Charset:UTF-8'));
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $fields);
    }

    public function getRejectionsReport()
    {
        $data = array();
        $values = array();
        // Reminder : test file encoding =. Tests = erreur
        $fileRows = explode("\n", trim(mb_convert_encoding($this->_response, 'UTF-8', 'iso-8859-1')));
        $oTrans = new RecursiveTransactionsObject();
        $keys = $oTrans->getRejectFileCols();
        foreach($fileRows as $row){
            $values = str_getcsv($row);
            if (!empty($values) && count($keys) == count($values)){
                $data[] = array_combine($keys, $values);
            }
        }

        return $data;
    }

}