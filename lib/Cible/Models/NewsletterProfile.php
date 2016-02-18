<?php
  class NewsletterProfile extends GenericProfile
  {

      protected $_table = 'NewsletterProfiles';
      protected $_fields = array(
        'newsletter_categories' => 'NP_Categories',
        'NP_TypeID' => 'NP_TypeID',
        'NP_SubscriptionDate' => 'NP_SubscriptionDate',
      );

      protected $_db;

      public function __construct($options = null){
          $this->_db = Zend_Registry::get('db');
      }

      public function getProfileProperties(){
          return array_merge( parent::getProfileProperties(), array_keys($this->_fields) );
      }

      public function getMemberDetails($memberID){
          $select = $this->_db->select();
          $select->from( $this->_genericTable, $this->_genericFields )
                ->join( $this->_table, 'GP_MemberID = NP_ProfileId', $this->_fields)
                ->where('GP_MemberID = ?', $memberID);

          return $this->_db->fetchRow( $select );
      }

      public function findMember($filters = array()) {

          $select = $this->_db->select();
          $select->from( $this->_genericTable, $this->_genericFields )
                  ->join( $this->_table, 'GP_MemberID = NP_ProfileId', $this->_fields);

          foreach($filters as $key=>$value) {
              if( isset( $this->_genericFields[$key] ) || isset( $this->_fields[$key] ) )
                  $select->where("{$this->_genericFields[$key]} like '%{$value}%'");
          }

          return $this->_db->fetchRow( $select );
      }

      public function findMembers($filters = array()){

          $select = $this->_db->select();
          $select->from( $this->_genericTable, $this->_genericFields )
                 ->join( $this->_table, 'GP_MemberID = NP_ProfileId', $this->_fields);

          foreach($filters as $key=>$value){
            if( isset( $this->_genericFields[$key] ) || isset( $this->_fields[$key] ) )
            {
                if (is_string($value ))
                $select->where("{$this->_genericFields[$key]} like '%{$value}%'");
                elseif (is_integer($value))
                    $select->where($this->_genericFields[$key] . ' = ?',$value);
             }
          }

          return $this->_db->fetchAll( $select );
      }

      public function getSelectStatement(){
          $select = $this->_db->select();
          $select->from( $this->_genericTable, $this->_genericFields )
                 ->join( $this->_table, 'GP_MemberID = NP_ProfileId', $this->_fields);

          return $select;
      }

      protected function insert($data){

          $id = parent::insert($data);

          $columns = array();
          $keys = array_keys( $this->_fields );

          foreach( $data as $col_key => $col_value ){
              if( in_array( $col_key, $keys ) ){
                  $columns[ $this->_fields[ $col_key ] ] = $col_value;
              }
          }

          $columns['NP_ProfileId'] = $id;

          return $this->_db->insert($this->_table, $columns);
      }

      protected function save($memberId, $data){

          $memberId = (int)$memberId;
          parent::save($memberId, $data);

          $columns = array();
          $keys = array_keys( $this->_fields );

          foreach( $data as $col_key => $col_value ){
              if( in_array( $col_key, $keys ) ){
                  $columns[ $this->_fields[ $col_key ] ] = $col_value;
              }
          }

          if( !$this->findMembers(array(
            'member_id' => $memberId
          ))){
              $columns['NP_ProfileId'] = $memberId;
              $this->_db->insert( $this->_table, $columns );

          } else {
              $where = $this->_db->quoteInto('NP_ProfileId = ?', $memberId);
              $this->_db->update( $this->_table, $columns, $where );
          }
      }

      protected function delete($memberID){
          //parent::delete($memberID);

          $where = "NP_ProfileId = $memberID";
          $this->_db->delete($this->_table, $where);
      }
  }
?>
