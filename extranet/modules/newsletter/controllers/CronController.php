<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once APPLICATION_PATH . '/extranet/modules/newsletter/controllers/IndexController.php';
/**
 * Description of CronController
 *
 * @author soaser
 */
class CronController extends Newsletter_IndexController
{
    public function sendNewsletterAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        parent::sendNewsletterAction();
    }
    public function addAction()
    {

    }

    public function editAction()
    {
        ;
    }

    public function deleteAction()
    {
        ;
    }
}
?>
