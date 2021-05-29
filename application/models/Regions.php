<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Regions.php
 * File Description  : All Regions function
 * Created By : abhishek kumar mishra
 * Created Date: 24 july 2015

 ***************************************************************/

 

class Application_Model_Regions extends Zend_Db_Table_Abstract

{

	
         public function getAllRegionByCircleCode($circlceCode){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT RegionName,RegionCode FROM logi_regions WHERE CircleCode=?";  
            return $result = $db->fetchAll($query, array($circlceCode));
        }
        
        public function getAllRegions(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lci.Circle,lci.CircleCode,lci.state,lds.district_name,lt.tehsil_name,lr.id,lr.RegionName FROM logi_regions as lr
inner join logi_tehsil as lt on (lr.tehsil_id = lt.id)
inner join logi_district as lds on (lr.district_id = lds.id)
inner join logi_circle as lci on (lr.circle_id = lci.id)
where lr.deleted=0 order by lci.state asc";  
            return $result = $db->fetchAll($query, array());
        }
        
       
         
         public function  getAllTehsilByDistrictID($DisId)
         {
             $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT id,tehsil_name FROM `logi_tehsil`  where district_id='".$DisId."' order by tehsil_name asc";
             return $result = $db->fetchAll($query);
         }
            public  function getRegionDetails($regionCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_regions where  md5(id)='".$regionCode."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         
         public function UpdateRegionData($data, $id){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "update logi_regions set circle_id='".$data['circle_id']."', district_id='".$data['district_id']."', tehsil_id='".$data['tehsil_id']."' , RegionName='".$data['RegionName']."' where id='".$id."'";
		$db->query($query);
         }
         
          public function getAllVillageListByRegionID($regionID){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lv.id,lv.village_name FROM logi_villages as lv where lv.region_id='".$regionID."'";  
            return $result = $db->fetchAll($query, array());
        }
        
        public function  getAllRegionsByCircleID($TehsilID)
         {
             $db =  Zend_Db_Table::getDefaultAdapter();
              $query = "SELECT id,RegionName FROM `logi_regions`  where tehsil_id='".$TehsilID."' order by RegionName asc";
             
             
             return $result = $db->fetchAll($query);
         }
         
         public function getAllVillageListByRegionIDs($userID)
         
         { 
            $db =  Zend_Db_Table::getDefaultAdapter();
           
			$userquery = "SELECT id FROM logi_field_users where LoginID='".$userID."'";  
			$userresult = $db->fetchRow($userquery, array());

			$query = "SELECT vill.id,vill.user_id,vill.	circle_id,vill.district_id,vill.tehsil_id,vill.region_id,
			vill.vill_id,lci.state,lds.district_name,lt.tehsil_name,lr.RegionName,vl.village_name FROM logi_user_village_mapping vill
			inner join logi_regions as lr on (vill.region_id = lr.id)
			inner join logi_tehsil as lt on (vill.tehsil_id = lt.id)
			inner join logi_district as lds on (vill.district_id = lds.id)
			inner join logi_circle as lci on (vill.circle_id = lci.id)
			inner join logi_villages as vl on (vill.vill_id = vl.id)
			 where user_id='".$userresult['id']."' order by vill.id DESC";  

			return $result = $db->fetchAll($query, array());
        }
        
        public function getuser_id($userID){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           
			$userquery = "SELECT id FROM logi_field_users where LoginID='".$userID."'";  
			return $userresult = $db->fetchRow($userquery);

			
        }
        
		public function insertVillageMaping($userData)
		{
			$db =  Zend_Db_Table::getDefaultAdapter();
		 $sql="insert into logi_user_village_mapping(circle_id,district_id,tehsil_id,region_id,vill_id,user_id) value (".$userData['circle_id'].",".$userData['district_id'].",".$userData['tehsil_id'].",".$userData['region_id'].",".$userData['vill_id'].",".$userData['user_id'].")";
			
			$db->query($sql);
		}
		public function updateVillageCount($userid)
		{
			$db =  Zend_Db_Table::getDefaultAdapter();
			
			$userquery = "SELECT count(*) as val FROM logi_user_village_mapping where user_id='".$userid."'";  
			$uservill = $db->fetchRow($userquery, array());
			
			
			$sql="update logi_field_users set totalMappedVill='".$uservill['val']."' where id='".$userid."'";  
			$db->query($sql);
		}
		
		public function deleteRegion($TehsilId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_regions set deleted ='1' where md5(id)='".$TehsilId."'";
				
				$db->query($sql); 
			}
		
}
