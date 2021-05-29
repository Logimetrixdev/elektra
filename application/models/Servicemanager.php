<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Servicemanager.php
 * File Description  : Service Manager Model
 * Created By : Praveen Kumar
 * Created Date: 30 September 2013
 ***************************************************************/
 
class Application_Model_Servicemanager extends Zend_Db_Table_Abstract
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
	* getAllServiceManagerUserList() method is used to get all circle head users list
	* @param Array
	* @return Array 
	*/	
	public function getAllServiceManagerUserList($get,$staffCode,$title,$sortby)
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
		$circleQuery = $db->select()
								   ->from('local_user_mapping',array())
								   ->columns(array('StaffCode','circleCodes'))
									->where('md5(concat(StaffCode,"@@@@@",role)) =?',$staffCode)
								   ->where('role =?','circle_head')
								   ->where('StaffStatus =?', 'AC')
								   ->where('is_deleted =?', 0);   
		$circleCodesArr = $db->fetchRow($circleQuery);
        $circleCodesArr = explode(',',$circleCodesArr['circleCodes']);
		$i = 1;
		foreach($circleCodesArr as $val)
		{
			if($i == 1)
			{
				$cond .= " AND (concat(',',serviceManagerCircleCodes,',') like '%,$val,%'";
			}
			else
			{
				$cond .= " OR concat(',',serviceManagerCircleCodes,',') like '%,$val,%'";
			}
			$i++;
		}
		$cond .= ")";
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR StaffCode like '%$search%'  OR MobileNo like '%$search%')"; 
		}
		$cond .= " AND role='service_manager' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`role`,`serviceManagerCircleCodes` FROM local_user_mapping WHERE 1 $cond ORDER BY $order"; 
		return $result = $db->fetchAll($query);
	}
	
}
