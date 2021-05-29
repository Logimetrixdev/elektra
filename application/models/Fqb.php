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
		$query = "select fd.fqb_id,fd.fqb_no,fd.site_id,fd.circle,fd.cluster,fd.staff_code,fd.fqb_filled_date_time,fd.fqb_send_date_time,fd.fqb_status from fqb_details as fd";
		return $result = $db->fetchAll($query);
	}
        
        public function getFqbDetailsByID($id)
        {
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select fd.fqb_id,fd.fqb_no,fd.ome_remark,fd.sme_remarks,fd.site_id,fd.circle,fd.cluster,fd.fqb_filled_date_time,fd.site_photograph1,fd.site_photograph2,fd.site_photograph3,fd.site_photograph4,fd.site_photograph5,fd.ome_signature,fd.sme_signature from fqb_details as fd
where fd.fqb_id='".$id."' ";
            return $result = $db->fetchRow($query);
        }
        
        
    public function getProductForFqb($fqbId){
          $db =  Zend_Db_Table::getDefaultAdapter();  
          $query = "select * from fqb_products where fqb_id='".$fqbId."'";
          return $result = $db->fetchAll($query);
         }
    public function getStaffNameAndMobileNo($fqb_id)
        {
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select lum.LoginID,lum.StaffName,lum.MobileNo,fd.staff_code,fd.fqb_id from local_user_mapping as lum
            left join fqb_details as fd on (lum.LoginID = fd.staff_code)
            where LoginID = staff_code and fqb_id = '".$fqb_id."' ";
            return $result = $db->fetchRow($query);
        }
        
        
         public function deleteFqbImage($del_id,$img)
        {
              $db =  Zend_Db_Table::getDefaultAdapter();
            if($img==1){
              $sql ="UPDATE fqb_details SET site_photograph1='' where fqb_id ='".$del_id."'";
            }elseif($img==2){
                $sql ="UPDATE fqb_details SET site_photograph2='' where fqb_id ='".$del_id."'";  
            }elseif($img==3){
                $sql ="UPDATE fqb_details SET site_photograph3='' where fqb_id ='".$del_id."'";  
            }elseif($img==4){
                $sql ="UPDATE fqb_details SET site_photograph4='' where fqb_id ='".$del_id."'";  
            }elseif($img==5){
                $sql ="UPDATE fqb_details SET site_photograph5='' where fqb_id ='".$del_id."'";  
            }else{
                echo 'Invalid entry';
            }
            $db->query($sql);
        }

}
