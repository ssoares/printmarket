<?php

class Newsletter_IndexController extends Cible_Controller_Categorie_Action
{
    protected $_moduleID = 8;
    protected $_defaultAction = 'list';
    protected $_moduleTitle = 'newsletter';
    protected $_stats = array();

    public function getManageDescription($blockID = null)
    {
        $baseDescription = parent::getManageDescription($blockID);

        $listParams = $baseDescription;

        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        if ($blockParameters)
        {
            $blockParams = $blockParameters->toArray();

            // Catégorie
            $categoryID = $blockParameters[0]['P_Value'];
            $categoryDetails = Cible_FunctionsCategories::getCategoryDetails($categoryID);
            $categoryName = $categoryDetails['CI_Title'];
            $listParams .= "<div class='block_params_list'><strong>Infolettre : </strong>" . $categoryName . "</div>";
        }

        return $listParams;
    }

    public function getIndexDescription($blockID = null)
    {
        $baseDescription = parent::getManageDescription($blockID);

        $listParams = $baseDescription;

        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        if ($blockParameters)
        {
            $blockParams = $blockParameters->toArray();

            // Catégorie
            $categoryID = $blockParameters[0]['P_Value'];
            $categoryDetails = Cible_FunctionsCategories::getCategoryDetails($categoryID);
            $categoryName = $categoryDetails['CI_Title'];
            $listParams .= "<div class='block_params_list'><strong>Infolettre : </strong>" . $categoryName . "</div>";
        }

        return $listParams;
    }

    public function setOnlineBlockAction()
    {
        parent::setOnlineBlockAction();
    }

