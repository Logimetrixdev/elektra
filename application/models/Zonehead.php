<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Zonehead.php
 * File Description  : Zone Head Model
 * Created By : Praveen Kumar
 * Created Date: 30 September 2013
 ***************************************************************/
 
class Application_Model_Zonehead extends Zend_Db_Table_Abstract
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
	* getAllZoneHeadUserList() method is used to get all zone head users list
	* @param Array
	* @return Array 
	*/	
	public function getAllZoneHeadUserList($get,$title,$sortby)
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
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR StaffCode like '%$search%' OR MobileNo like '%$search%')"; 
		}
		
		$cond .= " AND role='zone_head' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`role`,`zoneCircleCodes` FROM local_user_mapping WHERE 1 $cond ORDER BY $order";  
		return $result = $db->fetchAll($query);
	}
	
	
	/**
	* getZoneHeadUserDetailsByEmail() method is used to get all national head users details
	* @param Array
	* @return Array 
	*/
	public function getZoneHeadUserDetailsByEmail($email)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = $db->select()
						   ->from('local_user_mapping',array())
						   ->columns(array('StaffCode','StaffName','EMail','Password'))
						   ->where('EMail =?',$email)
						   ->where('role =?','zone_head')
						   ->where('StaffStatus =?', 'AC')
						   ->where('is_deleted =?', 0);   
		$result = $db->fetchRow($query);
		if($result){
			return $result;
		}
		return false;
	}
	
}
