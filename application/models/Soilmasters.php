<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Seedmasters.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date:13 Aug 2015

 ***************************************************************/

 

class Application_Model_Soilmasters extends Zend_Db_Table_Abstract

{

    
         public function getSoilType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT * FROM logi_soilpotential where deleted=0 order by soilpotential asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getSoilTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_soilpotential where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        
        public function getTopographyType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT * FROM logi_topography where deleted=0 order by topography asc";  
            return $result = $db->fetchAll($query);
        }
        
      
         public function gettopographyTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_topography where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getAreaType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT * FROM logi_area order by area asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getAreaTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_area where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        
      
        
        public function getSeedTreatment(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_seedtreatment where deleted=0 order by treatment asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getSeedTreatmentDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedtreatment where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getHerbicide(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_herbicide where deleted=0 order by types asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getHerbicideDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_herbicide where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getCompany(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_company where deleted=0 order by company asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getCompanyDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_company where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getIngredient(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_ingredient where deleted=0 order by types asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getIngredientDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_ingredient where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getStageApplication(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_application where deleted=0 order by types asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getStageApplicationDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_application where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
         public function getSource(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_source where deleted=0 order by types asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getFertilizer(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * FROM logi_fertilizer_master where deleted=0 order by id asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getSourceDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_source where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
         public function getfertilizerDetailsbyid($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_fertilizer_master where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
         public function updateMasterData($table, $data, $whereField, $id){ 
		$db = new Zend_Db_table($table);
		$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
		$result = $db->update($data, $where);
	}
        
        
	
	public function deleteApp($id, $mode){
		$db =  Zend_Db_Table::getDefaultAdapter();
			
			if($mode=='soiltype'){$tbName='logi_soilpotential';}elseif($mode=='topography'){$tbName='logi_topography';}
			elseif($mode=='seedtreatment'){$tbName='logi_seedtreatment';}
			elseif($mode=='herbicide'){$tbName='logi_herbicide';}elseif($mode=='company'){$tbName='logi_company';}
			elseif($mode=='ingredient'){$tbName='logi_ingredient';}elseif($mode=='application'){$tbName='logi_application';}
                        elseif($mode=='Fertilizer'){$tbName='logi_fertilizer_master';}elseif($mode=='application'){$tbName='logi_application';}
			elseif($mode=='source'){$tbName='logi_source';}
			
				 $sql = "update ".$tbName." set deleted ='1' where md5(id)='".$id."'";
				 
			$db->query($sql); 
			
		}
       




		









































        
}
