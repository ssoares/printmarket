<?php
    abstract class Cible_FunctionsProfile
    {
        public static function getAllProfileMembersDetails($profileID, $includeProperties = array(), $searchfor = '', $filters = '', $returnFormat = 'text'){
            $profile = new Profile($profileID);
            $profileProperties = $profile->getProfileProperties();
            $profileMembers = $profile->findMember(array());
            
            $db = Zend_Registry::get('db');
            $select = $db->select();
            
            $profilePropertiesCount = count($profileProperties);
            for( $i = 0; $i < $profilePropertiesCount; $i++ ){
                $includeInSelect = true;
                if(count($includeProperties) > 0){
                    if(!in_array($profileProperties[$i]['PropertyID'],$includeProperties))
                        $includeInSelect = false;
                }
                    
                if($includeInSelect){
                    if($i == 0){
                        $select->from(array("p{$profileProperties[$i]['PropertyID']}" => "{$profileProperties[$i]['PropertyType_TableName']}"),
                                        array("memberID" => "MemberID", "{$profileProperties[$i]['PropertyName']}" => "p{$profileProperties[$i]['PropertyID']}.{$profileProperties[$i]['PropertyType_Fields']}")
                                     );
                    }
                    else{
                        $select->from(array("p{$profileProperties[$i]['PropertyID']}" => "{$profileProperties[$i]['PropertyType_TableName']}"),
                                        array("{$profileProperties[$i]['PropertyName']}" => "p{$profileProperties[$i]['PropertyID']}.{$profileProperties[$i]['PropertyType_Fields']}")
                                     );
                    }
                }
            }
            
            if($searchfor<>''){
                $searching_on = array();
                $search_keywords = explode(' ', $searchfor);
            }
            
            $profileMembersCount = count($profileMembers);
            for( $i = 0; $i < $profileMembersCount; $i++ ){
                $y = 0;
                $where = "";
                
                $profilePropertiesCount = count($profileProperties);
                for( $j = 0; $j < $profilePropertiesCount; $j++ ){
                    $includeInSelect = true;
                    if(count($includeProperties) > 0){
                        if(!in_array($profileProperties[$j]['PropertyID'],$includeProperties))
                            $includeInSelect = false;
                    }
                        
                    if($includeInSelect){
                        if($y <> 0)
                            $where .= "AND";
                        
                        $where .= "(";
                        $where .= "p{$profileProperties[$j]['PropertyID']}.PropertyID = {$profileProperties[$j]['PropertyID']} AND p{$profileProperties[$j]['PropertyID']}.MemberID = {$profileMembers[$i]['MemberID']}";
                        $where .= ")"; 
                        $y++;
                    }
                }
                
                if($searchfor<>''){
                    $where .= " AND (";
                    $z = 0;
                    
                    $profilePropertiesCount = count($profileProperties);
                    for( $k = 0; $k < $profilePropertiesCount; $k++ ){
                        $includeInSelect = true;
                        if(count($includeProperties) > 0){
                            if(!in_array($profileProperties[$k]['PropertyID'],$includeProperties))
                                $includeInSelect = false;
                        }
                            
                        if($includeInSelect){
                            foreach( $search_keywords as $keyword ){
                                if($z == 0){
                                    $where .= "p{$profileProperties[$k]['PropertyID']}.{$profileProperties[$k]['PropertyType_Fields']} LIKE '%{$keyword}%'";
                                }
                                else{
                                    $where .= " OR p{$profileProperties[$k]['PropertyID']}.{$profileProperties[$k]['PropertyType_Fields']} LIKE '%{$keyword}%'";    
                                }
                                $z++;
                            }
                        }
                    }
                    $where .= ")";
                }
                
                if($filters <> ''){
                    $where .= " AND (";
                    $z = 0;
                    foreach($filters as $key=>$value){
                        foreach($profileProperties as $property){
                            $includeInSelect = true;
                            if(count($includeProperties) > 0){
                                if(!in_array($property['PropertyID'],$includeProperties))
                                    $includeInSelect = false;
                            }
                           
                            if($includeInSelect){
                                if($key == $property['PropertyName']){
                                    if($z <> 0)
                                        $where .= " OR ";
                                    
                                    if($property['PropertyType_Fields'] == 'Choice'){
                                            $where .= " p{$property['PropertyID']}.{$property['PropertyType_Fields']} = '$value'";
                                            $where .= " OR p{$property['PropertyID']}.{$property['PropertyType_Fields']} like '%,$value'";
                                            $where .= " OR p{$property['PropertyID']}.{$property['PropertyType_Fields']} like '%,$value,%'";
                                            $where .= " OR p{$property['PropertyID']}.{$property['PropertyType_Fields']} like '$value,%'";
                                    }
                                    else{
                                        $where .= " p{$property['PropertyID']}.{$property['PropertyType_Fields']} = '$value'";
                                    }
                                    
                                    $z++;
                                }
                            }
                            
                        }    
                    }
                    $where .= ")";
                }
                            
                if($i == 0)
                    $select->where($where);
                    
                else
                    $select->orWhere($where);
                
                $i++;
                    
            }
            
            if($returnFormat == 'text')
                return $select;
            elseif($returnFormat == 'data'){
                $rows = $db->fetchAll($select);
                return $rows;
            }
        }
    }
?>
