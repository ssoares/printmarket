<?php

/**
 * LICENSE
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Retailers
 * @copyright Copyright (c)2010 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RetailersLog.php 829 2012-02-03 22:22:04Z ssoares $
 */

/**
 * Manage the statistics reports for the nwesletter activities
 *
 * @category  Extranet_Module
 * @package   Extranet_Module_Retailers
 * @copyright Copyright (c)2010 Cibles solutions d'affaires - http://www.ciblesolutions.com
 * @license   Empty
 * @version   $Id: RetailersLog.php 829 2012-02-03 22:22:04Z ssoares $
 */
class OrderLog extends LogObject
{
    const NB_DAY = 7;
    
    public function __construct($options = array())
    {
        parent::__construct($options);

        foreach ($options as $key => $value)
        {
            $property = '_' . $key;
            $this->$property = $value;
        }

        parent::setModuleId($this->_moduleId);

        $now = Zend_Date::now();
        $date = $now->subDay(self::NB_DAY);
        $this->_dateLimit = $date->toString('yyyy-MM-dd HH:mm:ss');
    }

    /**
     * @see parent::getDataPairs
     */
    public function getDataPairs($pairs)
    {
        return parent::getDataPairs($pairs);
    }

    public function getGlobalLog($data)
    {
        $render = array();
        $select = parent::getAll(null, false);
        $select->order('L_Datetime asc');

        //Here some where clauses to filter data if needed
        $results = $this->_db->fetchAll($select);

        foreach ($results as $log)
        {
            $tmpDetails = array();
            $tmpData = $this->getDataPairs($log['L_Data']);
            foreach ($tmpData as $key => $value)
            {
                $methods = get_class_methods($this);
                $tmpName = 'set' . ucfirst($key);
                if (array_search($tmpName, $methods))
                {
                    $this->$tmpName($value);
//                    $paramMethod = '_getDetailsFor' . ucfirst($key);
//                    $tmpDetails[$key] = $this->$paramMethod();
                    $this->$tmpName(0);

                    unset($tmpData[$key]);
                }
                else
                {
                    $tmpDetails[] = $value;
                }
            }

            $render[] = array(
                'L_Datetime' => $log['L_Datetime'],
                'L_Action'   => $log['L_Action'],
                'L_ModuleNm' => $data['module'],
                'L_Details' => $tmpDetails,
            );
        }

        return $render;
    }
}
