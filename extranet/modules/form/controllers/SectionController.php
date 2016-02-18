<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SectionController
 *
 * @author soaser
 */
class Form_SectionController extends Cible_Extranet_Controller_Module_Action
{
    public function deleteAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);

            $deleted = $oForm->deleteAll($initData['id']);

            echo $deleted;
            exit;
        }
    }

    /**
     * Add a new section to the form
     *
     * @return void
     */
    public function addAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            $oForm    = new FormSectionObject();
            $initData = $oForm->getInitialData($data);

            if (count($initData) > 0)
            {
                $saved = $oForm->insert($data, $initData['lang']);
                $languages = Cible_FunctionsGeneral::getAllLanguage();
                foreach ($languages as $key => $lang)
                {
                    if ($lang['L_ID'] != $initData['lang'])
                    {
                        $oForm->save($saved, $data, $lang['L_ID']);
                    }
                }
            }
            else
            {
                $saved = '0';
            }

            echo $saved;
        }
        exit;

    }

    public function editAction()
    {
        // Tests if the user has permissions
        if ($this->view->aclIsAllowed($this->view->current_module, 'manage', true))
        {

        }
    }

    /**
     * Manage the update of the sections parameters sent via ajax (jQuery)
     *
     * @return void
     */
    public function updateAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $saved = false;
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            if($oForm->recordExists($data, $initData['lang']))
                $saved = $oForm->save($initData['id'], $data, $initData['lang']);

            echo $saved;
            exit;
        }
    }

    public function getSectionsAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']);
            else
                $dataObject = ucfirst($data['model']);

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            $saved    = $oForm->populate($initData['id'], $initData['lang']);

            echo $saved;
            exit;
        }
    }

    public function showAction()
    {
        $formId  = $this->_getParam('id');
        $langId  = $this->_getParam('langID');
        $section = new FormSectionObject();
        $html    = $section->show($formId, $langId, true);

        $data = array('html'=> $html);
        echo(json_encode($data));
        exit;
    }

    public function showBreakPageAction(){
        $section = new FormSectionObject();
        $html = $section->showBreakPage();


        $data = array('html'=>$html);
        echo(json_encode($data));
        exit;
    }
}
?>
