<?php
/**
 * Class Form_TextController - Manage text zone data
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */

/**
 * TextController - Manage the text zone data. This is the text that
 * can be include in the form. It's one of the elements.
 *
 * Not to be confused with the question type text.
 *
 * @package    Form
 * @copyright  Copyright (c) Cible solutions d'affaires (http://www.ciblesolutions.com)
 * @version    $Id:
 */
class Form_TextController extends Cible_Extranet_Controller_Module_Action
{
    protected $_name = 'form';

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
            $data['FTI_Text'] = $data['FTI_Text'];

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
        $this->view->title = "Modification d'une galerie";

        // Tests if the user has permissions
        if ($this->view->aclIsAllowed($this->view->current_module, 'manage', true))
        {
            // variables
            $this->view->assign('isXmlHttpRequest', $this->_isXmlHttpRequest);
            $this->view->assign('success', false);


            $textID = $this->_getParam('textID');
            $baseDir = $this->view->baseUrl();

            $oText    = new FormTextObject();
            $textData = $oText->populate($textID, Zend_Registry::get("currentEditLanguage"));


            if(!$textData){
                if ($this->_request->isPost()) {
                    $this->view->assign('success', true);
                }
                $this->view->assign('deleted', true);
                $this->view->assign('textID', $textID);
            }
            else{
                $this->view->assign('deleted', false);

                $config = Zend_Registry::get('config')->toArray();

                if ($this->_request->isPost()) {
                    $formData = $this->_request->getPost();

                }

                // generate the form form/index/edit/formID/1
                $returnUrl =  "$baseDir/form/index/list/";
                $form = new FormTextzoneForm(array(
                    'baseDir'   => $baseDir,
                    'cancelUrl' => '',
                    'textID'    => $textID
                ));

                if ($this->_request->isPost())
                {
                    $formData = $this->_request->getPost();
                    if ($form->isValid($formData))
                    {
                        $oText->save($textID, $formData, $this->getCurrentEditLanguage());
                        if( $this->_isXmlHttpRequest )
                        {
                            $buttonAction = $formData['buttonAction'];
                            $this->view->assign('buttonAction', $buttonAction);
                            $this->view->assign('success', true);
                            $this->view->assign('textID', $textID);
                            $this->view->assign('text', $form->getValue('FTI_Text'));
                        }
                        else
                        {
                            $this->_redirect($returnUrl);
                        }
                    }
                    else
                    {
                        $this->view->form = $form;
                    }

                }
                else
                {
                    $form->populate($textData);
                    $this->view->form = $form;
                }
            }
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

    public function getTextAction()
    {
        if( $this->view->aclIsAllowed($this->view->current_module, 'edit') )
        {
            $data   = $this->_getAllParams();

            if ($data['model'] != 'Form')
                $dataObject = 'Form' . ucfirst($data['model']) . 'Object';
            else
                $dataObject = ucfirst($data['model']);

            $oForm    = new $dataObject();
            $initData = $oForm->getInitialData($data);
            $text    = $oForm->populate($initData['id'], $initData['lang']);

            echo json_encode($text['FTI_Text']);
            exit;
        }
    }
}
?>
