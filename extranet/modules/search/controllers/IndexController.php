<?php
class Search_IndexController extends Cible_Controller_Block_Abstract
{
    protected $_moduleID = 10;
    protected $_defaultAction = 'index';

    public function indexAction()
    {

    }

    public function listAction()
    {

    }
    public function addAction()
    {
        $startTime = microtime(true);
        Cible_FunctionsIndexation::indexationBuild();
        $endTime = microtime(true);
        $totalTime = round($endTime - $startTime, 2);
        if ($totalTime > 60){
            $mins = floor ($totalTime / 60);
            $secs = $totalTime % 60;

            $totalTime = $mins . "." . $secs . " minute";
            if($mins > 1)
                $totalTime .= "s";
        }
        else{
            if ($totalTime > 1)
                $totalTime .= " secondes";
            else
                $totalTime .= " seconde";
        }


        echo json_encode(array('totalTime'=>$totalTime));

    }
    public function addPdfAction()
    {
        $this->disableView();
        $startTime = microtime(true);
        $root = substr_replace($_SERVER['DOCUMENT_ROOT'], '', -1);
        Cible_FunctionsIndexation::indexationBuildPdf($root . $this->_rootFilesPath . '*.pdf');

        $endTime = microtime(true);
        $totalTime = round($endTime - $startTime, 2);
        if ($totalTime > 60){
            $mins = floor ($totalTime / 60);
            $secs = $totalTime % 60;

            $totalTime = $mins . "." . $secs . " minute";
            if($mins > 1)
                $totalTime .= "s";
        }
        else{
            if ($totalTime > 1)
                $totalTime .= " secondes";
            else
                $totalTime .= " seconde";
        }


        echo json_encode(array('totalTime'=>$totalTime));

    }
    public function editAction()
    {

    }

    public function deleteAction()
    {
        $startTime = microtime(true);
        Cible_FunctionsIndexation::indexationDeleteAll();
        $endTime = microtime(true);
        $totalTime = round($endTime - $startTime, 2) . " seconde";
        echo json_encode(array('totalTime'=>$totalTime));
    }
    public function deletePdfAction()
    {
        $this->disableView();
        $startTime = microtime(true);
        Cible_FunctionsIndexation::indexationDeleteAllPdf();
        $endTime = microtime(true);
        $totalTime = round($endTime - $startTime, 2) . " seconde";
        echo json_encode(array('totalTime'=>$totalTime));
    }

    public function reindexingAction(){
        if ($this->_request->isPost()) {
            $reindexing = $this->_request->getPost('reindexing');
            if($reindexing){
                $this->view->assign('reindexing',true);
                $this->view->deleteIndexationAjaxLink = $this->view->baseUrl()."/search/index/delete";
                $this->view->addIndexationAjaxLink = $this->view->baseUrl()."/search/index/add";
                //Cible_FunctionsIndexation::indexationDeleteAll();
                //Cible_FunctionsIndexation::indexationBuild();
            }
            else{
                $this->_redirect("");

            }
        }
        else
            $this->view->assign('reindexing',false);
    }

    public function reindexingPdfAction()
    {
        if ($this->_request->isPost()) {
            $reindexing = $this->_request->getPost('reindexing');
            if($reindexing){
                $this->view->assign('reindexing',true);
                $this->view->deleteIndexationAjaxLink = $this->view->baseUrl()."/search/index/delete-pdf";
                $this->view->addIndexationAjaxLink = $this->view->baseUrl()."/search/index/add-pdf";
                //Cible_FunctionsIndexation::indexationDeleteAll();
                //Cible_FunctionsIndexation::indexationBuild();
            }
            else{
                $this->_redirect("");

            }
        }
        else
            $this->view->assign('reindexing',false);

    }

}