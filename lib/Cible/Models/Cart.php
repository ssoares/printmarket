<?php

class Cart
{

    protected $_id;
    protected $_db;
    protected $_type;

    public function __construct($type = null)
    {

//        if (is_null($type))
//            throw new Exception('Cart type cannot be null');

        $this->_db = Zend_Registry::get('db');
        if (!empty($_COOKIE['cart']))
        {
            $this->_id = $_COOKIE['cart'];
            $this->createIfNotCreated();
        }
        else
        {
            $this->_id = $this->generateId();
            $path = Zend_Registry::get('web_root') . '/';
            setcookie('cart', $this->_id, time() + (60 * 60 * 24 * 365), $path);
            $this->createCart();
        }

        $this->_type = $type;
    }

    /**
     * Add an item into the cart items table or update it. <br>
     * Called after an ajax request.
     *
     * @param int   $id       Product id.
     * @param int   $quantity Numbers of product
     * @param array $options
     *
     * @return void
     */
    public function addItem($id, $quantity = 1, $options = array())
    {
        $langId = 0;
        $tmpId  = explode('-', $id);
        $id     = $tmpId[0];
        $itemId = $tmpId[1];

        if (empty($id) && $id <> 0)
            return;

        if (count($options) > 0 && !empty ($options['langId']))
            $langId = $options['langId'];

        $oItem = new ItemsObject();
        $oItem->setId($itemId);

        if ($this->alreadyInCart($id, $itemId))
        {
            $row      = $this->listAll($id, $itemId, 0);
            $data     = current($row);
//            $data      = $this->getItem($id, $itemId);
            $quantity += $data['Quantity'];
            $price     = $oItem->getPrice($quantity);

            $this->update(
                $id,
                array(
                    'CI_Quantity'    => $quantity,
                    'CI_Total'       => $price,
                    'CI_ItemID'      => $itemId,
                    'CI_CartItemsID' => $data['CartItemId']),
                true
            );
        }
        else
        {
            $price = $oItem->getPrice($quantity);

            $this->_db->insert('CartItems', array(
                'CI_CartID'   => $this->_id,
                'CI_ID'       => $id,
                'CI_Quantity' => $quantity,
                'CI_ItemID'   => $itemId,
                'CI_Total'    => $price
            ));
            // Test if we meet a condition to add discounted product.
            $this->manageItemPromo($id, $itemId);
        }

        $this->updateCartLastModified();
    }

    /**
     * Add an item into the cart items table or update it. <br>
     * Called after an ajax request.
     *
     * @param int   $id       Product id.
     * @param int   $quantity Numbers of product
     * @param array $options
     *
     * @return void
     */
    public function updateItem($id, $quantity = 0, $options = array())
    {
        $tmpId = explode('-', $id);
        $id    = $tmpId[0];

        if(isset($tmpId[1]))
            $itemId = $tmpId[1];
        elseif(isset($options['itemId']))
            $itemId = $options['itemId'];

        if ($quantity == 0 && count($options) == 0)
        {
            $this->delete($id);
        }
        else
        {
            if (count($options) > 0)
            {
                foreach ($options as $key => $value)
                {
                    $item[$key] = $value;
                }
            }

            $details = $this->getItem($item['CI_CartItemsID'], $item['CI_ItemID']);

            if ($quantity == -1)
            {
                $quantity = $details['Quantity'];
            }

            $item['CI_Quantity'] = $quantity;

            if($quantity == 0)
                $this->delete($id, $item);
            else
                $this->update($id, $item);
        }
    }

    /**
     * Add a new row for the item into the cart items table. <br>
     * Called after an ajax request.
     *
     * @param array $options
     *
     * @return void
     */
    public function addSize($options)
    {

        if (count($options) > 0)
        {
            foreach ($options as $key => $value)
            {
                $item[$key] = $value;
            }
        }

        $item['CI_CartID'] =  $this->_id;

        $this->_db->insert('CartItems', $item);

        $lastId = $this->_db->lastInsertID();

        echo 'inserted-' . $lastId;
    }

