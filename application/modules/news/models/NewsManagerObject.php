<?php

class NewsManagerObject extends DataObject
{
    protected $_dataClass = 'NewsData';
    protected $_dataId = 'ND_ID';
    protected $_indexClass = 'NewsIndex';
    protected $_indexId = 'NI_NewsDataID';
    protected $_titleField = 'NI_Title';
    protected $_valurlField = 'NI_ValUrl';
    protected $_indexLanguageId = 'NI_LanguageID';
    protected $_teamId = 0;
    protected $_blockID = 0;
    protected $_blockParams = array();


    public function getBlockParams( $field = '')
    {
        if (!empty($field) &&  isset($this->_blockParams[$field])){
            $value = $this->_blockParams[$field];
        }elseif (!isset($this->_blockParams[$field])){
            $value = null;
        }else{
            $value = $this->_blockParams;
        }

        return $value;

    }

    public function setBlockParams(array $blockParams = array())
    {
        if( $this->_blockID )
        {
            $_params = Cible_FunctionsBlocks::getBlockParameters( $this->_blockID );

            foreach( $_params as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
        else
        {
            foreach( $blockParams as $param)
                $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
        }
    }

    public function insert($data, $langId)
    {
        $data = $this->_formatInputData($data);
        $id = parent::insert($data, $langId);
        $languages = Cible_FunctionsGeneral::getAllLanguage(true);
        foreach($languages as $key => $lang)
        {
            if ($lang['L_ID'] != $langId){
                parent::save($id, $data, $lang['L_ID']);
            }
        }
        return $id;
    }


    public function save($id, $data, $langId)
    {
        $data = $this->_formatInputData($data);
        $status = parent::save($id, $data, $langId);
        $languages = Cible_FunctionsGeneral::getAllLanguage(true);
        foreach($languages as $key => $lang)
        {
            if ($lang['L_ID'] != $langId){
                parent::save($id, $data, $lang['L_ID']);
            }
        }
        return $status;
    }

        public function populate($id, $langId)
    {
        return $this->_formatOutputData(parent::populate($id, $langId));
    }

    protected function _formatInputData(array $data)
    {
        $data = parent::_formatInputData($data);
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'ND_Date':
                case 'ND_ReleaseDateEnd':
                case 'ND_ReleaseDate':
                    if (!empty($values)){
                        $data[$field] = date('Y-m-d', strtotime($values));
                    }
                    break;

                default:
                    break;
            }
        }
        $data['ND_BelongsToTeam'] = 1;

        return $data;
    }

    private function _formatOutputData(array $data)
    {
        foreach ($data as $field => $values)
        {
            switch ($field)
            {
                case 'CA_GalleryGroups':
                    $data[$field] = explode(',', $values);
                    break;
                case 'ND_Date':
                case 'ND_ReleaseDateEnd':
                case 'ND_ReleaseDate':
                    if (!empty($values) && $values != '0000-00-00'){
                        $data[$field . 'Dt'] = $values;
                        $data[$field] = date('d-m-Y', strtotime($values));
                    }else{
                        $data[$field . 'Dt'] = '';
                        $data[$field] = '';
                    }

                    break;

                default:
                    break;
            }
        }

        return $data;
    }
}
