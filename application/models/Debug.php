<?php

/***************************************************************

 * Logimetrix TechSolutions Pvt Ltd.

 * File Name   : DebugController.php

 * File Description  : All Method for Debugging the data

 * Created By : Abhishek Kumar Mishra

 * Created Date: 24 Dec 2014

 ***************************************************************/
 
class Application_Model_Debug extends Zend_Db_Table_Abstract
{
    public function GetUserDetails($staffCode)
    {
        $db =  Zend_Db_Table::getDefaultAdapter();
	//$Query_Staff_User = "select LoginID,StaffCode,StaffName,CMSStaffStockCode,StaffStatus,clusterInchargeCircleCodes,role from local_user_mapping where CMSStaffStockCode='".$cmsCode."';";
        $Query_Staff_User = "select lum.LoginID,lum.StaffCode,lum.StaffName,lum.CMSStaffStockCode,lum.StaffStatus,lum.fieldUserCodes,lum.fieldUserParent,lum.role,
lum2.StaffCode AS clusterStaffCode,lum2.StaffName AS cluster_incharge
from local_user_mapping AS lum
LEFT JOIN local_user_mapping AS lum2 on (lum.fieldUserParent=lum2.StaffCode)
where lum.StaffCode='".$staffCode."'";
        return  $result = $db->fetchRow($Query_Staff_User, array()); 
    }
    
    public function GetAllenggDetails($clusterInchageStaffCode)
    {
        $db =  Zend_Db_Table::getDefaultAdapter();
      //  echo $clusterInchageStaffCode;
        //exit();
        
       $cluster_engg="select LoginID,StaffCode,StaffName,CMSStaffStockCode,StaffStatus,clusterInchargeCircleCodes,role from local_user_mapping where fieldUserParent='".$clusterInchageStaffCode."'AND role='field_user';";
      // echo  $cluster_engg;exit;    
      return  $result2 = $db->fetchAll($cluster_engg, array()); 
     //   print_r($result);exit();
        
    }
   
}

