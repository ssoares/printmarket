<?php
/**
 * Cible Solutions - Vêtements SP
 *
 *
 * @category  Modules
 * @package   Cart
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 * @version   $Id: IndexController.php 1769 2015-01-10 04:03:03Z ssoares $
 */

/**
 * Cart index controller
 * Manage actions to add, delete and display the cart content.
 *
 * @category  Modules
 * @package   Cart
 * @copyright Copyright (c) Cibles solutions d'affaires. (http://www.ciblesolutions.com)
 */
class Cart_IndexController extends Cible_Controller_Action
{
    protected $_orderPageId = 39;

    public function setOrderPageId($_orderPageId)
    {
        $this->_orderPageId = $_orderPageId;
    }

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('cart.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('cart.css'), 'all');
    }

    /**
     * Get the action type sent via ajax and update the total of items in the
     * cart.
     * Gets parameters from POST
     *
     * @return void
     */
    public function ajaxAction()
    {
        $this->getHelper('viewRenderer')->setNoRender();
        $action = $this->_getParam('actionAjax');
        $langId = $this->_getParam('langId');

        if ($action == 'updateCart')
        {
            $cart = new Cart();

            $cartItems = $cart->getTotalItem();
            $nbItems   = $cartItems['Quantity'];
            $subTotal  = $cartItems['Subtotal'];
            echo json_encode($cart->getTotalItem());
        }
        elseif ($action == 'addToCart')
        {
            $cart = new Cart();
            if (!$this->_isXmlHttpRequest)
            {
                $itemID  = $this->_getParam('p_id');
                $session = new Zend_Session_Namespace();
                $langId  = $session->languageId;
                $srcOriginal = "../www/"
                              . "data/images/catalog/originals/"
                              . $itemID . ".png";

                $width  = $this->_config->cart->image->thumb->maxWidth;
                $height = $this->_config->cart->image->thumb->maxHeight;

                Cible_FunctionsImageResampler::resampled(
                        array(
                            'src'       => $srcOriginal,
                            'maxWidth'  => $width,
                            'maxHeight' => $height)
                        );
            }
            else{
                $itemID = $this->_getParam('itemID');
            }
            if (($itemID <> ''))
            {
                try{
                $cart->addItem($itemID, 1, array('langId' => $langId));
            }
                catch(Exception $exc){
                    echo $exc->getMessage();
                    echo $exc->getTraceAsString();exit;
        }
            }
        }
        elseif ($action == 'update')
        {
            $cart   = new Cart();
            $itemId = $this->_getParam('itemId');
            $id     = $this->_getParam('cartItemsId');

            $cartItem = $cart->getItem($id, $itemId);

            echo json_encode($cartItem['Total']);
        }
        else
            echo 'null';

        if (!$this->_isXmlHttpRequest)
        {
            $config = Zend_Registry::get('config');
            $pageId = $config->cartPageId;
            $url = $this->_registry->absolute_web_root . "/"
                    . Cible_FunctionsPages::getPageNameByID($pageId, $langId);

            $this->_redirect ($url);
        }
    }

