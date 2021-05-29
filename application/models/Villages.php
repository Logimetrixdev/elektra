<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Villages.php
 * File Description  : All Regions function
 * Created By : abhishek kumar mishra
 * Created Date: 24 july 2015

 ***************************************************************/

 

class Application_Model_Villages extends Zend_Db_Table_Abstract

{

	
      
        
        public function getAllVillageList(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lci.Circle,lci.CircleCode,lci.state,lds.district_name,lt.tehsil_name,lr.RegionName,lv.id,lv.village_name,lv.pincode FROM logi_villages as lv
inner join logi_regions as lr on (lv.region_id = lr.id)
inner join logi_tehsil as lt on (lv.tehsil_id = lt.id)
inner join logi_district as lds on (lv.district_id = lds.id)
inner join logi_circle as lci on (lv.circle_id = lci.id)
order by lci.state asc, lv.village_name asc";  
            return $result = $db->fetchAll($query, array());
        }
        
       
         
         public function  getAllRegionByTehsilID($tehId)
         {
             $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT id,RegionName FROM `logi_regions`  where tehsil_id='".$tehId."' order by RegionName asc";
            return $result = $db->fetchAll($query);
         }
         
         
            public  function getVillageDetails($villCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_villages where  md5(id)='".$villCode."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         
         public function UpdateVillageData($data, $id){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "update logi_villages set circle_id='".$data['circle_id']."', district_id='".$data['district_id']."', tehsil_id='".$data['tehsil_id']."' , region_id='".$data['region_id']."' , village_name='".$data['village_name']."', pincode='".$data['pincode']."' where id='".$id."'";
		$db->query($query);
         }
         
         public function deleteVillage($VillageId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_villages set deleted ='1' where md5(id)='".$VillageId."'";
				
				$db->query($sql); 
			}
         
         
  
}
