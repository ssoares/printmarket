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
class Form_QuestionController extends Cible_Extranet_Controller_Module_Action
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

            $oForm->deleteAll($initData['id']);

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
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            $saved    = $oForm->save($initData['id'], $data, $initData['lang']);

            echo $saved;
            exit;
        }
    }

//    public function getSectionsAction()
//    {
//        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
//        {
//            $data   = $this->_getAllParams();
//
//            if ($data['model'] != 'Form')
//                $dataObject = 'Form' . ucfirst($data['model']);
//            else
//                $dataObject = ucfirst($data['model']);
//
//            $oForm    = new $dataObject();
//            $initData = $oForm->getInitialData($data);
//            $saved    = $oForm->populate($initData['id'], $initData['lang']);
//
//            echo $saved;
//            exit;
//        }
//    }

    public function addValidatorAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

            if ($data['FQVT_Category'] == 'VAL')
            {
                unset($data['FQVT_Category']);
                $oValid   = new $dataObject();
                $initData = $oValid->getInitialData($data);
                $saved    = $oValid->insert($data, 1);
            }
            else
            {
                unset($data['FQVT_Category']);

                $oValid   = new $dataObject();
                $initData = $oValid->getInitialData($data);
                $saved    = $oValid->insert($data, 1);

                foreach ($data as $key => $val)
                {
                    $key          = str_replace("FQV_", "FQO_", $key);
                    $option[$key] = $val;
                }

                $oOption = new FormQuestionOptionObject();
                $saved   = $oOption->insert($option, 1);
            }

            echo $saved;
            exit;
        }
    }

    public function addResponseOptionAction()
    {
        $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']) . 'Object';

        $oForm    = new $dataObject();
        $initData = $oForm->getInitialData($data);
        if ($oForm->recordExists($data, $initData['lang'])){
            unset($data[$oForm->getDataId()]);
        }
        $saved    = $oForm->insert($data, $initData['lang']);

        $languages = Cible_FunctionsGeneral::getAllLanguage();
        foreach ($languages as $key => $lang)
        {
            if ($lang['L_ID'] != $initData['lang'])
            {
                $oForm->save($saved, $data, $lang['L_ID']);
            }
        }

        echo $saved;
        exit;
    }

    public function addNewLineToOptionsAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']);

            $oOptions = new $dataObject();
            $newTr    = $oOptions->getNewOptionLine(
                $data['FRO_ID'],
                $data['FRO_Type'],
                $data['FRO_Seq']);

            $line = array('html' => $newTr);
            echo json_encode($line);
            exit;
        }
    }


    public function deleteOptionAction()
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

            $deleted = $oForm->delete($initData['id']);

            echo true;
            exit;
        }
    }
}
?>
