<?php
class Application_Model_Fqb extends Zend_Db_Table_Abstract
{
	
   
	
	public function getProductDetails()
	{
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select * from fqb_product";
		return $result = $db->fetchAll($query);		
	}
        
        public function GetNewIdforFQB(){
                $db =  Zend_Db_Table::getDefaultAdapter();
                $sql = "select fqb_id from fqb_details  order by fqb_id desc";
                $result = $db->fetchRow($query);
               
                $newId = $result['fqb_id']+1;
                return $newId;
        }

                public function getFqbDetails()
	{
		
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select fd.fqb_no,fd.site_id,fd.circle,fd.cluster,fd.staff_code,fd.fqb_filled_date_time,fd.fqb_send_date_time,fd.fqb_status from fqb_details as fd";
		return $result = $db->fetchAll($query);
//                $result = $db->fetchAll($query);
//                print_r($result);
//                exit();
	}
        
  
}