    /**
     * Controller action to manage the details of the cart.
     * Update/delete items or load the list.
     *
     * @return void
     */
    public function cartdetailsAction()
    {
        $account = Cible_FunctionsGeneral::getAuthentication();
//        if(!$account)
//            $this->_redirect (Cible_FunctionsPages::getPageNameByID (1, Zend_Registry::get('languageID')));

        $productData = array();
        $cart        = new Cart();

        if ($this->_isXmlHttpRequest)
        {
            $this->disableLayout();
            $this->disableView();

            $action    = $this->_getParam('do');
            $productId = $this->_getParam('pId');
            $itemId    = $this->_getParam('itemId');
            $quantity  = $this->_getParam('quantity');
            $category  = $this->_getParam('category');
            $disable   = $this->_getParam('disable');
            $cartId    = $this->_getParam('cartItemsId');

            if ($action == 'update' && !empty($productId))
            {
                if (!empty($size)){
                    $cart->updateItem(
                                $productId,
                                -1,
                                array(
                                    'CI_ItemID'      => $itemId,
                                    'CI_CartItemsID' => $cartId)
                        );
                }elseif (!empty($category)){
                    $cart->updateItem(
                                $productId,
                                -1,
                                array(
                                    'CI_CatTailleID' => $category,
                                    'CI_ItemID'      => $itemId,
                                    'CI_CartItemsID' => $cartId)
                        );
                }else{
                    $oItem  = new ItemsObject();
                    $oItem->setId($itemId);
                    $amount = $oItem->getPrice($quantity);

                    $cart->updateItem(
                            $productId,
                            $quantity,
                            array(
                                'CI_ItemID'      => $itemId,
                                'CI_Total'       => $amount,
                                'CI_CartItemsID' => $cartId)
                            );
                }
                echo json_encode(array('status' => 'updated', 'value' => $amount));
            }
            elseif ($action == 'delete' && !empty($productId))
            {
                if ($itemId && $cartId)
                {
                    $cart->updateItem(
                            $productId,
                            0,
                            array(
                                'CI_ItemID'      => $itemId,
                                'CI_CartItemsID' => $cartId)
                            );
                    echo json_encode(array('status' => 'deletedRow'));
                }
                else
                {
                    $cart->updateItem($productId);

                    echo json_encode(array('status' => 'deleted'));
                }
            }
            elseif ($action == 'disable' && !empty($productId))
            {
                $cart->updateItem(
                                $productId,
                                -1,
                                array(
                                    'CI_IsToSend' => $disable,
                                    'CI_ItemID' => $itemId)
                        );
            }
            elseif ($action == 'addSize' && !empty($productId))
            {
                $lastId = $cart->addSize(array(
                                'CI_ID'          => $productId,
                                'CI_Quantity'    => 1,
                                'CI_CatTailleID' => $category,
                                'CI_ItemID'      => $itemId)
                        );
            }
            elseif ($action == 'getSizes' && !empty($category))
            {
                $oSize = new TailleObject();
                $langId = $this->_getParam('langId');
                $size = $oSize->getDataByCategoryTailleId($category, $langId);

                echo json_encode($size);
                exit;
            }
        }
        else
        {
            $url = $this->view->absolute_web_root
                 . ltrim($this->getRequest()->getPathInfo(), '/');

            $exclude = preg_match('/resume-order/', $url);

            if(!$exclude)
                Cible_View_Helper_LastVisited::saveThis($url);

            $urlBack     = '';
            $urlNextStep = '';
            $urls        = Cible_View_Helper_LastVisited::getLastVisited();

            if (count($urls) > 1)
                $urlBack     = $urls[1];

            $cartData = $cart->getAllIds();
            $allIds   = $cartData['cartId'];

            if (count($allIds)){
                $urlNextStep = $this->view->baseUrl() . '/';
                $urlNextStep .= Cible_FunctionsCategories::getPagePerCategoryView(0, 'order', 17, null, true);
                $urlNextStep .= '/auth-order/';
            }

            $this->view->assign('itemCount', count($allIds));
            $this->view->assign('cartTotal', $cart->getTotalItem());

            $oProduct    = new CatalogCollection();
//            $orderPageId = Cible_FunctionsCategories::getPagePerCategoryView(0, 'order', 17);
            $resume      = false;

            if ($this->_registry->pageID == $this->_orderPageId)
                $resume = true;

            foreach ($allIds as $key => $id)
            {
                $itemId = $cartData['itemId'][$key];
                $prodId = $cartData['prodId'][$key];

                $productData[$id] = $oProduct->getDetails($prodId, $itemId, $resume);

                $cartDetails = $cart->getItem($id, $itemId);

                if($resume)
                    $renderItem  = $cart->renderResume ($cartDetails, $itemId);
                else
                    $renderItem  = $cart->renderCartLine($cartDetails, $itemId);

                $productData[$id]['items']['render'] = $renderItem;
                if (isset($cartDetails['Disable'])){
                $productData[$id]['cart']['disable'] = $cartDetails['Disable'];
                }
                $productData[$id]['cart']['promoId'] = $cartDetails['PromoId'];
            }

            $hasBonus    = $oProduct->getBonus();
            $orderParams = Cible_FunctionsGeneral::getParameters ();
            $params = array(
                'nbPoint'     => 0,
                'limitOrder'  => 0,
                'tpsFee'      => $orderParams['CP_ShippingFees'],
                'limitTpsFee' => $orderParams['CP_ShippingFeesLimit'],
                'CODFees'     => $orderParams['CP_MontantFraisCOD'],
            );
            if($account){
                $profile = new MemberProfile();
                $memberData = $profile->findMember(array('email' => $account['email']));
                $memberData = $profile->addTaxRate($memberData);
    //            if ($memberData['validatedEmail'] == '')
    //                $this->view->assign('valide', true);
    //            else
    //                $this->view->assign('valide', false);
                $tmp = array(
                    'taxeProv'    => $memberData['taxProv'],
                    'taxeCode'    => $memberData['taxCode'],
                'noProvTax'   => $memberData['noProvTax'],
                'noFedTax'    => $memberData['noFedTax']
            );
            if($memberData['taxCode'] == 'QC')
                $parameters['taxeFed'] = $orderParams['CP_TauxTaxeFed'];
            }else{
                $oTaxes = new TaxesObject();
                $stateId = '11';
                $taxRate = $oTaxes->getTaxData($stateId);
                $tmp = array(
                    'taxeProv'    => $taxRate['TP_Rate'],
                    'taxeCode'    => $taxRate['TZ_GroupName'],
                );
            }

            $parameters = $params + $tmp;

            if($hasBonus)
                $parameters['nbPoint'] = $orderParams['CP_BonusPointDollar'];

            $this->view->assign('productData', $productData);
            $this->view->assign('urlBack', $urlBack);
            $this->view->assign('nextStep', $urlNextStep);
            $this->view->assign('step', 1);
            $this->view->assign('hasBonus', $oProduct->getBonus());
            $this->view->assign('parameters', $parameters);

            if ($this->_registry->pageID == $this->_orderPageId)
                $this->renderScript('index/cart-summary.phtml');
            else
                $this->renderScript('index/cart-details.phtml');
        }
    }
}