    /**
     * Fetch product details into the cart.
     *
     * @param int  $id     Product id
     * @param int  $itemId Item id
     * @oaram bool $filter Filter items which have been disable  = offers
     *
     * @return array
     */
    public function getItem($id, $itemId = null, $filter = false)
    {
        $tmpArray = array();
        $select   = $this->_db->select();
        $select->from(
                'CartItems',
                array(
                    'CartItemId' => 'CI_CartItemsID',
                    'ID'         => 'CI_ID',
                    'ItemId'     => 'CI_ItemID',
                    'PromoId'    => 'CI_PromoId',
                    'Disable'    => 'CI_Disable',
                    'Total'      => 'CI_Total',
                    'Quantity'   => 'CI_Quantity'))
            ->where('CartItems.CI_CartID = ?', $this->_id)
            ->where('CartItems.CI_CartItemsID = ?', $id);

        if($filter)
            $select->where('CartItems.CI_Disable = ?', 0);

        if ($itemId != null)
        {
            $select->where('CartItems.CI_ItemID = ?', $itemId);

            $tmpArray = $this->_db->fetchRow($select);
        }
        else
            $tmpArray = $this->_db->fetchAll($select);

        return $tmpArray;
    }

    /**
     * Fetch data from cartItems table
     *
     * @return array
     */
    public function getAllIds()
    {
        $select = $this->_db->select();

        $select->from(
            'CartItems',
            array(
                'ID'     => 'CI_ID',
                'itemId' => 'CI_ItemID',
                'cartId' => 'CI_CartItemsID')
            )
            ->joinLeft('Cart', 'Cart.C_ID = CartItems.CI_CartID', array())
            ->where('CartItems.CI_CartID = ?', $this->_id)
            ->order(array('CI_PromoId ASC','ID ASC', 'itemId ASC'));

        if (!empty($this->_type))
            $select->where('CartItems.CI_Type = ?', $this->_type);

        $ids = $this->_db->fetchAll($select);

        $_ids['prodId'] = array();
        $_ids['itemId'] = array();
        $_ids['cartId'] = array();

        foreach ($ids as $id)
        {
            array_push($_ids['prodId'], $id['ID']);
            array_push($_ids['itemId'], $id['itemId']);
            array_push($_ids['cartId'], $id['cartId']);
        }

        return $_ids;
    }

    /**
     * Count how many products are saved in the cart.
     *
     * @return array
     */
    public function getTotalItem()
    {
        $select = $this->_db->select();

        $select->from(
            'CartItems',
            array(
                'Quantity' => 'SUM(CI_Quantity)',
                'Subtotal' => 'ROUND(SUM(CI_Total), 2)')
            )
            ->joinLeft('Cart', 'Cart.C_ID = CartItems.CI_CartID', array())
            ->where('CartItems.CI_CartID = ?', $this->_id)
            ->where('CartItems.CI_PromoId = ?', 0);

        return $this->_db->fetchRow($select);
    }

    /**
     * Fetch the list of items/products and associated data for cart detail.
     *
     * @param int $productId Product id
     * @param int $itemId    If not null, that means the current item is a discount
     *                       and we want to find data to update its status.
     *
     * @return array
     */
    public function listAll($productId = null, $itemId = null, $filter = false)
    {
        $select = $this->_db->select();

        $select->from(
                'CartItems',
                array(
                    'CartItemId' => 'CI_CartItemsID',
                    'ID'         => 'CI_ID',
                    'ItemId'     => 'CI_ItemID',
                    'PromoId'    => 'CI_PromoId',
                    'Disable'    => 'CI_Disable',
                    'Quantity'   => 'CI_Quantity',
                    'Total'      => 'CI_Total'))
                ->joinLeft('Cart', 'Cart.C_ID = CartItems.CI_CartID', array())
                ->where('CartItems.CI_CartID = ?', $this->_id)
                ->order('CI_CartItemsID ASC');

        if($productId)
            $select->where('CartItems.CI_ID = ?', $productId);
        if($itemId)
            $select->where('CartItems.CI_ItemID = ?', $itemId);
        if($filter)
            $select->where('CartItems.CI_PromoId = ?', 1);
        else
            $select->where('CartItems.CI_PromoId = ?', 0);


        $items = $this->_db->fetchAll($select);

        $_items = array();
        foreach ($items as $item)
        {
            if (!isset($_items[$item['CartItemId']]))
            {
                $_items[$item['CartItemId']] = array(
                    'ID'         => $item['ID'],
                    'CartItemId' => $item['CartItemId'],
                    'ItemId'     => $item['ItemId'],
                    'Quantity'   => $item['Quantity'],
                    'PromoId'    => $item['PromoId'],
                    'Disable'    => $item['Disable'],
                    'Total'      => $item['Total']);
            }
        }

        return $_items;
    }

