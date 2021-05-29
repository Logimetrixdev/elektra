<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Regionalhead.php
 * File Description  : Regional Head Model
 * Created By : Praveen Kumar
 * Created Date: 30 September 2013
 ***************************************************************/
 
class Application_Model_Regionalhead extends Zend_Db_Table_Abstract
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
	* getAllRegionalHeadUserList() method is used to get all regional head users list
	* @param Array
	* @return Array 
	*/	
	public function getAllRegionalHeadUserList($get,$staffCode,$title,$sortby)
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
		$userQuery = $db->select()
								   ->from('local_user_mapping',array())
								   ->columns(array('StaffCode','zoneCircleCodes'))
									->where('md5(concat(StaffCode,"@@@@@",role)) =?',$staffCode)
								   ->where('role =?','zone_head')
								   ->where('StaffStatus =?', 'AC')
								   ->where('is_deleted =?', 0); 
		$zoneCircleCodeArr = $db->fetchRow($userQuery);
        $zoneCircleCodeArr = explode(',',$zoneCircleCodeArr['zoneCircleCodes']);
		$i = 1;
		foreach($zoneCircleCodeArr as $val)
		{
			if($i == 1)
			{
				$cond .= " AND (concat(',',regionalCircleCodes,',') like '%,$val,%'";
			}
			else
			{
				$cond .= " OR concat(',',regionalCircleCodes,',') like '%,$val,%'";
			}
			$i++;
		}
		$cond .= ")";
		
		if($get['search'])
		{
			$search = $get['search'];
			$cond = " AND (StaffName like '%$search%' OR EMail like '%$search%' OR StaffCode like '%$search%' OR MobileNo like '%$search%')"; 
		}
		
		$cond .= " AND role='regional_head' AND StaffStatus='AC' AND is_deleted='0' ";
		
		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`role`,`regionalCircleCodes` FROM local_user_mapping WHERE 1 $cond ORDER BY $order";
		return $result = $db->fetchAll($query);
	}
	
	
	/**
	* getRegionalHeadUserDetailsByEmail() method is used to get all regional head users details
	* @param Array
	* @return Array 
	*/
	public function getRegionalHeadUserDetailsByEmail($email)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = $db->select()
						   ->from('local_user_mapping',array())
						   ->columns(array('StaffCode','StaffName','EMail','Password'))
						   ->where('EMail =?',$email)
						   ->where('role =?','regional_head')
						   ->where('StaffStatus =?', 'AC')
						   ->where('is_deleted =?', 0);   
		$result = $db->fetchRow($query);
		if($result){
			return $result;
		}
		return false;
	}
	
}
