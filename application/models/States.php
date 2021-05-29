<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * Created By : abhishek kumar mishra

 * Created Date: 31 july 2015

 ***************************************************************/

 

class Application_Model_States extends Zend_Db_Table_Abstract

{
    
    public function getCircleDetailsbyCircleCode($circleCode){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT  id, state, CircleCode, Circle FROM logi_circle WHERE id=?";  
            return $result = $db->fetchRow($query, array($circleCode));
        }
	   
        
         public function getAllState(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_circle where deleted='0' order by state asc";  
            return $result = $db->fetchAll($query, array());
        }
        
        public  function getStateDetails($branchCode,$stateId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_circle where md5(CircleCode)='".$branchCode."' and md5(id)='".$stateId."'";  
            return $result = $db->fetchRow($query, array());
         }
         
         public function UpdateStateData( $UnitData,$unitId){ 
			
		$db =  Zend_Db_Table::getDefaultAdapter();
			$sql = "update logi_units set stateId =  '".$UnitData['stateId']."',unittype ='".$UnitData['unittype']."',unit ='".$UnitData['unit']."',disp ='".$UnitData['disp']."',acr_val ='".$UnitData['acr_val']."'  where md5(id)='".$unitId."'";
		
			$db->query($sql); 
	
}
	
	
	/*----- All master for chemical Type and chemicals ---- */
	
	
	
			public  function getAllchemical(){
			$db =  Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT * from logi_chemical_type where deleted=0 order by chemical_type asc";  
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
            $query = "SELECT cm.id,cm.chemical_name,cm.rate_per_unit,cm.quantity,cm.late_delivery,cm.active_ingredient,ct.chemical_type,lu.unit,cm.created
            from logi_chemicals as cm
            left join logi_chemical_type as ct on(ct.id = cm.chemical_type_id)
            left join logi_units as lu on(lu.id = cm.unit_id) where cm.deleted=0";  
            return $result = $db->fetchAll($query, array());
			}
			
			public  function getAllUnits(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT ut.id,ut.unittype,ut.unit,ut.disp,ut.acr_val,lc.state FROM logi_units as ut
				left join logi_circle as lc on(ut.stateId = lc.id) where ut.deleted=0 order by ut.unittype asc";  
            return $result = $db->fetchAll($query, array());
			}
			public  function getUnitDetails($unitId){
                                $db =  Zend_Db_Table::getDefaultAdapter();
                                $query = "SELECT lu.unittype,lu.unit,state,lu.disp,lu.acr_val,lu.stateId from logi_units as lu
                                         left join logi_circle as lc on(lu.stateId = lc.id) where md5(lu.id) ='".$unitId."'";         
                                return $result = $db->fetchRow($query, array());
            
			}
			
			public  function getChemicalmasterDetails($chemicalId){
                                $db =  Zend_Db_Table::getDefaultAdapter();
                                $query = "SELECT * FROM logi_chemicals where md5(id)='".$chemicalId."'";  
                                return $result = $db->fetchRow($query, array());
			}
			
                   
			public function UpdatechemicalmasterData($UnitData,$id){
                                $db =  Zend_Db_Table::getDefaultAdapter();
                                $sql = "update logi_chemicals set chemical_type_id =  '".$UnitData['chemical_type_id']."',chemical_name =  '".$UnitData['chemical_name']."',unit_id =  '".$UnitData['unit_id']."',quantity =  '".$UnitData['quantity']."',rate_per_unit =  '".$UnitData['rate_per_unit']."',active_ingredient =  '".$UnitData['active_ingredient']."',late_delivery =  '".$UnitData['late_delivery']."'  where md5(id)='".$id."'";
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
			
			public function deleteState($StateId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_circle set deleted ='1' where md5(id)='".$StateId."'";
				$db->query($sql); 
			}
			
			public function deleteUnit($UnitId){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$sql = "update logi_units set deleted ='1' where md5(id)='".$UnitId."'";
				$db->query($sql); 
			}
                        
                        
                        public  function getAllchemicalLIST($id){
                                $db =  Zend_Db_Table::getDefaultAdapter();
                                $query = "SELECT cm.id,cm.chemical_name,cm.rate_per_unit,cm.quantity,cm.late_delivery,cm.active_ingredient,ct.chemical_type,lu.unit,cm.created
                                          from logi_chemicals as cm
                                          left join logi_chemical_type as ct on(ct.id = cm.chemical_type_id)
                                          left join logi_units as lu on(lu.id = cm.unit_id) where md5(cm.chemical_type_id) = '".$id."' and cm.deleted=0 order by cm.id desc";  
                                return $result = $db->fetchAll($query, array());
			}
			
        /*----- End All master for chemical Type and chemicals ---- */            
       
}
