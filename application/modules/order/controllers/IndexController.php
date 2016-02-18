<?php

class Order_IndexController extends Cible_Controller_Action
{
    const SEPARATOR  = '||';
    const EXTENSION  = '.csv';
    const UNDERSCORE = '_';
    const STATUS     = 'aucun';

    protected $_moduleID      = 17;
    protected $_defaultAction = '';
    protected $_moduleTitle   = 'order';
    protected $_name          = 'index';
    protected $_paramId       = '';
    protected $_emailRenderData = array();

    public function init()
    {
        parent::init();
    }

    public function ajaxAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $action = $this->_getParam('actionAjax');

        if ($action == 'updateSessionVar')
        {
            $quoteRequestOrderVar['shippingShipperName'] = $this->_getParam('shippingShipperName');
            $quoteRequestOrderVar['shippingMethod'] = $this->_getParam('shippingMethod');
            $quoteRequestOrderVar['shippingAccountNumber'] = $this->_getParam('shippingAccountNumber');
            $quoteRequestOrderVar['shippingComment'] = $this->_getParam('shippingComment');

            $quoteRequestOrderVar['shippingShipToADifferentAddress'] = $this->_getParam('shippingShipToADifferentAddress');
            $quoteRequestOrderVar['lastName'] = $this->_getParam('lastName');
            $quoteRequestOrderVar['firstName'] = $this->_getParam('firstName');
            $quoteRequestOrderVar['company'] = $this->_getParam('company');
            $quoteRequestOrderVar['address'] = $this->_getParam('address');
            $quoteRequestOrderVar['city'] = $this->_getParam('city');
            $quoteRequestOrderVar['state'] = $this->_getParam('state');
            $quoteRequestOrderVar['country'] = $this->_getParam('country');
            $quoteRequestOrderVar['zipCode'] = $this->_getParam('zipCode');
            $quoteRequestOrderVar['phone'] = $this->_getParam('phone');

            $quoteRequestOrderVar['poNumber'] = $this->_getParam('poNumber');
            $quoteRequestOrderVar['projectName'] = $this->_getParam('projectName');
            $quoteRequestOrderVar['contactMe'] = $this->_getParam('contactMe');
            $quoteRequestOrderVar['newsletterSubscription'] = $this->_getParam('newsletterSubscription');
            $quoteRequestOrderVar['termsAgreement'] = $this->_getParam('termsAgreement');

            //echo($quoteRequestOrderVar['lastName']);
            $quoteRequestOrder = new Zend_Session_Namespace('quoteRequestOrderVar');
            foreach ($quoteRequestOrderVar as $key => $value)
            {
                $quoteRequestOrder->$key = $value;
            }

            echo json_encode((array('result' => '')));
        }
    }


    public function loginwithinscriptionAction()
    {
        $account = Cible_FunctionsGeneral::getAuthentication();

        if (is_null($account))
        {
            $path = Zend_Registry::get('web_root') . '/';
            setcookie('returnUrl', $this->view->selectedPage, 0, $path);

            $form = new FormLogin();

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData))
                {
                    $result = Cible_FunctionsGeneral::authenticate($formData['email'], $formData['password']);

                    if ($result['success'] == 'true'
                        && empty($result['validatedEmail'])
                        && $result['status'] == 2)
                    {
                        $this->disableView();
                        $path = Zend_Registry::get('web_root') . '/';
                        $hash = md5(session_id());
                        $duration = $formData['stayOn'] ? time() + (60 * 60 * 24 * 365) : 0;
                        $cookie = array(
                            'lastName' => $result['lastName'],
                            'firstName' => $result['firstName'],
                            'email' => $result['email'],
                            'hash' => $hash,
                            'status' => $result['status']
                        );
                        setcookie("authentication", json_encode($cookie), $duration, $path);

                        $memberProfile = new MemberProfile();
                        $memberProfile->updateMember($result['member_id'], array('hash' => $hash));

                        $this->_redirect(Cible_FunctionsPages::getPageNameByID($this->_config->catalog->pageId));
                    }
                    else
                    {
                        if ($result['success'] == 'true')
                        {
                            if(!empty($result['validatedEmail']) || $result['status'] < 2)
                            {
                                setcookie("authentication");
                                $url  = Cible_FunctionsCategories::getPagePerCategoryView(0, 'confirm_email', $this->_moduleID);
                                $url .= '/email/' . $formData['email'];
                                $this->_redirect($url);
                            }
                        }
                        else
                        {
                            $error = Cible_Translation::getClientText('login_form_auth_fail_error');
                            $this->view->assign('error', $error);
                        }
                    }
                }
            }

            $this->view->assign('form', $form);
        }
        else
        {
            if (Zend_Registry::get('pageID') == $this->_config->authentication->pageId)
            {
                if ($account['status'] == 0)
                {
                    setcookie("authentication");
                    $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView(0, 'confirm_email', $this->_moduleID));
                }
                elseif ($account['status'] == 1)
                {
                    setcookie("authentication");
                    $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView(0, 'become_client', $this->_moduleID));
                }
                elseif ($account['status'] == 2)
                {
                    $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView(0, 'modify_inscription', $this->_moduleID));
                }
            }
            $this->disableView();
        }
    }

    public function loginStepTwoAction()
    {
        $account = Cible_FunctionsGeneral::getAuthentication();

        if (is_null($account))
        {
            $path = Zend_Registry::get('web_root') . '/';
            setcookie('returnUrl', $this->view->selectedPage, 0, $path);
            $path = Zend_Registry::get('web_root') . '/';
            $url = $this->view->selectedPage . '/resume-order/';

            setcookie('returnUrl', $url, 0, $path);

            $form = new FormLogin();

            if ($this->_request->isPost() && array_key_exists('submit_login', $_POST))
            {
                $formData = $this->_request->getPost();

                if ($form->isValid($formData))
                {
                    $result = Cible_FunctionsGeneral::authenticate($formData['email'], $formData['password']);

                    if ($result['success'] == 'true')
                    {
                        $this->disableView();
                        $hash = md5(session_id());
                        $duration = $formData['stayOn'] ? time() + (60 * 60 * 24 * 365) : 0;
                        $cookie = array(
                            'lastName' => $result['lastName'],
                            'firstName' => $result['firstName'],
                            'email' => $result['email'],
                            'hash' => $hash
                        );
                        setcookie("authentication", json_encode($cookie), $duration, $path);

                        $memberProfile = new MemberProfile();
                        $memberProfile->updateMember($result['member_id'], array('hash' => $hash));

                        $this->_redirect($url);
                    }
                    else
                    {
                        $error = Cible_Translation::getClientText('login_form_auth_fail_error');
                        $this->view->assign('error', $error);
                    }
                }
            }

            $this->view->assign('form', $form);
        }
    }

    public function orderAction()
    {
        $session = new Zend_Session_Namespace('order');
        $urlBack     = '';
        $urlNextStep = '';
        $urls        = Cible_View_Helper_LastVisited::getLastVisited();
        $memberInfos = array();
        $profile  = new MemberProfilesObject();
        $oAddress = new AddressObject();

        $authentication = Cible_FunctionsGeneral::getAuthentication();

        $page = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list_collections', 14);

        // If authentication is not present or if cart is empty, redirect to the cart page
        if (!is_null($authentication))
        {
            $memberInfos = $profile->findData(array(
                'email' => $authentication['email']
            ));
//            $memberInfos = $profile->addTaxRate($memberInfos);
        }

            $return = $this->_getParam('return');
            if ($return && isset($_COOKIE['returnUrl']))
            {
                $returnUrl = $_COOKIE['returnUrl'];
                $this->view->assign('return', $returnUrl);
            }

        $pageOrderName = Cible_FunctionsCategories::getPagePerCategoryView(0, 'order', $this->_moduleID, null, true);
            $stepValues = array(
                'auth-order' => array(
                    'step' => 2,
                    'next' => $pageOrderName . '/resume-order',
                    'prev' => ''),
                'resume-order' => array(
                    'step' => 3,
                    'next' => $pageOrderName . '/send-order',
                    'prev' => 'auth-order'),
                'send-order' => array(
                    'step' => 4,
                    'next' => '',
                    'prev' => 'resume-order')
            );
            $stepAction = $this->_getParam('action');
            $urlBack = $stepValues[$stepAction]['prev'];

        if (empty($stepValues[$stepAction]['prev']) && isset($urls[0])){
                $urlBack = $urls[0];
        }
            $this->view->assign('step', $stepValues[$stepAction]['step']);
            $this->view->assign('nextStep', $stepValues[$stepAction]['next']);
            $this->view->assign('urlBack', $urlBack);
            $orderParams = Cible_FunctionsGeneral::getParameters ();

            switch ($stepAction)
            {
                case 'resume-order':
                    if(empty($session->customer))
                        $this->_redirect(Cible_FunctionsPages::getPageNameByID (1));
                $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('cart.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('cart.css'), 'all');
                    // Create this form to fill with values used for the read-only rendering
                    $formOrder = new FormOrder(array('resume' => true));
                    // Store the state id in the session to allow tax calculation
                    $session->stateId = $billAddr['A_StateId'];
                    // Calculate totals to display and for the bill.
                    $totals = $this->calculateTotal($memberInfos);

                    $session->order['charge_total'] = $totals['total'];
                    $session->order['subTotal']     = $totals['subTot'];
                    $session->order['taxFed']       = $totals['taxFed'];
                    $session->order['taxProv']      = $totals['taxProv'];
                    $session->order['nbPoint']      = $totals['nbPoint'];
                    $session->order['shipFee']      = $orderParams['CP_ShippingFees'];
                    $session->order['limitShip']    = $orderParams['CP_ShippingFeesLimit'];
                    $session->order['CODFees']      = $orderParams['CP_MontantFraisCOD'];
                    $session->order['rateFed']      = 0;

                    if($session->stateId == 11)
                        $session->order['rateFed'] = $orderParams['CP_TauxTaxeFed'];

                    if(isset($session->customer['addressShipping']['duplicate']) && $session->customer['addressShipping']['duplicate'])
                    {
                        unset($session->customer['addressShipping']);
                        $session->customer['addressShipping'] = $session->customer['addressFact'];
                    }

                    $dataBill = $this->getAddrData($session->customer['addressFact'], 'addressFact', $session);
                    $dataShip = $this->getAddrData($session->customer['addressShipping'], 'addressShipping', $session);

                    $salut = Cible_FunctionsGeneral::getSalutations(
                        $memberInfos['salutation'],
                        Zend_Registry::get('languageID')
                    );

                    if (isset($salut[$memberInfos['salutation']]))
                        $session->customer['identification']['salutation'] = $salut[$memberInfos['salutation']];
                    else
                        $session->customer['identification']['salutation'] = "-";

                    $formOrder->populate($session->customer);

                    $formOrder->getSubForm('addressShipping')->removeElement('duplicate');
                    $formOrder->getSubForm('identification')->removeElement('password');
                    $formOrder->getSubForm('identification')->removeElement('passwordConfirmation');
                    $formOrder->getSubForm('identification')->removeElement('noFedTax');
                    $formOrder->getSubForm('identification')->removeElement('noProvTax');
                    $formOrder->getSubForm('identification')->removeElement('AI_FirstTel');
                    $formOrder->getSubForm('identification')->removeElement('AI_SecondTel');
                    $formOrder->getSubForm('identification')->removeElement('AI_WebSite');
                    $formOrder->getSubForm('identification')->removeElement('A_Fax');

                    $readOnly = new Cible_View_Helper_FormReadOnly();
                    $readOnly->setAddSeparator(true);
                    $readOnly->setSeparatorClass('dotLine');
                    $readOnly->setListOpened(false);
                    $readOnly->setSeparatorPositon(array(1));
                    $readOnlyForm = $readOnly->subFormRender($formOrder);

                    $formPayment = new FormOrderPayment(
                        array(
                            'readOnlyForm' => $readOnlyForm,
                            'payMean'      => $session->customer['paymentMeans'])
                        );
//                    $formPayment->populate($session->order);

                    if ($this->_request->isPost() && array_key_exists('submit', $_POST))
                    {
                        $formData = $this->_request->getPost();
                        $session->customer['invoice'] = $formData;
//                        $session->customer['indentification'] = $memberInfos;

                        $this->_redirect($stepValues[$stepAction]['next']);
                    }

                    $session->customer['charge_total'] = sprintf('%.2f', $totals['total']);
                    $formPayment->populate($session->customer);
                    $this->view->assign('CODFees',$orderParams['CP_MontantFraisCOD']);
                    $this->view->assign('memberInfos', $memberInfos);
                    $this->view->assign('formOrder', $formPayment);
                    $this->renderScript('index/order-summary.phtml');

                    break;

                case 'send-order':
                    if ($this->_request->isPost())
                    {
//                        if ($this->_request->getParam('response_code') > 50)
                        if ($this->_request->getParam('response_code') < 50 &&
                            $this->_request->getParam('response_code') != 'null')
                        {
                            $session->order['confirmation'] = $_POST;
                        }
                        else
                        {
                            $this->view->assign('errorValidation', $this->view->getClientText('card_payment_error_message'));
                            $session->customer['message'] = $this->view->getClientText('card_payment_error_message');
                            $this->_redirect($pageOrderName .'/'.$stepValues['resume-order']['prev'] . '/errorValidation/1');
                        }
                    }

                    $this->sendOrder();
                    $urlBack = $this->view->BaseUrl() . '/' . $page;
                    $this->view->assign('backHomeLink', $urlBack    );
                    $this->renderScript('index/order-sent.phtml');

                    break;

                default:
                $options = array();
                if (!is_null($authentication)){
                    $options['MP_HasAccount'] = $memberInfos['MP_HasAccount'];
                    $this->view->assign('accountValidate', $memberInfos['validatedEmail']);
                }
                $form = new FormOrderAddr($options);

                    if ($this->_request->isPost())
                    {
                        $data = $this->_request->getPost();
                        $currentCity  = 0;
                    $current_state  = $data['address']['A_StateId'] . '||';
                        $current_state .= $data['addressShipping']['A_StateId']  ;

                        $memberInfos['selectedState'] = $session->customer['selectedState'];
                        $memberInfos['selectedCity']  = $session->customer['selectedCity'];
                    }

                    if ($this->_request->isPost() && array_key_exists('submit', $_POST))
                    {
                        $formData = $this->_request->getPost();
                        $formData['selectedState'] = $current_state;
                        $formData['selectedCity']  = $currentCity;

                    if($form->isValid($formData)){
//                            if($formData['paymentMeans'] == 'cod')
//                                $session->order['cod'] = $formData['paymentMeans'];
//                            elseif(isset($session->order['cod']))
//                                unset($session->order['cod']);

                            $session->customer = $formData;
                            $session->customer['identification'] = $memberInfos;
                            $this->_redirect($stepValues[$stepAction]['next']);
                        }
                    else{
                            $form->populate($formData);
                        }
                    }
                        else
                        if($session->customer)
                        {
                            $form->populate($session->customer);
                            $errorValidation = $this->_getParam('errorValidation');
                            if(isset($session->customer['message']) && !empty($errorValidation))
                                $this->view->assign('message', $session->customer['message']);
                        }
                    else
                    {
                            $form->populate($memberInfos);
                        }

                    $this->view->assign('CODFees',$orderParams['CP_MontantFraisCOD']);
                    $this->view->assign('form', $form);
                    $this->view->assign('memberInfos', $memberInfos);
                    break;
            }
//        }
//        else
//            $this->_redirect(Cible_FunctionsPages::getPageNameByID(1));

    }

    /**
     * Saves quote request data and send email to client and manager.
     *
     * @return void
     */
    public function sendOrder()
    {
        $session = new Zend_Session_Namespace('order');
        $page = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list_collections', 14);
        if (!count($session->customer))
            $this->_redirect($page);

        $oCart = new Cart();
        // Créer les tableaux pour sauvegarder les données de la commande
        $language = Cible_FunctionsGeneral::getLanguageTitle($session->customer['identification']['language']);
        $custAccount = array(
            'O_LastName'   => $session->customer['identification']['lastName'],
            'O_FirstName'  => $session->customer['identification']['firstName'],
            'O_Email'      => $session->customer['identification']['email'],
            'O_AcombaId'   => $session->customer['identification']['acombaNum'],
            'O_Salutation' => $session->customer['identification']['salutation'],
            'O_Language'   => $language
        );
         if(!empty($session->customer['addressFact']['A_CityId']))
             $cityBill = $session->customer['addressFact']['A_CityId'];
         else
             $cityBill = $session->customer['addressFact']['A_CityTextValue'];

         if(!empty($session->customer['addressShipping']['A_CityId']))
             $cityShip = $session->customer['addressShipping']['A_CityId'];
         else
             $cityShip = $session->customer['addressShipping']['A_CityTextValue'];

         $addressBilling = array(
            'O_FirstBillingTel'   => $session->customer['addressFact']['AI_FirstTel'],
            'O_SecondBillingTel'  => $session->customer['addressFact']['AI_SecondTel'],
            'O_FirstBillingAddr'  => $session->customer['addressFact']['AI_FirstAddress'],
            'O_SecondBillingAddr' => $session->customer['addressFact']['AI_SecondAddress'],
            'O_BillingCity'       => $cityBill,
            'O_BillingState'      => $session->customer['addressFact']['A_StateId'],
            'O_BillingCountry'    => $session->customer['addressFact']['A_CountryId'],
            'O_ZipCode'           => $session->customer['addressFact']['A_ZipCode']
        );

         $addressShipping = array(
            'O_FirstShippingTel'   => $session->customer['addressShipping']['AI_FirstTel'],
            'O_SecondShippingTel'  => $session->customer['addressShipping']['AI_FirstTel'],
            'O_FirstShippingAddr'  => $session->customer['addressShipping']['AI_FirstAddress'],
            'O_SecondShippingAddr' => $session->customer['addressShipping']['AI_SecondAddress'],
            'O_ShippingCity'       => $cityShip,
            'O_ShippingState'      => $session->customer['addressShipping']['A_StateId'],
            'O_ShippingCountry'    => $session->customer['addressShipping']['A_CountryId'],
            'O_ShippingZipCode'    => $session->customer['addressShipping']['A_ZipCode']
        );


         $paid = false;
         $responseId  = 0;
         $datePayed   = 0;
         $bankTransId = 0;
         $cardHolder  = '';
         $cardNumber  = '';
         $cardType    = '';
         $chargeTotal = $session->order['charge_total'];

         $cardexpiryDate = 0;

         if (isset($session->order['confirmation']))
         {

             $paid = true;
             $responseId  = $session->order['confirmation']['response_order_id'];
             $datePayed   = $session->order['confirmation']['date_stamp'] . ' ' . $session->order['confirmation']['time_stamp'];
             $bankTransId = $session->order['confirmation']['bank_transaction_id'];
             $cardHolder  = $session->order['confirmation']['cardholder'];
             $cardNumber  = $session->order['confirmation']['f4l4'];

             switch ($session->order['confirmation']['card'])
             {
                 case 'V':
                    $cardType = 'INTERNET';
                     break;
                 case 'M':
                    $cardType = 'INTERNET';
                     break;

                 default:
                     break;
             }

             if ($session->customer['paymentMeans'] == 'visa' || $session->customer['paymentMeans'] == 'mastercard')
                 $cardType = 'INTERNET';

             $cardexpiryDate = $session->order['confirmation']['expiry_date'];
             $chargeTotal    = $session->order['confirmation']['charge_total'];
         }
         $transFees = $session->order['shipFee'];
         $display   = true;
         if (Cible_FunctionsGeneral::compareFloats($session->order['subTotal'], ">=", $session->order['limitShip']))
         {
            $display   = false;
            $transFees = 0;
         }

         $nbPoints = 0;

         if ($session->customer['identification']['cumulPoint'])
             $nbPoints = $session->order['nbPoint'];

         $orderData = array(
            'O_ResponseOrderID'   => $responseId,
            'O_ClientProfileId'   => $session->customer['identification']['member_id'],
            'O_Comments'          => $session->customer['O_Comments'],
            'O_CreateDate'        => date('Y-m-d H:i:s', time()),
            'O_ApprobDate'        => date('Y-m-d H:i:s', time()),
            'O_SubTotal'          => $session->order['subTotal'],
            'O_TotTaxProv'        => $session->order['taxProv'],
            'O_TotTaxFed'         => $session->order['taxFed'],
            'O_RateTaxProv'       => sprintf('%.2f',$session->order['rateProv']['TP_Rate']),
            'O_RateTaxFed'        => $session->order['rateFed'],
            'O_TaxProvId'         => $session->stateId,
            'O_TransFees'         => $transFees,
            'O_Total'             => $session->order['charge_total'],
            'O_PaymentMode'       => $session->customer['paymentMeans'],
            'O_Paid'              => $paid,
            'O_DatePayed'         => $datePayed,
            'O_BankTransactionId' => $bankTransId,
            'O_CardHolder'        => $cardHolder,
            'O_CardNum'           => $cardNumber,
            'O_CardType'          => $cardType,
            'O_CardExpiryDate'    => $cardexpiryDate,
            'O_TotalPaid'         => $chargeTotal,
            'O_BonusPoint'        => $session->order['nbPoint']
        );

        $order = array_merge(
            $orderData,
            $addressBilling,
            $addressShipping,
            $custAccount);
        //Enregistrer la commades dans la db
            //Recuprer l'id pour inserer le numéro de commande
        $oOrder  = new OrderObject();
        $orderId = $oOrder->insert($order, 1);

        //Créer le numéro de commade
        $OrderNumber = 'I' . $orderId;
        //Mettre à jour la cde avec son numéro
        $oOrder->save($orderId, array('O_OrderNumber' => $OrderNumber), 1);
        $memberInfos = $session->customer['identification'];
        //Créer les données pour les lignes de commades
        $oOrderLine= new OrderLinesObject();

        $oCart    = new Cart();
        $allIds   = $oCart->getAllIds();
        $oProduct = new CatalogCollection();
        $oItems   = new ItemsObject();

        $productData  = array();
        $productItems = array();

        foreach ($allIds['cartId'] as $key => $id)
        {
            $itemId = $allIds['itemId'][$key];
            $prodId = $allIds['prodId'][$key];
            // Récupérer la ligne du cart
            $cartDetails = $oCart->getItem($id, $itemId);

            if(!$cartDetails['Disable'])
            {
                // Récupérer les produits
                $productData = $oProduct->getDetails($prodId, $itemId);
                // Recupérer les items
                $itemDetails = $oItems->getAll(null, true, $itemId);
                //Calcul des taxes et des montants
                $price    = $cartDetails['Quantity'] * $itemDetails[0]['I_PriceVol1'];
                $discount = abs($price - $cartDetails['Total']);
                $itemPrice = $cartDetails['Total'] / $cartDetails['Quantity'];
                $codeProd = $itemDetails[0]['I_ProductCode'];
                $taxProv  = Cible_FunctionsGeneral::provinceTax($cartDetails['Total']);
                $taxFed   = 0;
                if($session->stateId == 11)
                    $taxFed = Cible_FunctionsGeneral::federalTax($cartDetails['Total']);

                // Tableau pour la liste des données
                $lineData = array(
                    'OL_ProductId'    => $prodId,
                    'OL_OrderId'      => $orderId,
                    'OL_ItemId'       => $itemId,
                    'OL_Type'         => 'LigneItem',
                    'OL_Quantity'     => $cartDetails['Quantity'],
                    'OL_ProductCode'  => $codeProd,
                    'OL_Price'        => $itemPrice,
                    'OL_Discount'     => $discount,
                    'OL_FinalPrice'   => $cartDetails['Total'],
                    'OL_FirstTax'     => $itemDetails[0]['I_TaxFed'],
                    'OL_SecondTax'    => $itemDetails[0]['I_TaxProv'],
                    'OL_TotFirstTax'  => $taxFed,
                    'OL_TotSecondTax' => $taxProv,
                    'OL_Description'  => $productData['data']['PI_Name'] . ' - ' . $itemDetails[0]['II_Name']
                );
                //Enregistrer les lignes
                if($cartDetails['PromoId'] > 0)
                {
                    $lineDataTxt = array(
                        'OL_ProductId'   => $prodId,
                        'OL_OrderId'     => $orderId,
                        'OL_ItemId'      => $itemId,
                        'OL_Type'        => 'LigneTexte',
                        'OL_Description' => Cible_Translation::getClientText('alert_special_offer_item'));

                    $oOrderLine->insert($lineDataTxt, 1);

                    $lineData['OL_Price'] = $cartDetails['Total'] / $cartDetails['Quantity'];
                    array_push($productItems, $lineDataTxt);
                }

                $oOrderLine->insert($lineData, 1);
                array_push($productItems, $lineData);
            }
        }

        // send a notification to the client
        // Set data to the view
         $this->_emailRenderData['emailHeader'] = "<img src='"
                . Zend_Registry::get('absolute_web_root')
                . "/themes/default/images/common"
                . "/logoEmail.jpg' alt='' border='0'>";
        $this->_emailRenderData['footer'] = $this->view->getClientText("email_notification_footer");
        $this->view->assign('template', $this->_emailRenderData);
        $this->view->assign('subTotal', $session->order['subTotal']);
        $this->view->assign('orderNumber', $OrderNumber);
        $this->view->assign('orderNumber', $OrderNumber);
        $this->view->assign('custAccount', $custAccount);
        $this->view->assign('addressBilling', $addressBilling);
        $this->view->assign('addressShipping', $addressShipping);
        $this->view->assign('cardType', $cardType);
        $this->view->assign('cardHolder', $cardHolder);
        $this->view->assign('cardNumber', $cardNumber);
        $this->view->assign('cardExpiryDate', $cardexpiryDate);
        $this->view->assign('productItems', $productItems);
        $this->view->assign('chargeTotal', $chargeTotal);
        $this->view->assign('taxeTVQ', $session->order['taxProv']);
        $this->view->assign('taxeTPS', $session->order['taxFed']);
        $this->view->assign('shipFee', $session->order['shipFee']);
        $this->view->assign('limitShip', $session->order['limitShip']);
        $this->view->assign('CODFees', $session->order['CODFees']);
        $this->view->assign('comments', $session->customer['O_Comments']);
        $this->view->assign('display', $display);

        if(isset($session->order['cod']))
            $this->view->assign('displayCODFees', true);

        $this->view->assign('paid', $paid);
        //Get html content for email and page displaying
        $view = $this->getHelper('ViewRenderer')->view;
        $view->assign('online', false);
        $html = $view->render('index/emailToSend.phtml');
        //Prepare notification email for customer
        $adminEmail = Cible_FunctionsGeneral::getParameters('CP_AdminOrdersEmail');
        $notification = new Cible_Notify();
        $notification->isHtml(1);
        $notification->addTo($memberInfos['email']);
        $notification->setFrom($adminEmail);
        $notification->setTitle($this->view->getClientText('email_to_customer_title') . ': n° ' . $OrderNumber);
        $notification->setMessage($html);
        //Prepare notification email for admin
        $notifyAdmin = new Cible_Notify();
        $notifyAdmin->isHtml(1);
        $notifyAdmin->addTo($adminEmail);
        $notifyAdmin->setFrom($memberInfos['email']);
        $notifyAdmin->setTitle($this->view->getClientText('email_to_company_title') . $OrderNumber);
        $notifyAdmin->setMessage($html);
        //Send emails
        $notifyAdmin->send();
        $notification->send();
        //Create the csv file to export orders - Set status to exported
        $this->writeFile();
        //Display message on the site.
        $view->assign('online', true);
        $html = $view->render('index/emailToSend.phtml');
        $this->view->assign('html', $html);
        //Empty data
        $this->emptyCart();
        $session->unsetAll();
//        new Cart();
    }

    private function _fillAddressShipping($data)
    {
        $addressShipping = array();
        if (!empty ($data))
        {
            if (!empty($data['A_CityId']))
                $cityShip = $data['A_CityId'];
            else
                $cityShip = $data['A_CityTextValue'];

            $addressShipping = array(
                'O_FirstShippingTel' => $data['AI_FirstTel'],
                'O_SecondShippingTel' => $data['AI_FirstTel'],
                'O_FirstShippingAddr' => $data['AI_FirstAddress'],
                'O_SecondShippingAddr' => $data['AI_SecondAddress'],
                'O_ShippingCity' => $cityShip,
                'O_ShippingState' => $data['A_StateId'],
                'O_ShippingCountry' => $data['A_CountryId'],
                'O_ShippingZipCode' => $data['A_ZipCode']
            );
        }
        return $addressShipping;
    }

    public function emptyCart()
    {
        $oCart = new Cart();
        $allIds = $oCart->getAllIds();
        $oCart->emptyCart();
    }

    public function returnconfirmemailAction()
    {
        $email = $this->_getParam('email');
        if (!empty($email))
            $account['email'] = $email;
        else
        $account = Cible_FunctionsGeneral::getAuthentication();

        if (!is_null($account))
        {
            $profile = new MemberProfile();
            $user = $profile->findMember(array('email' => $account['email']));
            if ($user)
            {
                if ($user['validatedEmail'] == '')
                {
                    $this->view->assign('alreadyValide', true);
                }
                else
                {
                    $data = array(
                        'firstName' => $user['firstName'],
                        'lastName' => $user['lastName'],
                        'email' => $user['email'],
                        'language' => $user['language'],
                        'validatedEmail' => $user['validatedEmail']
                        );
                    $options = array(
                        'send' => true,
                        'isHtml' => true,
                        'to' => $user['email'],
                        'moduleId' => $this->_moduleID,
                        'event' => 'editResend',
                        'type' => 'email',
                        'recipient' => 'client',
                        'data' => $data
                    );

                    $oNotification = new Cible_Notifications_Email($options);

                    $this->view->assign('needConfirm', true);
                }
            }
        }
        $this->renderScript('index/confirm-email.phtml');
    }

    // When the client click on the link in the email to confirm his email, he will come to this action/page
    public function confirmemailAction()
    {
        $email = $this->_getParam('email');
        $validateNumber = $this->_getParam('validateNumber');

        $profile = new MemberProfile();
        $user = $profile->findMember(array('email' => $email));
        var_dump($user);
        exit;
        $cart = new Cart();
        if ($cart->getTotalItem() >= 1)
        {
            $this->view->assign("return", Cible_FunctionsCategories::getPagePerCategoryView(0, 'cart_details', 15));
        }

        if ($user)
        {
            if ($user['validatedEmail'] == '')
            {
                $url = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list_collections', 14);
                if ($user['status'] == 2)
                    $this->_redirect ($url);
                $this->view->assign('alreadyValide', true);
            }
            elseif ($user['validatedEmail'] == $validateNumber)
            {
                $this->view->assign('valide', true);
                $profile->updateMember($user['member_id'], array('validatedEmail' => '', 'status' => '1'));
                $this->_emailRenderData['emailHeader'] = $this->view->clientImage('logo.jpg', null, true);

                $data = array(
                    'firstName' => $user['firstName'],
                    'lastName' => $user['lastName'],
                    'email' => $user['email'],
                    'language' => $user['language'],
                    'NEWID' => $user['member_id']
                    );
                $options = array(
                    'send' => true,
                    'isHtml' => true,
                    'moduleId' => $this->_moduleID,
                    'event' => 'newAccount',
                    'type' => 'email',
                    'recipient' => 'admin',
                    'data' => $data
                );

                $oNotification = new Cible_Notifications_Email($options);

                $this->renderScript('index/become-client-thank-you.phtml');
            }
            else
            {
                $this->view->assign('email', $email);
                $this->view->assign('valid', false);
            }
        }
    }

    /**
     * Insert data about the item for the quote request submission.
     *
     * @param array $items          Data of the items in the cart.
     * @param int   $reqProductId   Id of the requested product in the quote request
     * @param int   $quoteRequestId Id of the quote request. Usefull for export only.
     *
     * @return void
     */
    private function _insertRequestedItem($items, $reqProductId, $quoteRequestId)
    {

        $oRequestedItem = new ItemObject();

        foreach ($items as $itemId => $item)
        {
            $details = $item['cartDetails'][0];

            if ($details['Disabled'])
            {
                $reqItemData['itemId'] = $details['ItemId'];
                $reqItemData['sizeId'] = $details['SizeId'];
                $reqItemData['quantity'] = $details['Quantity'];
                $reqItemData['reqProdId'] = $reqProductId;
                $reqItemData['quotReqId'] = $quoteRequestId;

                $oRequestedItem->insert($reqItemData, 1);
            }
        }
    }

    /**
     * Transforms data of the posted form in one array
     *
     * @param array $formData Data to save.
     *
     * @return array
     */
    protected function _mergeFormData(array $formData)
    {
        (array) $tmpArray = array();

        foreach ($formData as $key => $data)
        {
            if (is_array($data))
            {
                $tmpArray = array_merge($tmpArray, $data);
            }
            else
                $tmpArray[$key] = $data;
        }

        return $tmpArray;
    }

    public function captchaReloadAction()
    {
        $baseDir = $this->view->baseUrl();
        $captcha_image = new Zend_Captcha_Image(array(
                    'captcha' => 'Word',
                    'wordLen' => 5,
                    'fontSize' => 16,
                    'height' => 50,
                    'width' => 100,
                    'timeout' => 300,
                    'dotNoiseLevel' => 0,
                    'lineNoiseLevel' => 0,
                    'font' => Zend_Registry::get('application_path') . "/../{$this->_config->document_root}/captcha/fonts/ARIAL.TTF",
                    'imgDir' => Zend_Registry::get('application_path') . "/../{$this->_config->document_root}/captcha/tmp",
                    'imgUrl' => "$baseDir/captcha/tmp"
                ));

        $image = $captcha_image->generate();
        $captcha['id'] = $captcha_image->getId();
        $captcha['word'] = $captcha_image->getWord();
        $captcha['url'] = $captcha_image->getImgUrl() . $image . $captcha_image->getSuffix();

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        echo Zend_Json::encode($captcha);
    }

    /**
     * Format address and set string values.
     *
     * @param array $address
     * @param array $addressName
     * @param Zend_Session_Namespace $session
     */
    protected function getAddrData($address, $addressName, $session)
    {
        if (isset($address['A_CityId']) && (int)$address['A_CityId'])
        {
            $city = Cible_FunctionsGeneral::getCities(
                            Zend_Registry::get('languageID'),
                            $address['A_CityId']
            );
            $session->customer[$addressName]['A_CityId']    = $city['value'];
        }

        if ((int)$address['A_CountryId'])
        {
            $country = Cible_FunctionsGeneral::getCountries(
                            Zend_Registry::get('languageID'),
                            $address['A_CountryId']
            );
            $session->customer[$addressName]['A_CountryId'] = $country['name'];
        }
        if ((int)$address['A_StateId'])
        {
            $state = Cible_FunctionsGeneral::getStates(
                            Zend_Registry::get('languageID'),
                            $address['A_StateId']
            );
            $session->customer[$addressName]['A_StateId']   = $state['name'];
        }
    }

    /**
     * Calculation of taxes and amounts for orders rendrering.
     *
     * @param array $memberInfos
     * @param bool $lines
     *
     * @return array
     */
    public function calculateTotal($memberInfos, $lines = false)
    {
        $session = new Zend_Session_Namespace('order');

        $data  = array();
        $oCart = new Cart();
        $oItem = new ItemsObject();
        $oProd = new CatalogCollection();

        $subTotProv = 0;
        $subTotFed  = 0;
        $subTot     = 0;
        $total      = 0;
        $taxProv    = 0;
        $taxFed     = 0;
        $tmpSum     = 0;
        $nbPoint    = 0;

        $cartData    = $oCart->getAllIds();
        $orderParams = Cible_FunctionsGeneral::getParameters ();

        if(!$memberInfos['noFedTax'] && $session->stateId == 11)
        {
            foreach ($cartData['cartId'] as $key => $id)
            {
                $itemId = $cartData['itemId'][$key];
                $prodId = $cartData['prodId'][$key];

                $itemDetails = $oItem->getAll(null, true, $itemId);
                $cartDetails = $oCart->getItem($id, $itemId, true);
                if($itemDetails[0]['I_TaxFed'])
                    $subTotFed += $cartDetails['Total'];
            }

            $addShipFee = Cible_FunctionsGeneral::compareFloats($subTotFed, '<', $orderParams['CP_ShippingFeesLimit'], 2);
            if($addShipFee)
                $subTotFed += $orderParams['CP_ShippingFees'];
            if(isset($session->order['cod']))
                $subTotFed += $orderParams['CP_MontantFraisCOD'];

            $taxFed = Cible_FunctionsGeneral::federalTax($subTotFed);

        }

        if(!$memberInfos['noProvTax'])
        {
            foreach ($cartData['cartId'] as $key => $id)
            {
                $itemId = $cartData['itemId'][$key];
                $prodId = $cartData['prodId'][$key];

                $itemDetails = $oItem->getAll(null, true, $itemId);
                $cartDetails = $oCart->getItem($id, $itemId, true);
                if($itemDetails[0]['I_TaxProv'])
                    $subTotProv += $cartDetails['Total'];
            }

            $addShipFee = Cible_FunctionsGeneral::compareFloats($subTotProv, '<', $orderParams['CP_ShippingFeesLimit'], 2);
            if($addShipFee)
                $subTotProv += $orderParams['CP_ShippingFees'];
            if(isset($session->order['cod']))
                $subTotProv += $orderParams['CP_MontantFraisCOD'];

            $taxProv = Cible_FunctionsGeneral::provinceTax($subTotProv);
        }

        foreach ($cartData['cartId'] as $key => $id)
        {
            $itemId = $cartData['itemId'][$key];
            $prodId = $cartData['prodId'][$key];

            $productData = $oProd->getDetails($prodId);
            $cartDetails = $oCart->getItem($id, $itemId, true);
            $subTot += $cartDetails['Total'];
            if ($oProd->getBonus())
                $nbPoint += ceil($cartDetails['Total'] * $orderParams['CP_BonusPointDollar']);
        }

        $addShipFee = Cible_FunctionsGeneral::compareFloats($subTot, '<', $orderParams['CP_ShippingFeesLimit'], 2);

        if($addShipFee)
            $tmpSum += $orderParams['CP_ShippingFees'];

        if(isset($session->order['cod']))
            $tmpSum += $orderParams['CP_MontantFraisCOD'];

        $total = $subTot + $tmpSum + round($taxFed,2) + round($taxProv,2);

        $data = array(
            'subTotProv' => $subTotProv,
            'subTotFed'  => $subTotFed,
            'subTot'     => $subTot,
            'total'      => $total,
            'taxProv'    => $taxProv,
            'nbPoint'    => $nbPoint,
            'taxFed'     => $taxFed
            );

        return $data;
    }

    public function writeFile()
    {
        $session = new Zend_Session_Namespace('order');
        $db = Zend_Registry::get('db');

        $startDate = date('d-m-Y H:i:s');
        $string = "--------- Export starting date: " . $startDate . "--------- \r\n";
        $this->writeLog($string);
        $this->orderExportPath = Zend_Registry::get('web_root') . "/data/files/order/export/";

        $columns = array();

        $oOrder       = new OrderObject();
        $oOrderLine   = new OrderLinesObject();
        $nbOrder      = 0;
        $totLines     = 0;
        $tableName    = $oOrder->getDataTableName();
        $orderHeader  = 'O_ID, DATE(O_CreateDate), O_Email, CONCAT(O_FirstBillingAddr, " ", O_SecondBillingAddr), CONCAT(O_BillingCity," - ",O_BillingState), "CA" as ISOCodeBill, O_ZipCode, ';
        $orderHeader .= 'CONCAT(O_FirstShippingAddr, " ", O_SecondShippingAddr), CONCAT(O_ShippingCity," - ", O_ShippingState), "CA" as ISOCodeShip, O_ShippingZipCode, "Transaction par panier d achat" as Label, O_TransFees, ';
        $orderHeader .= "O_Total, O_CardType, null as NAN, '{$session->customer['identification']['taxCode']}' as taxCode";

        $orderFooter  = array('O_Notes', 'O_FirstBillingTel', 'concat(O_FirstName, " ", O_LastName)', 'O_AcombaId');
//        $orderFooter .= 'O_AcombaId';
        $LineColumns = array('OL_ProductCode', 'OL_Description', 'OL_Quantity', 'OL_Price');

        $orders = $oOrder->getDataForExport($orderHeader, self::STATUS);

        foreach ($orders as $order)
        {
            $orderId = $order['O_ID'];
            // Define variables to fill export file
            $fileLine = "";
            $nbLines  = 0;
            $fileName = $tableName . self::UNDERSCORE . $orderId . self::EXTENSION;
            $file     = $_SERVER['DOCUMENT_ROOT'] . $this->orderExportPath . $fileName;

            //Open file to write data into it.
            $fh = fopen($file, 'w');
            // Prepare header data
            if(isset($session->order['cod']))
                $order['O_TransFees'] = $order['O_TransFees'] + $session->order['CODFees'];

            $header   = implode(self::SEPARATOR, $order);

            // Prepaqre footer data
            $footData = $oOrder->getDataForExport($orderFooter, self::STATUS, $orderId);
            $tel      = str_replace(array('(',')',' ', '-'), array('','','',''), $footData[0]['O_FirstBillingTel']);

            // Set the values in new array ordered to fit with sql
            $footDt['O_Notes'] = $footData[0]['O_Notes'];
            $footDt['Empty']   = 'F';

            $footDt['O_FirstBillingTel'] = $tel;
            $footDt['Name']    = $footData[0]['concat(O_FirstName, " ", O_LastName)'];
            $footDt['O_AcombaId']        = $footData[0]['O_AcombaId'];

            $footer   = implode(self::SEPARATOR, $footDt);

            // Select related lines
            $lines = $oOrderLine->getDataForExport($orderId, $LineColumns);

            foreach ($lines as $line)
            {
                ++$nbLines;
                $lineData  = $nbLines . self::SEPARATOR;
                array_push($line, "");
                array_push($line, "");
                $lineData .= implode(self::SEPARATOR, $line);

                $fileLine .= $header . self::SEPARATOR
                            . $lineData . self::SEPARATOR . $footer . "\r\n";
            }

            if(!$fh)
            {
                $string = 'Cannot open ' . $file . ' at ' . date('d-m-Y H:i:s');
                $this->writeLog($string);
            }
            else
            {
                $this->writeData($fh, $fileLine);
                //Colse the file;
                fclose($fh);
                ++$nbOrder;
                $totLines += $nbLines;

                $status = array('O_Status' => 'exportee');
                $oOrder->save($orderId, $status, 1);

                $endDate = date('d-m-Y H:i:s');
                $string  = $endDate ;
                $string .= " : " . $fileName . ' - ' . $nbLines . " lines(products) exported\r\n";
                $this->writeLog($string);
            }
        }
    }

    /**
* Write data in the current file
* @param resource $handle Current file handler.
* @param array    $data   Data to insert in the file
*
* @return void
*/
    private function writeData($handle, $data)
    {
        if(!fwrite($handle, $data))
        {
            $errorDate = date('d-m-Y H:i:s');
            $string    = "Error while writing data at " . $errorDate  . "\r\n";
            $this->writeLog($string);
        }
    }

    /**
     * Write informations
     *
     * @param string $string Messag to add in the log file
     */
    private function writeLog($string)
    {
        $orderLogPath = Zend_Registry::get('logPath'). "/order/";

        // Log file
        $suffix      = date('Ym');
        $logFileName = 'log_' . $suffix . '.txt';
        $fileLog     = $orderLogPath . $logFileName;
        $fLog        = fopen($fileLog, 'a');

        fwrite($fLog, $string);
        //Close log file
        fclose($fLog);
    }

    /**
     * Callback function to add double quote to the given string.
     * Usefull to format array data for file export.
     *
     * @param string $string
     *
     * @return string
     */
    private function addQuotes($string)
    {
        return '"' . $string . '"';
    }

    public function listAction()
    {
        $config = Zend_Registry::get('config');
        $select = $this->_buildData(true);

        $adapter   = new Zend_Paginator_Adapter_DbSelect($select);
        $paginator = new Zend_Paginator($adapter);
        $paginator->setItemCountPerPage($config->lists->itemPerPage);
        $paginator->setCurrentPageNumber($this->_request->getParam('page'));
        $this->view->assign('paginator', $paginator);
}

    public function editAction()
    {
        $oCart = new Cart();
        $db      = Zend_Registry::get('db');
        $select  = $this->_buildData();
        $data    = $db->fetchAll($select);
        if (count($data) > 0)
        {
            $id = $this->_getParam('qr');
            $files   = $oCart->manageFileUpload($id);
            $this->view->assign('filesData', $files);
            $this->view->assign('filePath', "/order/" . $id . "/");
            $html = $this->view->render('index/renderFileLine.phtml');
            $this->view->assign('fileLines', $html);
            $this->view->assign('allowsUpdateFile', true);

            $cartHeader = array(
                'C_ID' => $data[0]['O_ID'],
                'C_ProjectTitle' => $data[0]['O_ProjectName'],
                'C_DesiredDate' => $data[0]['O_DesiredDate']
            );

            $this->view->assign('cartHeader', $cartHeader);
        }
        else
        {
            $url = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list', 17);
            $this->_redirect($url);
        }
    }
    public function updateFileAction()
    {
        $this->disableLayout();
        $this->disableView();
        if ($this->_isXmlHttpRequest)
        {
            $oOrderFiles = new OrderAttachedFilesObject();

            $fileName = $this->_getParam('comments');
            $orderId  = $this->_getParam('cartId');
            $data = array(
                'OAF_Filename' => $fileName,
                'OAF_OrderID' => $orderId
                );
            $fileId = $oOrderFiles->fileExists($fileName, $orderId);

            if ($fileId > 0)
                $oOrderFiles->delete ($fileId);
            else
                $oOrderFiles->insert ($data, 1);
        }
    }

    private function _buildData($orderOnly = false)
    {
        $url = Cible_FunctionsCategories::getPagePerCategoryView(0, 'list_collections', 14);
        $this->view->headLink()->appendStylesheet($this->view->locateFile('cart.css'),'all');
        $account = Cible_FunctionsGeneral::getAuthentication();
        if (!$account)
            $this->_redirect($url);

        $oMember = new MemberProfile();
        $user = $oMember->findMember($account);

        $orderId = $this->_getParam('qr');
        $oOrder = new OrderCollection();
        if ($orderId)
            $oOrder->setOrderId ($orderId);

        $oOrder->setUserId($user['member_id']);
        $oOrder->setOrderOnly($orderOnly);

        $select = $oOrder->getData();

        return $select;
    }
}