    /**
     * Creates the html code to render the page to manage the items
     *
     * @param array $data Details from cart for the item line.
     * @param int   $item Id of the item to render.
     *
     * @return string The html code to display
     */
    public function renderCartLine($data, $item)
    {
        $html = "";
        $unit = null;

        $unitPrice = "";
        $suffixIds = '-' . $data['ID']
                    . '-' . $data['ItemId']
                    . '-' . $data['CartItemId'];

        if (!$data['Disable'])
        {
            $oItem    = new ItemsObject();
            $itemData = $oItem->getAll(null, true, $item);
            $oItem->setId($item);
            $unit  = $oItem->getPrice($data['Quantity'], true);

            $taxProv = $itemData[0]['I_TaxProv'];
            $taxFed  = $itemData[0]['I_TaxFed'];

            if (!is_null($unit))
                $unitPrice = sprintf ('%.2f', $unit);

            $html .= '<div id="price' . $suffixIds .'" class="quantity">' . chr(13);
            if($data['PromoId'])
            {
                $html .= '    <p id="quantity' . $suffixIds .'" class="quantity qtyField">';
                $html .= $data['Quantity'] . '</p>' . chr(13);
            }
            else
            {
                $html .= '    <input id="quantity' . $suffixIds .'" class="quantity qtyField" ';
                $html .= ' value="' . $data['Quantity'] . '" type="text" />' . chr(13);
            }
            $html .= '    <input id="taxProv' . $suffixIds .'" class="taxProv" ';
            $html .= ' value="' . $taxProv . '" type="hidden" />' . chr(13);
            $html .= '    <input id="taxFed' . $suffixIds .'" class="taxFed" ';
            $html .= ' value="' . $taxFed . '" type="hidden" />' . chr(13);
            $html .= '</div>' . chr(13);
            $html .= '<div id="unitPrice' . $suffixIds .'" class="sumLine">' . chr(13);
            $html .= ' <span class="unitPrice">'  . $unitPrice . '</span> $' . chr(13);
            $html .= '</div>' . chr(13);
            $html .= '<div id="sumLine' . $suffixIds .'" class="sumLine right">' . chr(13);
            $html .= '    <span>' . sprintf('%.2f', $data['Total']) . '</span> $' . chr(13);
            $html .= '</div>' . chr(13);
        }
        return $html;
    }

    /**
     * Creates the html code to render the page display the summary list.
     *
     * @param array $data Details from cart for the item line.
     * @param int   $item Id of the item to render.
     *
     * @return string The html code to display
     */
    public function renderResume($data, $item)
    {
        $html = "";
        $unit = null;

        $pricePromo = 0;
        $unitPrice  = "";
        $suffixIds  = '-' . $data['ID']
                    . '-' . $data['ItemId']
                    . '-' . $data['CartItemId'];

        if (!$data['Disable'])
        {
            $oItem    = new ItemsObject();
            $itemData = $oItem->getAll(null, true, $item);
            $oItem->setId($item);
            $unit = $oItem->getPrice($data['Quantity'], true);
            if($data['PromoId'] > 0)
                $pricePromo = $data['Total'];
    //        $taxProv = Cible_FunctionsGeneral::provinceTax($data['Total']);
    //        $taxFed  = Cible_FunctionsGeneral::federalTax($data['Total']);

            $taxProv = $itemData[0]['I_TaxProv'];
            $taxFed  = $itemData[0]['I_TaxFed'];

            if (!is_null($unit))
                $unitPrice = sprintf ('%.2f', $unit);

            $html .= '<div id="price' . $suffixIds .'" class="quantity">';
            $html .= '    <span id="quantity' . $suffixIds .'" class="quantity qtyField" >';
            $html .=  $data['Quantity'] . '</span>' . chr(13);
            $html .= '    <span id="taxProv' . $suffixIds .'" class="taxProv" ';
            $html .= $taxProv . '</span>' . chr(13);
            $html .= '    <span id="taxFed' . $suffixIds .'" class="taxFed" ';
            $html .= $taxFed . '</span>' . chr(13);
            $html .= '</div>';
            $html .= '<div id="unitPrice' . $suffixIds .'" class="sumLine">' . chr(13);
            if($pricePromo > 0)
                $html .= ' <span class="unitPrice">'  . sprintf ('%.2f', $pricePromo) . '</span> $' . chr(13);
            else
                $html .= ' <span class="unitPrice">'  . $unitPrice . '</span> $' . chr(13);
            $html .= '</div>';
            $html .= '<div id="sumLine' . $suffixIds .'" class="sumLine right">' . chr(13);
            $html .= '    <span>' . sprintf('%.2f', $data['Total']) . '</span> $';
            $html .= '</div>';
        }

        return $html;
    }

