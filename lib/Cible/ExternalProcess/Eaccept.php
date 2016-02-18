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
 * Description of Cible_ExternalProcess_EAccept
 *
 * @category
 * @package
 * @copyright Copyright (c)2015 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id$
 */
class Cible_ExternalProcess_Eaccept extends Cible_ExternalProcess
{
    const REQUEST = 'request';
    const REFUND = 'refund';
    const RESPONSE = 'response';
    const PROCESS = 'eaccept';

    protected $_apiId = '';
    protected $_apiVersion = '1.0';
    protected $_currencies = array('CAD', 'USD');
    protected $_paymentPageId = null;
    protected $_data = array();
    protected $_urlPage = 'payment/purchase/form';

    protected $_keys = array("apiId", 'apiVersion', 'timestamp',
        'invoiceNumber', 'description', 'amount', 'currency', 'language',
        'fingerprint', 'paymentPageId', 'redirectUrl');

    protected $_messages = array(
        1 => 'Transaction for invoiceNumber ##INVOICENUM##',
    );

    public function getApiVersion(){
        return $this->_apiVersion;
    }
    public function getCurrencies(){
        return $this->_currencies;
    }
    public function getPaymentPageId(){
        return $this->_paymentPageId;
    }
    public function setApiVersion($apiVersion){
        $this->_apiVersion = $apiVersion;
        return $this;
    }
    public function setCurrencies($currencies)
    {
        $this->_currencies = $currencies;
        return $this;
    }
    public function setPaymentPageId($paymentPageId)
    {
        $this->_paymentPageId = $paymentPageId;
        return $this;
    }

    public function setData($data, $action = self::REQUEST)
    {
        switch($action)
        {
            case self::REFUND:

                break;
            case self::RESPONSE:
                $oDonTrans = new DonationTransactionObject();
                foreach($oDonTrans->getCryptoCols() as $key => $value){
                    $this->_data[$value] = $data[$value];
                }
                break;
            default:
                $rawAmount = empty($data['DO_Amount']) ? $data['proposals'] : $data['DO_Amount'];
                $amount = str_replace(array(' ',','), array('', '.'),$rawAmount);
                $this->_data = array($this->_apiId, $this->_apiVersion,
                    $this->_timestamp, $data['DTR_InvoiceNumber'], $data['description'],
                    $amount * 100, current($this->_currencies),$data['language']);
                break;
        }
        return $this;
    }
    public function __construct($config)
    {
        parent::__construct($config);
        $this->setTimestamp();
        $process = self::PROCESS;
        $this->_apiId = $this->_config->services->$process->id;
        $this->_key = $this->sanitizeKey($this->_config->services->$process->key);
        $this->_urlPage = $this->_config->services->$process->url
            . $this->_urlPage;
    }

    public function getUrlPage(){
        return $this->_urlPage;
    }
    public function getExtReqUrl()
    {
        $this->_urlPage .= '?';
        $this->_data[] = $this->_fingerPrint;
        $this->_data[] = $this->_paymentPageId > 0 ? $this->_paymentPageId : '';
        $this->_data[] = $this->_redirectUrl;
        if ($this->_supportRequired){
//            $this->_keys[] = 'supportRequired';
//            $this->_data[] = $this->_supportRequired;
        }
        $string = http_build_query(array_combine($this->_keys, $this->_data));

        return $this->_urlPage .= $string;
    }

    public function paymentRequest()
    {
        try
        {
            $wsdl_url = $this->_url;
            $client = new SOAPClient($wsdl_url);
            $params = array();
            $return = $client->createPayment($params);
            print_r($return);
        }
        catch(Exception $e)
        {
            echo "Exception occured: " . $e;
        }
    }

}
