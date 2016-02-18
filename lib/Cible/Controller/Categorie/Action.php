<?php

abstract class Cible_Controller_Categorie_Action extends Cible_Controller_Block_Abstract
{
    public function listCategoriesAction(){

        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){

             $tables = array(
                    'Categories' => array('C_ID'),
                    'CategoriesIndex' => array('CI_Title','CI_WordingShowAllRecords')
            );

            $field_list = array(
                'CI_Title' => array(
                    'label' => $this->view->getCibleText("list_column_{$this->_moduleID}_CI_Title"),
                    'width' => '150px'
                ),
                'CI_WordingShowAllRecords' => array(

                )
            );

            $select = $this->_db->select()->from('Categories', $tables['Categories'])
                                    ->joinInner('CategoriesIndex', 'Categories.C_ID = CategoriesIndex.CI_CategoryID', $tables['CategoriesIndex'])
                                    ->where('Categories.C_ModuleID = ?', Cible_FunctionsModules::getModuleIDByName($this->view->current_module))
                                    ->where('CategoriesIndex.CI_LanguageID = ?', $this->_defaultEditLanguage);

            $options = array(
                'commands' => array(
                    $this->view->link($this->view->url(array('controller'=> 'index','action'=>'add-categories')),$this->view->getCibleText("{$this->view->current_module}_button_add_category"), array('class'=>'action_submit add') )
                ),
                'to-excel-action' => 'categories-to-excel',
                'action_panel' => array(
                    'width' => '50',
                    'actions' => array(
                        'edit' => array(
                            'label' => $this->view->getCibleText('button_edit'),
                            'url' => "{$this->view->baseUrl()}/{$this->view->current_module}/index/edit-categories/ID/%ID%",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'C_ID'
                            )
                        ),
                        'delete' => array(
                            'label' => $this->view->getCibleText('button_delete'),
                            'url' => "{$this->view->baseUrl()}/{$this->view->current_module}/index/delete-categories/ID/%ID%",
                            'findReplace' => array(
                                'search' => '%ID%',
                                'replace' => 'C_ID'
                            )
                        )
                    )
                )
            );

            $mylist = New Cible_Paginator($select, $tables, $field_list, $options);

            $this->view->assign('mylist', $mylist);
        }
    }

    public function addCategoriesAction(){

        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){
            $categoriesObject = new CategoriesObject();
            $options = array(
                'moduleID' => $this->_moduleID,
                'cancelUrl' => $this->view->url(array('module'=> $this->view->current_module, 'controller' => 'index', 'action' => 'list-categories'))
             );

             $form = new FormCategory($options);

             $this->view->assign('form', $form);

             if ($this->_request->isPost()){

                 $formData = $this->_request->getPost();
                 if( $form->isValid($formData) ){

                     // save
                     $category_id = $categoriesObject->insert($formData, $this->_currentEditLanguage);

                     $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID);

                     foreach( $views as $view ){

                         $data = array(
                            'MCVP_ModuleID' => $this->_moduleID,
                            'MCVP_CategoryID' => $category_id,
                            'MCVP_ViewID' => $view['MV_ID'],
                            'MCVP_PageID' => $formData["{$view['MV_Name']}_pageID"]
                         );
                         if( !empty($formData["{$view['MV_Name']}_pageID"]) )
                             $this->_db->insert('ModuleCategoryViewPage', $data);
                     }

                     $this->_redirect(
                        "{$this->view->current_module}/index/list-categories"
                     );

                 } else {

                     $form->populate( $formData );

                 }
             }
        }
    }

    public function editCategoriesAction(){

        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){
            $id = $this->_getParam('ID');

             $categoriesObject = new CategoriesObject();

             $options = array(
                'moduleID' => Cible_FunctionsModules::getModuleIDByName($this->view->current_module),
                'cancelUrl' => "{$this->view->baseUrl()}/{$this->view->current_module}/index/list-categories/"
             );

             $form = new FormCategory($options);

             $this->view->assign('form', $form);

             if ($this->_request->isPost()){

                 $formData = $this->_request->getPost();
                 if( $form->isValid($formData) ){
                     // save
                     $categoriesObject->save($id, $formData, $this->_currentEditLanguage);

                     $allViews = Cible_FunctionsCategories::getCategoryViews($this->_moduleID);
                     $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID, $id);

                     $reference_views = array();

                     foreach($views as $view)
                         $reference_views[$view['MV_ID']] = $view;

                     $views = $reference_views;
                     $this->view->dump($views);

                     foreach( $allViews as $view ){
                         $this->view->dump($view);
                         $data = array(
                            'MCVP_ModuleID' => $this->_moduleID,
                            'MCVP_CategoryID' => $id,
                            'MCVP_ViewID' => $view['MV_ID'],
                            'MCVP_PageID' => $formData["{$view['MV_Name']}_pageID"]
                         );

                         if( !empty($formData["{$view['MV_Name']}_pageID"]) ){

                             if( isset($views[$view['MV_ID']] ) && isset($views[$view['MV_ID']]['MCVP_ID']) )
                                $this->_db->update('ModuleCategoryViewPage', $data, "MCVP_ID = '{$views[$view['MV_ID']]['MCVP_ID']}'");
                             else
                                $this->_db->insert('ModuleCategoryViewPage', $data);
                        }
                     }
                     $this->_redirect("{$this->view->current_module}/index/list-categories/");

                 } else {

                     $formData = $this->_request->getPost();
                     $form->populate( $formData );

                 }

             } else {
                 $data = $categoriesObject->populate($id, $this->_currentEditLanguage);

                 $views = Cible_FunctionsCategories::getCategoryViews($this->_moduleID, $id);

                 if($views){
                     foreach($views as $view){
                         if( !empty($view['MCVP_PageID']) ){
                            $data[ "{$view['MV_Name']}_pageID" ] = $view['MCVP_PageID'];
                            $data[ "{$view['MV_Name']}_controllerName" ] = $view['PI_PageIndex'];
                         }
                     }
                 }

                 $form->populate(
                    $data
                 );
             }
        }
    }

    public function deleteCategoriesAction(){
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){
            Throw new Exception("Not implemented, please implement this method in the module {$this->_moduleName}");
        }
    }

    public function categoriesToExcelAction(){
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') ){

            $this->filename = 'Categories.xlsx';

            $this->tables = array(
                    'Categories' => array('C_ID'),
                    'CategoriesIndex' => array('CI_Title','CI_WordingShowAllRecords')
            );

            $this->fields = array(
                'CI_Title' => array(
                    'label' => $this->view->getCibleText("list_column_{$this->_moduleID}_CI_Title")
                ),
                'CI_WordingShowAllRecords' => array(

                )
            );

            $this->filters = array();

            $this->select = $this->_db->select()->from('Categories', $this->tables['Categories'])
                                    ->joinInner('CategoriesIndex', 'Categories.C_ID = CategoriesIndex.CI_CategoryID', $this->tables['CategoriesIndex'])
                                    ->where('Categories.C_ModuleID = ?', Cible_FunctionsModules::getModuleIDByName($this->view->current_module))
                                    ->where('CategoriesIndex.CI_LanguageID = ?', $this->_currentInterfaceLanguage);

            parent::toExcelAction();
        }
    }

}
