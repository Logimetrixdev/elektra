<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * Created By : abhishek kumar mishra

 * Created Date: 31 july 2015

 ***************************************************************/

 

class Application_Model_Districts extends Zend_Db_Table_Abstract

{

	   
        
         public function getAllDistrict(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lci.Circle,lci.CircleCode,lci.state,lds.id,lds.district_name FROM logi_district as lds
inner join logi_circle as lci on (lds.circle_id = lci.id)
where lds.deleted=0 order by lci.state asc ";  
            return $result = $db->fetchAll($query, array());
        }
        
        public  function getAllState(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT id,state,Circle FROM logi_circle order by state asc";  
            return $result = $db->fetchAll($query, array());
         }
         
         public  function getDistrictDetails($disId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_district where  md5(id)='".$disId."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         public function UpdateDistrictData($data, $id){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "update logi_district set circle_id='".$data['circle_id']."', district_name='".$data['districtname']."' where id='".$id."'";
		$db->query($query);
         }
         
         public function deleteDistrict($DistrictId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_district set deleted ='1' where md5(id)='".$DistrictId."'";
				
				$db->query($sql); 
			}
       
}
