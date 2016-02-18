<?php

class Order_IndexController extends Cible_Extranet_Controller_Import_Action implements Zend_Acl_Resource_Interface
{
    protected $_moduleID      = 17;
    protected $_defaultAction = '';
    protected $_moduleTitle   = 'order';
    protected $_name          = 'index';
    protected $_ID            = 'id';

    public function  getResourceId()
    {
        return $this->_moduleTitle;
    }

    public function listClientsAction()
    {
        // web page title
        $this->view->title = "Clients";
        if ($this->view->aclIsAllowed($this->_moduleTitle,'edit',true))
        {
            $profile = new GenericProfile();
            $member  = new MemberProfile();
            $oRetailer = new RetailersObject();
            $selectRetailer = $oRetailer->getAll(null, false);
            $selectRetailer->joinRight(
                    $profile->getGenericTable(),
                    'R_GenericProfileId = GP_MemberID',
                    array(
                        'lastName'  => 'GP_LastName',
                        'firstName' => 'GP_FirstName',
                        'email'     => 'GP_Email')
                    );

            $select = $selectRetailer->joinRight(
                    $member->getTable(),
                    'GP_MemberID = MP_GenericProfileMemberID',
                    array(
                        'member_id' => 'MP_GenericProfileMemberID',
                        'company'   => 'MP_CompanyName',
                        'MP_Status' => 'MP_Status')
                    );

            $tables = array(
                'GenericProfiles' => array('GP_LastName', 'GP_FirstName', 'GP_Email'),

                'RetailersData' => array('R_ID', 'R_GenericProfileID', 'R_AddressId', 'R_Status'),

            );

            $field_list = array(

                'company'  => array('width' => '250px'),
                'lastName'  => array('width' => '250px'),
                'firstName' => array('width' => '250px')
            );
            $tables = array(
                'GenericProfiles' => array('GP_lastName', 'GP_firstName'),
                'MemberProfiles' => array(
                    'MP_CompanyName'
                    )
            );

            $this->view->params = $this->_getAllParams();

            $pageID = $this->_getParam( 'pageID' );
            $langId = $this->_registry->languageID;

            $options = array(
                'commands' => array(
                    $this->view->link(
                            $this->view->url(
                                    array(
                                        'module'=>'users',
                                        'controller'=>'index',
                                        'action'=>'general',
                                        'actionKey'=>'add',
                                        'returnModule'=>$this->_moduleTitle,
                                        'returnAction'=>'list-clients'
                                        )
                                    ),
                            $this->view->getCibleText('button_add_profile'),
                            array('class'=>'action_submit add')
                            )
                ),
                'excludedColums' => array('Nom'), // columns to exclude from search
                'disable-export-to-excel' => '',
                'to-excel-action' => 'clients-to-excel',
                'filters' => array(

                    'filter_1' => array(
                        'label' => 'Liste des détaillants',
                        'default_value' => null,
                        'associatedTo' => 'GP_MemberID',
                        'equalTo' => 'R_GenericProfileId',
                        'choices' => array(
                            ''  => 'Liste des détaillants',
                            '1' => "--> Affichés sur le site"
                            )
                    ),
                    'filter_2' => array(
                        'label' => 'Liste des détaillants',
                        'default_value' => null,
                        'associatedTo' => 'MP_Status',
                        'choices' => array(
                            '' => 'Filtrer par statut',
                            '-1' => 'Désactivé',
                            '0' => 'Email non validé',
                            '1' => 'À valider',
                            '2' => 'Activé'

                        )
                    )
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                            'edit' => array(
                            'label' => $this->view->getCibleText('menu_submenu_action_edit'),
                            'url' => $this->view->url(
                                    array('module'=>'users',
                                        'action' => 'general',
                                        'actionKey' => 'edit',
                                        'ID' => "-ID-",
                                        'returnModule'=>$this->_moduleTitle,
                                        'returnAction'=>'list-clients'
                                        )
                                    ),
                            'findReplace' => array(
                                'search' => '-ID-',
                                'replace' => 'member_id'
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('menu_submenu_action_delete'),
                            'url' => $this->view->url(
                                        array(
                                            'module'=>'users',
                                            'action' => 'general',
                                            'actionKey' => 'delete',
                                            'ID' => "-ID-",
                                            'returnModule'=>$this->_moduleTitle,
                                            'returnAction'=>'list-clients'
                                            )
                                    ),
                            'findReplace' => array(
                                'search' => '-ID-',
                                'replace' => 'member_id'
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator(
                    $select,
                    $tables,
                    $field_list,
                    $options
                    );
            $this->view->assign('mylist', $mylist);
        }
    }

    public function listOrdersAction()
    {
        // web page title
        $this->view->title = "Commandes";
        if ($this->view->aclIsAllowed($this->_moduleTitle,'edit_submission',true))
        {
            $select = $this->_db->select();
            $select->from(
                    'Orders',
                    array(
                        'O_ID'          => 'O_ID',
                        'O_OrderNumber' => 'O_OrderNumber',
                        'Name'          => 'concat(O_LastName, " " ,O_FirstName, " <br />Num Acomba: ",O_AccountId)',
                        'O_Total'       => 'O_Total',
                        'O_PaymentMode' => 'O_PaymentMode',
                        'O_Paid'        => 'O_Paid')
                    )
                ->order('O_ID ASC');
//                ->joinInner(
//                    'GenericProfiles',
//                    'O_ClientProfileId = GP_MemberProfileID',
//                    array()
//                    )
//                ->joinInner(
//                    'MemberProfiles',
//                    'DS_ClientProfileID = MP_GenericProfileMemberID',
//                    array('MP_Entreprise_1', 'MP_Entreprise_2')
//                    )

            $tables = array(
                 'Orders' => array(
                    'O_OrderNumber' => 'O_OrderNumber',
                    'O_LastName'    => 'O_LastName',
                    'O_FirstName'   => 'O_LastName',
                    'O_AcombaId'    => 'O_AccountId',
                    'O_Total'       => 'O_Total',
                    'O_PaymentMode' => 'O_PaymentMode',
                    'O_Paid'        => 'O_Paid')
//                'GenericProfiles' => array('GP_LastName', 'GP_FirstName'),
//                'MemberProfiles'  => array('MP_Entreprise_1', 'MP_Entreprise_2')
            );

            $field_list = array(
                    'O_OrderNumber'     => array('width' => '150px'),
                    'Name'  => array('width' => '150px'),
                    'O_Total' => array('width' => '80px'),
                    'O_PaymentMode' => array('width' => '80px'),
                    'O_Paid'    => array('width' => '80px',
                        'postProcess' => array(
                            'type' => 'dictionnary',
                            'prefix' => 'orderCompleted_'
                            )),
            );

            $options = array(
                'disable-export-to-excel' => 'true',
                'filters' => array(
//                    'OrderId' => array(
//                        'label'         => 'OrderId',
//                        'default_value' => '',
//                        'associatedTo'  => 'O_OrderNumber',
//                        'choices'       => array(''=>'')
//                    ),
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
//                        'export' => array(
//                            'label' => $this->view->getCibleText('menu_submenu_action_export'),
//                            'url'   => $this->view->url(
//                                array(
//                                    'module' =>$this->_moduleTitle,
//                                    'action' => 'export-quote-request',
//                                    'ID'     => "-ID-")
//                                ),
//                            'findReplace' => array(
//                                    'search' => '-ID-',
//                                    'replace' => 'DS_ID'
//                            )
//                        ),
                        'view' => array(
                            'label' => $this->view->getCibleText('menu_submenu_action_view'),
                            'url' => $this->view->url(
                                array(
                                    'module' => $this->_moduleTitle,
                                    'action' => 'view-order',
                                    'ID'     => "-ID-")
                                ),
                            'findReplace' => array(
                                    'search'  => '-ID-',
                                    'replace' => 'O_ID'
                            )
                        ),
//                        'delete' => array(
//                            'label' => $this->view->getCibleText('menu_submenu_action_delete'),
//                            'url'   => $this->view->url(
//                                array(
//                                    'module' => $this->_moduleTitle,
//                                    'action' => 'delete-quote-request',
//                                    'ID'     => "-ID-")
//                                ),
//                            'findReplace' => array(
//                                    'search'  => '-ID-',
//                                    'replace' => 'DS_ID'
//                            )
//                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
            $this->view->assign('mylist', $mylist);
        }
    }

    public function editOrderAction()
    {
        // web page title
        $this->view->title = "Édition d'une commande";
        $quoteId = $this->_getParam('ID');
        $redirectTo = "/Order/index/list-quote-requests/";

        if( !empty($quoteId) )
        {
            $details = array();
            $form = new FormOrder(
                array(
                    'cancelUrl' => "{$this->view->baseUrl()}$redirectTo"
            ));
            $this->view->assign('form', $form);

            $Order = new DemandeSoumissionObject();
            $details = $Order->populate(
                $quoteId,
                $this->_defaultEditLanguage);

            $oProdReq  = new ProduitDemandeObject();
            $oItemReq  = new ItemDemandeObject();
            $oProduct  = new ProduitObject();
            $oItem     = new ItemObject();
            $oSize     = new TailleObject();
            $oCategory = new CategorieTailleObject();

            $prodReq = $oProdReq->getByDemandeId($quoteId);

            foreach ($prodReq as $data)
            {
                $product[$data['PD_ProduitID']] = $oProduct->populate(
                    $data['PD_ProduitID'],
                    $this->_defaultEditLanguage);
                $itemReq = $oItemReq->getByProductId($data['PD_ID']);

                foreach ($itemReq as $itemData)
                {
                    $key = $itemData['ID_ID'];
                    $item[$key] = $oItem->populate(
                        $itemData['ID_ItemID'],
                        $this->_defaultEditLanguage);

                    $item[$key]['quantity'] = $itemData['ID_Quantite'];

                    if ($itemData['ID_TailleID'] != 0)
                    {
                        $taille = $oSize->populate(
                            $itemData['ID_TailleID'],
                            $this->_defaultEditLanguage);

                        $category = $oCategory->populate(
                            $taille['T_CategorieTailleId'],
                            $this->_defaultEditLanguage);

                        $item[$key]['size'] = $taille;
                        $item[$key]['sCat'] = $category;

                    }
                }
                $product[$data['PD_ProduitID']]['item'] = $item;
            }

            $render = $this->_renderSummary($product);

            if(count($details) > 0)
            {
                $this->view->assign('quoteDetails', $details);
                $this->view->assign('render', $render);

                if( $this->_request->isPost())
                {
                    $formData = $this->_request->getPost();

                    $Order->save(
                        $quoteId,
                        $formData,
                        $this->_currentEditLanguage);

                    $this->_redirect($redirectTo);
                }

            }
            else
                $this->_redirect($redirectTo);

            $form->populate($details);

        }
        else
            $this->_redirect($redirectTo);
    }

    public function deleteOrderAction(){
        $quoteId = $this->_getParam('ID');
        $redirectTo = "/Order/index/list-quote-requests/";

        if( !empty($quoteId) ) {

            $Order = new DemandeSoumissionObject();
            $details = $Order->populate(
                $quoteId,
                $this->_currentEditLanguage);

            if(count($details))
            {
                $this->view->assign('quoteDetails', $details);
            }
            else
                $this->_redirect($redirectTo);


        } else
            $this->_redirect($redirectTo);

        if( $this->_request->isPost()){
            $del = $this->_request->getPost('delete');

            if ($del && $quoteId > 0)
            {
                $oItem = new ItemDemandeObject();
                $oProd = new ProduitDemandeObject();

                $oItem->deleteByDemandeId($quoteId);
                $oProd->deleteByDemandeId($quoteId);

                $Order->delete($quoteId);
                $this->_redirect($redirectTo);
            }

            $cancel = $this->_request->getPost('cancel');

            if($cancel)
                $this->_redirect($redirectTo);
        }
    }

    public function exportOrderAction()
    {
        $this->_firstAction = $this->_defaultAction;
        $this->_exportCsv  = false;
        $this->_tablesList = array();

        if ($this->_request->isPost())
        {
            $OrderId = $this->_getParam('ID');
            $oOrder  = new DemandeSoumissionObject();

            $data = array('DS_Status' => 'closed');

            $oOrder->save($OrderId, $data, 1);

            $relatedId = array('SP_DemandeSoumission' => $OrderId);

            $relatedId['SP_ProduitDemande'] = $OrderId;
            $relatedId['SP_ItemDemande']    = $OrderId;

            $oRequestedproduct = new ProduitDemandeObject();
            //Get requested products for this quote request
            $qryReqProd = $oRequestedproduct->getByDemandeId($OrderId);

            foreach ($qryReqProd as $data)
            {
                $relatedId['SP_Produit'][] = $data['PD_ProduitID'];
                $relatedId['SP_Piece'][]   = $data['PD_ProduitID'];
            }

            $this->relatedId = $relatedId;
        }

        $this->addColumnsLabel = true;
        $this->_ftpTransfer    = true;

        parent::toCsvAction();

        $this->render('to-csv');

    }

    public function addAction(){
        throw new Exception('Not implemented');
    }

    public function editAction(){
        throw new Exception('Not implemented');
    }

    public function deleteAction(){
        throw new Exception('Not implemented');
    }

    /**
     * Create the html code to render a quote request summary.
     *
     * @param array $product
     * @param array $item
     *
     * @return string
     */
    protected function _renderSummary($products = array())
    {
        (string) $render = "";

        if (count($products) == 0)
        {
            $render = "Pas de produit dans cette demande.";
        }
        else
        {
            foreach ($products as $product)
            {
                $render .= "<p class='titleproductQR'>Produit : " . $product['P_Description'] . "</p>" . chr(13);
                $render .= "<table class='list'>" . chr(13);
                $render .= "<tr>" . chr(13);
                $render .= "<th>" . chr(13);
                $render .= "Item" . chr(13);
                $render .= "</th>" . chr(13);
                $render .= "<th>" . chr(13);
                $render .= "Categorie" . chr(13);
                $render .= "</th>" . chr(13);
                $render .= "<th>" . chr(13);
                $render .= "Taille" . chr(13);
                $render .= "</th>" . chr(13);
                $render .= "<th class='quantity'>" . chr(13);
                $render .= "Quantité" . chr(13);
                $render .= "</th>" . chr(13);
                $render .= "</tr>" . chr(13);

                $line = 1;

                foreach ($product['item'] as $item)
                {
                    $trStyle = '';
                    $category = "undef";
                    $size     = "undef";

                    if ($line % 3 == 0)
                        $trStyle = 'class="bottomLine"';

                    $render .= "<tr ". $trStyle .">" . chr(13);
                    $render .= "<td>" . chr(13);
                    $render .= $item['II_Nom'] . chr(13);
                    $render .= "</td>" . chr(13);
                    $render .= "<td>" . chr(13);

                    if (!empty($item['sCat']))
                        $category = $item['sCat']['CTI_Nom'];
                    if (!empty($item['size']))
                        $size = $item['size']['TI_Nom'];
                    $render .=  $category . chr(13);
                    $render .= "</td>" . chr(13);
                    $render .= "<td>" . chr(13);
                    $render .=  $size . chr(13);
                    $render .= "</td>" . chr(13);
                    $render .= "<td class='quantity'>" . chr(13);
                    $render .=  $item['quantity'] . chr(13);
                    $render .= "</td>" . chr(13);
                    $render .= "</tr>" . chr(13);

                    $line++;
                }

                $render .= "</table>" . chr(13);
            }
        }

        return $render;
    }

    public function viewOrderAction()
    {
        // web page title
        $this->view->title = "Résumé de la commande";
        $orderId = $this->_getParam('ID');
        $redirectTo = "/order/index/list-orders/";

        if( !empty($orderId) )
        {
            $details = array();

            $oOrder = new OrderObject();
            $details = $oOrder->populate(
                $orderId,
                $this->_defaultEditLanguage);
            $form = new FormViewOrder(
                array(
                    'cancelUrl' => "{$this->view->baseUrl()}$redirectTo",
                    'disabledSaveAction' => true,
                    'data' => $details
            ));

            $this->view->assign('form', $form);
        }
    }

    public function importTaxFileAction()
    {
        $this->_helper->viewRenderer->setNoRender(true);
        // Defines the list of tables to receive data
        $this->_tablesList = array('Catalog_TaxeZone');
        // Define the prefix used to define table/file to format object name
        $this->_prefix = 'Catalog_';
        // Process to import action
        $this->importAction();
        // Update Edith tax table
        $oTaxes = new TaxesObject();
        $oTaxes->importTaxes();
        // Delete tax file at the end of the process
        //Get the config file data
        $config = Zend_Registry::get('config');
        //Defines path to the folder containing files to import
        $this->_importFilesFolder = $this->_exportFilesFolder;
        $filePath  = $this->_exportFilesFolder . $config->taxFileName;
        $nameForDb = $this->_exportFilesFolder . $config->taxTableName;
        unlink($filePath);
        unlink($nameForDb);
        // Redirect current action to default index page.
        // There's no need to set a view for this action
        $this->_redirect();

    }

    public function newTaxesAction()
    {

        //Get the config file data
        $config = Zend_Registry::get('config');
        //Defines path to the folder containing files to import
        $this->_importFilesFolder = $this->_exportFilesFolder;
        $filePath  = $this->_exportFilesFolder . $config->taxFileName;
        $nameForDb = $this->_exportFilesFolder . $config->taxTableName;
        //Real path to the import folder, to be set for the online server
        //$filePath = $this->_importFilesFolder . '/' . $config->taxFileName;
        // Find if the file to update taxes exists
        $newFile = file_exists($filePath);
        if($newFile)
        {
            //renameFile to match with database schema
            copy($filePath, $nameForDb);
            //Update the list of files to import
            $this->_fileToProcess = $config->taxTableName;
            $this->_findCsvFiles('list');

            $this->view->assign('newTaxes', $newFile);
        }
    }
}