    public function listAllAction()
    {
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            $tables = array(
                'Newsletter_Releases' => array('NR_ID', 'NR_Title', 'NR_Date', 'NR_Online', 'NR_Status'),
                'CategoriesIndex' => array('CI_Title'),
                'Status' => array('S_Code')
            );

            $field_list = array(
                'NR_Title' => array(
                //'width' => '400px'
                ),
                'NR_Date' => array(
                    'width' => '120px'
                ),
                'CI_Title' => array(
                    'width' => '100px'
                ),
                'S_Code' => array(
                    'width' => '80px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'status_'
                    )
                ),
                'NR_Status' => array(
                    'width' => '80px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'send_'
                    )
                )
            );

            //get all releases
            $releasesSelect = new NewsletterReleases();
            $select = $releasesSelect->getQuery();
            // Check newsletters status from external tool
            $releasesSelect->setConfig($this->_config)->checkExternalStatus();
            $options = array(
                'commands' => array(
                    $this->view->link(
                        $this->view->url(array('controller' => 'index', 'action' => 'add')),
                        $this->view->getCibleText('button_add_newsletter'),
                        array('class' => 'action_submit add')
                    )
                ),
                'disable-export-to-excel' => 'true',
                'filters' => array(
                    'newsletter-code-filter' => array(
                        'label' => 'Filtre 1',
                        'default_value' => null,
                        'associatedTo' => 'S_Code',
                        'choices' => array(
                            '' => $this->view->getCibleText('filter_empty_diffusion'),
                            'online' => $this->view->getCibleText('status_online'),
                            'offline' => $this->view->getCibleText('status_offline')
                        )
                    ),
                    'newsletter-status-filter' => array(
                        'label' => 'Filtre 2',
                        'default_value' => null,
                        'associatedTo' => 'NR_Status',
                        'choices' => array(
                            '' => $this->view->getCibleText('filter_empty_status'),
                            1 => $this->view->getCibleText('send_1'),
                            0 => $this->view->getCibleText('send_2')
                        )
                    )
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => "{$this->view->baseUrl()}/newsletter/index/edit/newsletterID/%ID%/",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'NR_ID'
                            )
                        ),
                        'duplicate' => array(
                            'label' => $this->view->getCibleText('button_copy'),
                            'url' => "{$this->view->baseUrl()}/newsletter/index/duplicate/releaseId/%ID%/",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'NR_ID'
                            )
                        ),
                        'statistics' => array(
                            'label' => $this->view->getCibleText('button_stats'),
                            'url' => "{$this->view->baseUrl()}/newsletter/statistic/index/releaseId/%ID%/",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'NR_ExternalId'
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => "{$this->view->baseUrl()}/newsletter/index/delete/newsletterID/%ID%/",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'NR_ID'
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

            $this->view->assign('mylist', $mylist);
        }
    }

    public function addAction()
    {
        // web page title
        $this->view->title = "Ajout d'une publication";
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            // variables
            $pageID = $this->_getParam('pageID');
            $blockID = $this->_getParam('blockID');
            $baseDir = $this->view->baseUrl();

            // generate the form
            if (empty($pageID) && empty($blockID))
            {
                $cancelUrl = "$baseDir/newsletter/index/list-all/";
                $returnUrl = "/newsletter/index/list-all/";
            }
            else
            {
                $cancelUrl = "$baseDir/newsletter/index/list/blockID/$blockID/pageID/$pageID";
                $returnUrl = "/newsletter/index/list/blockID/$blockID/pageID/$pageID";
            }

            $form = new FormNewsletter(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => $cancelUrl
                ));

            $form->getElement('NR_TextIntro')->setValue($this->view->getClientText('infolettre_text_salutation'));
            $this->view->form = $form;

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $oExtM = new Cible_Newsletters_Mailings();
                    $oExtM->setConfig($this->_config)->setParameters();
                    $externalId = (int)$oExtM->addMailing($formData['NR_Title'])
                        ->getResults();
                    if ($formData['NR_Online'] == 0)
                        $formData['NR_Online'] = 2;

                    $newsletterRelease = new NewsletterReleases();
                    $newsletterReleaseData = $newsletterRelease->createRow();
                    $newsletterReleaseData->NR_ExternalId = $externalId;
                    $newsletterReleaseData->NR_LanguageID = $formData['NR_LanguageID'];
                    $newsletterReleaseData->NR_CategoryID = $formData['NR_CategoryID'];
                    $newsletterReleaseData->NR_ModelID = $formData['NR_ModelID'];
                    $newsletterReleaseData->NR_Title = $formData['NR_Title'];
                    $newsletterReleaseData->NR_AdminEmail = $formData['NR_AdminEmail'];
                    $newsletterReleaseData->NR_TextIntro    = $formData['NR_TextIntro'];
                    $newsletterReleaseData->NR_ValUrl = Cible_FunctionsGeneral::formatValueForUrl($formData['NR_Title']);
                    $newsletterReleaseData->NR_Date = $formData['NR_Date'];
                    $newsletterReleaseData->NR_Online = $formData['NR_Online'];
                    $newsletterReleaseData->NR_AfficherTitre = $formData['NR_AfficherTitre'];

                    $newsletterReleaseData->save();

                    mkdir($this->_imagesFolder . $newsletterReleaseData->NR_ID) or die("Could not make directory");
                    mkdir($this->_imagesFolder . "{$newsletterReleaseData->NR_ID}/tmp") or die("Could not make directory");
                    // redirect
                    $returnUrl = "/newsletter/index/edit/newsletterID/{$newsletterReleaseData['NR_ID']}";
                    $this->_redirect($returnUrl);
                }
                else
                {
                    $form->populate($formData);
                }
            }
        }
    }

    public function duplicateAction()
    {
        $this->view->title = "Copie d'une infolettre";
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            // variables
            $newsletterID = $this->_getParam('releaseId');
            $baseDir = $this->view->baseUrl();
            // generate the form
            $cancelUrl = "/newsletter/index/list-all";
            $editUrl = "/newsletter/index/edit-info/blockID//pageID//newsletterID/";
            $form = new FormNewsletterCopy(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => $baseDir . $cancelUrl
                ));
            $newsletterSelect = new NewsletterReleases();
            $newsletterData = $newsletterSelect->setAction('edit')
                ->setLangId($this->_defaultEditLanguage)
                ->getReleaseData($newsletterID);

            $newsletterArticlesSelect = new NewsletterArticles();
            $select = $newsletterArticlesSelect->select();
            $select->where('NA_ReleaseID = ?', $newsletterID)
                ->order('NA_ZoneID')
                ->order('NA_PositionID');

            $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
            $this->view->articles = $newsletterArticlesData->toArray();
            foreach($newsletterData->toArray() as $key => $value)
            {
                if (preg_match('/^NR_/', $key)){
                    $data[$key] = $value;
                }
            }
            $data['NR_Online'] = 2;
            $data['NR_Date'] = '';
            $data['NR_ExternalId'] = '';
            $data['NR_Status'] = 0;
            $data['NR_MailingDateTimeScheduled'] = '0000-00-00 00:00:00';
            $data['NR_MailingDateTimeStart'] = '0000-00-00 00:00:00';
            $data['NR_MailingDateTimeEnd'] = '0000-00-00 00:00:00';
            $data['NR_SendTo'] = 0;
            $data['NR_TargetedTotal'] = 0;
            $data['NR_CollectionFiltersID'] = 0;
            unset($data['NR_ID']);
            if ($this->_request->isPost())
            {
                $tmpPost = $this->_request->getPost();
                $formData = array_merge($data, $tmpPost);

                if ($form->isValid($formData))
                {
                    $oExtM = new Cible_Newsletters_Mailings();
                    $oExtM->setConfig($this->_config)->setParameters();
                    $externalId = (int)$oExtM->addMailing($formData['NR_Title'])
                        ->getResults();
                    $newsletterRelease = new NewsletterReleases();
                    $newsletterReleaseData = $newsletterRelease->createRow();
                    $newsletterReleaseData->setFromArray($formData);
                    $newsletterReleaseData->NR_ExternalId = $externalId;
                    $newsletterReleaseData->NR_ValUrl = Cible_FunctionsGeneral::formatValueForUrl($formData['NR_Title']);
                    $newsletterReleaseData->save();

                    // $newsletterReleaseData->NR_ID
                    mkdir($this->_imagesFolder . $newsletterReleaseData->NR_ID) or die("Could not make directory");
                    mkdir($this->_imagesFolder . "{$newsletterReleaseData->NR_ID}/tmp") or die("Could not make directory");


                    $this->recurse_copy($this->_imagesFolder .   $newsletterID, $this->_imagesFolder .  $newsletterReleaseData->NR_ID);


                    foreach($newsletterArticlesData->toArray() as $key => $article)
                    {
                        $newsletterArticle = new NewsletterArticles();
                        $newsletterArtData = $newsletterArticle->createRow();
                        $newsletterArtData->setFromArray($article);
                        $newsletterArtData->NA_ID = null;
                        $newsletterArtData->NA_ReleaseID = $newsletterReleaseData->NR_ID;
                        $id = $newsletterArtData->save();

                        rename( $this->_imagesFolder . $newsletterReleaseData->NR_ID . "/" . $article['NA_ID'], $this->_imagesFolder . $newsletterReleaseData->NR_ID . "/" . $id );

                    }

                    $this->_redirect($editUrl . $newsletterReleaseData->NR_ID);
                }
            }
            else
            {
                $data = $newsletterData->toArray();
                $form->populate($data);

                $this->view->form = $form;
            }
        }
    }


    public function recurse_copy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }


    public function editAction()
    {
        $this->view->title = "Modification d'une infolettre";
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            // variables
            $pageID = $this->_getParam('pageID');
            $blockID = $this->_getParam('blockID');
            $newsletterID = $this->_getParam('newsletterID');
            $baseDir = $this->view->baseUrl();
            $baseDirWeb = str_replace("extranet", "", $baseDir);

            $base = substr($baseDir, 0, strpos($baseDir, "/{$this->_config->document_root}/"));
            $this->view->headScript()->appendFile($baseDir . '/js/csa/overlay.js');
            $this->view->headScript()->appendFile($baseDir . '/js/jquery.json-1.3.min.js');

            $this->view->ajaxLink = "$baseDir/newsletter/index/sendemail-test";

            $newsletterSelect = new NewsletterReleases();
            $newsletterData = $newsletterSelect->setAction('edit')
                ->setLangId($this->_defaultEditLanguage)
                ->getReleaseData($newsletterID);

            $this->view->allowEdit = in_array($newsletterData['NR_Status'], array(0,3))  ? true : false;


            $this->view->newsletterData = $newsletterData;
            $this->view->assign('sentOn', $newsletterData['NR_MailingDateTimeStart'] != '0000-00-00 00:00:00' ? $newsletterData['NR_MailingDateTimeStart'] : '');
            $this->view->assign('sendTo', $newsletterData['NR_SendTo']);
            $this->view->assign('targetedTotal', $newsletterData['NR_TargetedTotal']);
            $this->view->assign('newsletterID',$newsletterID);
            $this->view->newsletter = $newsletterData->toArray();
            $this->view->editLink = "$baseDir/newsletter/index/edit-info/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
            $this->view->manageRecipientsLink = "$baseDir/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
            $this->view->manageSendLink = "$baseDir/newsletter/index/manage-send/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
            $this->view->showWebLink = $baseDirWeb . "show-web/index/ID/$newsletterID";
            $this->view->showEmailLink = "$baseDir/newsletter/index/show-email/newsletterID/$newsletterID";
            $this->view->pageID = $pageID;

            $newsletterArticlesSelect = new NewsletterArticles();
            $select = $newsletterArticlesSelect->select();
            $select->where('NA_ReleaseID = ?', $newsletterID)
                ->order('NA_ZoneID')
                ->order('NA_PositionID');

            $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
            $this->view->articles = $newsletterArticlesData->toArray();
        }
    }

    public function editInfoAction()
    {
        // web page title
        $this->view->title = "Modification d'une infolettre";

        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            // variables
            $pageID = $this->_getParam('pageID');
            $blockID = $this->_getParam('blockID');
            $newsletterID = $this->_getParam('newsletterID');
            $baseDir = $this->view->baseUrl();

            $newsletterSelect = new NewsletterReleases();
            $select = $newsletterSelect->select();
            $select->where('NR_ID = ?', $newsletterID);
            $newsletterData = $newsletterSelect->fetchRow($select);

            $this->view->assign("sent",$newsletterData['NR_Status']);

            // generate the form
            $cancelUrl = "/newsletter/index/edit/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
            $form = new FormNewsletter(array(
                    'baseDir' => $baseDir,
                    'cancelUrl' => $baseDir . $cancelUrl
                ));
            $form->getElement('NR_Date')->clearValidators()->setRequired(false);
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    $oExtM = new Cible_Newsletters_Mailings();
                    $oExtM->setConfig($this->_config)->setParameters();
                    $newsletterData['NR_LanguageID'] = $form->getValue('NR_LanguageID');
                    $newsletterData['NR_CategoryID'] = $form->getValue('NR_CategoryID');
                    $newsletterData['NR_ModelID'] = $form->getValue('NR_ModelID');
                    $newsletterData['NR_Title'] = $form->getValue('NR_Title');
                    $newsletterData['NR_AdminEmail'] = $form->getValue('NR_AdminEmail');
                    $newsletterData['NR_ValUrl'] = Cible_FunctionsGeneral::formatValueForUrl($form->getValue('NR_Title'));
                   $newsletterData['NR_Date'] = $form->getValue('NR_Date');
                    $newsletterData['NR_TextIntro'] = $form->getValue('NR_TextIntro');
                    $newsletterData['NR_Online'] = $formData['NR_Online'] == 0 ? 2 : 1;
                    $newsletterData['NR_AfficherTitre'] = $form->getValue('NR_AfficherTitre');




                    $newsletterData->save();
                    $oExtM->setId($newsletterData['NR_ExternalId'])
                        ->setData(array(
                            'subject' => $newsletterData['NR_Title'],
                            'name' => $newsletterData['NR_Title']))
                        ->setMailingInfo();

                    $blockData = Cible_FunctionsBlocks::getBlockDetails($blockID);
                    $status = $blockData['B_Online'];

                    if (($newsletterData['NR_Online'] == 1 && $status == 1) || $newsletterData['NR_Online'] == 0)
                    {
                        $indexData['pageID'] = $newsletterData['NR_CategoryID'];
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['contentID'] = $newsletterID;
                        $indexData['languageID'] = $newsletterData['NR_LanguageID'];
                        $indexData['title'] = $newsletterData['NR_Title'];
                        $indexData['text'] = '';
                        $indexData['link'] = $newsletterData['NR_Date'] . '/' . $newsletterData['NR_ValUrl'];
                        $indexData['object']  = 'NewsletterObject';
                        $indexData['contents'] = $newsletterData['NR_Title'];

                        Cible_FunctionsIndexation::indexation($indexData);

                        // get all article in the release
                        $articlesSelect = new NewsletterArticles();
                        $select = $articlesSelect->select()
                                ->where('NA_ReleaseID = ?', $newsletterID);
                        $articlesData = $articlesSelect->fetchAll($select);

                        $indexData['pageID'] = $newsletterData['NR_CategoryID'];
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['languageID'] = $newsletterData['NR_LanguageID'];
                        foreach ($articlesData as $article)
                        {
                            if ($newsletterData['NR_Online'] == 1)
                            {
                                $indexData['contentID'] = $newsletterID;
                                $indexData['title'] = $article['NA_Title'];
                                $indexData['text'] = Cible_FunctionsGeneral::stripTextWords(Cible_FunctionsGeneral::html2text($article['NA_Resume']));
                                $indexData['link'] = $newsletterData['NR_Date'] . '/' . $newsletterData['NR_ValUrl'] . '/' . $article['NA_ValUrl'];
                                $indexData['object']  = 'NewsletterObject';
                                $indexData['contents'] = Cible_FunctionsGeneral::html2text($article['NA_Resume'] . "<br/>" . $article['NA_Text']);
                                $indexData['action'] = 'update';
                            }
                            elseif ($newsletterData['NR_Online'] == 0)
                            {
                                $indexData['action'] = 'delete';
                            }
                            Cible_FunctionsIndexation::indexation($indexData);
                        }
                    }
                }
                $this->_redirect($cancelUrl);
            }
            else
            {
                $data = $newsletterData->toArray();
                if ($data['NR_Online'] == 2)
                    $data['NR_Online'] = 0;

                $form->populate($data);

                $this->view->form = $form;
            }
        }
    }

    public function deleteAction()
    {
        // web page title
        $this->view->title = "Suppression d'une parution";

        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            // variables
            $pageID = (int) $this->_getParam('pageID');
            $blockID = (int) $this->_getParam('blockID');
            $newsletterID = (int) $this->_getParam('newsletterID');

            // generate the form
            if (empty($pageID) && empty($blockID))
                $returnUrl = "/newsletter/index/list-all/";
            else
                $returnUrl = "/newsletter/index/list/blockID/$blockID/pageID/$pageID";

            $this->view->assign('return', "{$this->view->baseUrl()}{$returnUrl}");

            $newsletterSelect = new NewsletterReleases();
            $select = $newsletterSelect->select();
            $select->where('NR_ID = ?', $newsletterID);
            $newsletterData = $newsletterSelect->fetchRow($select);

            $this->view->newsletter = $newsletterData->toArray();

            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $newsletterData)
                {
                    // get all article in the release
                    $articlesSelect = new NewsletterArticles();
                    $select = $articlesSelect->select()
                            ->where('NA_ReleaseID = ?', $newsletterID);
                    $articlesData = $articlesSelect->fetchAll($select);

                    $indexData['pageID'] = $pageID;
                    $indexData['moduleID'] = 8;
                    $indexData['languageID'] = $newsletterData['NR_LanguageID'];
                    $indexData['action'] = 'delete';

                    foreach ($articlesData as $article)
                    {
                        $indexData['contentID'] = $article['NA_ID'];
                        Cible_FunctionsIndexation::indexation($indexData);
                    }

                    $oExtM = new Cible_Newsletters_Mailings();
                    $oExtM->setConfig($this->_config)->setParameters()
                        ->setId($newsletterData['NR_ExternalId'])
                        ->delMailing();
                    $newsletterData->delete();
                    $newsletterArticleDelete = new NewsletterArticles();
                    $where = "NA_ReleaseID = " . $newsletterID;
                    $newsletterArticleDelete->delete($where);

                    Cible_FunctionsGeneral::delFolder($this->_imagesFolder . $newsletterID);
                }

                $this->_redirect($returnUrl);
            }
        }
    }

    public function listRecipientsAction()
    {

        $searchfor = $this->_request->getParam('searchfor');
        $filters['newsletter_categories'] = $this->_request->getParam('filter_1');
        if ($filters['newsletter_categories'] == '')
            $filters = '';

        $profile = new NewsletterProfile();

        $select = $profile->getSelectStatement();
        $select->joinLeft('ReferencesIndex', 'NP_TypeID = RI_RefId', array('RI_Value'))
            ->where('RI_LanguageID = ?', $this->_defaultEditLanguage)
            ->where('NP_Categories <> ?', 0)
            ->where('NP_Categories <> ?', "");
        $newsletterCategories = $this->view->getAllNewsletterCategories();
        $newsletterCategories = $newsletterCategories->toArray();
        $listCat = array('' => 'Toutes les infolettres');
        foreach ($newsletterCategories as $cat)
        {
            $listCat[$cat['C_ID']] = $cat['CI_Title'];
        }
        $oRef = new ReferencesObject();
        $listType = $oRef->getListValues('typeClient', $this->_defaultEditLanguage, true);
        $tables = array(
            'GenericProfiles' => array('GP_lastName', 'GP_firstName', 'GP_Email'),
            'NewsletterProfiles' => array('NP_Categories'),
            'ReferencesIndex' => array('RI_Value')
        );

        $field_list = array(
            'lastName' => array('width' => '150px'),
            'firstName' => array('width' => '150px'),
            'email' => array(),
            'RI_Value' => array('label' => $this->view->getCibleText('form_enum_typeClient'))
        );

        $options = array(
            'commands' => array(
                $this->view->link(
                    $this->view->url(
                        array(
                            'module' => 'users',
                            'controller' => 'index',
                            'action' => 'general',
                            'actionKey'=>'add',
                            'returnModule' => 'newsletter',
                            'returnAction' => 'list-recipients')), $this->view->getCibleText('button_add_profile'), array('class' => 'action_submit add'))
            ),
            'disable-export-to-excel' => '',
            'filters' => array(
//                'filter_1' => array(
//                    'label' => 'Filtre 1',
//                    'default_value' => null,
//                    'associatedTo' => 'NP_Categories',
//                    'kindOfFilter' => 'list',
//                    'choices' => $listCat
//                ),
                'filter_2' => array(
                    'label' => $this->view->getCibleText('form_enum_typeClient'),
                    'default_value' => null,
                    'associatedTo' => 'NP_TypeID',
                    'kindOfFilter' => 'list',
                    'choices' => $listType
                )
            ),
            'action_panel' => array(
                'width' => '50',
                'actions' => array(
                    'edit' => array(
                        'label' => $this->view->getCibleText('menu_submenu_action_edit'),
                        'url' => $this->view->url(array('module' => 'users', 'action' => 'general', 'actionKey'=>'edit', 'id' => "-ID-", 'returnModule' => 'newsletter', 'returnAction' => 'list-recipients')),
                        'findReplace' => array(
                            'search' => '-ID-',
                            'replace' => 'member_id'
                        )
                    ),
                    'delete' => array(
                        'label' => $this->view->getCibleText('menu_submenu_action_delete'),
                        'url' => $this->view->url(array('module' => 'users', 'action' => 'general', 'actionKey'=>'delete', 'id' => "-ID-", 'returnModule' => 'newsletter', 'returnAction' => 'list-recipients')),
                        'findReplace' => array(
                            'search' => '-ID-',
                            'replace' => 'member_id'
                        )
                    )
                )
            )
        );

        $mylist = New Cible_Paginator($select, $tables, $field_list, $options);
        $this->view->assign('mylist', $mylist);
    }

    public function manageRecipientsAction()
    {
        // web page title
        $this->view->title = "Gestion des destinataires";


        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            $blockID = (int) $this->_getParam('blockID');
            $pageID = (int) $this->_getParam('pageID');
            $newsletterID = (int) $this->_getParam('newsletterID');
            $orderField = $this->_getParam('orderField');
            $orderParam = $this->_getParam('orderParam');
            $tablePage = $this->_getParam('tablePage');
            $search = $this->_getParam('search');
            $nbByPage = 5;

            if ($orderField == "")
            {
                $orderField = 'lastName';
                $orderParam = 'asc';
            }
            elseif ($orderParam == "")
            {
                $orderParam = 'asc';
            }

            if ($tablePage == "")
                $tablePage = 1;

            $this->view->addRecipientLink = $this->view->baseUrl() . "/newsletter/recipient/add/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";

            $newsletterSelect = new NewsletterReleases();
            $select = $newsletterSelect->select();
            $select->where('NR_ID = ?', $newsletterID);
            $newsletterData = $newsletterSelect->fetchRow($select);

            $profile = new NewsletterProfile();
            //$profile->updateMember(9,array('newsletter_categories'=>'1'));
            //$profileProperties = $profile->getProfileProperties();


            $sort[0]['field'] = $orderField;
            $sort[0]['param'] = $orderParam;

            if ($search <> "")
                $members = $profile->findMembers(array('newsletter_categories' => $newsletterData['NR_CategoryID'], 'lastName' => $search));
            else
                $members = $profile->findMembers(array('newsletter_categories' => $newsletterData['NR_CategoryID']));

            $nbMembers = count($members);
            //$nbMembers = 0;
            $this->view->memberCount = $nbMembers;

            if ($nbMembers > 0)
            {
                $i = 0;
                foreach ($members as $member)
                {
                    //$membersDetails[$i] = $profile->getMemberDetails($member['MemberID']);
                    $membersDetails[$i] = $profile->getMemberDetails($member);
                    $i++;
                }
                $membersDetails = $this->subval_sort($membersDetails, $sort);
            }


            $tableTitle = "Liste des destinataires";

            $tableTH[0]["Title"] = "Nom";
            $tableTH[0]["OrderField"] = "lastName";
            $tableTH[1]["Title"] = "Envoi";
            $tableTH[2]["Title"] = "Actions";

            if ($nbMembers > 0)
            {
                $i = 0;
                foreach ($tableTH as $TH)
                {
                    if (array_key_exists("OrderField", $TH))
                    {
                        if ($orderField == $TH["OrderField"])
                        {
                            $tableTH[$i]["Order"] = strtolower($orderParam);
                        }
                        else
                        {
                            $tableTH[$i]["Order"] = "asc";
                        }

                        if ($tableTH[$i]["Order"] == "asc")
                            $orderParamTH = "desc";
                        else
                            $orderParamTH = "asc";

                        $tableTH[$i]["OrderLink"] = $this->view->baseUrl() . "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID/orderField/" . $tableTH[$i]['OrderField'] . "/orderParam/" . $orderParamTH;
                    }
                    $i++;
                }

                $nbTablePage = ceil(count($members) / $nbByPage);
                if ($tablePage > $nbTablePage || $tablePage < 1)
                    $tablePage = 1;

                $startMember = ($tablePage - 1) * $nbByPage;
                $endMember = ($tablePage * $nbByPage) - 1;

                if ($endMember >= count($membersDetails))
                    $endMember = count($membersDetails) - 1;


                for ($i = $startMember; $i <= $endMember; $i++)
                {

                    $tableRows[$i][0] = $membersDetails[$i]['lastName'] . " " . $membersDetails[$i]['firstName'] . "<br>" . $membersDetails[$i]['email'];
                    $tableRows[$i][1] = "---";
                    $tableRows[$i][2] = '<a href="' . $this->view->baseUrl() . '/newsletter/recipient/edit/blockID/' . $blockID . '/pageID/' . $pageID . '/newsletterID/' . $newsletterID . '/recipientID/' . $membersDetails[$i]['memberID'] . '"><img class="action_icon" alt="Editer" src="' . $this->view->baseUrl() . '/icons/edit_icon_16x16.png"/></a>&nbsp;&nbsp';
                    $tableRows[$i][2] .= '<a href="' . $this->view->baseUrl() . '/newsletter/recipient/delete/blockID/' . $blockID . '/pageID/' . $pageID . '/newsletterID/' . $newsletterID . '/recipientID/' . $membersDetails[$i]['memberID'] . '"><img class="action_icon" alt="Supprimer" src="' . $this->view->baseUrl() . '/icons/del_icon_16x16.png"/></a>';
                }
                $listLink = $this->view->baseUrl() . "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $search = array('searchLink' => $listLink, 'searchText' => $search, 'searchCount' => $nbMembers);
                $list = array('caption' => $tableTitle, 'thArray' => $tableTH, 'rowsArray' => $tableRows);
                $navigation = array('tablePage' => $tablePage, 'navigationLink' => $listLink, 'nbTablePage' => $nbTablePage);
                $this->view->htmltable = Cible_FunctionsGeneral::generateHtmlTableV2($search, $list, $navigation);
            }
            else
            {
                $listLink = $this->view->baseUrl() . "/newsletter/index/manage-recipients/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $search = array('searchLink' => $listLink, 'searchText' => $search, 'searchCount' => $nbMembers);
                $this->view->htmltable = Cible_FunctionsGeneral::generateHtmlTableV2($search, '', '');
            }
        }
    }

    public function showWebAction()
    {
        $this->view->title = "Aperçu de l'infolettre";
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {
            $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
            $this->view->assign('success', false);

            if ($this->_request->isPost())
            {
                $this->view->assign('success', true);
            }
            else
            {

                $newsletterID = $this->_getParam('newsletterID');
                $articleID = $this->_getParam('articleID');

                $this->view->newsletterID = $newsletterID;
                $this->view->articleID = $articleID;



                // release info
                $newsletterSelect = new NewsletterReleases();
                $select = $newsletterSelect->select()->setIntegrityCheck(false);
                $select->from('Newsletter_Releases')
                    ->join('Languages', 'L_ID = NR_LanguageID')
                    ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID')
                    ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID')
                    ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID')
                    ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
                    ->where('NMI_LanguageID = ?', Zend_Registry::get("languageID"))
                    ->where('NR_ID = ?', $newsletterID);
                $newsletterData = $newsletterSelect->fetchRow($select);

                $this->view->template = $newsletterData['NM_DirectoryWeb'];

                $newsletterTextIntro = $newsletterData['NR_TextIntro'];
                $newsletterTextIntro = str_replace('##prenom##', 'Prénom Test', $newsletterTextIntro);
                $newsletterTextIntro = str_replace('##nom##', 'Nom Test', $newsletterTextIntro);
                $newsletterTextIntro = str_replace('##salutation##', 'Salutation Test', $newsletterTextIntro);
                $this->view->intro = $newsletterTextIntro;

                $this->view->newsletterTitle = $newsletterData['NR_Title'];


                if ($articleID <> '')
                {
                    // articles info
                    $newsletterArticlesSelect = new NewsletterArticles();
                    $select = $newsletterArticlesSelect->select();
                    $select->where('NA_ID = ?', $articleID);
                    $newsletterArticlesData = $newsletterArticlesSelect->fetchRow($select);

                    $this->view->article = $newsletterArticlesData->toArray();
                }
                else
                {


                    // articles info
                    $newsletterArticlesSelect = new NewsletterArticles();
                    $select = $newsletterArticlesSelect->select();
                    $select->where('NA_ReleaseID = ?', $newsletterID)
                        ->order('NA_ZoneID')
                        ->order('NA_PositionID');
                    $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);

                    $this->view->articles = $newsletterArticlesData->toArray();
                }



                $registry = Zend_Registry::getInstance()->set('format', 'web');
            }
        }
    }

    public function showEmailAction()
    {
        $this->view->title = "Aperçu de l'infolettre";
        if ($this->view->aclIsAllowed('newsletter', 'manage', true))
        {

            $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
            $this->view->assign('success', false);


            if ($this->_request->isPost())
            {
                $this->view->assign('success', true);
            }
            else
            {
                $newsletterID = $this->_getParam('newsletterID');


                  $newsletterSelect = new NewsletterReleases();
                $select = $newsletterSelect->select()->setIntegrityCheck(false)
                        ->from('Newsletter_Releases')
                        ->join('Newsletter_Models', 'NM_ID = NR_ModelID')
                        ->where('NR_ID = ?', $newsletterID);
                $newsletterData = $newsletterSelect->fetchRow($select);

                $releaseLanguage = $newsletterData['NR_LanguageID'];
                $langSuffix = Cible_FunctionsGeneral::getLocalForLanguage($releaseLanguage);
                $date = new Zend_Date($newsletterData['NR_Date'],null, $langSuffix);
                $date_string = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_LONG_NO_DAY,'.');
                $date_string_url = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_SQL,'-');

                $this->view->assign('languageRelease', $releaseLanguage);
                $this->view->assign('titleRelease', $newsletterData['NR_Title']);
                $this->view->newsletter = $newsletterData->toArray();
                $this->view->template = $newsletterData['NM_DirectoryEmail'];

                $newsletterTextIntro = $newsletterData['NR_TextIntro'];
                $newsletterTextIntro = str_replace('[GPFirstName]', 'Prénom Test', $newsletterTextIntro);
                $newsletterTextIntro = str_replace('[GPLastName]', 'Nom Test', $newsletterTextIntro);
                $newsletterTextIntro = str_replace('[GPSalutation]', 'Salutation Test', $newsletterTextIntro);
                $this->view->intro = $newsletterTextIntro;

                $sourceHeaderLeft = Zend_Registry::get('absolute_web_root');
                if($releaseLanguage==1){
                    $sourceHeaderLeft .= "/themes/default/images/common/header.png";
                }
                else if($releaseLanguage==2){
                    $sourceHeaderLeft .= "/themes/default/images/common/header.png";
                }
                else{
                    $sourceHeaderLeft .= "/themes/default/images/common/header.png";
                }

                $readMore = Zend_Registry::get('absolute_web_root');
                if($releaseLanguage==1){
                    $readMore .= "/themes/default/images/fr/linkReadMore.jpg";
                }
                else if($releaseLanguage==2){
                    $readMore .= "/themes/default/images/en/linkReadMore.jpg";
                }
                else{
                    $readMore .= "/themes/default/images/fr/linkReadMore.jpg";
                }


                $this->view->linkReadMore = $readMore;

                $this->view->imageHeader = $sourceHeaderLeft;

                $this->view->newsletterTitle = $newsletterData['NR_Title'];

                // articles info
                $newsletterArticlesSelect = new NewsletterArticles();
                $select = $newsletterArticlesSelect->select();
                $select->where('NA_ReleaseID = ?', $newsletterID)
                    ->order('NA_ZoneID')
                    ->order('NA_PositionID');
                $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);

                $this->view->articles = $newsletterArticlesData->toArray();

                $newsletterCategoryID = $newsletterData['NR_CategoryID'];
                $this->view->assign('unsubscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', $this->_moduleID, $releaseLanguage));
                //$this->view->assign('subscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', $this->_moduleID, $releaseLanguage));

                $this->view->assign('subscribeLink', "/" . Cible_FunctionsPages::getPageLinkByIDExtranet(24031));


                $this->view->assign('archiveLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', $this->_moduleID, $releaseLanguage));
                $this->view->assign('details_release', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', $this->_moduleID, $releaseLanguage) . "/ID/" . $newsletterID);
                $this->view->assign('details_release', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', $this->_moduleID, $releaseLanguage) . "/"  . $date_string_url . "/" . $newsletterData['NR_ValUrl']);
                $this->view->assign('details_page', Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_article', $this->_moduleID, $releaseLanguage));
                $this->view->assign('newsletterID', $newsletterID);
                $this->view->assign('memberId', 1);
                $this->view->assign('moduleId', $this->_moduleID);
                $this->view->assign('dateString',$date_string);
                $this->view->assign('parutionDate',$date_string_url);
                $this->view->assign('parutionValUrl', $newsletterData['NR_ValUrl']);
                $this->view->assign('isOnline', $newsletterData['NR_Online']);

                $registry = Zend_Registry::getInstance()->set('format', 'email');
            }
        }
    }

    public function sendemailTestAction()
    {
        // get the email
        $releaseID = $_REQUEST['releaseID'];
        $email = $_REQUEST['email'];

        $newsletterSelect = new NewsletterReleases();
        $select = $newsletterSelect->select()->setIntegrityCheck(false)
                ->from('Newsletter_Releases')
                ->join('Newsletter_Models', 'NM_ID = NR_ModelID')
                ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID')
                ->where('NMI_LanguageID = NR_LanguageID')
                ->where('NR_ID = ?', $releaseID);

        $newsletterData = $newsletterSelect->fetchRow($select);

        $releaseLanguage = $newsletterData['NR_LanguageID'];
        $langSuffix = Cible_FunctionsGeneral::getLocalForLanguage($releaseLanguage);
        $date = new Zend_Date($newsletterData['NR_Date'],null, $langSuffix);
        $date_string = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_LONG_NO_DAY,'.');
        $date_string_url = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_SQL,'-');

        $this->view->assign('languageRelease', $releaseLanguage);

        $this->view->newsletterTitle = $newsletterData['NR_Title'];
        $this->view->titleRelease = $newsletterData['NR_Title'];

        $fromEmail = $newsletterData['NM_FromEmail'];
        $fromName = $newsletterData['NMI_FromName'];
        $subject = $newsletterData['NR_Title'];

        $newsletterArticlesSelect = new NewsletterArticles();
        $select = $newsletterArticlesSelect->select();
        $select->where('NA_ReleaseID = ?', $releaseID)
            ->order('NA_ZoneID')
            ->order('NA_PositionID');
        $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
        $this->view->articles = $newsletterArticlesData->toArray();

        $newsletterCategoryID = $newsletterData['NR_CategoryID'];
        $this->view->assign('memberId', 1);
        $this->view->assign('moduleId', $this->_moduleID);
        $this->view->assign('newsletterID', $releaseID);
        $this->view->assign('dateString',$date_string);
        $this->view->assign('parutionDate',$date_string_url);
        $this->view->assign('unsubscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', $this->_moduleID, $releaseLanguage));
        //$this->view->assign('subscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', $this->_moduleID, $releaseLanguage));
       $this->view->assign('subscribeLink', "/" . Cible_FunctionsPages::getPageLinkByIDExtranet(24031));

        $this->view->assign('archiveLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', $this->_moduleID, $releaseLanguage));
        $this->view->assign('details_release', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', $this->_moduleID, $releaseLanguage) . "/"  . $date_string_url . "/" . $newsletterData['NR_ValUrl'] . '-uid-[SHOWEMAIL]' );
        $this->view->assign('details_page', Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_article', $this->_moduleID, $releaseLanguage));
        $this->view->assign('parutionValUrl', $newsletterData['NR_ValUrl']);
        $this->view->assign('isOnline', $newsletterData['NR_Online']);

        $newsletterAfficherTitre = "";
        $newsletterTextIntro = $newsletterData['NR_TextIntro'];
        $newsletterTextIntro = str_replace('[GPFirstName]', 'Prénom Test', $newsletterTextIntro);
        $newsletterTextIntro = str_replace('[GPLastName]', 'Nom Test', $newsletterTextIntro);
        $newsletterTextIntro = str_replace('[GPSalutation]', 'Salutation Test', $newsletterTextIntro);
        $this->view->intro = $newsletterTextIntro;
        $sourceHeaderLeft = Zend_Registry::get('absolute_web_root');
        if($releaseLanguage==1){
            $sourceHeaderLeft .= "/themes/default/images/common/header.png";
        }
        else if($releaseLanguage==2){
            $sourceHeaderLeft .= "/themes/default/images/common/header.png";
        }
        else{
            $sourceHeaderLeft .= "/themes/default/images/common/header.png";
        }

        $readMore = Zend_Registry::get('absolute_web_root');
        if($releaseLanguage==1){
            $readMore .= "/themes/default/images/fr/linkReadMore.jpg";
        }
        else if($releaseLanguage==2){
            $readMore .= "/themes/default/images/en/linkReadMore.jpg";
        }
        else{
            $readMore .= "/themes/default/images/fr/linkReadMore.jpg";
        }

        $this->view->linkReadMore = $readMore;

        $this->view->imageHeader = $sourceHeaderLeft;

        $this->view->newsletterAfficherTitre = $newsletterAfficherTitre;


        Zend_Registry::getInstance()->set('format', 'email');
        $bodyText = $this->view->render($newsletterData['NM_DirectoryEmail']);
        // Set external tool parameters and send the mail
        $oExtM = new Cible_Newsletters_Mailings();
        $oExtM->setConfig($this->_config)->setParameters()
            ->setId($newsletterData['NR_ExternalId'])
            ->setData(array('html_message' => $bodyText))
            ->setMailingInfo()
            ->setData(array('test_email' => $email, 'test_type' => 'merged'), true)
            ->sendTestMailing();

        echo(Zend_Json::encode(array('action' => 'done', 'response' => $oExtM->getResults())));
    }

    public function sendNewsletterAction()
    {
        $releaseID = $this->_getParam('newsletterID');
        $this->view->assign('releaseID', $releaseID);

        if ($this->_request->isPost())
        {
            $this->_redirect("/newsletter/index/edit/newsletterID/$releaseID");
        }
        elseif(empty($releaseID))
        {
            /*
             * The following part of code is to send data with CRON task.
             * Code not finished. To be implemented in further version.
             */
            $this->disableView();
            $oNewsRelease = new NewsletterReleases();

            $primary = $oNewsRelease->info('primary');

            $select = $oNewsRelease->select()
                    ->from($oNewsRelease->info('name'));

            $nrData = $oNewsRelease->fetchAll($select)->toArray();
            $listSent = array();
            $listDest = array();
            $listIds  = array();
            $db = Zend_Registry::get('db');

            foreach($nrData as $release)
            {
                $scheduled  = $release['NR_MailingDateTimeScheduled'];
                $oDate      = New Zend_Date($scheduled, 'fr');
                $now        = Zend_Date::now('fr')->getTimestamp();
                $timeToSend = $oDate->compareTimestamp($now);

                if ($timeToSend < 1 && (int)$release['NR_Status'] != 1
                    && $oDate->get() > 0
                    && $release['NR_CronSending'] == 0
                    )
                {
                    $oNewsRelease->update(array('NR_CronSending' => 1), $db->quoteInto('NR_ID = ?', $release['NR_ID']));
                    $this->sendMassMailingAction($release['NR_ID']);
                    array_push($listSent, $release['NR_Title']);
                    array_push($listIds, $release['NR_ID']);
                    array_push($listDest, $release['NR_AdminEmail']);
                }
                if (!empty($listDest) || !empty($listSent) || !empty($listIds))
                    $oNewsRelease->update(array('NR_CronSending' => 0), $db->quoteInto('NR_ID = ?', $release['NR_ID']));
            }

        }
    }

    private function _logSending(array $data=array())
    {
        $userId = 0;
        $oLog = new LogObject();

        $idList = implode(',', $data['ids']);

        $strData = array('releaseId' => $idList);

        if ($this->_isXmlHttpRequest)
            $userId = $this->view->user['EU_ID'];

        $data = array(
            'L_ModuleID' => $this->_moduleID,
            'L_UserID' => $userId,
            'L_Action' => 'sent',
            'L_Data' => $strData
        );

        $oLog->writeData($data);

    }
    /**
     * Create and send an email to notify the administrator about the newsletter
     * sending.
     *
     * @param array $data Contains emails and titles of newsletter.
     *
     * @return void
     */
    private function _adminNotification(array $data = array())
    {
        $receivers = array_unique($data['dest']);
        unset($data['dest']);
        $this->view->assign('alert', '');

        $time = Zend_Date::now('fr_CA');
        $message = $this->view->getCibleText(
                        'newsletter_notification_admin_email_message',
                        $this->_defaultInterfaceLanguage);
        $message = str_replace('##NUMBER_OF_NEWSLETTER##', count($data['list']), $message);
        $message = str_replace('##NB_TOTAL_TO_SEND##', $this->_stats['totalToSend'], $message);
        $message = str_replace('##SENDING_ERRORS##', $this->_stats['errors'], $message);

        $this->_emailRenderData['emailHeader'] = $this->_emailRenderData['emailHeader'] = "<img src='"
                            . Zend_Registry::get('absolute_web_root')
                            . "/themes/default/images/fr"
                            . "/{$this->_config->clientLogo->src}' alt='' border='0'>";
        $this->_emailRenderData['footer'] = $this->view->getClientText("email_notification_footer", 1);
        $this->_emailRenderData['message'] = $message;
        $this->_emailRenderData['list'] = $data['list'];

        $this->view->assign('emailRenderData', $this->_emailRenderData);
        $view = $this->getHelper('ViewRenderer')->view;
        $html = $view->render('index/emailNotification.phtml');

        if (count($receivers) > 0 && !empty ($receivers[0]))
        {
            $mail = new Zend_Mail('utf-8');
            $mail->setSubject($this->view->getCibleText(
                            'newsletter_notification_admin_email_subject',
                            $this->_defaultInterfaceLanguage));
            $mail->setBodyHtml($html);

            foreach($receivers as $recipient)
                $mail->addTo($recipient);

            $mail->send();
        }
    }

    /**
     * Sends mass mailing.
     * Prepares data and tests the recipients list before sendig emails.
     *
     * @param int $releaseID The release of the the newsletter to send.
     *
     * @return array Contains data corresponding to the send result.
     */
    public function sendMassMailingAction($releaseID = null)
    {
        $this->disableView();
        if (!$releaseID)
            $releaseID = $_REQUEST['releaseID'];
//            $releaseID  = $this->view->params['releaseID'];
        // 1- Get all newsletter to send
        $newsletterSelect = new NewsletterReleases();
        $newsletterData = $newsletterSelect->setAction('sendMassMailing')
            ->getReleaseData($releaseID);

        foreach ($newsletterData as $release)
        {
            $listSent = array();
            $listDest = array();
            $listIds  = array();
            $mailLog  = array();

            $releaseLanguage = $release['NR_LanguageID'];
            $langSuffix = Cible_FunctionsGeneral::getLocalForLanguage($releaseLanguage);
            $date = new Zend_Date($release['NR_Date'],null, $langSuffix);
            $date_string = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_LONG_NO_DAY,'.');
            $date_string_url = Cible_FunctionsGeneral::dateToString($date,Cible_FunctionsGeneral::DATE_SQL,'-');

            $this->view->assign('languageRelease', $releaseLanguage);
            $this->view->assign('titleRelease', $release['NR_Title']);
            $this->_helper->reports->buildRequest($release['NR_CollectionFiltersID']);
            $count = $this->_helper->reports->countData();
            if ($count > 10000){
                $this->_helper->reports->setLimit(25000, 0);
            }
            $members       = $this->_helper->reports->findData();
            $selection     = $this->_helper->reports->getQuery();
            $dateTimeStart = date('Y-m-d H:i:s');
            $member_count  = $this->_helper->reports->countData();

            $stats = array('action' => 'set',
                'sentTo' => 0,
                'targetedTotal' => 0);

            if ($release['NR_Status'] == 0 || $release['NR_Status'] == 3){
                //Send to all recipient even if they have already received it
                $member_count  = count($members);
            }
            $sendResult = $sublist = $bodyText = '';
            if (!empty($members) && $member_count > 0)
            {
                $newsletterArticlesSelect = new NewsletterArticles();
                $select = $newsletterArticlesSelect->select();
                $select->where('NA_ReleaseID = ?', $release['NR_ID'])
                    ->order('NA_ZoneID')
                    ->order('NA_PositionID');
                $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
                $this->view->articles = $newsletterArticlesData->toArray();

                $langSuffix = Cible_FunctionsGeneral::getLocalForLanguage($releaseLanguage);
                $date = new Zend_Date($release['NR_Date'],null, $langSuffix);
                $date_string = Cible_FunctionsGeneral::dateToString($date, Cible_FunctionsGeneral::DATE_LONG_NO_DAY, '.');
                $date_string_url = Cible_FunctionsGeneral::dateToString($date, Cible_FunctionsGeneral::DATE_SQL, '-');
                $newsletterCategoryID = $release['NR_CategoryID'];
                $this->view->assign('unsubscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', $this->_moduleID, $releaseLanguage));
                //$this->view->assign('subscribeLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', $this->_moduleID, $releaseLanguage));
                $this->view->assign('subscribeLink', "/" . Cible_FunctionsPages::getPageLinkByIDExtranet(24031));

                $this->view->assign('archiveLink', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', $this->_moduleID, $releaseLanguage));
                $this->view->assign('details_release', "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', $this->_moduleID, $releaseLanguage) . "/" . $date_string_url . "/" . $release['NR_ValUrl'] . '-uid-[SHOWEMAIL]' );
                $this->view->assign('details_page', Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_article', $this->_moduleID, $releaseLanguage));
                $this->view->assign('isOnline', $release['NR_Online']);
                $this->view->assign('newsletterID', $release['NR_ID']);
                $this->view->assign('moduleId', $this->_moduleID);
                $this->view->assign('dateString', $date_string);
                $this->view->assign('parutionDate', $date_string_url);
                $this->view->assign('parutionValUrl', $release['NR_ValUrl']);
                $this->view->intro = $release['NR_TextIntro'];
                $bodyText = $this->view->render($release['NM_DirectoryEmail']);
                foreach($members as $key => $member){
                    $tmp[] = '`id` = "' . $member['NP_ExternalId'] . '"';
                }
                $sublist = implode(' OR ', $tmp);
                $mailInfo = array('html_message' => $bodyText,
                    'name' => $release['NR_Title'],
                    'subject' => $release['NR_Title'],
                    'sender_email' => $release['NM_FromEmail'],
                    'sender_name' => $release['NMI_FromName']
                    );
                if (!empty($sublist)){
                    $oList = new Cible_Newsletters_Lists();
                    $subListId = $oList->setConfig($this->_config)->setParameters()
                        ->setData(array('sublist_name' => $this->_helper->reports->getFilename(),
                            'query' => $sublist))
                        ->addSubList()
                        ->getResults();
                    $mailInfo['sublist_id'] = $subListId;
                }
                $sendInfo = array();
                // Set external tool parameters and send the mail
                $oExtM = new Cible_Newsletters_Mailings();
                $sendResult = $oExtM->setConfig($this->_config)->setParameters()
                    ->setId($release['NR_ExternalId'])
                    ->setData($mailInfo)
                    ->setMailingInfo()
                    ->setData($sendInfo, true)
                    ->sendMailing()
                    ->getResults();

                if ($subListId > 0){
//                    $release['NR_SublistId'] = $subListId;
                   // $oList->setId($subListId)->deleteSublist();
                }
            }

            $this->_stats = array(
                'totalToSend' => $member_count,
            );
            $release['NR_MailingDateTimeStart'] = $dateTimeStart;
            $release['NR_MailingDateTimeEnd'] = date('Y-m-d H:i:s');
            $release['NR_TargetedTotal'] = $member_count;
            $release['NR_Status'] = 2;
            array_push($listSent, $release['NR_Title']);
            array_push($listIds, $release['NR_ID']);
            array_push($listDest, $release['NR_AdminEmail']);
            $data = array(
                'list' => $listSent,
                'dest' => $listDest,
                'ids'  => $listIds
            );
            $failedEmail = false;
            if (!empty($sendResult) && $sendResult['status'] != 'success'){
                $this->_stats['errors'] = $sendResult['data'] . ' (ID /';
                $this->_stats['errors'] .= ' externalId : ' . $releaseID;
                $this->_stats['errors'] .= ' / ' . $sendResult['post']['mailing_id'] .')';
                $release['NR_Status'] = 3;
                $failedEmail = true;
            }else{
                $this->_adminNotification($data);
                $this->_logSending($data);
            }
            $release->save();
            if ($this->_isXmlHttpRequest)
            {
                echo(Zend_Json::encode(array('sentTo' => null,
                    'targetedTotal' => $member_count,
                    'failedEmail' => $failedEmail,
                    'errors' => $this->_stats['errors'],
                    'select' => $selection)));
                exit;
            }
        }
    }

    private function _recordEmails($failedEmailAddress, $releaseId)
    {
        $oEmails = new NewsletterInvalidEmailsObject();
        foreach ($failedEmailAddress as $failedEmail)
        {
            $errMerge = '';
            $errors = $failedEmail['errors'];
            $msgErr = $failedEmail['msg'];
            unset($failedEmail['errors'], $failedEmail['msg']);

            foreach ($errors as $value)
                $errMerge .= $value . ':' . $msgErr[$value] . '||';

            $failedEmail['message'] = $errMerge;

            $oEmails->insertInvalidEmails($failedEmail, $releaseId);
        }
    }

    /**
     * Save and / or send email to the selected list.
     * Set the filter and the status of recipients.
     *
     * @return void
     */
    public function manageSendAction()
    {
        $this->view->title = "Gestion de l'envoie de l'infolettre";
        if ($this->view->aclIsAllowed($this->_moduleTitle, 'manage', true))
        {
            $blockID = (int) $this->_getParam('blockID');
            $pageID = (int) $this->_getParam('pageID');
            $newsletterID = (int) $this->_getParam('newsletterID');

            //Fetch data of the current newsletter
            $releaseSelect = new NewsletterReleases();
            $releaseData = $releaseSelect->populate($newsletterID);
            $releaseDataArray = $releaseData->toArray();
            // Display data if newsletter exists
            if (count($releaseDataArray) > 0)
            {
                // generate the form
                $baseDir   = $this->view->baseUrl();
                $cancelUrl = "/newsletter/index/edit/blockID/$blockID/pageID/$pageID/newsletterID/$newsletterID";
                $oDate = new Zend_Date($releaseDataArray['NR_MailingDateTimeScheduled'],
                    Zend_Registry::get('languageSuffix'));

                $props = get_class_vars(get_class($this));
                $collectionList = $this->_helper->reports->setProperties($props)
                    ->setForModule($this->_moduleID)
                    ->getList();
                $listeId = (int)$this->_getParam( 'listeID' );
                if(!$listeId){
                    $listeId = $releaseDataArray["NR_CollectionFiltersID"];
                }
                $this->_helper->reports->buildRequest($listeId);
                $count = $this->_helper->reports->countData();
                if ($count > 1000){
                    $this->_helper->reports->setLimit(1000, 0);
                }
                $result = $this->_helper->reports->findData();

                $planedDate  = $oDate->toString('YYYY-MM-dd');
                $planedTime  = $oDate->toString('HH:mm');
                // options to the form
                $form = new FormNewsletterManageSend(array(
                    'status'     => $releaseDataArray['NR_Status'],
                    'planedDate' => $planedDate,
                    'planedTime' => $planedTime,
                    'baseDir'    => $baseDir,
                    'cancelUrl'  => $baseDir.$cancelUrl,
                    'filterList' => $collectionList
                    ));

                $this->view->form = $form;
                $this->view->releaseData = $releaseDataArray;
                $this->view->count = $count;
                $this->view->rsDataMembers = $result;

                if ($this->_request->isPost())
                {
                    $formData = $this->_request->getPost();
                    if ($formData['NR_MailingDate'] == "0000-00-00")
                        unset($formData['NR_MailingDate']);

                    if ($form->isValid($formData))
                    {
                        $releaseData['NR_MailingDateTimeScheduled'] = $form->getValue('NR_MailingDate') . " " . $form->getValue('NR_MailingTime') . ":00";
                        $releaseData['NR_Status'] = $form->getValue('NR_Status');
                        $releaseData['NR_CollectionFiltersID'] = $form->getValue('NR_CollectionFiltersID');
                        $releaseData->save();

                        if ($formData['newsletter_send'])
                            $this->_redirect("/newsletter/index/send-newsletter/newsletterID/$newsletterID");
                        else
                            $this->_redirect($cancelUrl);
                    }
                    else
                    {
                        $form->populate($formData);
                    }
                }
                else
                {
                    $form->populate($releaseData->toArray());
                    if ($releaseData['NR_Status'] == 0){
                        $form->getElement('NR_Status')->setValue(3);
                    }
                    if($listeId){
                        $form->getElement('NR_CollectionFiltersID')->setValue($listeId);
                    }
                    $form->getElement('NR_MailingDate')->setValue(substr($releaseData['NR_MailingDateTimeScheduled'], 0, 10));
                    $form->getElement('NR_MailingTime')->setValue(substr($releaseData['NR_MailingDateTimeScheduled'], 10, 6));
                }
            }
        }
    }

    public function subval_sort($a, $sort)
    {
        $subkey = $sort[0]['field'];
        $param = $sort[0]['param'];

        foreach ($a as $k => $v)
        {
            $b[$k] = strtolower($v[$subkey]);
        }
        if ($param == "asc")
            asort($b);
        else
            arsort($b);

        foreach ($b as $key => $val)
        {
            $c[] = $a[$key];
        }

        return $c;
    }

    function get_time_difference($start, $end)
    {
        $uts['start'] = strtotime($start);
        $uts['end'] = strtotime($end);
        if ($uts['start'] !== -1 && $uts['end'] !== -1)
        {
            if ($uts['end'] >= $uts['start'])
            {
                $diff = $uts['end'] - $uts['start'];
                if ($days = intval((floor($diff / 86400))))
                    $diff = $diff % 86400;
                if ($hours = intval((floor($diff / 3600))))
                    $diff = $diff % 3600;
                if ($minutes = intval((floor($diff / 60))))
                    $diff = $diff % 60;
                $diff = intval($diff);
                return( array('days' => $days, 'hours' => $hours, 'minutes' => $minutes, 'seconds' => $diff) );
            }
            else
            {
                trigger_error("Ending date/time is earlier than the start date/time", E_USER_WARNING);
            }
        }
        else
        {
            trigger_error("Invalid date/time data detected", E_USER_WARNING);
        }
        return( false );
    }

    public function deleteCategoriesAction()
    {

        if ($this->view->aclIsAllowed($this->view->current_module, 'edit'))
        {
            $id = $this->_getParam('ID');

            if ($this->_request->isPost() && isset($_POST['delete']))
            {

                $this->_db->delete('Categories', "C_ID = '$id'");
                $this->_db->delete('CategoriesIndex', "CI_CategoryID = '$id'");

                $this->_redirect("/newsletter/index/list-categories/");
            }
            else if ($this->_request->isPost() && isset($_POST['cancel']))
            {
                $this->_redirect('/newsletter/index/list-categories/');
            }
            else
            {
                $fails = false;

                $select = $this->_db->select();
                $select->from('CategoriesIndex', array('CI_Title'))
                    ->where('CategoriesIndex.CI_CategoryID = ?', $id);

                $categoryName = $this->_db->fetchOne($select);

                $this->view->assign('category_id', $id);
                $this->view->assign('category_name', $categoryName);

                $select = $this->_db->select();
                $select->from('Newsletter_Releases')
                    ->where('Newsletter_Releases.NR_CategoryID = ?', $id);

                $result = $this->_db->fetchAll($select);

                if ($result)
                {
                    $fails = true;
                }

                if (!$fails)
                {
                    $select = $this->_db->select();
                    $select->from('Blocks')
                        ->joinRight('Parameters', 'Parameters.P_BlockID = Blocks.B_ID')
                        ->where('Parameters.P_Number = ?', 1)
                        ->where('Parameters.P_Value = ?', $id)
                        ->where('Blocks.B_ModuleID = ?', $this->_moduleID);

                    $result = $this->_db->fetchAll($select);

                    if ($result)
                    {
                        $fails = true;
                    }
                }

                $this->_db->delete('ModuleCategoryViewPage', $this->_db->quoteInto('MCVP_CategoryID = ?', $id));

                $this->view->assign('module_name', $this->_moduleName);
                $this->view->assign('module_id', $this->_moduleID);
                $this->view->assign('returnUrl', '/newsletter/index/list-categories/');
                $this->view->assign('fails', $fails);
            }
        }
    }

    public function importAction()
    {
        $form = new FormImportNewsletter(array(
                'cancelUrl' => $this->view->baseUrl()
            ));

        $this->view->assign('form', $form);

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();

            if ($form->isValid($formData))
            {

                if ($form->file->receive())
                {

                    $file = fopen($form->file->getFileName(), "r") or die('no file were selected');

                    while (!feof($file))
                    {
                        $line = fgets($file);

                        list($salutation, $nom, $prenom, $courriel, $lang) = explode(';', $line);

                        $newsletterProfile = new NewsletterProfile();
                        $memberId = $newsletterProfile->addMember(array(
                                'lastName' => $nom,
                                'firstName' => $prenom,
                                'salutation' => $salutation,
                                'email' => $courriel,
                                'language' => $lang,
                                'newsletter_categories' => '21'
                            ));

                        $memberProfile = new MemberProfile();
                        $memberProfile->updateMember($memberId, array(
                            'isMember' => 1
                        ));
                    }
                    fclose($file);
                }
            }
        }
    }

    public function toExcelAction()
    {
        $this->filename = 'Newsletter.xlsx';

        $searchfor = $this->_request->getParam('searchfor');

        $profile = new NewsletterProfile();

        $this->select = $profile->getSelectStatement();

        $this->tables = array(
            'GenericProfiles' => array('GP_lastName', 'GP_firstName', 'GP_Email')
        );


        $this->fields = array(
            'lastName' => array('width' => '', 'label' => ''),
            'firstName' => array('width' => '', 'label' => ''),
            'email' => array('width' => '', 'label' => '')
        );

        $this->filters = array(
        );

        parent::toExcelAction();
    }

    /**
     * Set the list of members for the newsletter according to filters data.
     *
     * @param int $collectionSetId
     *
     * @return array
     */
    private function _countFilterMembers($collectionSetId)
    {
        $filterSetSelect = new NewsletterFilterCollectionsFiltersSet();
        $select = $filterSetSelect->select()->setIntegrityCheck(false);
        $select->from('NewsletterFilter_CollectionsFiltersSet')
            ->where('NFCFS_CollectionSetID = ?', $collectionSetId)
            ->join('NewsletterFilter_CollectionsSet', 'NFCS_ID = NFCFS_CollectionSetID')
            ->join('NewsletterFilter_Filters', 'NFF_FilterSetID = NFCFS_FilterSetID')
            ->join('NewsletterFilter_ProfilesFields', 'NFPF_Name = NFF_ProfileFieldName')
            ->join('NewsletterFilter_ProfilesTables', 'NFPT_ID = NFPF_ProfileTableID')
            ->order('NFF_FilterSetID')
            ->order('NFF_ID');
        $filterSetData = $filterSetSelect->fetchAll($select)->toArray();

        $db = Zend_Registry::get('db');
        $select = $db->select();
        $select->from('GenericProfiles');

        $filterSetID = 0;
        $profileTables = array('GenericProfiles');
        $whereOR = "";
        foreach ($filterSetData as $filterSet)
        {
            $name = $filterSet['NFCS_Name'];
            if ($filterSetID <> $filterSet['NFF_FilterSetID'])
            {
                $filterSetID = $filterSet['NFF_FilterSetID'];

                if ($whereOR <> '')
                {
                    $select->orWhere($whereOR);
                    $whereOR = '';
}
            }

            if (!in_array($filterSet['NFPT_Name'], $profileTables))
            {
                $profileTables[] = $filterSet['NFPT_Name'];
                $select->joinLeft($filterSet['NFPT_Name'], $filterSet['NFPT_JoinOn']);
            }

            if ($whereOR <> '')
                $whereOR .= ' AND ';

            if ($filterSet['NFPF_Type'] == 'int')
            {
                $whereOR .= " {$filterSet['NFPF_Name']} = {$filterSet['NFF_Value']}";
            }
            elseif ($filterSet['NFPF_Type'] == 'list')
            {
                $whereOR .= " ({$filterSet['NFPF_Name']} = {$filterSet['NFF_Value']}";
                $whereOR .= " OR {$filterSet['NFPF_Name']} like '%,{$filterSet['NFF_Value']}'";
                $whereOR .= " OR {$filterSet['NFPF_Name']} like '{$filterSet['NFF_Value']},%'";
                $whereOR .= " OR {$filterSet['NFPF_Name']} like '%,{$filterSet['NFF_Value']},%')";
            }
            elseif ($filterSet['NFPF_Type'] == 'char')
            {
                $whereOR .= " {$filterSet['NFPF_Name']} = '{$filterSet['NFF_Value']}'";
            }
            elseif ($filterSet['NFPF_Type'] == 'qrystr')
            {
                $whereOR .= " {$filterSet['NFPF_Name']} {$filterSet['NFF_Value']}";
            }
        }
//        $whereOR .= ' AND NP_Categories <> 0';
        if ($whereOR <> '')
            $select->orWhere($whereOR);

        $members = $db->fetchAll($select);

        $data['name'] = $name;
        $data['query'] = $select;
        $data['members'] = $members;
        $data['selection'] = $select;

        return $data;
    }

    /**
     * Send an email to the administator after a mass mailing action.
     *
     * @param array $data Data to build the email content. Report after sending.
     * @return void
     */
    private function _sendMassMailingReport(array $data)
    {
        $fromEmail = $this->_config->massMailing->sender;
        $recipient = $this->_config->massMailing->reportTo;
        $registry = Zend_Registry::getInstance()->set('format','email');

        $NRTitle = '';
        $sentTo = "Envoyée à ";
        $bodyText = '';

        // send the mail
        $mail = new Zend_Mail('utf-8');
        $mail->setBodyHtml($bodyText);
        $mail->setFrom($fromEmail, $fromName);
        $mail->setReturnPath($fromEmail);
        $mail->addTo($recipient);
        $mail->setSubject("Rapport d'envoi d' infolettres.");
        $mail->send();
    }
}
