<?php

/***************************************************************
 * Logimetrix Tech Solutions Pvt. Ltd.
 * File Name   : AccountSettingController.php
 * File Description  : Account Settings
 * Created By : Abhishek Kumar Mishra
 * Created Date: 20 June 2015
 ***************************************************************/
 
class Application_Model_Nationalhead extends Zend_Db_Table_Abstract
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
	* getAllNationalHeadUserList() method is used to get all national head users list
	* @param Array
	* @return Array 
	*/	
	public function getAllNationalHeadUserList($get)
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR MobileNo like '%$search%')"; 
		}
		
		$cond .= " AND role='national_head' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`role` FROM local_user_mapping WHERE 1 $cond ";
		return $result = $db->fetchAll($query);
	}
	
	
	/**
	* getNationalHeadUserDetails() method is used to get all national head users details
	* @param Array
	* @return Array 
	*/	
	public function getNationalHeadUserDetails()
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
	    $query = $db->select()
						   ->from('local_user_mapping',array())
						   ->columns(array('StaffCode','StaffName'))
						   ->where('role =?','national_head')
						   ->where('StaffStatus =?', 'AC')
						   ->where('is_deleted =?', 0);   
		return $result = $db->fetchRow($query);
	}
	
	
}
