<?php

class Newsletter_IndexController extends Cible_Controller_Action
{
    protected $_moduleID = 8;
    protected $pairSepar = '|';
    protected $separator = '||';
    protected $_params = array();
    protected $_template = '';
    protected $_moduleTitle = 'newsletter';
    protected $_profileData = null;

    public function init()
    {
        parent::init();
        $this->setModuleId();
        $this->view->headLink()->offsetSetStylesheet($this->_moduleID, $this->view->locateFile('newsletter.css'), 'all');
        $this->view->headLink()->appendStylesheet($this->view->locateFile('newsletter.css'), 'all');
    }

    /**
    * Overwrite the function define in the SiteMapInterface implement in Cible_Controller_Action
    *
    * This function return the sitemap specific for this module
    *
    * @access public
    *
    * @return a string containing xml sitemap
    */
    public function siteMapAction()
    {
        $newsRob = new NewsletterRobots();
        $dataXml = $newsRob->getXMLFile($this->_request->getParam('lang'));

        parent::siteMapAction($dataXml);
    }

    public function indexAction()
    {

    }

    public function showwebdetailsAction()
    {
        $auth = Zend_Auth::getInstance();
        $data = (array) $auth->getStorage()->read();
        $this->disableView();
        if ($data)
        {

            $id = $this->_getParam('ID');

            $newsletterID = $this->_getParam('newsletterID');
            $back_to_newsletter = !empty($newsletterID) ? "/ID/{$newsletterID}" : '';
            $blockID = $this->_getParam('BlockID');
            $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
            $newsletterCategoryID = $blockParams[0]['P_Value'];
            $show_page = 'show-web/index/ID/' . $newsletterID;

            if (!empty($id))
            {
                $newsletterSelect = new NewsletterReleases();
                $select = $newsletterSelect->select();
                $select->from('Newsletter_Releases')
                    ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                    ->where('NR_ID = ?', $newsletterID);
                $newsletterData = $newsletterSelect->fetchRow($select);
                $titleParution = $newsletterData['NR_Title'];
                $this->_setModel($newsletterData);
                // article info
                $newsletterArticlesSelect = new NewsletterArticles();
                $select = $newsletterArticlesSelect->select();
                $select->where('NA_ID = ?', $id);
                //$select->where('NA_ReleaseID = ?', $newsletterID);
                $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
                if (count($newsletterArticlesData) < 0)
                {
                    $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8));
                }
                $this->view->articles = $newsletterArticlesData->toArray();

                $blockID = $this->_getParam('BlockID');

                $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
                $newsletterCategoryID = $blockParams[0]['P_Value'];
                $this->view->assign('parution_title', $titleParution);
                $this->view->assign('parution_date', $newsletterData['NR_Date']);
                $this->view->assign('newsletterID', $newsletterID);

                $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
                $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
                $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . $show_page);
                $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
            }
            else
            {
                $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
                $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
                $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . $show_page);
                $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
                $this->view->articles = array();
            }

            echo $this->view->render($this->_template);
        }
        else
        {
            $this->_redirect('index');
        }
    }

    public function showwebAction()
    {
        $auth = Zend_Auth::getInstance();
        $data = (array) $auth->getStorage()->read();
        $this->disableView();
        if ($data)
        {

            $id = $this->_request->getParam('ID');
            $blockID = $this->_getParam('BlockID');
            $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
            $newsletterCategoryID = $blockParams[0]['P_Value'];
            if (!empty($id))
            {
                // release info
                $newsletterSelect = new NewsletterReleases();
                $select = $newsletterSelect->select()->setIntegrityCheck(false);
                $select->from('Newsletter_Releases')
                    ->join('Languages', 'L_ID = NR_LanguageID', array())
                    ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID', array())
                    ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID', array())
                    ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID', array())
                    ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
//                    ->where('NR_CategoryID = ?', $newsletterCategoryID)
                    ->where('NR_ID = ?', $id)
                    ->order('NR_Date DESC');
                $newsletterData = $newsletterSelect->fetchRow($select);
            }
            if ($id <> '')
            {
                // articles info
                $newsletterArticlesSelect = new NewsletterArticles();
                $select = $newsletterArticlesSelect->select();
                $select->where('NA_ReleaseID = ?', $id)
                    ->order('NA_ZoneID')
                    ->order('NA_PositionID');
                $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);

                $this->view->articles = $newsletterArticlesData->toArray();
            }

            $this->_setModel($newsletterData);

            $titleParution = $newsletterData['NR_Title'];
            $dateParution = $newsletterData['NR_Date'];
            $this->view->assign('newsletterID', $id);
            //echo $this->view->baseUrl();
            $details_page = 'show-web-details/index';
            $this->view->assign('parution_title', $titleParution);
            $this->view->assign('parution_date', $dateParution);
            $this->view->assign('details_page', $details_page);
            $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
            $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
            $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);

            echo $this->view->render($this->_template);
        }
        else
        {
            $this->_redirect('index');
        }
    }

    public function detailsreleaseAction()
    {
        $this->disableView();
        $userId = 0;
        $id = $this->_getParam('ID');
        $newsletterSelect = new NewsletterReleases();
        $url = $this->_request->getPathInfo();
        $fromEmail = (bool) preg_match('/-uid-[0-9]+/', $url);
        $user = Zend_Registry::get('user');
        $memberId = 0;

        $blockID = $this->_getParam('BlockID');
        $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
        $newsletterCategoryID = $blockParams[0]['P_Value'] | 2;
        if (empty($id))
        {
            if ($fromEmail)
            {
                $pos = strpos($url, '-uid-');
                $uid = explode('-', substr($url, $pos));
                $memberId = end($uid);
                $url = substr_replace($url, '', $pos);
                $path = Zend_Registry::get('web_root') . '/';
                setcookie('uid_newsletter', (string)$memberId, 0, $path);
            }
            else if (isset($_COOKIE['uid_newsletter'])){
                $memberId = $_COOKIE['uid_newsletter'];
            }

            if ($memberId == 0 && $user)
            {
                $oMember = new MemberProfile();
                $member = $oMember->findMember(array('email' => $user['email']));
                $memberId = $member['member_id'];
            }

            $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($url);
            $dateUrl = Cible_FunctionsGeneral::getDateFromPath($url);

            if ($titleUrl != "")
                $id = $newsletterSelect->getNewsletterIdByName($titleUrl,$dateUrl);
        }

        if (!empty($id))
        {
            // release info

            $select = $newsletterSelect->select()->setIntegrityCheck(false);
            $select->from('Newsletter_Releases')
                ->join('Languages', 'L_ID = NR_LanguageID', array())
                ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID', array())
                ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID', array())
                ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID', array())
                ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                ->where('NR_CategoryID = ?', $newsletterCategoryID)
                ->where('NR_Online = ?', 1)
                ->where('NR_ID = ?', $id)
                ->order('NR_Date DESC');
            $newsletterData = $newsletterSelect->fetchRow($select);
        }
        else
        {
            $newsletterSelect = new NewsletterReleases();
            $select = $newsletterSelect->select()->setIntegrityCheck(false);
            $select->from('Newsletter_Releases')
                ->join('Languages', 'L_ID = NR_LanguageID', array())
                ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID', array())
                ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID', array())
                ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID', array())
                ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                ->where('NR_CategoryID = ?', $newsletterCategoryID)
                ->where('NR_Online = ?', 1)
                ->order('NR_Date DESC');
            $newsletterData = $newsletterSelect->fetchRow($select);

            $id = $newsletterData['NR_ID'];
        }
        $oMailing = new Cible_Newsletters_Mailings();
        $htmlMessage = $oMailing->setConfig($this->_config)->setParameters()
            ->setId($newsletterData['NR_ExternalId'])
            ->getHtmlMessage()
            ->getResults();
        $links = $oMailing->setConfig($this->_config)->setParameters()
            ->setId($newsletterData['NR_ExternalId'])
            ->getLinks()
            ->getResults();
//        preg_match_all('!href\="https?://\S+!', $htmlMessage['data'], $matches);
        preg_match_all('!"https?://suivi.\S+!', $htmlMessage['data'], $matches);

        if ($id <> '' && $newsletterData['NR_Online'] == 1)
        {
            // articles info
            $newsletterArticlesSelect = new NewsletterArticles();
            $select = $newsletterArticlesSelect->select();
            $select->where('NA_ReleaseID = ?', $id)
                ->order('NA_ZoneID')
                ->order('NA_PositionID');
            $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
            $this->_setModel($newsletterData);
            $this->view->articles = $newsletterArticlesData->toArray();
        }
        else
        {
            $this->view->articles = array();
        }

        $titleParution = $newsletterData['NR_Title'];
        $blockID = $this->_getParam('BlockID');

        $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
        $newsletterCategoryID = $blockParams[0]['P_Value'];

        $details_page = Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_article', 8);
        $this->view->assign('parution_title', $titleParution);
        $this->view->assign('newsletterID', $newsletterData['NR_ID']);
        $this->view->assign('parution_date', $newsletterData['NR_Date']);
        $this->view->assign('details_page', $details_page);
        $this->view->assign('parutionValURL', $newsletterData['NR_ValUrl']);

        $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8));
        $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
        $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
        $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
        if (!empty($this->_template))
            echo $this->view->render($this->_template);
        else
            echo $this->view->render('index/templates/one/details-release.phtml');
    }

    private function _setModel($data,$connect=1)
    {
        $id = 0;
        $oNewletter = new NewsletterObject();
        if (!empty($data['NR_ModelID']))
            $id = $data['NR_ModelID'];
        $model = $oNewletter->getModel($id);

        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer($this->view);
        $viewRenderer->setViewBasePathSpec($model);
        $script = str_replace('_', '-', $this->_request->getActionName());
        $this->_template = $model . '/' . $script . '.phtml';


    }

    public function captchaReloadAction()
    {
        $baseDir = $this->view->baseUrl();
        $captcha_image = new Zend_Captcha_Image(array(
                'captcha' => 'Word',
                'wordLen' => 5,
                'fontSize' => 18,
                'height' => 50,
                'width' => 100,
                'timeout' => 300,
                'dotNoiseLevel' => 0,
                'lineNoiseLevel' => 0,
                'font' => "/captcha/fonts/ARIAL.TTF",
                'imgDir' => "captcha/tmp",
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

    public function subscribeAction()
    {
        // VARIABLES
        $blockID = $this->_getParam('BlockID');
        $viewScript = $this->_getParam('viewScript');
        $pageId = $this->_config->privacyPolicy->pageId;
        $urlPrivacy = Cible_FunctionsPages::getPageLinkByID($pageId);
        $messageConf = $this->view->link($urlPrivacy,$this->view->getCibleText('joindre_fo_form_label_confident_joindre'),array('target'=>'_blank'));
        $this->view->assign('messageConfidentialite', $messageConf);
        $newsletterID = $this->_getParam('newsletterID');
        $back_to_newsletter = !empty($newsletterID) ? "/ID/{$newsletterID}" : '';

        if (!$blockID){
            $newsletterCategoryDetails = array();
            $newsletterCategoryID = $this->_getParam('nlCatId');
            $this->_helper->viewRenderer->setRender($viewScript);
        }else{
            $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)
                ->toArray();
            $newsletterCategoryID = $blockParams[0]['P_Value'];
            $newsletterCategoryDetails = Cible_FunctionsCategories::getCategoryDetails(
                $newsletterCategoryID);
            $this->view->assign('confidentialityPolitics', $this->view->baseUrl() . "/" . Cible_FunctionsPages::getActionNameByLang('confidentiality'));
            $this->view->assign('newsletterTitle', $newsletterCategoryDetails['CI_Title']);
            $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
            $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
            $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
        }
        $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
        $form = new FormNewsletterSubscription(array('viewScript' => $viewScript));
        $this->view->assign('form', $form);

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if (array_key_exists('subscribe', $formData))
            {
                if(!empty($viewScript)){
                    $url = Cible_FunctionsCategories::getPagePerCategoryView(
                        2, 'subscribe', $this->_moduleID);
                    $url .= '/email/' . $formData['email'];
                    $this->_redirect($url);
                }
                if ($form->isValid($formData))
                {
                    $messageSuccess = str_replace('###member_name###', " <b>{$formData['firstName']} {$formData['lastName']}</b>", $this->view->getCibleText('newsletter_subscribe_confirmation_message1'));
                    $messageSuccess = str_replace('###img-pierre-gervais###', $this->view->baseUrl() . '/themes/default/images/common/img-signature-pierre-gervais.png', $messageSuccess);

                    $message[0] = $messageSuccess;
                    $message[1] = $this->view->getCibleText('newsletter_subscribe_confirmation_message2');

                    $this->view->assign('inscriptionValidate', true);
                    $this->view->assign('returnLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));

                    $genericProfil = new GenericProfile();
                    $members = $genericProfil->findMembers(array('email' => $formData['email']));

                    $memberProfile = new MemberProfile();


                    // if member dont exist in the system
                    if (count($members) == 0)
                    {
                        $newsletterProfile = new NewsletterProfile();

                        $formData['newsletter_categories'] = $newsletterCategoryID;
                        $newsletterProfile->addMember($formData);

                        $members  = $genericProfil->findMembers(array('email' => $formData['email']));
                        $memberID = $members[0]['member_id'];

                        $memberProfile->updateMember($memberID, $formData);

                        $this->view->assign('message', $message[0]);
                    }
                    else
                    {
                        $memberID = $members[0]['member_id'];
                        $newsletterProfile = new NewsletterProfile();
                        $memberDetails = $newsletterProfile->getMemberDetails($memberID);

                        //if(array_key_exists('newsletter_categories',$memberDetails)){
                        if ($memberDetails <> '')
                        {
                            $memberNewsletterCategories = explode(',', $memberDetails['newsletter_categories']);
                            // if member is already subscribe to the newsletter
                            if (in_array($newsletterCategoryID, $memberNewsletterCategories))
                            {
                                $this->view->assign('message', $message[1]);
                            }
                            // if member is NOT already subscribe to the newsletter
                            else
                            {
                                $memberNewsletterCategories[] = $newsletterCategoryID;
                                $newMemberNewsletterCategories = implode(',', $memberNewsletterCategories);
                                $newsletterProfile->updateMember($memberID, array('newsletter_categories' => $newMemberNewsletterCategories));

                                $this->view->assign('message', $message[0]);
                            }
                        }
                        // if member is NOT already subscribe to the newsletter
                        else
                        {
                            $newsletterProfile->updateMember($memberID, array('newsletter_categories' => $newsletterCategoryID));
                            $this->view->assign('message', $message[0]);
                        }
                    }
                    $this->_params = array(
                        'memberId' => $memberID,
                        'category' => $newsletterCategoryID
                    );
                    $this->logSubscription();
                }
            }
            elseif (array_key_exists('newslettersubmit', $formData))
            {
                $options['email'] = $formData['newsletteremail'];
                $form = new FormNewsletterSubscription($options);
            }
            elseif (array_key_exists('unsubscribe', $formData))
            {
                $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
            }
        }else{
            $form->populate(array('email' => $this->_getParam('email')));
        }

        $this->view->assign('form', $form);
    }

    public function unsubscribeAction()
    {
        $blockID = $this->_getParam('BlockID');
        $config = Zend_Registry::get('config');
        $pageId = $config->privacyPolicy->pageId;
        $urlPrivacy = Cible_FunctionsPages::getPageLinkByID($pageId);
        $messageConf = $this->view->link($urlPrivacy,$this->view->getCibleText('joindre_fo_form_label_confident_joindre'),array('target'=>'_blank'));
        $this->view->assign('messageConfidentialite', $messageConf);

        $newsletterID = $this->_getParam('newsletterID');
        $back_to_newsletter = !empty($newsletterID) ? "/ID/{$newsletterID}" : '';

        $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
        $newsletterCategoryID = $blockParams[0]['P_Value'];
        $newsletterCategoryDetails = Cible_FunctionsCategories::getCategoryDetails($newsletterCategoryID);
        $this->view->assign('newsletterTitle', $newsletterCategoryDetails['CI_Title']);
        $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
        $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
        $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
        $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);

        $form = new FormNewsletterUnsubscription(array());
        $this->view->assign('form', $form);

        if ($this->_request->isPost())
        {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData))
            {
                $message = str_replace('###newsletter_title_release###', " <b>{$newsletterCategoryDetails['CI_Title']}</b>", $this->view->getCibleText('newsletter_unsubscribe_confirmation_message'));

                $this->view->assign('message', $message);
                $this->view->assign('unsubscribeValidate', true);

                $genericProfil = new GenericProfile();
                $members = $genericProfil->findMembers(array('email' => $formData['email']));
                if (count($members) <> 0)
                {
                    $memberID = $members[0]['member_id'];
                    $newsletterProfile = new NewsletterProfile();
                    $memberDetails = $newsletterProfile->getMemberDetails($memberID);

                    $memberNewsletterCategories = explode(',', $memberDetails['newsletter_categories']);
                    $i = 0;
                    foreach ($memberNewsletterCategories as $newsletterCategory)
                    {
                        if ($newsletterCategory == $newsletterCategoryID)
                        {
                            array_splice($memberNewsletterCategories, $i, 1);
                        }
                        else
                            $i++;
                    }


                    $oNewsletter = new NewsletterObject();
                    $oNewsletter->setConfig($this->_config)->setLoginUserInfo();
                    $oNewsletter->unsubscribeToExternal($formData);

                    if (empty($newsletterID))
                        $newsletterID = 0;

                    $this->_params = array(
                        'memberId'  => $memberID,
                        'releaseId' => $newsletterID,
                        'category'  => $newsletterCategoryID,
                        'reason'    => $formData
                    );
                    $this->logUnsubscribe();


                }
            }
        }
    }

    public function detailsarticleAction()
    {

        $this->disableView();
        // Article ID and Newsletter ID
        $newsletterArticlesSelect = new NewsletterArticles();

        $id = 0;
        $newsletterID = 0;
        $url = $this->_request->getPathInfo();
        $fromEmail = (bool) preg_match('/-uid-[0-9]+$/', $url);
        $isPreview = (bool) preg_match('/-preview$/', $url);
        $user = Zend_Registry::get('user');
        $memberId = 0;

        $titleUrl = Cible_FunctionsGeneral::getTitleFromPath($url);
        $dateUrl = Cible_FunctionsGeneral::getDateFromPathArticle($url);

        if ($titleUrl != "")
        {
            $string = explode("-uid-", $titleUrl);
            $id = $newsletterArticlesSelect->getArticleIdByName($string[0],$dateUrl);
            $newsletterID = $newsletterArticlesSelect->getNewsletterIdByName($string[0],$dateUrl);
        }

        if ($fromEmail)
        {
            $pos = strpos($url, '-uid-');
            $uid = explode('-', substr($url, $pos));
            $memberId = end($uid);
            $url = substr_replace($url, '', $pos);
            $path = Zend_Registry::get('web_root') . '/';
            setcookie('uid_newsletter', (string)$memberId, 0, $path);
        }
        else if (isset($_COOKIE['uid_newsletter'])){
            $memberId = $_COOKIE['uid_newsletter'];
        }

        if ($memberId == 0 && $user)
        {
            $oMember = new MemberProfile();
            $member = $oMember->findMember(array('email' => $user['email']));
            $memberId = $member['member_id'];
        }

        $back_to_newsletter = !empty($newsletterID) ? "/ID/{$newsletterID}" : '';
        $blockID = $this->_getParam('BlockID');
        $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
        $newsletterCategoryID = $blockParams[0]['P_Value'];
        if (!empty($id))
        {
            if (!$isPreview)
            {
                $this->_params = array(
                    'moduleId'  => $this->_moduleID,
                    'releaseId' => $newsletterID,
                    'memberId'  => $memberId,
                    'articleId' => $id
                    );

                $this->logDetailsread();
            }

            $newsletterSelect = new NewsletterReleases();
            $select = $newsletterSelect->select();
            $select->from('Newsletter_Releases')
                ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                ->where('NR_ID = ?', $newsletterID);
            $newsletterData = $newsletterSelect->fetchRow($select);
            $titleParution = $newsletterData['NR_Title'];
            $titleURLParution = $newsletterData['NR_ValUrl'];

            $this->_setModel($newsletterData);

            if ($newsletterData['NR_Online'] == 1)
            {
                // article info

                $select = $newsletterArticlesSelect->select();
                $select->where('NA_ID = ?', $id);
                //$select->where('NA_ReleaseID = ?', $newsletterID);
                $newsletterArticlesData = $newsletterArticlesSelect->fetchAll($select);
                if (count($newsletterArticlesData) < 0)
                {
                    $this->_redirect(Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8));
                }
                $this->view->articles = $newsletterArticlesData->toArray();


                $blockID = $this->_getParam('BlockID');

                $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
                $newsletterCategoryID = $blockParams[0]['P_Value'];
                $this->view->assign('parution_title', $titleParution);
                $this->view->assign('parution_date', $newsletterData['NR_Date']);
                $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
                $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
                $this->view->assign('parutionURL',$titleURLParution);
                $this->view->assign('back_to_release', Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8));
                //$this->view->assign('back_to_release', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
                $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
            }
            else
            {
                $this->view->articles = array();
                $this->view->assign('parutionURL',$titleURLParution);
                $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
                $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
                $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
                $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
            }
        }
        else
        {

            $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
            $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
            $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);

            $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
            $this->view->articles = array();

        }
        //echo $this->view->render($this->_template);
        if (!empty($this->_template))
            echo $this->view->render($this->_template);
        else
            echo $this->view->render('index/templates/one/details-article.phtml');
    }

    public function listarchivesAction()
    {
        // Newsletter ID and Category ID
        $categoryID = $this->_getParam('categoryID');
        if (empty($categoryID))
            $categoryID = 2;

        $newsletterID = $this->_getParam('newsletterID');
        $back_to_newsletter = !empty($newsletterID) ? "/ID/{$newsletterID}" : '';

        $options["filtre"] = $this->_request->getParam('listeFiltre');

        $newsletterSelect = new NewsletterReleases();
        $arraySelect = $newsletterSelect->getFilterArchive();
        $year = 0;
        $yearsList = array();
        foreach ($arraySelect as $value)
        {
            if ($value['Annee'] > $year)
                $year = $value['Annee'];

            $yearsList[$value['Annee']] = $value['Annee'];
        }
        if(!isset($yearsList)){
            $yearsList[0] = 0;
        }
        $options['dates'] = $yearsList;
        if($options['filtre'] == "")
            $options['filtre'] = $year;

        $form = new FormSelect($options);
        $this->view->formSelect = $form;
        if($this->_request->isPost())
        {
             $form->populate($this->_request->getPost());
        }

        // Get the Newsletter ID to dont show
        $select = $newsletterSelect->select()->setIntegrityCheck(false);
        $select->from('Newsletter_Releases')
            ->join('Languages', 'L_ID = NR_LanguageID', array())
            ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID', array())
            ->join('Newsletter_Models_Index', 'NMI_NewsletterModelID = NR_ModelID', array())
            ->join('Newsletter_Models', 'NM_ID = NMI_NewsletterModelID', array())
            ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
            ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
            ->where('NR_Online = ?', 1)
            ->order('NR_Date DESC');
        $newsletterData = $newsletterSelect->fetchRow($select);
        $actualNewsletterOnline = $newsletterData['NR_ID'];

        if ($actualNewsletterOnline <> '')
        {
            if (empty($categoryID) || $categoryID == '')
                $categoryID = $newsletterData['NR_CategoryID'];

            //get all releases exept the one online
            $releasesSelect = new NewsletterReleases();
            $select = $releasesSelect->select()->setIntegrityCheck(false);
            $select->from('Newsletter_Releases')
                ->join('CategoriesIndex', 'CI_CategoryID = NR_CategoryID')
                ->join('Status', 'Newsletter_Releases.NR_Online = Status.S_ID')
                ->where('CI_LanguageID = ?', Zend_Registry::get("languageID"))
                ->where('NR_LanguageID = ?', Zend_Registry::get("languageID"))
                ->where('NR_Online = ?', 1)
                ->where("year(NR_Date) = ?", $options["filtre"])
                ->where('NR_CategoryID = ?', $categoryID)
                ->where('NR_ID <> ?', $actualNewsletterOnline)

                ->order('NR_Date DESC');

            $releasesData = $releasesSelect->fetchAll($select);

            $this->view->assign('listArchives', $releasesData);

            $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'subscribe', 8));
            $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'unsubscribe', 8));
            //$this->view->assign('detailsRelease', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details_release', 8));
            $this->view->assign('detailsRelease', Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details_release', 8));
            //$this->view->assign('back_to_release', Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details_release', 8));
            $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'details_release', 8) . $back_to_newsletter);
            $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($categoryID, 'list_archives', 8) . '/categoryID/' . $categoryID);
        }
        else
        {
            $this->view->assign('listArchives', array());
            $blockID = $this->_getParam('BlockID');

            $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
            $newsletterCategoryID = $blockParams[0]['P_Value'];

            $this->view->assign('subscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
            $this->view->assign('unsubscribeLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
            $this->view->assign('back_to_release', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
            $this->view->assign('archivesLink', $this->view->baseUrl() . "/" . Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
        }
    }

    /**
     * Logs data of the users/release for NL sent and opened (if images are displayed)
     */
    public function logAccessAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $params = explode('-', $this->_getParam('params'));
        $absPath = $_SERVER['DOCUMENT_ROOT'] .Zend_Registry::get('document_root');
        if(file_exists( $absPath. 'themes/default/images/common/' . $params[0] . '.jpg'))
            $imagepath = $absPath . 'themes/default/images/common/' . $params[0] . '.jpg';
        elseif(file_exists($absPath . 'themes/default/images/fr/' . $params[0] . '.jpg'))
            $imagepath = $absPath . 'themes/default/images/fr/' . $params[0] . '.jpg';
        elseif(file_exists($absPath . 'themes/default/images/en/' . $params[0] . '.jpg'))
            $imagepath = $absPath . 'themes/default/images/en/' . $params[0] . '.jpg';

        $moduleId = $params[1];
        $releaseId = $params[2];
        $memberId = $params[3];

        $data = array(
            'L_ModuleID' => $params[1],
            'L_UserID' => $params[3],
            'L_Action' => 'opened',
            'L_Data' => "releaseId" . $this->pairSepar . $params[2] . $this->separator
        );

        $image = imagecreatefromjpeg($imagepath);

        header('Content-Type: image/jpeg');
        imagejpeg($image, null, 100);

        $oLog = new LogObject();

        $found = $oLog->findRecords($data);

        if (!$found)
            $oLog->writeData($data);

        exit;
    }
    /**
     * Logs data of the users/release when read next is clicked
     */
    protected function logDetailsread()
    {
        $releaseId = $this->_params['releaseId'];
        $memberId  = $this->_params['memberId'];
        $strData = array(
            "releaseId" => $this->_params['releaseId'],
            'articleId' => $this->_params['articleId']
        );

//        $strData   = "releaseId" . $this->pairSepar . $this->_params['releaseId'] . $this->separator
//            . 'articleId' . $this->pairSepar . $this->_params['articleId'] . $this->separator;

        $data = array(
            'L_ModuleID' => $this->_moduleID,
            'L_UserID' => $this->_params['memberId'],
            'L_Action' => 'details',
            'L_Data' => $strData
        );

        $oLog = new LogObject();
        $oLog->writeData($data);
    }

    protected function logSubscription()
    {
        $strData   = 'categoryId' . $this->pairSepar . $this->_params['category'] . $this->separator;

        $data = array(
            'L_ModuleID' => $this->_moduleID,
            'L_UserID' => $this->_params['memberId'],
            'L_Action' => 'subscribe',
            'L_Data' => $strData
        );

        $oLog = new LogObject();
        $oLog->writeData($data);

    }
    protected function logUnsubscribe()
    {
        $reason  = 0;//  $this->_params['reason']['reason'];
        $reasonValue = array();
        if ($reason == 0)
        {
            $reasonValue = array('reason' => 'other');
            $reasonValue['value'] = "1"; //$this->_params['reason']['reasonOther'];
        }
        else
        {
            $oRef = new ReferencesObject();
            $reasonValue = $oRef->getValueById($reason);
        }

        $strData = "releaseId" . $this->pairSepar . $this->_params['releaseId'] . $this->separator
            . 'categoryId' . $this->pairSepar . $this->_params['category'] . $this->separator
            . $reasonValue['reason'] . $this->pairSepar . $reasonValue['value'] . $this->separator;

        $data = array(
            'L_ModuleID' => $this->_moduleID,
            'L_UserID' => $this->_params['memberId'],
            'L_Action' => 'unsubscribe',
            'L_Data' => $strData
        );

        $oLog = new LogObject();
        $oLog->writeData($data);

    }
    public function listAction()
    {

    }

    public function addAction()
    {

    }

    public function editAction()
    {

    }

    public function deleteAction()
    {

    }

    public function langswitchAction()
    {
        $this->disableView();
        $lang = $this->_getParam('lang');
        $url = $this->_getParam('url');

        echo $url;
}

    public function resubscribeAction()
    {
        $blockID = $this->_getParam('BlockID');

        $blockParams = Cible_FunctionsBlocks::getBlockParameters($blockID)->toArray();
        $newsletterCategoryID = $blockParams[0]['P_Value'];
        $newsletterCategoryDetails = Cible_FunctionsCategories::getCategoryDetails($newsletterCategoryID);
        $this->view->assign('newsletterTitle', $newsletterCategoryDetails['CI_Title']);
        $this->view->assign('subscribeLink', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'subscribe', 8));
//        $this->view->assign('back_to_release', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'details_release', 8) . $back_to_newsletter);
        $this->view->assign('unsubscribeLink', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'unsubscribe', 8));
        $this->view->assign('archivesLink', $this->view->baseUrl()."/".Cible_FunctionsCategories::getPagePerCategoryView($newsletterCategoryID, 'list_archives', 8) . '/categoryID/' . $newsletterCategoryID);
        $this->view->assign('confidentialityPolitics', $this->view->baseUrl() . "/" . Cible_FunctionsPages::getActionNameByLang('confidentiality'));

        $form = new FormNewsletterResubscription(array());
        $this->view->assign('form', $form);

        $genericProfil = new GenericProfile();
        $memberID = $this->_getParam('uid');
        $member = $genericProfil->findMembers(array('member_id'=>$memberID));
        if(count($member) <> 0){
            $newsletterProfile = new NewsletterProfile();
            $memberDetails = $newsletterProfile->getMemberDetails($memberID);

            $this->view->assign('memberEmail', $member[0]['email']);
        }

        if ($this->_request->isPost()) {
            $formData = $this->_request->getPost();
            if ($form->isValid($formData)){

                $formData['NP_SubscriptionDate'] = date("Y-m-d");
                $message = $this->view->getCibleText('newsletter_subscribe_confirmation_message1');
                $this->view->assign('reinscriptionValidate',true);
                $this->view->assign('message', $message);

                if(!empty($memberDetails)){
                    $memberDetails['NP_SubscriptionDate'] = date("Y-m-d");

                    $memberDetails['NP_TypeID']= !empty($memberDetails['NP_TypeID']) ? $memberDetails['NP_TypeID'] : $this->_config->newletter->defaultTypeId;
                    $genericProfil->updateMember($memberID,$memberDetails);
                    $newsletterProfile->updateMember($memberID,$memberDetails);
                }
            }
        }
    }
}
