<?php

class Retailers_IndexController extends Cible_Controller_Action
{

    public function indexAction()
    {
        $form = new FormRetailers();
        $this->view->assign('form', $form);


        if ($this->_request->isPost())
        {

        }
        else
        {

        }

        $list = '';
        $this->view->assign('list', $list);
        $this->view->assign('selectedState', '11');
    }

    public function selectOneAction()
    {
        $authentication = Cible_FunctionsGeneral::getAuthentication();


        if ($authentication)
        {
            $profile = new MemberProfile();
            $memberInfos = $profile->findMember(
                array(
                    'email' => $authentication['email']));

            $data = array(
                'city' => $memberInfos['cityId'],
                'country' => $memberInfos['country'],
                'state' => $memberInfos['state']);

            $this->view->assign('selectedState', $data['state']);
            $this->view->assign('selectedCity', $data['city']);
        }

        $form = new FormRetailersSelectOne();
        $this->view->assign('form', $form);


        if ($this->_request->isPost())
        {

        }
        else
        {
            $form->populate($data);
        }

        $list = '';
        $this->view->assign('list', $list);
    }

    public function ajaxCitiesAction()
    {
        if ($this->_isXmlHttpRequest)
        {
            $this->getHelper('viewRenderer')->setNoRender();

            $stateId = $this->_getParam('stateId');
            $filter = $this->_getParam('filter');

            $cities = new CitiesObject();

            /*
             * if $filter = true(1) then only display cities having
             * retailers else list all the cities for registration for
             * example.
             */
            if ($filter)
                $cities->setFilter(true);

            $citiesData = $cities->getCitiesDataByStates($stateId);

            foreach ($citiesData as $id => $data)
            {
                $citiesData[$id]['C_Name'] = $data['C_Name'];
            }

            echo json_encode($citiesData);
        }
    }

    public function ajaxStatesAction()
    {
        $filter = 0;

        if ($this->_isXmlHttpRequest)
        {
            $this->getHelper('viewRenderer')->setNoRender();

            $countryId = $this->_getParam('countryId');
            $languageId = $this->_getParam('langId');
            $filter = $this->_getParam('filter');

            $states = Cible_FunctionsGeneral::getStateByCode(
                    $countryId, null, $languageId);

            if (is_array($states))
            {
                foreach ($states as $id => $data)
                {
                    $statesData[$id]['id'] = $data['id'];
                    $statesData[$id]['name'] = $data['name'];
                }
            }

            echo json_encode($statesData);
        }
    }

    public function ajaxRetailersAction()
    {
        if ($this->_isXmlHttpRequest)
        {
            $this->getHelper('viewRenderer')->setNoRender();
            $this->disableLayout();
            $this->disableView();

            $langId = $this->_getParam('langId');
            $retailerId = $this->_getParam('retailerId');
            $field = $this->_getParam('field');
            $oRetailers = new RetailersObject();

            $params = array(
                'field' => $field,
                'value' => $this->_getParam('value')
            );

            if ($retailerId)
            {
                $retailersData = $oRetailers->getRetailersDataByCities($params, $retailerId, $langId);
            }
            else
                $retailersData = $oRetailers->getRetailersDataByCities($params, null, $langId);

            if ($this->_getParam('render') != 'false')
            {
                $retailers = $this->renderRetailers($retailersData);
            }
            else
            {
                foreach ($retailersData as $data)
                {
                    foreach ($data as $key => $value)
                    {
                        $data[$key] = $value;
                    }
                    $dataForJson[] = $data;
                }
                $retailers = $dataForJson;
            }
            echo json_encode($retailers);
        }
    }

    private function renderRetailers($retailersData)
    {
        $tmp = '';
        $i = 0;
        $columns = 2;
        $modulo = 0;

        if ($retailersData)
        {
            $telLabel = $this->view->getClientText('phone_short_label');
            $faxLabel = $this->view->getClientText('fax_short_label');

            $tmp .= '<table class="table_partenaire">';
            foreach ($retailersData as $retailer)
            {
                $modulo = $i % $columns;
                if ($modulo == 0)
                    $tmp .= '<tr><td width="300">';
                else
                    $tmp .= '<td width="300">';

                // Retailer info
                $tmp .= '<span class="greenParagraph">'
                    . $retailer['Name']
                    . '</span><br />';
                if (!empty($retailer['FirstAddress']))
                    $tmp .= $retailer['FirstAddress'] . '<br />';
                if (!empty($retailer['SecondAddress']))
                    $tmp .= $retailer['SecondAddress'] . '<br />';
                if (!empty($retailer['cityName']))
                    $tmp .= $retailer['cityName'];
                if (!empty($retailer['stateName']))
                    $tmp .= ', ' . $retailer['stateName'] . '<br />';
                if (!empty($retailer['ZipCode']))
                    $tmp .= '<span class="zipCode_format">' . $retailer['ZipCode'] . '</span><br />';
                if (!empty($retailer['FirstTel']))
                    $tmp .= $telLabel . ": " . $retailer['FirstTel'];
                if (!empty($retailer['FirstExt']))
                    $tmp .= '  ext: ' . $retailer['FirstExt'];
                $tmp .= '<br />';
                if (!empty($retailer['SecondTel']))
                    $tmp .= $telLabel . ": " . $retailer['SecondTel'];
                if (!empty($retailer['SecondExt']))
                    $tmp .= '  ext: ' . $retailer['SecondExt'];
                $tmp .= '<br />';
                if (!empty($retailer['Fax']))
                    $tmp .= $faxLabel . ": " . $retailer['Fax'] . '<br />';
                if (!empty($retailer['Email']))
                    $tmp .= $retailer['Email'] . '<br />';
                if (!empty($retailer['Website']))
                    $tmp .= $retailer['Website'] . '<br />';
                if ($modulo == $columns - 1)
                    $tmp .= '</td>' . chr(13) . '</tr>';
                else
                    $tmp .= '</td>' . chr(13);

                $i++;
            }

            $emptyRowCount = ($columns - 1) - $modulo;
            for ($i = 0; $i < $emptyRowCount; $i++)
                $tmp .= "<td>&nbsp;</td>" . chr(13);

            if ($emptyRowCount > 0)
                $tmp .= '</tr>';

            $tmp .= '</table>';
        }
        return $tmp;
    }

}