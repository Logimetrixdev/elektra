<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * Created By : abhishek kumar mishra

 * Created Date: 31 july 2015

 ***************************************************************/

 

class Application_Model_Distributioncenters extends Zend_Db_Table_Abstract

{

	   
        
         public function getAllDistributionCenters(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lds.id as ID,lds.distribution_center as DC,lci.RegionName as RN FROM logi_distibution_center as lds
inner join logi_regions as lci on (lds.regionId = lci.id)
where lds.deleted=0 order by lci.RegionName asc";  
            return $result = $db->fetchAll($query, array());
        }
        
        
        
        
        public  function getAllRegions(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT id,RegionName FROM logi_regions order by RegionName asc";  
            return $result = $db->fetchAll($query, array());
         }
         
         public  function getDCDetails($dcId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_distibution_center where  md5(id)='".$dcId."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         public function UpdateDCData($data, $id){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "update logi_distibution_center set regionId='".$data['regionId']."', distribution_center='".$data['distribution_center']."' where id='".$id."'";
		$db->query($query);
         }
         
         public function deletedistribution($Id){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_distibution_center set deleted ='1' where md5(id)='".$Id."'";
				
				$db->query($sql); 
			}
         
         
       
}
