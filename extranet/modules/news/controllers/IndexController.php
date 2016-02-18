<?php


class News_IndexController extends Cible_Controller_Categorie_Action
{

    protected $_moduleID = 2;
    protected $_moduleTitle   = 'news';
    protected $_name = 'index';
    protected $_defaultAction = 'list-all';
    protected $_paramId = 'newsID';
    protected $_imageSrc = 'ImageSrc';
    protected $_filterCategory = 0;

    public function init()
    {
        parent::init();
        $this->view->maxChar = $this->_config->news->maxChar;
    }

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
            $listParams .= "<div class='block_params_list'><strong>";
            $listParams .= $this->view->getCibleText('label_category');
            $listParams .= "</strong>" . $categoryName . "</div>";

            // Nombre d'events afficher
            $nbNewsShow = $blockParameters[1]['P_Value'];
            $listParams .= "<div class='block_params_list'><strong>";
            $listParams .= $this->view->getCibleText('label_number_to_show');
            $listParams .= "</strong>" . $nbNewsShow . "</div>";
        }

        return $listParams;
    }

    public function getIndexDescription($blockID = null)
    {

        $listParams = '';
        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
        if ($blockParameters)
        {
            $blockParams = $blockParameters->toArray();

            // Catégorie
            $categoryID = $blockParameters[0]['P_Value'];
            $categoryDetails = Cible_FunctionsCategories::getCategoryDetails($categoryID);
            $categoryName = $categoryDetails['CI_Title'];
            $listParams .= "<div class='block_params_list'><strong>Catégorie : </strong>" . $categoryName . "</div>";
        }

        // Nombre de news Online
        $listParams .= "<div class='block_params_list'><strong>Nouvelles en ligne : </strong>" . $this->getNewsOnlineCount($categoryID) . "</div>";

        return $listParams;
    }

    public function listTeamsAction()
    {
        $this->_filterCategory = 999;
        $this->_helper->viewRenderer->setRender('list-all');
        $this->listAllAction();
    }

    public function listAllAction()
    {
        if ($this->view->aclIsAllowed('news', 'edit', true))
        {
            $lang = $this->_getParam('lang');
            if (!$lang)
            {
                $this->_registry->currentEditLanguage = $this->_defaultEditLanguage;
                $langId = $this->_defaultEditLanguage;
            }
            else
            {
                $langId = Cible_FunctionsGeneral::getLanguageID($lang);
                $this->_registry->currentEditLanguage = $langId;
            }

            // NEW LIST GENERATOR CODE //
            $tables = array(
                'NewsData' => array('ND_ID', 'ND_CategoryID', 'ND_Date'),
                'NewsIndex' => array('NI_NewsDataID', 'NI_LanguageID', 'NI_Title', 'NI_Status'),
                'Status' => array('S_Code'),
                'CategoriesIndex' => array('CI_Title')
            );

            $field_list = array(
                'ND_ID' => array('width' => '50px', 'label' => $this->view->getCibleText('list_column_id')),
                'NI_Title' => array(
                    'width' => '300px'
                ),
                'ND_Date' => array('width' => '100px'),
                'CI_Title' => array('width' => '80px'),
                'S_Code' => array(
                    'width' => '80px',
                    'postProcess' => array(
                        'type' => 'dictionnary',
                        'prefix' => 'status_'
                    )
                )
            );

            $news = new NewsData();
            $select = $news->select()
                ->from('NewsData')
                ->setIntegrityCheck(false)
                ->join('NewsIndex', 'NewsData.ND_ID = NewsIndex.NI_NewsDataID')
                ->join('Status', 'NewsIndex.NI_Status = Status.S_ID')
                ->joinRight('CategoriesIndex', 'NewsData.ND_CategoryID = CategoriesIndex.CI_CategoryID')
                ->joinRight('Categories', 'NewsData.ND_CategoryID = Categories.C_ID')
                ->joinRight('Languages', 'Languages.L_ID = NewsIndex.NI_LanguageID')
                ->where('NI_LanguageID = ?', $langId)
                ->where('NewsIndex.NI_LanguageID = CategoriesIndex.CI_LanguageID')
                ->where('C_ModuleID = ?', $this->_moduleID)
                ->order('ND_Date DESC');
            if ($this->_filterCategory > 0){
                $select->where('C_ParentID = ?', $this->_filterCategory);
                $categories = Cible_FunctionsCategories::getFilterCategories($this->_moduleID,
                    $this->_filterCategory);
            }else{
                $select->where('C_ParentID = ?', 0);
                $categories = Cible_FunctionsCategories::getFilterCategories($this->_moduleID);
            }
            //->order('NI_Title');
            $commands = array();
            if ($langId == $this->_defaultEditLanguage)
                $commands = array(
                    $this->view->link($this->view->url(
                            array(
                                'controller' => 'index',
                                'action' => 'add',
                                'filterCategory' => $this->_filterCategory
                            )
                        ), $this->view->getCibleText('button_add_news'), array('class' => 'action_submit add')
                    )
                );
            $options = array(
                'commands' => $commands,
                //'disable-export-to-excel' => 'true',
                'to-excel-action' => 'all-news-to-excel',
                'filters' => array(
                    'news-category-filter' => array(
                        'label' => 'Filtre 1',
                        'default_value' => null,
                        'associatedTo' => 'ND_CategoryID',
                        'choices' => $categories
                    ),
                    'news-status-filter' => array(
                        'label' => 'Filtre 2',
                        'default_value' => null,
                        'associatedTo' => 'S_Code',
                        'choices' => array(
                            '' => $this->view->getCibleText('filter_empty_status'),
                            'online' => $this->view->getCibleText('status_online'),
                            'offline' => $this->view->getCibleText('status_offline')
                        )
                    )
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => $this->view->url(array(
                                'action' => 'edit',
                                $this->_paramId => "xIDx",
                                'lang' => "xLANGx",
                                '' => "xLANGx",
                                'filterCategory' => $this->_filterCategory
                                )),
                            'findReplace' => array(
                                 array(
                                'search' => 'xIDx',
                                'replace' => 'ND_ID'
                                ),
                                 array(
                                'search' => 'xLANGx',
                                'replace' => 'L_Suffix'
                                )
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => $this->view->url(array(
                                'action' => 'delete',
                                $this->_paramId => "xIDx",
                                'filterCategory' => $this->_filterCategory
                                )),
                            'findReplace' => array(
                                'search' => 'xIDx',
                                'replace' => 'ND_ID'
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
        // variables
        $pageID = $this->_getParam('pageID');
        $blockID = $this->_getParam('blockID');
        $returnAction = $this->_getParam('return');
        $baseDir = $this->view->baseUrl();
        $page = (int) $this->_getParam('page');
        $this->_filterCategory = $this->_getParam('filterCategory');
        $categoriesList = 'false';
        if (empty($pageID)){
            $categoriesList = 'true';
        }
        if ($this->_filterCategory > 0){
            $this->_defaultAction = 'list-teams';
        }
        $urlOptions = array('action' => $this->_defaultAction,
            $this->_paramId => null);
        $cancelUrl = $this->view->url($urlOptions);

        if ($returnAction)
            $returnUrl = $this->_moduleTitle . "/"
                    . $this->_name . "/"
                    . $returnAction;
        else
            $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                    'action' => $this->_defaultAction,
                    $this->_paramId => null
                )));

        $config = Zend_Registry::get('config')->toArray();
        $this->view->assign('showCrop', $config['news']['show']['crop']);

        $this->view->assign('thumbWidth',$config['news']['image']['thumb']['maxWidth']);
        $this->view->assign('thumbHeight',$config['news']['image']['thumb']['maxHeight']);

        if ($this->view->aclIsAllowed('news', 'edit', true))
        {
            $imageSource = $this->_setImageSrc(array(), $this->_imageSrc, null);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];

            // generate the form
            $form = new FormNews(array(
                    'baseDir' => $baseDir,
                    'imageSrc' => $imageSrc,
                    'cancelUrl' => $cancelUrl,
                    'categoriesList' => "$categoriesList",
                    'newsID' => '',
                    'isNewImage' => $isNewImage,
                    'showCrop' => true,
                    'isSpecial' => (bool)$this->_filterCategory,
                    'addAction' => true
                    /* ,
                      'toApprove' => 0,
                      'status'    => 2 */
                ));
            $this->view->form = $form;
            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();

                if($formData['cropImage']!=""){
                    $formData['ImageSrc'] = $formData['cropImage'];
                }

                if ($form->isValid($formData))
                {
                    $formData['checkValidAndAlert'] = false;
                    if ($this->_filterCategory > 0){
                        $formData['checkValidAndAlert'] = true;
                    }
                    if (!empty($pageID))
                    {
                        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
                        $formData['CategoryID'] = $blockParameters[0]['P_Value'];
                    }
                    else
                        $formData['CategoryID'] = $this->_getParam('Param1');

                    if ($formData['Status'] == 0)
                        $formData['Status'] = 2;

                    $newsObject = new NewsObject();

                    $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['Title']);
                    $formData['ValUrl'] = $formattedName;
                    $formData['ND_AuthorID'] = $_SESSION['user']->EU_ID;
                    $newsID = $newsObject->insert($formData, $this->_config->defaultEditLanguage);

                    /* IMAGES */
                    if (!is_dir($this->_imagesFolder . $newsID))
                    {
                        mkdir($this->_imagesFolder. $newsID) or die("Could not make directory");
                        mkdir($this->_imagesFolder . $newsID . "/tmp") or die("Could not make directory");
                    }
                    // Save image
                    $this->_setImage($this->_imageSrc, $formData, $newsID);

                    if ($formData['Status'] == 1)
                    {
                        //$blockData  = Cible_FunctionsBlocks::getBlockDetails($blockID);
                        //$blockStatus    = $blockData['B_Online'];

                        $indexData['pageID'] = $formData['CategoryID'];
                        $indexData['moduleID'] = $this->_moduleID;
                        $indexData['contentID'] = $newID;
                        $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                        $indexData['title'] = $formData['Title'];
                        $indexData['text'] = '';
                        $indexData['link'] = $formData['Date'] . '/' . $formData['ValUrl'];
                        $indexData['object'] = 'NewsObject';
                        $indexData['contents'] = $formData['Title'] . " " . $formData['Brief'] . " " . $formData['Text'] . " " . $formData['ImageAlt'];
                        $indexData['action'] = 'add';

                        Cible_FunctionsIndexation::indexation($indexData);
                    }


                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                    'action' => 'edit',
                                    $this->_paramId => $newsID
                                )))
                        );
                }
                else
                    $form->populate($formData);
            }
        }
    }

    public function editAction()
    {
        $this->_editMode = true;
        // variables
        $newsID = $this->_getParam($this->_paramId);
        $pageID = $this->_getParam('pageID');
        $returnAction = $this->_getParam('return');
        $blockID = $this->_getParam('blockID');
        $baseDir = $this->view->baseUrl();
        $page = (int) $this->_getParam('page');
        $this->_filterCategory = $this->_getParam('filterCategory');
        if ($this->view->aclIsAllowed('news', 'edit', true))
        {
            if (empty($pageID))
                $categoriesList = 'true';
            else
                $categoriesList = 'false';
            if ($this->_filterCategory > 0){
                $this->_defaultAction = 'list-teams';
            }
            $cancelUrl = $this->view->url(array(
                        'action' => $this->_defaultAction,
                        $this->_paramId => null
                    ));

            if ($returnAction)
                $returnUrl = $this->_moduleTitle . "/"
                        . $this->_name . "/"
                        . $returnAction;
            else
                $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_defaultAction,
                        $this->_paramId => null
                    )));

            $newsObject = new NewsObject();
            $news = $newsObject->populate($newsID, $this->_currentEditLanguage);
            if($news['ReleaseDateEnd']=="0000-00-00"){
                $news['ReleaseDateEnd'] = "";
            }

            // image src.
            $config = Zend_Registry::get('config')->toArray();
            $thumbMaxHeight = $config['news']['image']['thumb']['maxHeight'];
            $thumbMaxWidth = $config['news']['image']['thumb']['maxWidth'];
            $showCrop = isset($config['news']['image']['crop']) ?
                (bool)$config['news']['image']['crop'] : false;
            $this->view->assign('showCrop', $showCrop);
            $this->view->assign($this->_paramId,$newsID);
            $this->view->assign('thumbWidth',$config['news']['image']['thumb']['maxWidth']);
            $this->view->assign('thumbHeight',$config['news']['image']['thumb']['maxHeight']);
            $this->view->assign('originalWidth',$config['news']['image']['original']['maxWidth']);
            $this->view->assign('originalHeight',$config['news']['image']['original']['maxHeight']);

            $imageSource = $this->_setImageSrc($news, $this->_imageSrc, $newsID);
            $imageSrc = $imageSource['imageSrc'];
            $isNewImage = $imageSource['isNewImage'];

            if (empty($pageID))
               $categoriesList = 'true';
            else
                $categoriesList = 'false';

            // generate the form
            $form = new FormNews(array(
                    'baseDir' => $baseDir,
                    'imageSrc' => $imageSrc,
                    'cancelUrl' => $cancelUrl,
                    'categoriesList' => $categoriesList,
                    'newsID' => $newsID,
                    'showCrop' => $showCrop,
                    'catagoryID' => $news['CategoryID'],
                    'isNewImage' => $isNewImage,
                    'isSpecial' => (bool)$this->_filterCategory,
                ));
            $this->view->form = $form;

            // action
            if (!$this->_request->isPost())
            {
                if (isset($news['Status']) && $news['Status'] == 2)
                    $news['Status'] = 0;

                $form->populate($news);
            }
            else
            {
                $formData = $this->_request->getPost();

                if($formData['cropImage']!="")
                    $formData['ImageSrc'] = $formData['cropImage'];

                if ($form->isValid($formData))
                {
                    if ($formData[$this->_imageSrc] <> ''  && $isNewImage)
                        $this->_setImage($this->_imageSrc, $formData, $newsID, $isNewImage);
                    else if ($formData['cropImage']!=""){
                        $config = Zend_Registry::get('config')->toArray();

                        $srcOriginal = "../../{$this->_config->document_root}/data/images/news/$newsID/" . $formData['cropImage'];

                        $originalMaxHeight = $config['news']['image']['original']['maxHeight'];
                        $originalMaxWidth = $config['news']['image']['original']['maxWidth'];
                        $originalName = str_replace($form->getValue('ImageSrc'), $originalMaxWidth . 'x' . $originalMaxHeight . '_' . $formData['cropImage'], $formData['cropImage']);
                        $originalNameToCopy = "../../{$this->_config->document_root}/data/images/news/$newsID/" . $originalName;

                        $srcMedium = "../../{$this->_config->document_root}/data/images/news/$newsID/tmp/medium_{$form->getValue('ImageSrc')}";
                        $mediumMaxHeight = $config['news']['image']['medium']['maxHeight'];
                        $mediumMaxWidth = $config['news']['image']['medium']['maxWidth'];
                        $mediumName = str_replace($form->getValue('ImageSrc'), $mediumMaxWidth . 'x' . $mediumMaxHeight . '_' . $formData['cropImage'], $formData['cropImage']);

                        $srcThumb = "../../{$this->_config->document_root}/data/images/news/$newsID/tmp/thumb_{$form->getValue('ImageSrc')}";
                        $thumbMaxHeight = $config['news']['image']['thumb']['maxHeight'];
                        $thumbMaxWidth = $config['news']['image']['thumb']['maxWidth'];
                        $thumbName = str_replace($form->getValue('ImageSrc'), $thumbMaxWidth . 'x' . $thumbMaxHeight . '_' . $formData['cropImage'], $formData['cropImage']);

                        copy($originalNameToCopy, $srcMedium);
                        copy($originalNameToCopy, $srcThumb);

                        Cible_FunctionsImageResampler::resampled(array('src' => $srcMedium, 'maxWidth' => $mediumMaxWidth, 'maxHeight' => $mediumMaxHeight));
                        Cible_FunctionsImageResampler::resampled(array('src' => $srcThumb, 'maxWidth' => $thumbMaxWidth, 'maxHeight' => $thumbMaxHeight));

                        rename($srcMedium, "../../{$this->_config->document_root}/data/images/news/$newsID/$mediumName");
                        rename($srcThumb, "../../{$this->_config->document_root}/data/images/news/$newsID/$thumbName");
                    }
                    if ($formData['Status'] == 0)
                        $formData['Status'] = 2;

                    $formattedName = Cible_FunctionsGeneral::formatValueForUrl($formData['Title']);
                    $formData['ValUrl'] = $formattedName;
                    $formData['CategoryID'] = $this->_getParam('Param1');
                    $newsObject->save($newsID, $formData, Zend_Registry::get("currentEditLanguage"));

                    $indexData = array();
                    $indexData['pageID'] = $formData['CategoryID'];
                    $indexData['moduleID'] = $this->_moduleID;
                    $indexData['contentID'] = $newsID;
                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                    $indexData['title'] = $formData['Title'];
                    $indexData['text'] = '';
                    $indexData['link'] = $formData['Date'] . '/' . $formData['ValUrl'];
                    $indexData['object'] = 'NewsObject';
                    $indexData['contents'] = $formData['Title'] . " " . $formData['Brief'] . " " . $formData['Text'] . " " . $formData['ImageAlt'];
                    $indexData['action'] = '';

                    if ($formData['Status'] == 1)
                        $indexData['action'] = 'update';
                    else
                        $indexData['action'] = 'delete';

                    Cible_FunctionsIndexation::indexation($indexData);
                    if ($this->_filterCategory > 0
                        && $data['Status'] != $formData['Status']
                        && $formData['Status'] == 1){
                        $label = $form->getElement('Param1')->getMultiOption($formData['Param1']);
                        list($tId, $tName) = explode('-', $label);
                        $params = array('id' => trim($tId),
                            'newsTitle' => $formData['Title'],
                            'event' => 'teamNewsValid');
                        $this->view->action('alert', 'index', 'teams', $params);
                    }
                    if (isset($formData['submitSaveClose']))
                        $this->_redirect($returnUrl);
                    else
                    {
                        $this->_redirect(str_replace($this->view->baseUrl(), '', $this->view->url(array(
                                    'action' => 'edit',
                                    $this->_paramId => $newsID
                                )))
                        );
                    }
                }
            }
        }
    }

    public function deleteAction()
    {
        // variables
        $pageID = (int) $this->_getParam('pageID');
        $blockID = (int) $this->_getParam('blockID');
        $newsID = (int) $this->_getParam($this->_paramId);
        $this->_filterCategory = $this->_getParam('filterCategory');
        if ($this->_filterCategory > 0){
            $this->_defaultAction = 'list-teams';
        }
        $returnUrl = str_replace($this->view->baseUrl(), '', $this->view->url(array(
                        'action' => $this->_defaultAction,
                        $this->_paramId => null
                    )));

        $this->view->assign(
                'return',
                $this->view->baseUrl() . "/" . $returnUrl
        );
        if ($this->view->aclIsAllowed('news', 'edit', true))
        {
            $this->view->return = !empty($pageID) ? $this->view->baseUrl() . "/news/index/list/blockID/$blockID/pageID/$pageID" : $this->view->baseUrl() . "/news/index/list-all/";

            $newsObject = new NewsObject();

            if ($this->_request->isPost())
            {
                $del = $this->_request->getPost('delete');
                if ($del && $newsID > 0)
                {
                    $newsObject->delete($newsID);
                    $indexData['moduleID'] = $this->_moduleID;
                    $indexData['contentID'] = $newsID;
                    $indexData['languageID'] = Zend_Registry::get("currentEditLanguage");
                    $indexData['action'] = 'delete';
                    Cible_FunctionsIndexation::indexation($indexData);

                    Cible_FunctionsGeneral::delFolder($this->_imagesFolder . $newsID);
                }

                $this->_redirect($returnUrl);
            }
            elseif ($newsID > 0)
                $this->view->news = $newsObject->populate($newsID, $this->_defaultEditLanguage);
        }
    }

    public function toExcelAction()
    {
        $this->filename = 'News.xlsx';

        $tables = array(
            'NewsData' => array('ND_ID', 'ND_CategoryID', 'ND_Date', 'ND_ReleaseDate'),
            'NewsIndex' => array('NI_NewsDataID', 'NI_LanguageID', 'NI_Title', 'NI_Status'),
            'Status' => array('S_Code')
        );

        $this->fields = array(
            'NI_Title' => array(
                'width' => '',
                'label' => ''
            ),
            'ND_ReleaseDate' => array(
                'width' => '',
                'label' => ''
            ),
            'ND_Date' => array(
                'width' => '',
                'label' => ''
            ),
            'S_Code' => array(
                'width' => '',
                'label' => ''
            )
        );

        $this->filters = array(
        );

        $this->view->params = $this->_getAllParams();
        $blockID = $this->_getParam('blockID');
        $pageID = $this->_getParam('pageID');

        $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);

        $categoryID = $blockParameters[0]['P_Value'];

        $news = new NewsData();
        $this->select = $this->_db->select()
                ->from('NewsData')
                //->setIntegrityCheck(false)
                ->join('NewsIndex', 'NewsData.ND_ID = NewsIndex.NI_NewsDataID')
                ->join('Status', 'NewsIndex.NI_Status = Status.S_ID')
                ->where('ND_CategoryID = ?', $categoryID)
                ->where('CA_LanguageID = ?', $this->_defaultEditLanguage)
                ->where('NI_LanguageID = ?', $this->_defaultEditLanguage)
                ->order('NI_Title');

        parent::toExcelAction();
    }

    public function allNewsToExcelAction()
    {
        $this->filename = 'News.xlsx';

        $tables = array(
            'NewsData' => array('ND_ID', 'ND_CategoryID', 'ND_Date', 'ND_ReleaseDate'),
            'NewsIndex' => array('NI_NewsDataID', 'NI_LanguageID', 'NI_Title', 'NI_Status'),
            'Status' => array('S_Code'),
            'CategoriesIndex' => array('CI_Title'),
        );

        $this->fields = array(
            'NI_Title' => array(
                'width' => '',
                'label' => ''
            ),
            'ND_ReleaseDate' => array(
                'width' => '',
                'label' => ''
            ),
            'ND_Date' => array(
                'width' => '',
                'label' => ''
            ),
            'CI_Title' => array(
                'width' => '',
                'label' => ''
            ),
            'S_Code' => array(
                'width' => '',
                'label' => ''
            )
        );

        $this->filters = array(
        );

        $this->view->params = $this->_getAllParams();

        $news = new NewsData();
        $this->select = $this->_db->select()
                ->from('NewsData')
                //->setIntegrityCheck(false)
                ->join('NewsIndex', 'NewsData.ND_ID = NewsIndex.NI_NewsDataID')
                ->join('Status', 'NewsIndex.NI_Status = Status.S_ID')
                ->join('CategoriesIndex', 'NewsData.ND_CategoryID = CategoriesIndex.CI_CategoryID')
                ->where('NI_LanguageID = ?', $this->_defaultEditLanguage)
                ->order('NI_Title');

        $blockID = $this->_getParam('blockID');
        $pageID = $this->_getParam('pageID');

        if ($blockID && $pageID)
        {
            $blockParameters = Cible_FunctionsBlocks::getBlockParameters($blockID);
            $categoryID = $blockParameters[0]['P_Value'];

            $this->select->where('ND_CategoryID = ?', $categoryID);
        }

        parent::toExcelAction();
    }

    public function listApprobationRequestAction()
    {
        if ($this->view->aclIsAllowed('news', 'edit'))
        {

            $tables = array(
                'NewsData' => array('ND_ID', 'ND_CategoryID', 'ND_ReleaseDate'),
                'NewsIndex' => array('NI_NewsDataID', 'NI_LanguageID', 'NI_Title', 'NI_Status'),
                'CategoriesIndex' => array('CI_Title')
            );

            $field_list = array(
                'NI_Title' => array(
                    'width' => '400px'
                ),
                'CI_Title' => array(
                /* 'width' => '80px',
                  'postProcess' => array(
                  'type' => 'dictionnary',
                  'prefix' => 'status_'
                  ) */
                ),
                'ND_ReleaseDate' => array(
                    'width' => '120px'
                )
            );

            $news = new NewsData();
            $select = $news->select()
                    ->from('NewsData')
                    ->setIntegrityCheck(false)
                    ->join('NewsIndex', 'NewsData.ND_ID = NewsIndex.NI_NewsDataID')
                    ->joinRight('CategoriesIndex', 'NewsData.ND_CategoryID = CategoriesIndex.CI_CategoryID')
                    ->joinRight('Languages', 'Languages.L_ID = NewsIndex.NI_LanguageID')
                    ->where('NewsData.ND_ToApprove = ?', 1)
                    ->where('NewsIndex.NI_LanguageID = CategoriesIndex.CI_LanguageID')
                    ->order('NI_Title');


            $options = array(
                'disable-export-to-excel' => 'true',
                'filters' => array(
                    'filter_1' => array(
                        'default_value' => null,
                        'associatedTo' => 'CI_Title',
                        'choices' => Cible_FunctionsCategories::getFilterCategories($this->_moduleID)
                    ),
                    'filter_2' => array(
                        'default_value' => null,
                        'associatedTo' => 'CI_LanguageID',
                        'choices' => Cible_FunctionsGeneral::getFilterLanguages()
                    )
                ),
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => "{$this->view->baseUrl()}/news/index/edit/newsID/%ID%/lang/%LANG%/approbation/true",
                            'findReplace' => array(
                                array(
                                    'search' => '%ID%',
                                    'replace' => 'ND_ID'
                                ),
                                array(
                                    'search' => '%LANG%',
                                    'replace' => 'L_Suffix'
                                )
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

            $this->view->assign('mylist', $mylist);
        }
    }

    public function addCategoriesAction()
    {

        if ($this->view->aclIsAllowed($this->view->current_module, 'edit'))
        {
            $categoriesObject = new CategoriesObject();
            $cancelUrl = $this->view->url(array(
                'action' => 'list-categories',
                'ID' => null
            ));
            $returnUrl = str_replace($this->view->baseUrl(), '',
                $this->view->url(array(
                    'action' => 'list-categories',
                    'ID' => null
            )));
            $options = array(
                'moduleID' => $this->_moduleID,
                'cancelUrl' => $cancelUrl,
                'addAction' => true
            );

            $form = new NewsFormCategory($options);

            $this->view->assign('form', $form);

            if ($this->_request->isPost())
            {
                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    // save
                    $category_id = $categoriesObject->insert($formData, $this->_defaultEditLanguage);

                    $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID);

                    foreach ($views as $view){

                        $data = array(
                            'MCVP_ModuleID' => $this->_moduleID,
                            'MCVP_CategoryID' => $category_id,
                            'MCVP_ViewID' => $view['MV_ID'],
                            'MCVP_PageID' => $formData["{$view['MV_Name']}_pageID"]
                        );
                        if (!empty($formData["{$view['MV_Name']}_pageID"]))
                            $this->_db->insert('ModuleCategoryViewPage', $data);
                    }
                    // redirect
                    if(!isset($formData['submitSaveClose'])){
                        $this->_redirect(str_replace($this->view->baseUrl(), '',
                                $this->view->url(array(
                                    'action' => 'edit-categories',
                                    'ID' => $category_id
                        ))));
                    }
                    $this->_redirect($returnUrl);
                } else{
                    $form->populate($formData);
                }
            }
        }
    }

    public function editCategoriesAction()
    {

        if ($this->view->aclIsAllowed($this->view->current_module, 'edit'))
        {
            $id = $this->_getParam('ID');

            $categoriesObject = new CategoriesObject();

            $cancelUrl = $this->view->url(array(
                'action' => 'list-categories',
                'ID' => null
            ));
            $returnUrl = str_replace($this->view->baseUrl(), '',
                $this->view->url(array(
                    'action' => 'list-categories',
                    'ID' => null
            )));
             $options = array(
                'moduleID' => Cible_FunctionsModules::getModuleIDByName($this->view->current_module),
                'cancelUrl' => $cancelUrl
             );

            $form = new NewsFormCategory($options);

            $this->view->assign('form', $form);

            if ($this->_request->isPost())
            {

                $formData = $this->_request->getPost();
                if ($form->isValid($formData))
                {
                    // save
                    $categoriesObject->save($id, $formData, $this->_currentEditLanguage);

                    $allViews = Cible_FunctionsCategories::getCategoryViews($this->_moduleID);
                    $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID, $id);

                    $reference_views = array();

                    foreach ($views as $view)
                        $reference_views[$view['MV_ID']] = $view;

                    $views = $reference_views;
                    $this->view->dump($views);

                    foreach ($allViews as $view)
                    {
                        $this->view->dump($view);
                        $data = array(
                            'MCVP_ModuleID' => $this->_moduleID,
                            'MCVP_CategoryID' => $id,
                            'MCVP_ViewID' => $view['MV_ID'],
                            'MCVP_PageID' => $formData["{$view['MV_Name']}_pageID"]
                        );

                        if (!empty($formData["{$view['MV_Name']}_pageID"]))
                        {

                            if (isset($views[$view['MV_ID']]) && isset($views[$view['MV_ID']]['MCVP_ID']))
                                $this->_db->update('ModuleCategoryViewPage', $data, "MCVP_ID = '{$views[$view['MV_ID']]['MCVP_ID']}'");
                            else
                                $this->_db->insert('ModuleCategoryViewPage', $data);
                        }
                    }
                    // redirect
                    if(!isset($formData['submitSaveClose'])){
                        $this->_redirect(str_replace($this->view->baseUrl(), '',
                                $this->view->url(array(
                                    'action' => 'edit-categories',
                                    'ID' => $id
                        ))));
                    }
                    $this->_redirect($returnUrl);
                } else
                {

                    $formData = $this->_request->getPost();
                    $form->populate($formData);
                }
            }
            else
            {
                $data = $categoriesObject->populate($id, $this->_currentEditLanguage);

                $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID, $id);

                if ($views)
                {
                    foreach ($views as $view)
                    {
                        if (!empty($view['MCVP_PageID']))
                        {
                            $data["{$view['MV_Name']}_pageID"] = $view['MCVP_PageID'];
                            $data["{$view['MV_Name']}_controllerName"] = $view['PI_PageIndex'];
                        }
                    }
                }
                $form->populate(
                    $data
                );
            }
        }
    }

    public function deleteCategoriesAction()
    {

        if ($this->view->aclIsAllowed($this->view->current_module, 'edit'))
        {
            $id = $this->_getParam('ID');

            if ($this->_request->isPost() && isset($_POST['delete']))
            {
                $this->_db->delete('ModuleCategoryViewPage', $this->_db->quoteInto('MCVP_CategoryID = ?', $id));
                $this->_db->delete('Categories', "C_ID = '$id'");
                $this->_db->delete('CategoriesIndex', "CI_CategoryID = '$id'");

                $this->_redirect("/news/index/list-categories/");
            }
            else if ($this->_request->isPost() && isset($_POST['cancel']))
            {
                $this->_redirect('/news/index/list-categories/');
            }
            else
            {
                $fails = false;
                $related = array();
                $oCategories = new CategoriesObject();
                $categoryName = $oCategories->getTitle($id);

                $this->view->assign('category_id', $id);
                $this->view->assign('category_name', $categoryName);

                $oObj = new NewsObject();
                $result = $oObj->getListForCategory($id);

                if ($result){
                    $fails = true;
                    $related['content'] = $result;
                }

                $resultB = Cible_FunctionsBlocks::getBlocksWithParameters(1, $id, $this->_moduleID);
                if(count($resultB) > 0){
                    $fails = true;
                    $related['blocks'] = $resultB;
                }

                $this->view->assign('module_name', $this->_moduleName);
                $this->view->assign('module_id', $this->_moduleID);
                $this->view->assign('returnUrl', '/news/index/list-categories/');
                $this->view->assign('fails', $fails);
                $this->view->assign('module', $this->_moduleTitle);
                $this->view->assign('related', $related);
            }
        }
    }

    private function getNewsOnlineCount($categoryID)
    {
        return $this->_db->fetchOne("SELECT COUNT(*) FROM NewsData LEFT JOIN NewsIndex ON NewsData.ND_ID = NewsIndex.NI_NewsDataID WHERE ND_CategoryID = '$categoryID' AND NI_Status = '1'");
    }


    function cropimageAction(){
        $params = $this->_request->getParams();
        $image = $params['image'];

        $config = $this->_config->toArray();
        $headerWidth = $config['news']['image']['original']['maxWidth'];
        $headerHeight = $config['news']['image']['original']['maxHeight'];

        $imageS = $this->_imagesFolder . "tmp/" . $image;
        $imageSource = $this->_rootImgPath . "tmp/" . $image;

        $this->_headerWidth = $headerWidth;
        $this->_headerHeight = $headerHeight;
        $this->_imageSource = $imageSource;

        $this->_showActionButton = false;

        parent::cropimageAction();
    }

    function cropeditimageAction(){
        $params = $this->_request->getParams();

        $imageFolder = $this->_imagesFolder . $params['newsID'] . "/";
        $rootImgPath = $this->_rootImgPath . $params['newsID'] . "/";
        $image = $params['image'];

        $config = Zend_Registry::get('config')->toArray();
        $headerWidth = $config['news']['image']['original']['maxWidth'];
        $headerHeight = $config['news']['image']['original']['maxHeight'];

        if($params['new']=='N'){
            $imageS = $imageFolder . $headerWidth . "x" . $headerHeight . "_" . $image;
            $imageSource = $rootImgPath . $headerWidth . "x" . $headerHeight . "_" . $image;
        }
        else{
            $imageS = $imageFolder . "/tmp/" . $image;
            $imageSource = $rootImgPath . "/tmp/" . $image;
        }

        $this->_headerWidth = $headerWidth;
        $this->_headerHeight = $headerHeight;
        $this->_imageSource = $imageSource;

        $this->_showActionButton = false;
        parent::cropimageAction();
    }

    public function formatNameAction()
    {
        $this->disableView();
        $oObj = new NewsObject();

        $select = $oObj->getAll(null, false);
//        $select->where('PI_ValUrl is NULL');
        $db = Zend_Registry::get('db');
        $field = 'Title';
        $fieldVal = 'ValUrl';
        $data = $db->fetchAll($select);
        foreach ($data as $values)
        {
            $formatted = Cible_FunctionsGeneral::formatValueForUrl($values[$field]);

            $tmpdata = array($fieldVal => $formatted);
            try
            {
                $oObj->save($values[$oObj->getDataId()], $tmpdata, $values[$oObj->getIndexLanguageId()]);
            }
            catch (Exception $exc)
            {
                echo $exc->getTraceAsString();
            }

        }
    }

    public function deleteEldersAction()
    {
        $obj = new NewsObject();
        $obj->deleteOld();

    }

    public function traverseHierarchyAction()
    {
        $this->disableView();
        $this->importImages();
    }

    public function importImages()
    {
        $obj = new NewsObject();
        $data = $obj->getAll(1);
        foreach ($data as $values)
        {
            $id = $values[$obj->getDataId()];
            $img = $values[$this->_imageSrc];
            $path = $_SERVER['DOCUMENT_ROOT'] . $this->_rootImgPath . $id;
            if (!is_dir($path))
            {
                mkdir ($path);
                mkdir ($path . '/tmp');
            }

            if (!empty($img))
                $this->_setImage($this->_imageSrc, $values, $id);

        }

    }

    /**
     * Create a dorpdown list for the association to do
     * Retrieve parameters from url parameters sent via ajax.
     *
     * @return void
     */
    public function ajaxAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $actionAjax = $this->_getParam('actionAjax');
        $oCategory = new CategoriesObject();
        switch($actionAjax)
        {
            case 'addCategory':

                break;
            case 'editCategory':
                break;
            default:
                break;
        }
        return $data;
    }
}
