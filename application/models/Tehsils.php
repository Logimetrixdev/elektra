<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * Created By : abhishek kumar mishra

 * Created Date: 31 july 2015

 ***************************************************************/

 

class Application_Model_Tehsils extends Zend_Db_Table_Abstract

{

	   
        
         public function getAllTehsil(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lci.Circle,lci.CircleCode,lci.state,lds.district_name,lt.id,lt.tehsil_name FROM logi_tehsil as lt
inner join logi_district as lds on (lt.district_id = lds.id)
inner join logi_circle as lci on (lt.circle_id = lci.id)
where lt.deleted=0 order by lci.state asc";  
            return $result = $db->fetchAll($query, array());
        }
        
       
         
         public function  getAllDistrictByCircleID($circleID)
         {
             $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT id,district_name FROM `logi_district`  where circle_id='".$circleID."' order by district_name asc";
             return $result = $db->fetchAll($query);
         }
                  public  function getTehsilDetails($tehCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_tehsil where  md5(id)='".$tehCode."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         public function UpdateTehsilData($data, $id){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "update logi_tehsil set circle_id='".$data['circle_id']."', district_id='".$data['district_id']."', tehsil_name='".$data['tehsil_name']."' where id='".$id."'";
		$db->query($query);
         }
         
         
         public function  getAllTehsilsListByCircleID($DistrictID)
         {
             $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT id,tehsil_name FROM `logi_tehsil`  where district_id='".$DistrictID."' order by tehsil_name asc";
             
             
             return $result = $db->fetchAll($query);
         }
         
         public function deleteTehsil($TehsilId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_tehsil set deleted ='1' where md5(id)='".$TehsilId."'";
				
				$db->query($sql); 
			}
         
                        
                        
                        
                        
        public  function thesilList(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_tehsil";  
            return $result = $db->fetchAll($query, array());
        }
         
         
         
       
}