    /**
     * Empty the cart for the current cart id
     *
     * @return void
     */
    public function emptyCart()
    {
        if (!empty($this->_type))
            $where[] = $this->_db->quoteInto('CI_Type = ?', $this->_type);

        $where[] = $this->_db->quoteInto('CI_CartID = ?', $this->_id);

        $this->_db->delete('CartItems', $where);
    }

    /**
     * Called if the record already exists. <br>
     * Updates the CI_Quantity field.
     *
     * @access private
     *
     * @param int   $id        Product id
     * @param array $item      Data for the current item.
     * @param bool  $increment <OPTIONAL> Default = false. <br>
     *                         If the quantity is to be incremented = true.
     *
     * @return void
     */
    private function update($id, $item = array(), $increment = false)
    {
        if (empty($item))
            return;

        if ($increment)
        {
            $details = $this->getItem($item['CI_CartItemsID'], $item['CI_ItemID']);
            $this->_db->query(
                "UPDATE CartItems SET CI_Quantity = {$item['CI_Quantity']},
                CI_Total = {$item['CI_Total']}
                WHERE CI_CartID = '{$this->_id}'
                AND CI_ID = '{$id}'
                AND CI_ItemID = '{$details['ItemId']}'
                AND CI_CartItemsID = '{$details['CartItemId']}'"
            );
        }
        else
        {
            $where[] = $this->_db->quoteInto('CI_ID = ?', $id);
            $where[] = $this->_db->quoteInto('CI_CartID = ?', $this->_id);
            $where[] = $this->_db->quoteInto('CI_ItemID = ?', $item['CI_ItemID']);
            if (!empty($item['CI_CartItemsID']))
                $where[] = $this->_db->quoteInto('CI_CartItemsID = ?', $item['CI_CartItemsID']);

            $this->_db->update('CartItems', $item, $where);
        }

        // Test if we meet a condition to set discounted product.
        $this->manageItemPromo($item['CI_CartItemsID'], $item['CI_ItemID']);
        $this->updateCartLastModified();
    }

    private function delete($id, $item = array())
    {
        if (empty($id) && $id <> 0)
            return;

//        if (!empty($item['CI_CartItemsID']))

        $where[] = $this->_db->quoteInto('CI_ID = ?', $id);
        $where[] = $this->_db->quoteInto('CI_ItemID = ?', $item['CI_ItemID']);
        $where[] = $this->_db->quoteInto('CI_CartID = ?', $this->_id);

        // If we delete a product from cart and it's a discount product set to false the field deactivate
        $detail = $this->getItem($item['CI_CartItemsID'], $item['CI_ItemID']);

        if($detail['PromoId'] > 0)
        {
            $where[] = $this->_db->quoteInto('CI_PromoId = ?', $detail['PromoId']);
            $this->_db->update('CartItems', array('CI_Disable' => 1), $where);
        }
        else
        {
            $oItemPromo = new ItemsPromoObject();
            $dataPromo  = $oItemPromo->getAll();

            if(count($dataPromo) && $dataPromo[0]['IP_NbItem'] > 0)
                $this->_db->delete('CartItems', $where);
            else
            {
                $where[] = $this->_db->quoteInto('CI_CartItemsID = ?', $item['CI_CartItemsID']);
                $this->_db->delete('CartItems', $where);
                //  Récupérer le total du cart
                $data = $this->getTotalItem();
                foreach($dataPromo as $promo)
                if($data['Subtotal'] < $promo['IP_ConditionAmount'])
                {
                    $where = $this->_db->quoteInto('CI_PromoId = ?', $promo['IP_ID']);
                    $this->_db->delete('CartItems', $where);
                }
            }
        }

        if (empty($item['CI_CartItemsID']))
            $this->updateCartLastModified();

    }

    private function createCart()
    {
        $this->_db->insert('Cart', array(
            'C_ID' => $this->_id,
            'C_CreatedOn' => date("Y-m-d h:i:s"),
            'C_UpdatedOn' => date("Y-m-d h:i:s")
                )
        );
    }

    private function updateCartLastModified()
    {
        $this->_db->update(
                'Cart',
                array('C_UpdatedOn' => date("Y-m-d h:i:s")),
                $this->_db->quoteInto('C_ID = ?', $this->_id)
        );
    }

