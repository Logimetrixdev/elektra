<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * Created By : abhishek kumar mishra

 * Created Date: 31 july 2015

 ***************************************************************/

 

class Application_Model_States extends Zend_Db_Table_Abstract

{
    
  
	/*----- All master for chemical Type and chemicals ---- */
	
	
	
			public  function getAllchemical(){
			$db =  Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT * from logi_chemical_type order by chemical_type asc";  
			return $result = $db->fetchAll($query, array());
			}
            
			public  function getchemicaltypeDetails($chemical_type){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$query = "SELECT * FROM logi_chemical_type where md5(id)='".$chemical_type."'";  
				return $result = $db->fetchRow($query, array());
			}
			
			public function UpdatechemicaltypeData($UnitData,$id){
			$db =  Zend_Db_Table::getDefaultAdapter();
			$sql = "update logi_chemical_type set chemical_type =  '".$UnitData['chemical_type']."'  where md5(id)='".$id."'";
			$db->query($sql); 
			}
			
			public  function getAllchemicalmaster(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT cm.id,cm.chemical_name,cm.rate_per_unit,cm.quantity,ct.chemical_type,lu.unit,cm.created
            from logi_chemicals as cm
            left join logi_chemical_type as ct on(ct.id = cm.chemical_type_id)
            left join logi_units as lu on(lu.id = cm.unit_id)";  
            return $result = $db->fetchAll($query, array());
			}
			
			
			public  function getChemicalmasterDetails($chemicalId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_chemicals where md5(id)='".$chemicalId."'";  
            return $result = $db->fetchRow($query, array());
			}
			
                   
			public function UpdatechemicalmasterData($UnitData,$id){
			$db =  Zend_Db_Table::getDefaultAdapter();
			$sql = "update logi_chemicals set chemical_type_id =  '".$UnitData['chemical_type_id']."',chemical_name =  '".$UnitData['chemical_name']."',unit_id =  '".$UnitData['unit_id']."',quantity =  '".$UnitData['quantity']."',rate_per_unit =  '".$UnitData['rate_per_unit']."'  where md5(id)='".$id."'";
			$db->query($sql); 
			}
			
			
			
			public  function getAllChemicalTypeForApi(){
			$db =  Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT id,chemical_type from logi_chemical_type order by chemical_type asc";  
			return $result = $db->fetchAll($query, array());
			}
			
			public  function getAllChemicalsForApi(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT cm.id,cm.chemical_type_id,cm.chemical_name,cm.unit_id,cm.rate_per_unit
            from logi_chemicals as cm order by chemical_name asc";  
            return $result = $db->fetchAll($query, array());
			}
			
        /*----- End All master for chemical Type and chemicals ---- */            
       
}
