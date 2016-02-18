<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ElementController
 *
 * @author soaser
 */
class Form_ElementController extends Cible_Extranet_Controller_Module_Action
{

    public function deleteAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $oData = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $oData = ucfirst($data['model']) . 'Object';

            $oForm    = new $oData();
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

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);

            if (count($initData) > 0)
            {
                $saved = $oForm->insert($data, 1);
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
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            $saved    = $oForm->update($initData['id'], $data);

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
        $id = $this->_getParam('id');
        $type = $this->_getParam('type');
        $langId = ($this->_registry->currentEditLanguage);

        if($type == 'textZone'){
            $item = new FormTextObject();
            $html = $item->show($id);
        }
        elseif($type == 'question'){
            $questionType = $this->_getParam('questionType');
            $item = new FormQuestionObject();
            $html = $item->show($id, $questionType, $langId);
        }


        $data = array('html'=>  $html);
        echo(json_encode($data));
        exit;
    }
}
?>
