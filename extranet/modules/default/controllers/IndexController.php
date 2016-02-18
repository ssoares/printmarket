<?php

/** Zend_Controller_Action */

class IndexController extends Cible_Extranet_Controller_Action
{
    function indexAction()
    {
//        $oRT = new RecursiveTransactionsObject();
//        $data['rejections'] = $oRT->setNbDayLimit(-1)->getRejections(true);
//
//        $oCC = new DonorsAccountObject();
//        $data['ccExpiration'] = $oCC->getCCExpiration(true);
//
//        $oDon = new DonationObject();
//        $total = $oDon->setNbDayLimit(-1)->getTotalDonations();
//        $data['totalDonation'] = Zend_Locale_Format::toNumber($total);
//
//        $oProfile = new GenericProfilesObject();
//        $oProfile->setQuery($oDon->getQuery())
//            ->setForeignKey($oDon->getForeignKey())
//            ->completeQuery(null, false);
//        $data['newDonors'] = $oProfile->setNbDayLimit(-6)->getNewDonors(true);
//
//        $oTeams = new TeamsObject();
//        $data['validateTeams'] = $oTeams->countTeamsToValidate();
//
//        $this->view->report = $data;


    }

    public function dictionnaryAction(){
        $identifier = $this->_getParam('identifier');
        $lang = $this->_getParam('lang');
        $type = $this->_getParam('type');

        $this->view->assign('success', 'false');

        $dictionaryForm = new FormDictionnary();

        if ( $this->_request->isPost() ){
            $formData = $this->_request->getPost();
            if ($dictionaryForm->isValid($formData)) {
                Cible_Translation::set($identifier, $type, $formData['ST_Value'], $lang);
                $this->view->assign('success', 'true');
                $this->view->assign('value', $formData['ST_Value']);
            } else {
                $dictionaryForm->populate($formData);
            }
        } else {
            $data = array(
                'ST_Identifier' => $identifier,
                'ST_Value'=> Cible_Translation::__($identifier,$type, $lang),
                'ST_LangID' => $lang,
                'ST_Type' => $type
            );

            $dictionaryForm->populate($data);
        }
        $this->view->assign('form', $dictionaryForm);
    }
}
