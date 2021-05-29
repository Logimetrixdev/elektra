<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Seedmasters.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date:13 Aug 2015

 ***************************************************************/

 

class Application_Model_Seedmasters extends Zend_Db_Table_Abstract

{

    
         public function getSeedType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedtype where deleted=0 order by seedType asc";  
            return $result = $db->fetchAll($query);
        }
        
        public function getSeedSubType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT subt.*,st.seedType FROM logi_seedsubtype as subt
left join logi_seedtype as st on (subt.seedType = st.id)
where subt.deleted=0 order by st.seedType asc";  
            return $result = $db->fetchAll($query);
        }
        
        public function getSeedSubAPIType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT subt.* FROM logi_seedsubtype as subt";  
            return $result = $db->fetchAll($query);
        }
        public function getAllRowType(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedrowtype where deleted=0 order by rowtype desc";  
            return $result = $db->fetchAll($query);
        }
        
        
         public function getSeedTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedtype where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getSeedSubTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedsubtype where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
         public function getAllRowTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_seedrowtype where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        
         public function updateMasterData($table, $data, $whereField, $id){ 
		$db = new Zend_Db_table($table);
		$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
		$result = $db->update($data, $where);
	}
        
        public function getAllseeds(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $q = "SELECT ls.SeedID,ls.SeedName,lst.seedType,lsbt.seedsubtype,lsrt.rowtype,ls.per_bag_cost,ls.minimum_payment FROM logi_seeds as ls
left join logi_seedtype as lst on(ls.SeedTypeID = lst.id)
left join logi_seedsubtype as lsbt on(ls.SeedSubTypeID = lsbt.id)
left join logi_seedrowtype as lsrt on(ls.SeedRowTypeID = lsrt.id)
where ls.deleted=0";
            return $result = $db->fetchAll($q);
        }
        
        public function getAllseedsforAPI(){
               $db =  Zend_Db_Table::getDefaultAdapter();
               $q = "SELECT ls.* FROM logi_seeds as ls";
               return $result = $db->fetchAll($q);
        }


        public  function GetAllSubTypeByTypeID($id){
            $db =  Zend_Db_Table::getDefaultAdapter();
             $q = "SELECT subt.id,subt.seedsubtype FROM logi_seedsubtype as subt where subt.seedType='".$id."'";
            //exit();
            return $result = $db->fetchAll($q);
        }

        
        
        public function  getSeedDetails($id)
        {
               $db =  Zend_Db_Table::getDefaultAdapter();
               $q = "SELECT * FROM logi_seeds  where md5(SeedID)='".$id."'";
               return $result = $db->fetchRow($q);
        }
		
		public function getAllSeedOrdersTotal($uid){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT SUM(Qty) as val from logi_farmer_seed_orders where AllotedFieldEngg='".$uid."'";  
            return $result = $db->fetchRow($query);
        }


		public function getAllChemicalOrdersTotal($uid){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT SUM(qty) as val from logi_chemical_order_list where allotedFieldEngg='".$uid."'";  
            return $result = $db->fetchRow($query);
        }
		
		public function deleteseed($seedId){
			$db =  Zend_Db_Table::getDefaultAdapter();
			
		 	$sql = "update logi_seeds set deleted='1' where md5(SeedID)='".$seedId."'";
			
			$db->query($sql); 
			
		}
		
		public function deleteApp($id, $mode){
		$db =  Zend_Db_Table::getDefaultAdapter();
			
			if($mode=='seedtype'){$tbName='logi_seedtype';}elseif($mode=='seedsubtype'){$tbName='logi_seedsubtype';}
			elseif($mode=='rowtype'){$tbName='logi_seedrowtype';}
			
			
				 $sql = "update ".$tbName." set deleted ='1' where md5(id)='".$id."'";
				 
			$db->query($sql); 
			
		}
	


        
}
