<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Supervisor.php
 * File Description  : Supervisor Model
 * Created By : Praveen Kumar
 * Created Date: 11 September 2013
 ***************************************************************/
 
class Application_Model_Supervisor extends Zend_Db_Table_Abstract
{
	public function checkUnique($username)
	{
		$select = $this->_dbTable->select()
		->from($this->_name,array('username'))
		->where('username=?',$username);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		if($result){
			return true;
		}
	return false;
	}
	
	
	/**
	* getAllSupervisorUserListWithFilter() method is used to get all supervisor users list
	* @param Array
	* @return Array 
	*/
	public function getAllSupervisorUserListWithFilter($get,$staffCode,$title,$sortby)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		if($title=="name")
		{
		   $sort_title = 'StaffName';
		}
		else if($title=="email")
		{
		   $sort_title = 'EMail';
		}
	    if($sort_title !="" && $sortby !="")
		{
		   $order = $sort_title.' '.$sortby;
		}
		else
		{
		    $order = 'StaffName';
		}
		$cond ="";
	    $serviceManagerQuery = $db->select()
								   ->from('local_user_mapping',array())
								   ->columns(array('StaffCode','serviceManagerCircleCodes'))
									->where('md5(concat(StaffCode,"@@@@@",role)) =?',$staffCode)
								   ->where('role =?','service_manager')
								   ->where('StaffStatus =?', 'AC')
								   ->where('is_deleted =?', 0);    
		$serviceManagerCircleCodesArr = $db->fetchRow($serviceManagerQuery);
        $serviceManagerCircleCodesArr = explode(',',$serviceManagerCircleCodesArr['serviceManagerCircleCodes']);
		$i = 1;
		foreach($serviceManagerCircleCodesArr as $val)
		{
			if($i == 1)
			{
				$cond .= " AND (concat(',',clusterInchargeCircleCodes,',') like '%,$val,%'";
			}
			else
			{
				$cond .= " OR concat(',',clusterInchargeCircleCodes,',') like '%,$val,%'";
			}
			$i++;
		}
		$cond .= ")";
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR StaffCode like '%$search%' OR MobileNo like '%$search%')"; 
		}
		$cond .= " AND role='cluster_incharge' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`StaffStatus`,`role`,`clusterInchargeCircleCodes` FROM local_user_mapping WHERE 1 $cond ORDER BY $order"; 
		return $result = $db->fetchAll($query);
	}
	
	
	/**
	* updateUserLoginDetailsByLoginID() method is used to update app user details
	* @param Array
	* @return True 
	*/	
	public function updateUserLoginDetailsByLoginID($userUniqueId,$lastLogin)
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update user_login_detail set login_time='".$lastLogin."' where mob_user_staff_code='".$userUniqueId."' ");
	}
	
}