    private function generateId()
    {
        do
        {
            $id = time();
        } while (!$this->isUnique($id));

        return $id;
    }

    private function alreadyInCart($id, $itemId, $promoId = 0)
    {
        $select = $this->_db->select()
            ->from('CartItems', true)
            ->where('CI_CartID = ?', $this->_id)
            ->where('CI_ID = ?', $id)
            ->where('CI_ItemID = ?', $itemId);

        $select->where('CI_PromoId = ?', $promoId);

        $result = $this->_db->fetchOne($select);
        return!empty($result);
    }

    private function createIfNotCreated()
    {
        $result = $this->_db->fetchOne("SELECT true FROM Cart WHERE C_ID = '{$this->_id}'");
        if (empty($result))
            $this->createCart();
    }

    private function isUnique($id)
    {
        $result = $this->_db->fetchOne('SELECT false FROM Cart WHERE C_ID = ?', $id);
        return empty($result);
    }

    private function manageItemPromo($id, $itemId)
    {
        // Chercher itemId dans la table des promo dans le champ IP_ConditionItemId
        $oItemPromo = new ItemsPromoObject();
        $oItem      = new ItemsObject();
        $dataPromo  = $oItemPromo->getAssociatedItems($itemId);

        // Si trouvé,
        if (count($dataPromo) > 0)
        {
            //  Récupré le nombre d'itemId dans le cart
            $detail = $this->getItem($id, $itemId);
            $nbItemInCart = $detail['Quantity'];
            foreach ($dataPromo as $promo)
            {
                $itemData = $oItem->getAll(null, true, $promo['IP_ItemId']);
                $promo['I_ProductID'] = $itemData[0]['I_ProductID'];
                //  Comparer avec le champ IP_NbItem
                //  Si le nombre dans cart sup à qté dans table promo
                if ($nbItemInCart >= $promo['IP_NbItem'])
                {
                    //      Tester si l'item promo existe déjà
                    //      Si non: Ajouter dans la table CartItems
                    if (!$this->alreadyInCart($promo['I_ProductID'], $promo['IP_ItemId'], $promo['IP_ID']))
                        $this->_insertPromo($promo);
                }
                elseif ($promo['IP_NbItem'] == 0)
                {
                    // Si le nombre d'item conditionnel est null, alors on test
                    // sur le total
                    $this->_testByTotal($promo, $oItem);
                }
                else
                {
                    $this->_db->delete('CartItems', array("CI_PromoId = {$promo['IP_ID']}", 'CI_Disable != 1'));
                }
            }
        }
        else
        {
            // Si itemId <>
            $dataPromo = $oItemPromo->getAll();
            foreach ($dataPromo as $promo)
            {
                $this->_testByTotal($promo, $oItem);
            }

        }
    }

    /**
     * Tests if the total of the cart is compliant with promo data to insert product.
     *
     * @param int   $id
     * @param int   $itemId
     * @param array $promo
     *
     * @return void
     */
    private function _testByTotal($promo, $oItem)
    {
        //  Récupérer le total du cart
        $data = $this->getTotalItem();
        //  Si Total sup A champ IP_ConditionAmount
        $gte = (bool)Cible_FunctionsGeneral::compareFloats((float)$data['Subtotal'], '>=', (float)$promo['IP_ConditionAmount']);
        $itemData = $oItem->getAll(null, true, $promo['IP_ItemId']);
        $promo['I_ProductID'] = $itemData[0]['I_ProductID'];
        if ($gte && $promo['IP_NbItem'] == 0 && !$this->alreadyInCart($promo['I_ProductID'], $promo['IP_ItemId'], $promo['IP_ID']))
        {
            //  Ajouter dans la table CartItems
            $this->_insertPromo($promo);
        }
        elseif(!$gte)
        {
            $this->_db->delete('CartItems', array("CI_PromoId = {$promo['IP_ID']}", 'CI_Disable != 1'));
        }


    }

    /**
     * Insert the discounted item.
     *
     * @param int   $id
     * @param int   $itemId
     * @param array $promo
     *
     * @return void
     */
    private function _insertPromo($promo)
    {
        $this->_db->insert(
            'CartItems',
            array(
                'CI_CartID'   => $this->_id,
                'CI_ID'       => $promo['I_ProductID'],
                'CI_Quantity' => 1,
                'CI_ItemID'   => $promo['IP_ItemId'],
                'CI_Disable'  => 0,
                'CI_PromoId'  => $promo['IP_ID'],
                'CI_Total'    => $promo['IP_Price']
            ));
    }
}
