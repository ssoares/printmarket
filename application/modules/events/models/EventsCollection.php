<?php
    class EventsCollection// extends objectsCollection
    {
        protected $_db;
        protected $_current_lang;
        protected $_blockID;
        protected $_blockParams;

        public function setCurrentLang($lang)
        {
            $this->_current_lang = $lang;
        }
        public function __construct($blockID = null){
            $this->_db = Zend_Registry::get('db');
            $this->_current_lang = Zend_Registry::get('languageID');

            if( $blockID ){
                $this->_blockID = $blockID;
                $_params = Cible_FunctionsBlocks::getBlockParameters( $blockID );

                foreach( $_params as $param){
                    $this->_blockParams[ $param['P_Number'] ] = $param['P_Value'];
                }
            } else {

                $this->_blockID = null;
                $this->_blockParams = array();

            }
        }

        public function getDetails($id){
            $select = $this->_db->select();

            $select->from('EventsData', array('ED_ID', 'ED_ImageSrc','ED_Allstar'))
                   ->distinct()
                   ->join('EventsIndex','EventsIndex.EI_EventsDataID = EventsData.ED_ID' )
                   ->joinRight('EventsDateRange', 'EventsDateRange.EDR_EventsDataID = EventsData.ED_ID', array())
                   ->where('EventsIndex.EI_LanguageID = ?', $this->_current_lang )
                   ->where('EventsData.ED_ID = ?', $id )
                   ->where('EventsIndex.EI_Status = ?', 1 );
            $events = $this->_db->fetchAll($select);

            $num_events = count($events);
            $session = new Zend_Session_Namespace();
            for( $i = 0; $i < $num_events; $i++ ){
                $events[$i]['dates'] = $this->getEventDates( $events[$i]['ED_ID'] );
                $events[$i]['site'] = $session->currentSite;
            }

            return $events;
        }

        public function getList($limit = null,$siteName = NULL){

            $select = $this->_db->select();
            $select->from('EventsData')
               ->distinct()
               ->join('EventsIndex','EventsIndex.EI_EventsDataID = EventsData.ED_ID' )
               ->joinRight('EventsDateRange', 'EventsDateRange.EDR_EventsDataID = EventsData.ED_ID')
               ->where('EventsIndex.EI_LanguageID = ?', $this->_current_lang );
               if(isset($this->_blockParams[4]) && !$this->_blockParams[4]){
                    $select->where('EventsData.ED_CategoryID = ?', $this->_blockParams[1] );
               }
               if(isset($this->_blockParams[5]) && $this->_blockParams[5]){
                   $select->where('EventsData.ED_Allstar = 1');
               }
               $select->where('EventsIndex.EI_Status = ?', 1 )
                    ->where('EventsDateRange.EDR_EndDate >= CURDATE()' )
                    ->order('EventsDateRange.EDR_StartDate ASC');

            if( $limit )
                   $select->limit($limit);

            $events = $this->_db->fetchAll($select);

            $num_events = count($events);
            $session =new Zend_Session_Namespace();
            for( $i = 0; $i < $num_events; $i++ ){
                $events[$i]['dates'] = $this->getEventDates( $events[$i]['ED_ID'] );
                if($siteName==NULL)
                    $events[$i]['site'] = $session->currentSite;
                else
                 $events[$i]['site'] = $siteName;
            }
            return $events;
        }

        public function getListYearMonth($Year, $Month, $limit = null,$siteName = NULL){

            $select = $this->_db->select();
            $select->from('EventsData')
                   ->distinct()
                   ->join('EventsIndex','EventsIndex.EI_EventsDataID = EventsData.ED_ID' )
                   ->joinRight('EventsDateRange', 'EventsDateRange.EDR_EventsDataID = EventsData.ED_ID')
                   ->where('EventsIndex.EI_LanguageID = ?', $this->_current_lang );
                   if($this->_blockParams[4]==false){
                        $select->where('EventsData.ED_CategoryID = ?', $this->_blockParams[1] );
                   }
                   if($this->_blockParams[5]==true){
                       $select->where('EventsData.ED_Allstar = 1');
                   }
                   $select->where('EventsIndex.EI_Status = 1')
                  // ->where('Year(EventsDateRange.EDR_StartDate) = ' . intval($Year) . " OR Year(EventsDateRange.EDR_EndDate) = " . intval($Year) )
                  // ->where('Month(EventsDateRange.EDR_StartDate) = ' . intval($Month) . " OR Month(EventsDateRange.EDR_EndDate) = " . intval($Month)  )


                  ->where("EventsDateRange.EDR_StartDate <= '" . intval($Year) . "-" . intval($Month) . "-31' AND EventsDateRange.EDR_EndDate >= '" . intval($Year) . "-" . intval($Month) . "-01'")
                  // ->where('Month(EventsDateRange.EDR_StartDate) = ' . intval($Month) . " OR Month(EventsDateRange.EDR_EndDate) = " . intval($Month)  )
                  ->order('EventsDateRange.EDR_StartDate ASC');

            if( $limit )
                   $select->limit($limit);

            $events = $this->_db->fetchAll($select);
            $num_events = count($events);
            $session =new Zend_Session_Namespace();
            for( $i = 0; $i < $num_events; $i++ ){
                $events[$i]['dates'] = $this->getEventDates( $events[$i]['ED_ID'] );
                if($siteName==NULL)
                    $events[$i]['site'] = $session->currentSite;
                else
                 $events[$i]['site'] = $siteName;
            }
            return $events;
        }


        public function getOtherEvents($limit = null, $not_ID){
            $select = $this->_db->select();
            $select->from('EventsData', array('ED_ID', 'ED_ImageSrc','ED_Allstar'))
                   ->distinct()
                   ->join('EventsIndex','EventsIndex.EI_EventsDataID = EventsData.ED_ID' )
                   ->joinRight('EventsDateRange', 'EventsDateRange.EDR_EventsDataID = EventsData.ED_ID', array())
                   ->where('EventsIndex.EI_LanguageID = ?', $this->_current_lang )
                   ->where('EventsData.ED_CategoryID = ?', $this->_blockParams[1] )
                   ->where('EventsIndex.EI_Status = ?', 1 )
                   ->where('EventsData.ED_ID <> ?', $not_ID)
                   ->order('EventsDateRange.EDR_StartDate ASC');
            if( $limit )
                   $select->limit($limit);
            $events = $this->_db->fetchAll($select);
            $num_events = count($events);

            for( $i = 0; $i < $num_events; $i++ ){
                $events[$i]['dates'] = $this->getEventDates( $events[$i]['ED_ID'] );
            }

            return $events;
        }

        public function getBlockParam($param_name){
            return $this->_blockParams[$param_name];
        }

        public function getBlockParams(){
            return $this->_blockParams;
        }

        private function getEventDates( $eventID ){
            $select = $this->_db->select();

            $select->from('EventsDateRange',array('EDR_StartDate', 'EDR_EndDate'))
                   ->where('EventsDateRange.EDR_EventsDataID = ?', $eventID )
                   ->order('EventsDateRange.EDR_StartDate');

            return $this->_db->fetchAll($select);
        }

        /**
         * Fetch the id of an event according the formatted string from URL.
         *
         * @param string $string
         *
         * @return int Id of the searched event
         */
        public function getIdByName($string){
            $select = $this->_db->select();
            $select->from('EventsIndex','EI_EventsDataID')
                    ->where("EI_ValUrl = ?", $string);
            $id = $this->_db->fetchRow($select);
            return $id['EI_EventsDataID'];
        }

    }
?>