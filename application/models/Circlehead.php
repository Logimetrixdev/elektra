<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Circlehead.php
 * File Description  : Circle Head Model
 * Created By : Praveen Kumar
 * Created Date: 30 September 2013
 ***************************************************************/
 
class Application_Model_Circlehead extends Zend_Db_Table_Abstract
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
	* getAllCircleHeadUserList() method is used to get all circle head users list
	* @param Array
	* @return Array 
	*/	
	public function getAllCircleHeadUserList($get,$staffCode,$title,$sortby)
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
		$regionalQuery = $db->select()
								   ->from('local_user_mapping',array())
								   ->columns(array('StaffCode','regionalCircleCodes'))
									->where('md5(concat(StaffCode,"@@@@@",role)) =?',$staffCode)
								   ->where('role =?','regional_head')
								   ->where('StaffStatus =?', 'AC')
								   ->where('is_deleted =?', 0);  
		$regionalCircleCodesArr = $db->fetchRow($regionalQuery);
        $regionalCircleCodesArr = explode(',',$regionalCircleCodesArr['regionalCircleCodes']);
		$i = 1;
		foreach($regionalCircleCodesArr as $val)
		{
			if($i == 1)
			{
				$cond .= " AND (concat(',',CircleCodes,',') like '%,$val,%'";
			}
			else
			{
				$cond .= " OR concat(',',CircleCodes,',') like '%,$val,%'";
			}
			$i++;
		}
		$cond .= ")";
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR StaffCode like '%$search%'  OR MobileNo like '%$search%')"; 
		}
		$cond .= " AND role='circle_head' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`role`,`circleCodes` FROM local_user_mapping WHERE 1 $cond ORDER BY $order"; 
		return $result = $db->fetchAll($query);
	}
	
	
	
	function getAllcircleList()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
	   	$query = "SELECT `CircleCode`,`CircleName` FROM circle_master ORDER BY CircleCode"; 
		return $result = $db->fetchAll($query);
	}
	
	
	
}
