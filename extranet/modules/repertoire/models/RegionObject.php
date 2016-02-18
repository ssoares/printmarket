<?php

class RegionObject extends DataObject
{

    protected $_dataClass = 'RegionData';
    protected $_indexClass = 'RegionIndex';
    protected $_indexLanguageId = 'RGI_LanguageID';
    protected $_foreignKey = 'RG_GroupeID';

    public function _groupesSrc()
    {
        $data = array();
        $oGrp = new GroupeObject();
        $grps = $oGrp->getAll(Cible_Controller_Action::getDefaultEditLanguage());

        foreach ($grps as $key => $grp)
            $data[$grp['G_ID']] = $grp['GI_Name'];

        return $data;
    }

    public function getList()
    {
        $data = array('' => Cible_Translation::getCibleText('form_select_default_label'));
        $langId = Cible_Controller_Action::getDefaultEditLanguage();
        $select = $this->getAll($langId, false);
        $select->joinLeft('RegionGroupeData', 'RG_GroupeID = G_ID')
            ->joinLeft('RegionGroupeIndex', 'G_ID = GI_GroupeID')
            ->where('GI_LanguageID = ?', $langId)
            ->order('GI_Name');

        $list = $this->_db->fetchAll($select);

        foreach ($list as $region)
        {
            $key  = $region['GI_Name'];
            //If cat not in array add it as an array
            if(!array_key_exists($key, $data))
            {
                $data[$key] = array($region['RG_ID'] => $region['RGI_Name']);

            }
            //Else Add values product id and product name into the subcat array
            else
            {
                $data[$key][$region['RG_ID']] = $region['RGI_Name'];
            }
        }

        return $data;

    }
}
