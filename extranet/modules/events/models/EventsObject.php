<?php

    class EventsObject extends DataObject
    {
        protected $_dataClass = 'EventsData';
        protected $_dataId = 'ED_ID';
        protected $_dataColumns = array(
            'CategoryID' => 'ED_CategoryID',
            'ImageSrc' => 'ED_ImageSrc',
            'ED_Allstar' => 'ED_Allstar'
        );

        protected $_indexClass = 'EventsIndex';
        protected $_indexId = 'EI_EventsDataID';
        protected $_indexLanguageId = 'EI_LanguageID';
        protected $_indexColumns = array(
            'Title' => 'EI_Title',
            'Brief' => 'EI_BriefText',
            'Location' => 'EI_Location',
            'Text' => 'EI_Text',
            'ImageAlt' => 'EI_ImageAlt',
            'Status' => 'EI_Status',
            'ValUrl' => 'EI_ValUrl'
        );

        public function insert($data, $langId){
            $id = parent::insert($data, $langId);

            if( is_array( $data['DateRange'] ) ){

                $dateRangeObject = new EventsDateRange();
                $dateRangeObject->delete( $this->_db->quoteInto('EDR_EventsDataID = ?', $id) );

                foreach( $data['DateRange'] as $_range ){
                    if( !empty( $_range['from'] ) ){

                        $_range['to'] = !empty( $_range['to'] ) ? $_range['to'] : $_range['from'];

                        $dateRangeObject->insert(array(
                            'EDR_EventsDataID' => $id,
                            'EDR_StartDate' => $_range['from'],
                            'EDR_EndDate' => $_range['to'],
                        ));
                    }
                }

            }
            return $id;
        }

        public function save($id, $data, $langId){
            parent::save($id, $data, $langId);

            if( is_array( $data['DateRange'] ) ){

                $dateRangeObject = new EventsDateRange();
                $dateRangeObject->delete( $this->_db->quoteInto('EDR_EventsDataID = ?', $id) );

                foreach( $data['DateRange'] as $_range ){
                    if( !empty( $_range['from'] ) ){

                        $_range['to'] = !empty( $_range['to'] ) ? $_range['to'] : $_range['from'];

                        $dateRangeObject->insert(array(
                            'EDR_EventsDataID' => $id,
                            'EDR_StartDate' => $_range['from'],
                            'EDR_EndDate' => $_range['to'],
                        ));
                    }
                }

            }
        }



        public function populate($id, $langId){
            $object = parent::populate($id, $langId);

            if( empty($object['DateRange']) || !is_array($object['DateRange']) )
                $object['DateRange'] = array();

            $dateRangeObject = new EventsDateRange();
            $_select = $dateRangeObject->select();

            $_select->where( $this->_db->quoteInto('EDR_EventsDataID = ?', $id) );

            $ranges = $dateRangeObject->fetchAll( $_select );

            foreach($ranges as $_range){
                array_push($object['DateRange'], array('from' => $_range['EDR_StartDate'], 'to' => $_range['EDR_EndDate']));
            }

            return $object;
        }

        public function setIndexationData()
        {
            $eventsSelect = new EventsIndex();
            $select = $eventsSelect->select()->setIntegrityCheck(false)
                    ->from('EventsIndex',
                        array(
                            'ID' => 'EI_EventsDataID',
                            'LanguageID' => 'EI_LanguageID',
                            'Title' => 'EI_Title',
                            'ValUrl' => 'EI_ValUrl',
                            'Brief' => 'EI_BriefText',
                            'Location' => 'EI_Location',
                            'Text' => 'EI_Text',
                            'ImageAlt' => 'EI_ImageAlt'
                            )
                        )
                    ->join('EventsData', 'ED_ID = EI_EventsDataID', array('CategoryID' => 'ED_CategoryID'))
                    ->join('EventsDateRange', 'ED_ID = EDR_EventsDataID', array('Date' => 'EDR_StartDate'))
                    ->where('EI_Status = 1');

            $eventsData = $eventsSelect->fetchAll($select)->toArray();

            foreach ($eventsData as $data)
            {
                $indexData['action'] = "add";
                $indexData['pageID'] = $data['CategoryID'];
                $indexData['moduleID'] = 7;
                $indexData['contentID'] = $data['ID'];
                $indexData['languageID'] = $data['LanguageID'];
                $indexData['title'] = $data['Title'];
                $indexData['text'] = '';
                $indexData['link'] = $data['Date'] . '/' . $data['ValUrl'];
                $indexData['object'] = get_class();
                $indexData['contents'] = $data['Title'] . " " . $data['Brief'] . " " . $data['Text'] . " " . $data['Location']. " " . $data['ImageAlt'];

                Cible_FunctionsIndexation::indexation($indexData);
            }

            return $this;
        }

    /**
     * Builds folder to manage images and files according to the current website.
     *
     * @param string  $module The current module name.
     * @param string  $path Path relative to the current site.
     *
     * @return void
     */
    public function buildBasicsFolders($module, $path)
    {
        $imgPath = $path . '/data/images/' . $module ;
        if (!is_dir($imgPath))
        {
            mkdir ($imgPath);
            mkdir ($imgPath . '/tmp' );
        }
    }
    }
