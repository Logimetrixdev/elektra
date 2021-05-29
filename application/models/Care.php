<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * File Name   : ApiController.php

 * File Description  : All Api Method

 * Created By : abhishek kumar mishra

 * Created Date: 24 july 2015

 ***************************************************************/

 

class Application_Model_Care extends Zend_Db_Table_Abstract{

	//var $userCondi = " and (role='field_user' OR role='zone_head') and is_deleted='0' and access_status='0' and StaffStatus='AC'";
        
        
        
         public function getUserLoginDetailByWebLoginCode($WebLoginID){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_user_login_detail WHERE WebLoginID=?";  
            return $result = $db->fetchRow($query, array($WebLoginID));
        }
        
        /**
	* updateUserLoginDetailsByLoginID() method is used to update app user details
	* @param Array
	* @return True 
	* add by Abhishek(24-07-2015)
	*/	

	public function updateUserLoginDetailsByLoginID($WebLoginID,$login_time){ 
                $db =  Zend_Db_Table::getDefaultAdapter();
                $db->query("update logi_user_login_detail set login_time=? where WebLoginID=? ", array($login_time,$WebLoginID)); 
        }
        
        /**
	* insertUserLoginDetailsByWebLoginID() method is used to insert user login details  by web app
	* @param Array
	* @return True 
	*/	

	public function insertUserLoginDetailsByWebLoginID($user_login_data = array()){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            return $result = $db->insert("logi_user_login_detail",$user_login_data);
        }
        
        /**
	* insertUserLoginDetailsByLoginID() method is used to insert user login details by mobile app
	* @param Array
	* @return True 
	*/	

	public function insertUserLoginDetailsByLoginID($user_login_data = array()){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            return $result = $db->insert("logi_user_login_detail",$user_login_data);
        }
        
        
        
        
        /**
	* updateUserLogoutDetailsByLoginID() method is used to update app user details
	* @param Array
	* @return True 
	* add by Abhishek(24-07-2015)
	*/	

	public function updateUserLogoutDetailsByLoginID($WebLoginID,$logout_time){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update logi_user_login_detail set logout_time=? where WebLoginID=? ", array($logout_time,$WebLoginID)); 
	}
        
        
       /**
	* updateChangePasswordByStaffCode() method is used to get all users details
	* @param Array
	* @return Array 
	*/

	public function updateChangePasswordByStaffCode($updateData = array(), $where){  
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->update('logi_field_users', $updateData , $where);
	}
        
        
        public function GetAllFEList($webLoginId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT StaffName,LoginID,Email,MobileNo FROM logi_field_users 
            WHERE ParentWebLoginID=? and StaffStatus='AC' and role='field_user' and project = '' order by id DESC";  
            return $result = $db->fetchAll($query, array($webLoginId));
        }

        /**

	* getUserDataByUserUniqueId() method is used to get all users list by email
	* @param String and Array
	* @return Array 
	*/	

    public function getUserDataByLoginId($LoginID){ 
        $db =  Zend_Db_Table::getDefaultAdapter();
        $query = "SELECT id as userId,LoginID,DeviceID,StaffStatus,StaffName,Device_type,Device_token,Role,App_Access_Status,live_on_date FROM logi_field_users WHERE LoginID=? and StaffStatus='AC'
       AND Role='field_user'"; 

		return $result = $db->fetchRow($query, array($LoginID));

	}

        
        public function updateSingleTableData($table='logi_field_users', $data, $whereField, $id){ 
		$db = new Zend_Db_table($table);
		$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
		$result = $db->update($data, $where);
	}
        
//         public function getUserRecordByLoginID($LoginID){
//             $db =  Zend_Db_Table::getDefaultAdapter();
//             $query = "select lf.LoginID as loginId,lf.StaffName as staffName,lf.CircleID as circleId,lc.state as circleName,lf.DistrictID as districtId,
// ld.district_name as districtName,lf.TehsilID as tehsilId,lt.tehsil_name as tehsilName,lf.ReigonID as regionId,lr.RegionName as regionName
// from logi_field_users as lf
// left join logi_circle as lc on(lf.CircleID=lc.id)
// left join logi_district as ld on(lf.DistrictID=ld.id)
// left join logi_tehsil as lt on(lf.TehsilID=lt.id)
// left join logi_regions as lr on(lf.ReigonID=lr.id)
// where lf.LoginID=? and lf.StaffStatus='AC' and lf.Role='field_user'"; 
//           return $result = $db->fetchRow($query, array($LoginID));
//         }

	public function getUserRecordByLoginID($LoginID){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select lf.LoginID as loginId,lf.StaffName as staffName
from logi_field_users as lf
where lf.LoginID=? and lf.StaffStatus='AC' and lf.Role='field_user'"; 
          return $result = $db->fetchRow($query, array($LoginID));
        }



         


         

         
         
         
		public function getuserDetails($userID){
		 $db =  Zend_Db_Table::getDefaultAdapter();
		
		$query = "select * from logi_field_users where LoginID='".$userID."'";
		
		 return $result = $db->fetchAll($query, array($regionID));
		}




		public function getuser_id($UserID){
			$db =  Zend_Db_Table::getDefaultAdapter();
		
			$userquery = "SELECT id FROM logi_field_users where LoginID='".$UserID."'";  
			
			return $db->fetchRow($userquery);
		}






		public function getuserData(){
		 $db =  Zend_Db_Table::getDefaultAdapter();
		
		$query = "select * from logi_field_users where Role!='service_manager' and project !=' '";
		
		 return $result = $db->fetchAll($query);
		}














         public function insertSingleTableData($table='users', $data)

	{

		$db = new Zend_Db_table($table);

		return $db->insert($data);

	}

	/********* check unique id *************/

	public function checkUniqueField($table='users', $field = 'email',$email){

		$db = new Zend_Db_table($table);

		$select = $db->select()->where("$field = ?", $email);

		$eArr = new stdClass();

		$eArr = $db->fetchAll($select);

		if($eArr) {

		 $eArr->toArray();

		}

		else {

			$eArr = array();

		}		

		return count($eArr);

	}

	

	
        
     public function getUserDataFqbByLoginId($LoginID)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT EntryNo,LoginID,device_id,access_status, StaffCode,StaffStatus, StaffName,CMSStaffStockCode,device_type,device_token,role,Call_Log_No FROM local_user_mapping WHERE LoginID=? and StaffStatus='AC'
	 AND (role='field_user' OR role='cluster_incharge' OR role='service_manager')";  

		return $result = $db->fetchRow($query, array($LoginID));

	}

	

	/**

	* getUserLoginDetailByUserUniqueId() method is used to get all users list by email

	* @param String and Array

	* @return Array 

	*/	

   



    /**

	* getUserAllDataByLoginID() method is used to get all users list by loginId

	* @param String and Array

	* @return Array 

	*/	

    public function getUserNamebyStaffCode($StaffCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	   $query = "SELECT StaffName FROM logi_field_users WHERE LoginID='".$StaffCode."'";  
		return $result = $db->fetchRow($query);

	}		

	

	

	

	

	

	

	

	

	

	

	

	/**

	* getAllData() method is used to get all users list 

	* @param String and Array

	* @return Array 

	

	*/	

	public function getAllData($table='local_user_mapping', $whereField = 'LoginID',$fieldValue, $fieldArr = array()){

		$db = new Zend_Db_table($table);

		if(!count($fieldArr))

		{

			$fieldArr = array('*');

		}

		

		$select = $db->select()

					->from( array('tab' => $table), $fieldArr )

					->where("$whereField = ?", $fieldValue);

		$eArr = new stdClass();

		$eArr = $db->fetchRow($select);

		if($eArr)

		{

			$eArr = $eArr->toArray();

		}

		return $eArr;

	}

	



	/**

	* insertChangeJobStatusByUserUniqueId() method is used to user site track of user

	* @param Array

	* @return True 

	*/	

	public function insertChangeJobStatusByCMSStaffStockCode($insertData = array())

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		return $result = $db->insert("user_site_track",$insertData);

	}

	

	

	/**

	* insertLocationTrackByUserUniqueId() method is used to insert location track of user

	* @param Array

	* @return True 

	*/	

	public function insertLocationTrackByStaffCode($insertData=array())

	{  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->insert("user_path",$insertData);

		return $result = $db->lastInsertId(); 

	}

	

	

	/**

	* insertUserAttendanceByUserUniqueId() method is used to user attendance

	* @param Array

	* @return True 

	* @return True 

	*/	

	public function insertUserAttendance($insertData = array())

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		return $result = $db->insert("attendance",$insertData);

	}

	

	public function updateUserCloseDay($StaffCode, $insertData)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date('Y-m-d');

		//return $result = $db->query("update attendance set status=2, logout_date='".date('Y-m-d H:i:s')."' where mob_user_staff_code=? and DATE(attend_date)=?", array($StaffCode, $date));

		return $result = $db->update("attendance",$insertData, array('mob_user_staff_code =?'=>$StaffCode, "DATE(attend_date)=?"=>$date,"status=?"=>'1'));

	}

	

	/**

	* updateDeviceDetailsByUserId() method is used to update app user details

	* @param Array

	* @return True 

	*/	

	public function updateDeviceDetailsByUserId($userUniqueId)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update local_user_mapping set device_id='' where LoginID='".$userUniqueId."' ");

	}

	

	

	/**

	* updateLogoutDetailsByUserId() method is used to update app user details

	* @param Array

	* @return True 

	*/	

	public function updateLogoutDetailsByUserId($logout_time,$userUniqueId)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update user_login_detail set logout_time='".$logout_time."' where mob_user_staff_code='".$userUniqueId."' ");

	}

	

	

	/**

	* getAllUserListBySupervisorIdWithFilter() method is used to get all users list by supervisor id

	* @param Array

	* @return Array 

	*/	

	public function getAllUserListBySupervisorIdWithFilter($get,$staffCode,$title,$sortby)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		if($title=="name")

		{

		   $sort_title = 'StaffName';

		}

		else if($title=="staffcode")

		{

		   $sort_title = 'StaffCode';

		}

		else if($title=="email")

		{

		   $sort_title = 'EMail';

		}

		else if($title=="LoginID")

		{

		   $sort_title = 'LoginID';

		}

	    if($sort_title !="" && $sortby !="")

		{

		   $order = $sort_title.' '.$sortby;

		}

		else

		{

		    $order = 'StaffName';

		}

		

		$cond = "";

		

		

		if($get['search'])

		{

			$search = $get['search'];

			$cond .= " AND (StaffName like '%$search%' OR EMail like '%$search%' OR MobileNo like '%$search%')"; 

		}

		

		$cond .= " AND (md5(concat(fieldUserParent,'@@@@@','zone_head')) = '".$staffCode."' OR md5(concat(fieldUserParent,'@@@@@',role)) = '".$staffCode."' )";

		

		$query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`StaffStatus`,`role`, CMSStaffStockCode, LoginID FROM local_user_mapping WHERE (`role`='field_user' OR `role`='zone_head') AND  `StaffStatus`='AC' AND `is_deleted`='0' $cond ORDER BY $order";  

		return $result = $db->fetchAll($query);

	}

	

	/**

	* getUserdetailsByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserdetailsByUserId($user_id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT CMSStaffStockCode,StaffName,StaffCode,device_type,device_token,device_id, 	battery_status FROM local_user_mapping WHERE md5(concat(StaffCode,'@@@@@',role))=?";  

		return $result = $db->fetchRow($query, array($user_id));

	}

	

	/**

	* getUserDetailsByStaffCode() method is used to get users detail by StaffCode

	* @param Charector

	* @return Array 

	*/	

	/******** created by saurabh ************/

	public function getUserDetailsByStaffCode($staff_code)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT CMSStaffStockCode,StaffName,StaffCode FROM local_user_mapping WHERE StaffCode=?"; 

		return $result = $db->fetchRow($query, array($staff_code));

	}

	

	/**

	* getAllUserDetails() method is used to get all users detail 

	* @param Null

	* @return Array 

	*/	

	public function getAllUserDetails()

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT StaffCode,StaffName FROM local_user_mapping WHERE StaffStatus='AC' and is_deleted='0' ORDER BY StaffName"; 

		return $result = $db->fetchAll($query);

	}

	

	/**

	* insertUserMessage() method is used to insert user message

	* @param Array

	* @return True 

	*/	

	public function insertUserMessage($messageData = array())

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->insert("send_msg",$messageData);

		return $result = $db->lastInsertId(); 

	}

	

	

	/**

	* insertUserSendMessage() method is used to insert user message

	* @param Array

	* @return True 

	*/	

	public function insertUserSendMessage($users,$message_id,$circleCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		//echo "INSERT INTO send_msg_user SET msg_id='".$message_id."', mob_user_staff_code='".$users."',circleCode='".$circleCode."' ";die;

		$db->query("INSERT INTO send_msg_user SET msg_id='".$message_id."', mob_user_staff_code='".$users."',circleCode='".$circleCode."' ");

		//$db->query("INSERT INTO send_msg_user SET msg_id=?, mob_user_staff_code=?,circleCode=?", array($message_id,$users,$circleCode));

	}

	

	/**

	* getUserStaffEmailByStaffCode() method is used to get user sraff email

	* @param Integer

	* @return Array 

	*/	

	public function getUserStaffEmailByStaffCode($staff_code)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT StaffName,StaffCode,EMail,MobileNo,CMSStaffStockCode,device_type,device_token FROM local_user_mapping WHERE StaffCode=?"; 

		return $result = $db->fetchRow($query, array($staff_code));

	}

	

	

	/**

	* updateUserDeletedStaffCode() method is used to update is_deleted value

	* @param String

	* @return True 

	*/	

	public function updateUserDeletedStaffCode($staffCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update local_user_mapping set is_deleted='1' where md5(concat(StaffCode,'@@@@@',role)) =?", array($staffCode));

	}

	

		

	/**

	* insertUserDeletedStaffCode() method is used to insert userCode value

	* @param String

	* @return True 

	*/	

	public function insertUserDeletedStaffCode($userCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("INSERT INTO deleted_users SET mob_user_staff_code=?", array($userCode));

	}

	

	

	/**

	* insertUserSettingValue() method is used to insert frequency

	* @param Array

	* @return True 

	*/	

	public function insertUserSettingValue($default_frequency,$frequency_value_day,$frequency_value_night,$default_value)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("insert into setting set setting_name =?,setting_value_day =?,setting_value_night =?,default_value=? ",array($default_frequency,$frequency_value_day,$frequency_value_night,$default_value));	

	}

	

	

	/**

	* updateUserSettingValue() method is used to insert frequency

	* @param Array

	* @return True 

	*/	

	public function updateUserSettingValue($default_frequency,$frequency_value_day,$frequency_value_night,$default_value,$id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update setting set setting_name =?,setting_value_day =?,setting_value_night =?,default_value=? where id=?",array($default_frequency,$frequency_value_day,$frequency_value_night,$default_value,$id));		

	}

	

	

	

	

	

	/**

	* getUserLogDetailsByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserLogDetailsByUserId($user_id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

        $date = date('Y-m-d H:i:s');

	    $query = "SELECT noti_date,mob_user_staff_code FROM notification WHERE type='Login' and mob_user_staff_code=? and date(noti_date)=?";  

		return $result = $db->fetchRow($query,array($user_id,$date));

	}

	

	/**

	* getUserJobAcceptStatusLogByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserJobAcceptStatusLogByUserId($user_id)

	{ 

	    $currdate = date('Y-m-d');

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT DATE_FORMAT(noti_date,'%h:%i') as job_accept_time FROM notification WHERE mob_user_staff_code=? and DATE(noti_date)=? and message like '%accepted%' and type='Job' ";  

		return $result = $db->fetchRow($query,array($user_id,$currdate));

	}

	

	

    /**

	* getUserJobTowardLogByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserJobTowardLogByUserId($user_id)

	{ 

	    $currdate = date('Y-m-d');

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT DATE_FORMAT(noti_date,'%h:%i') as toward_time FROM notification WHERE mob_user_staff_code=? and DATE(noti_date)=? and message like '%Toward%' and type='Job' ";  

		return $result = $db->fetchRow($query,array($user_id,$currdate));

	}

	

	/**

	* getUserJobLeftLogByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserJobLeftLogByUserId($user_id)

	{ 

	    $currdate = date('Y-m-d');

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT DATE_FORMAT(noti_date,'%h:%i') as left_time FROM notification WHERE mob_user_staff_code=? and DATE(noti_date)=? and message like '%Left%' and type='Job' ";  

		return $result = $db->fetchRow($query,array($user_id,$currdate));

	}

	

	

	/**

	* getUserBatteryLowLogByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserBatteryLowLogByUserId($user_id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

        $date = date('Y-m-d H:i:s');

	    $query = "SELECT DATE_FORMAT(noti_date,'%h:%i') as battery_low_time,mob_user_staff_code FROM notification WHERE mob_user_staff_code=? and DATE(noti_date)=? and message like '%low%' and type='User' ";  

		return $result = $db->fetchRow($query,array($user_id,$date));

	}

	

	

	/**

	* getUserPathDetailsByUserId() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getUserPathDetailsByUserId($year,$month,$userId)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		/* if($year)

		{

			$cond = " AND YEAR(add_date_time)='".$year."'";

		}

		if($year)

		{

		    $month = $month['month']; 

			$cond = " AND MONTH(add_date_time)='".$month."'";

		}*/

		if(!empty($year) && !empty($month['month']))

		{

			$month = $month['month']; 

		    $cond = " AND YEAR(add_date_time)='".$year."' AND MONTH(add_date_time)='".$month."' ";

		}

		

		$cond .=" and mob_user_staff_code='".$userId."' ";

	    $query = "SELECT DATE_FORMAT(add_date_time,'%Y-%m-%d') AS date_time,battery_status,mob_user_staff_code FROM user_path WHERE 1 $cond GROUP BY date_time ORDER BY date_time DESC";  

		return $result = $db->fetchAll($query); 

	}

	

	

	/**

	* getStartPointLatLongByUserId() method is used to get start lat long by user id

	* @param Integer

	* @return Array 

	*/	

	public function getStartPointLatLongByUserId($year='NULL',$month=array(),$date='NULL',$user_id='NULL')

	{ 

	    $db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		/*if(!empty($year))

		{

		    echo "dsfgsg";die;

			$cond = " AND YEAR(add_date_time)='".$year."'";

		}

		else if(!empty($month['month']))

		{

		    $month = $month['month']; 

			$cond = " AND MONTH(add_date_time)='".$month."'";

		} */

		if(!empty($year) && !empty($month['month']))

		{

			$month = $month['month']; 

		    $cond = " AND YEAR(add_date_time)='".$year."' AND MONTH(add_date_time)='".$month."' ";

		}

		else if(!empty($date))

		{

		 $cond = " AND DATE(add_date_time)='".$date."' ";

		}

		else

		{

		    $currdate = date('Y-m-d');

		    $cond = " AND DATE(add_date_time)='".$currdate."' ";

		}

		

		$cond .=" and mob_user_staff_code='".$user_id."' "; 

	    $query = "SELECT add_date_time as newtime,DATE_FORMAT(add_date_time,'%Y-%m-%d') as add_date_time,lat as start_lat ,longitude as start_long FROM user_path WHERE 1 $cond ORDER BY newtime LIMIT 0,1";

		return $result = $db->fetchRow($query);

	}

	

	

	/**

	* getEndPointLatLongByUserId() method is used to get end point lat long by user id

	* @param Integer

	* @return Array 

	*/	

	public function getEndPointLatLongByUserId($year='NULL',$month=array(),$date='NULL',$user_id='NULL')

	{ 

	    $db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		/* if($year)

		{

			$cond = " AND YEAR(add_date_time)='".$year."'";

		}

		else if($year)

		{

		    $month = $month['month']; 

			$cond = " AND MONTH(add_date_time)='".$month."'";

		}*/

	    if(!empty($year) && !empty($month['month']))

		{

			$month = $month['month']; 

		    $cond = " AND YEAR(add_date_time)='".$year."' AND MONTH(add_date_time)='".$month."' ";

		}

		else if(!empty($date))

		{

		 $cond = " AND DATE(add_date_time)='".$date."' ";

		}

		else

		{ 

		    $currdate = date('Y-m-d');

		    $cond = " AND DATE(add_date_time)='".$currdate."' ";

		}

		

		$cond .=" and mob_user_staff_code='".$user_id."' ";

	    $query = "SELECT add_date_time as newtime, DATE_FORMAT(add_date_time,'%Y-%m-%d') as add_date_time,lat as end_lat, longitude as end_long FROM user_path WHERE 1 $cond ORDER BY newtime DESC LIMIT 0,1";     

		return $result = $db->fetchRow($query);

	}

	

	/**

	* getAllPointLatLongByUserId() method is used to get all lat and long by user id

	* @param Integer

	* @return Array 

	*/	

	public function getAllPointLatLongByUserId($year='NULL',$month=array(),$date='NULL',$userId='NULL')

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		/* if($year)

		{

			$cond = " AND YEAR(add_date_time)='".$year."'";

		}

		else if($year)

		{

		    $month = $month['month']; 

			$cond = " AND MONTH(add_date_time)='".$month."'";

		}*/

		if(!empty($year) && !empty($month['month']))

		{

			$month = $month['month']; 

		    $cond = " AND YEAR(add_date_time)='".$year."' AND MONTH(add_date_time)='".$month."' ";

		}

		else if(!empty($date))

		{

		 $cond = " AND DATE(add_date_time)='".$date."' ";

		}

		else

		{

		    $currdate = date('Y-m-d');

		    $cond = " AND DATE(add_date_time)='".$currdate."' ";

		}

		

		$cond .=" and mob_user_staff_code='".$userId."' ";

	    $query = "SELECT DATE_FORMAT(add_date_time,'%Y-%m-%d') as add_date_time, add_date_time as old_add_date_time,lat, longitude, Call_Log_No, battery_status FROM user_path WHERE 1 $cond ";  

		return $result = $db->fetchAll($query); 

	}

	



    /**

	* getAllNationalHeadUserList() method is used to get all national head users list

	* @param Integer

	* @return Array 

	*/	

	public function getAllNationalHeadUserList($get)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT sm.StaffCode,sm.StaffName,sm.EMail FROM national_head AS nh JOIN local_user_mapping AS sm ON(nh.RD_StaffCode=sm.StaffCode) GROUP BY RD_StaffCode"; 

		return $result = $db->fetchAll($query);

	}

	

		

    /**

	* getAllRegionalHeadUserList() method is used to get all regiona head users list

	* @param Integer

	* @return Array 

	*/	

	public function getAllRegionalHeadUserList($get)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT sm.StaffCode,sm.StaffName FROM local_user_mapping AS sm JOIN region_head AS rh ON(sm.StaffCode=rh.RHead) GROUP BY StaffCode"; 

		return $result = $db->fetchAll($query);

	}

    

	

	/**

	* getAllCircleList() method is used to get all circle list 

	* @param Null

	* @return Array 

	*/	

	public function getAllCircleList()

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$query = $db->select()

		                   ->from('circle_master',array('CircleCode','CircleName'));

		return $result = $db->fetchAll($query);

	}

	

	

	/**

	* getAllUserListByCircleCode() method is used to get all users list by circle code

	* @param String

	* @return Array 

	*/	

    public function getAllUserListByCircleCode($circle_code='NULL')

	{ 

		$circle_codeArr = explode(",",$circle_code);

		$db =  Zend_Db_Table::getDefaultAdapter();

	 	$query = $db->select()

						   ->from('local_user_mapping',array())

						   ->columns(array('StaffCode','StaffName'))

						   ->where('fieldUserCodes IN (?)',$circle_codeArr)

						   ->where('StaffStatus =?', 'AC')

						   ->where('role =?', 'field_user')

						   ->where('is_deleted =?', 0)

						   ->order('StaffName');  

		return $result = $db->fetchAll($query);

	}

	

	// saurabh

	public function setMovingAndNotMoving($alldata = array())

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$movstatus = '';

		$staffCode = $alldata['StaffCode'];

		if(count($alldata))

		{

			if($alldata['user_curr_job_status'] == 'at_site')

			{

				$movstatus = 'at_site';

			}

			else

			{

				$date = date("Y-m-d H:i:s");

				$time =  time() - 1800; // substract 30 minutes

				$predate = date("Y-m-d H:i:s",$time);

			 	$query = "select sum(travelled_distance) as lasttravelled from user_path where 1 and mob_user_staff_code='".$staffCode."' and add_date_time between '".$predate."' and '".$date."' ";

	

				$result = $db->fetchRow($query);

				if(!$result['lasttravelled'])

				{

					$movstatus = 'notmoving';

				}

				else if($result['lasttravelled'] >= 300)

				{

					$movstatus = 'moving';

				}

				else

				{

					$movstatus = 'notmoving';

				}

			}

			return $movstatus;

			//$db->query("update local_user_mapping set move_status='".$movstatus."' where StaffCode=?",array($staffCode));

		}

	}

	

	public function getUserListByRole($rolename,$StaffCode)

	{

	    $db =  Zend_Db_Table::getDefaultAdapter();

		$circleCodesArr = $this->getCircleCodeByRoleName($rolename,$StaffCode);

		$cond =  "";

		if($circleCodesArr)

		{

			$i = 1;

			if($rolename=="national_head" or $rolename=="super_admin")

			{

				$cond =  "";

			}

			else if($rolename=="zone_head")

			{

				foreach($circleCodesArr as $val)

				{

					if($i == 1)

					{

						$cond .= " AND (concat(',',zoneCircleCodes,',') like '%,$val,%'";

					}

					else

					{

						$cond .= " OR concat(',',zoneCircleCodes,',') like '%,$val,%'";

					}

					$i++;

				}

				$cond .= ")";

			}

}

		$db->query("SET NAMES 'utf8'");

		//echo "<pre>"; print_r($authStorage->read()); 

		  $query = "select StaffName, md5(concat(StaffCode,'@@@@@',role)) as StaffCode,StaffCode AS user_staff_code from  local_user_mapping where role='$rolename' $cond";



		return $result = $db->fetchAll($query);	

	}

 

	

	public function getCircleCodeByRoleName()

	{

		$auth = Zend_Auth::getInstance();

		$authStorage = $auth->getStorage();

		$rolename = $authStorage->read()->role;

		if($rolename=="national_head" or $rolename=="super_admin")

		{

		    return false;

		}

		else if($rolename=="zone_head")

		{

		    $circleCodeStr = $authStorage->read()->zoneCircleCodes; 

		}

		else

		{
			return false;

		}

        

		$circleCodeArr =  explode(",", $circleCodeStr);

		//$circleCodeStr = "'".implode("','", $circleCodeArr)."'";

		

		return $circleCodeArr;

	}

	

	/**

	* getAllUserDetailsByEmail() method is used to get all users details

	* @param Array

	* @return Array 

	*/

	public function getAllUserDetailsByEmail($email)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$roleArr = array();

		$roleArr = array("national_head","zone_head","regional_head","circle_head","service_manager","cluster_incharge");

		$query = $db->select()

						   ->from('local_user_mapping',array())

						   ->columns(array('StaffCode','StaffName','EMail','Password','role'))

						   ->where('EMail =?',$email)

						   ->where('role IN (?)',$roleArr)

						   ->where('StaffStatus =?', 'AC')

						   ->where('is_deleted =?', 0);   

		$result = $db->fetchRow($query);

		if($result){

			return $result;

		}

		return false;

	}



	public function getAllUserDetailsByPhone($phone)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$roleArr = array();

		$roleArr = array("national_head","zone_head","regional_head","circle_head","service_manager","cluster_incharge");

		$query = $db->select()

						   ->from('local_user_mapping',array())

						   ->columns(array('StaffCode','StaffName','EMail','Password','role'))

						   ->where('MobileNo =?',$phone)

						   ->where('role IN (?)',$roleArr)

						   ->where('StaffStatus =?', 'AC')

						   ->where('is_deleted =?', 0);   

		$result = $db->fetchRow($query);

		if($result){

			return $result;

		}

		return false;

	}

	




	

	

	/**

	* getUserStaffDetailsBySearchStaffCode() method is used to get user 

	* @param Integer

	* @return Array 

	*/	

	public function getUserStaffDetailsBySearchStaffCode($keywords)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		if($keywords)

		{

		$con = " AND ( StaffName LIKE '%$keywords%' OR StaffCode LIKE '%$keywords%' OR MobileNo LIKE '%$keywords%' OR device_id LIKE '%$keywords%')";

		}

	   /* echo $query = $db->select()

						   ->from('local_user_mapping',array('*'))

						   //->columns(array('StaffCode','StaffName','EMail','MobileNo','CMSStaffStockCode','role'))

						   ->where('StaffStatus = ?', 'AC')

						   ->where('role = ?', 'field_user')

						   ->where('is_deleted = ?', '0')

						   ->where('StaffName LIKE ?', '%'.$keywords.'%')

						   ->orWhere('StaffCode LIKE ?','%'.$keywords.'%')

						   ->orWhere('MobileNo LIKE ?', '%'.$keywords.'%')

						   ->orWhere('device_id LIKE ?', '%'.$keywords.'%'); 

		*/

	    $query = "SELECT StaffName,StaffCode,EMail,MobileNo,CMSStaffStockCode,role, StaffStatus FROM local_user_mapping WHERE  role='field_user' AND is_deleted='0' $con ";  

		return $result = $db->fetchRow($query);

	}

	

	

	/**

	* updateDeviceDataByStaffCode() method is used to update user data

	* @param Array

	* @return Array 

	*/

	public function updateDeviceDataByStaffCode($device_type,$device_id,$StaffCode){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update local_user_mapping set device_type =?,device_id =? where StaffCode=?",array($device_type,$device_id,$StaffCode));		

	}

	

	/**

	* deleteDeviceDataByStaffCode() method is used to update user data

	* @param Array

	* @return Array 

	*/

	public function deleteDeviceDataByStaffCode($StaffCode){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("update local_user_mapping set device_type ='',device_token ='',device_id ='' where StaffCode=?",array($StaffCode));		

	}

	

	

	/**

	* getSettingData() method is used to get setting details

	* @param Array

	* @return Array 

	*/

	public function getSettingData($email)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$query = $db->select()

						   ->from('setting',array('*'))

						   ->where('id =?',1);   

		$result = $db->fetchRow($query);

		if($result){

			return $result;

		}

		return false;

	}

	

	

	/**

	* getSettingDataCountById() method is used to get users detail by user id

	* @param Integer

	* @return Array 

	*/	

	public function getSettingDataCountById($id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT count(*) AS total FROM setting WHERE id=? ";  

		return $result = $db->fetchRow($query,array($id));

	}

	

	

	/**

	* getEndPointLatLongByUserId() method is used to get job lat long by user id

	* @param Integer

	* @return Array 

	*/	



	public function getAllJobLocationWithUser($year='NULL',$month=array(),$date='NULL',$user_id='NULL')

	{ 



	    $db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";



	    if(!empty($year) && !empty($month['month']))

		{

			$month = $month['month']; 

		    $cond = " AND YEAR(dwts.track_date)='".$year."' AND MONTH(dwts.track_date)='".$month."' ";

		}

		else if(!empty($date))

		{

		 $cond = " AND DATE(dwts.track_date)='".$date."' ";

		}

		else

		{ 

		    $currdate = date('Y-m-d');

		    $cond = " AND DATE(dwts.track_date)='".$currdate."' ";

		}

		

		$cond .=" and dwts.mob_user_staff_code='".$user_id."' ";

		$cond .=" and dwts.job_latitude !='' and  dwts.job_longitude !='' ";

	     $query = "SELECT DATE_FORMAT(dwts.track_date,'%Y-%m-%d') as track_datenew,dwts.job_latitude,dwts.job_longitude,cl.Call_Log_No,cl.Site_ID,lum.StaffCode,lum.StaffName, dwts.job_process_status FROM day_wise_ticket_status AS dwts INNER JOIN call_log AS cl ON (dwts.Call_Log_No=cl.Call_Log_No) INNER JOIN local_user_mapping AS lum ON(dwts.mob_user_staff_code=lum.StaffCode) WHERE 1 $cond ORDER BY dwts.track_date DESC";   

	

		return $result = $db->fetchAll($query);

	}

	

	

	/**

	* getAllCircleListByCircleCode() method is used to get all circle list by circle code

	* @param String

	* @return Array 

	* @Added By Praveen Kumar date(22-10-2013)

	*/	

    public function getAllCircleListByCircleCode($circleCodeArr=array())

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	 	$query = $db->select()

						   ->from('circle_master',array())

						   ->columns(array('CircleCode','CircleName'))

						   ->where('CircleCode IN (?)',$circleCodeArr)

						   ->order('CircleName');  

		return $result = $db->fetchAll($query);

	}

	

	

		

	/**

	* getCurrentBatteryStatusByStaffCode() method is used to get battery status by staff code

	* @param String

	* @return Array 

	*/	

    public function getCurrentBatteryStatusByStaffCode($StaffCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	 	$query = $db->select()

						   ->from('notification',array())

						   ->columns(array('message','mob_user_staff_code','id','statustype'))

						   ->where('mob_user_staff_code =?',$StaffCode)

						   ->where('type =?','User')

						   //->where('message LIKE ?',"%$battery_status%")

						   ->order('id DESC')

						   ->limit(1);  

		return $result = $db->fetchRow($query);

	}

	

	

	/**

	* getAllSparePartsListByEnggCode() method is used to get all spare parts list by eng code

	* @param String

	* @return Array 

	*/	

	

	public function updateFsrSpare($EnggCode)

	{

		

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("SET NAMES utf8");

		$db->query("SET CHARACTER SET utf8");

		

		$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234');

		//$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

		

		$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);

		//$mysqldb = mysql_select_db('acme',$con_mysql);

		  

		mssql_query("SET ANSI_NULLS ON");

		mssql_query("SET ANSI_WARNINGS ON");

		

		$delQry = "delete from spare_parts where EnggCode='".$EnggCode."'";

		$db->query($delQry);

		

		//echo "select  TOP 50 * from View_New_Engr_Stock where EnggCode='".$EnggCode."'";die;

		$query = mssql_query("select  TOP 50 * from View_New_Engr_Stock where EnggCode='".$EnggCode."'");

		//echo '<pre>';

		while($fetch = mssql_fetch_assoc($query))

		{

			  $queryspare = "insert into  spare_parts set 

				`Comp_Code`='".trim($fetch['Comp_Code'])."',

			  `Comp_Descr`='".trim($fetch['Comp_Descr'])."',

			  `LOT`='".trim($fetch['LOT'])."',

			  `QTY`='".trim($fetch['QTY'])."',

			  `UOM`='".trim($fetch['UOM'])."',

			  `STATUS`='".trim($fetch['STATUS'])."',

			  `EnggCode`='".trim($fetch['EnggCode'])."',

			  `ItemStatus`='".trim($fetch['ItemStatus'])."',

			  `WH`='".trim($fetch['WH'])."',

			  `SpareCategory`='".trim($fetch['SpareCategory'])."'"; 

			

			$db->query($queryspare);

		}

	

	}

    public function getAllSparePartsListByEnggCode($EnggCode)

	{ 

	

		//$this->updateFsrSpare($EnggCode);

	

		$db =  Zend_Db_Table::getDefaultAdapter();

	 	$query = $db->select()

						   ->from('spare_parts',array())

						   ->columns(array('Comp_Code','Comp_Descr','QTY','LOT','UOM','WH'))

						   ->where('EnggCode =?',$EnggCode)

						   ->where('STATUS =?','ACCEPTED')

						   ->order('Comp_Code');

		return $result = $db->fetchAll($query);

	}

	

	

	/**

	* getAllUserData() method is used to get all user details

	* @param Integer

	* @return Array 

	*/	

	public function getAllUserData()

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

	    $query = "SELECT StaffName,StaffCode,CMSStaffStockCode,device_type,device_token FROM local_user_mapping WHERE StaffStatus='AC' and is_deleted='0' and device_token!='' ";

		return $result = $db->fetchAll($query);

	}

	

	

	/**

	* getTotalSiteVisitCount() method is used to get all user details

	* @param Integer

	* @return Array 

	*/	

	public function getTotalSiteVisitCount($CMSStaffStockCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");

		$query = "select count(Call_Log_No) as totalvisit from user_site_track where status='at_site' and DATE(track_date)='".$date."' and mob_user_cmsstaff_code='".$CMSStaffStockCode."' ";

		$result = $db->fetchRow($query);

		return $result['totalvisit'];

	}

	

	

	/**

	* getOnlineUser() method is used to application online or offline

	* @param Integer

	* @return Array 

	*/	

	public function getOnlineUser($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d H:i:s");

		

		$query = "select count(*) as totalonline  from local_user_mapping where TIMESTAMPDIFF(MINUTE,last_location_service_hit_time,'".$date."') < 20 and StaffCode='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		return $result['totalonline'];

	}

    

	

	/**

	* getFsrDetailsByStaffCode() method is used to get all spare parts list by eng code

	* @param String

	* @return Array 

	*/	

    public function getFsrDetailsByStaffCode($UserStaffCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");

	 	$query = $db->select()

						   ->from('fsr_detail',array())

						   ->columns(array('mob_user_staff_code','fsr_where_closed'))

						   ->where('mob_user_staff_code =?',$UserStaffCode)

						   ->where('DATE(fsr_fill_date) =?',$date);

		return $result = $db->fetchRow($query);

	}

	

	

	/**

	* getTtClosedByCMSStaffStockCode() method is used to get tt staus

	* @param String

	* @return Array 

	*/	

    public function getTtClosedByCMSStaffStockCode($CMSStaffStockCode)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");

	 	$query = $db->select()

						   ->from('call_log',array())

						   ->columns(array('curr_Alloted_Eng_Code','tt_closed_status'))

						   ->where('curr_Alloted_Eng_Code =?',$CMSStaffStockCode)

						   ->where('DATE(tt_closed_date) =?',$date);

		return $result = $db->fetchRow($query);

	}

	

	

	public function getFsrFillDetailsByStaffCode($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");

		$query = "select count(*) as totalonsite from fsr_detail AS fd INNER JOIN local_user_mapping AS lum ON (fd.mob_user_staff_code=lum.StaffCode) where fd.fsr_where_closed='onsite' and DATE(fd.fsr_fill_date)='".$date."' and fd.mob_user_staff_code = '".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$fsrcountArr['totalonsite'] = $result['totalonsite']?$result['totalonsite']:0;

		unset($result);

		$query = "select count(*) as totaloffsite from fsr_detail AS fd INNER JOIN local_user_mapping AS lum ON (fd.mob_user_staff_code=lum.StaffCode) where fd.fsr_where_closed='offsite' and DATE(fd.fsr_fill_date)='".$date."' and fd.mob_user_staff_code = '".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$fsrcountArr['totaloffsite'] = $result['totaloffsite']?$result['totaloffsite']:0;

		return $fsrcountArr;

	}

	

	

	public function getTtClosedCountArrByStaffCode($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");

		$query = "select count(*) as totalonsite from fsr_detail AS fd INNER JOIN call_log AS cl ON (fd.Call_Log_No=cl.Call_Log_No) where fd.fsr_where_closed='onsite' and DATE(fd.fsr_fill_date)='".$date."' and fd.job_process_status = 'Complete' and fd.mob_user_staff_code ='".$UserStaffCode."' "; 

		$result = $db->fetchRow($query);

		$ttcountArr['totalonsite'] = $result['totalonsite']?$result['totalonsite']:0;

		unset($result);

		

		$query = "select count(*) as totaloffsite from fsr_detail AS fd INNER JOIN call_log AS cl ON (fd.Call_Log_No=cl.Call_Log_No) where fd.fsr_where_closed='offsite' and DATE(fd.fsr_fill_date)='".$date."' and fd.job_process_status = 'Complete' and fd.mob_user_staff_code ='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$ttcountArr['totaloffsite'] = $result['totaloffsite']?$result['totaloffsite']:0;

        //echo "<pre>";print_r($ttcountArr);die;

		return $ttcountArr;

	}

	

	

	public function getJobStatusArrByStaffCode($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");



	    $query = "select count(*) as total_processing from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Open' and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$UserStaffCode."' ";  

		//die;

		$result = $db->fetchRow($query);

		$jobstatusArr['total_processing'] = $result['total_processing']?$result['total_processing']:0;

		unset($result);

		

		$query = "select count(*) as total_pending from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where (cl.job_process_status!='Closed' and cl.job_process_status!='Open') and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$UserStaffCode."' "; 

		$result = $db->fetchRow($query);

		$jobstatusArr['total_pending'] = $result['total_pending']?$result['total_pending']:0;

		unset($result);

		

		$query = "select count(*) as total_complete from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Closed' and schedule_status!=0

		and track_date = '".$date."' and lu.StaffCode ='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$jobstatusArr['total_complete'] = $result['total_complete']?$result['total_complete']:0;

		

		return $jobstatusArr;

	}

	

	

	public function getPresentUserCount($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$query = "select count(*) as totalattend from local_user_mapping where DATE(attendance_time)=? and StaffCode ='".$UserStaffCode."' ";

		$date = date("Y-m-d");

		$result = $db->fetchRow($query,array($date));

		$presentuser = $result['totalattend']?$result['totalattend']:0;

		return $presentuser;

	}

	

	

	public function getJobWithUserStatusByStaffCode($UserStaffCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date("Y-m-d");



		$query = "select count(*) as total_toward_site from local_user_mapping where user_curr_job_status='toward_site' and DATE(user_curr_job_status_date)='".$date."' and StaffCode='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$jobstatusArr['total_toward_site'] = $result['total_toward_site']?$result['total_toward_site']:0;

		unset($result);

		

		$query = "select count(*) as total_at_site from local_user_mapping where user_curr_job_status='at_site' and DATE(user_curr_job_status_date)='".$date."' and StaffCode='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$jobstatusArr['total_at_site'] = $result['total_at_site']?$result['total_at_site']:0;

		unset($result);

		

		$query = "select count(*) as total_left_site from local_user_mapping where user_curr_job_status='left_site' and DATE(user_curr_job_status_date)='".$date."' and StaffCode='".$UserStaffCode."' ";

		$result = $db->fetchRow($query);

		$jobstatusArr['total_left_site'] = $result['total_left_site']?$result['total_left_site']:0;

		return $jobstatusArr;

	}

	

	

	/**

	* getAllUserLogDetailsByUserId() method is used to get all user log details

	* @param String

	* @return Array 

	*/	

    public function getAllUserLogDetailsByUserId($userId) 

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();
                
                $date = date("Y-m-d");

	 	$query = $db->select()

						   ->from('notification',array())

						   ->columns(array('mob_user_staff_code','message','Call_Log_No','type','noti_date'))

						   ->where('mob_user_staff_code =?',$userId)

						   ->where('DATE(noti_date) =?',$date)->order('id DESC');

		return $result = $db->fetchAll($query);

	}

	

	

	

	/**

	* getAllUserListBySupervisorIdWithFilter() method is used to get all users list by supervisor id

	* @param Array

	* @return Array 

	*/	

	public function getAllUserSearchListByStaffCode($get,$staffCode,$title,$sortby)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		if($title=="name")

		{

		   $sort_title = 'StaffName';

		}

		else if($title=="staffcode")

		{

		   $sort_title = 'StaffCode';

		}

		else if($title=="email")

		{

		   $sort_title = 'EMail';

		}

		else if($title=="LoginID")

		{

		   $sort_title = 'LoginID';

		}

	    if($sort_title !="" && $sortby !="")

		{

		   $order = $sort_title.' '.$sortby;

		}

		else

		{

		    $order = 'StaffName';

		}

		

		$cond = "";

		/* $clusterQuery = $db->select()

									  ->from('local_user_mapping',array())

									  ->columns(array('StaffCode','clusterInchargeCircleCodes'))

									  ->where('md5(concat(StaffCode,"@@@@@",role)) =?',$staffCode)

									  ->where('role =?','cluster_incharge')

									  ->where('StaffStatus =?', 'AC')

									  ->where('is_deleted =?', 0);   

								

		$clusterInchargeCircleCodesArr = $db->fetchRow($clusterQuery);

        $clusterInchargeCircleCodesArr = explode(',',$clusterInchargeCircleCodesArr['clusterInchargeCircleCodes']);

		$i = 1;

		foreach($clusterInchargeCircleCodesArr as $val)

		{

			if($i == 1)

			{

				$cond .= " AND (concat(',',fieldUserCodes,',') like '%,$val,%'";

			}

			else

			{

				$cond .= " OR concat(',',fieldUserCodes,',') like '%,$val,%'";

			}

			$i++;

		}

		$cond .= ")";

		*/

		$userCondi = $this->userCondi;

		if($get['user_search'])

		{

			$user_search = $get['user_search'];

			$cond .= " AND (StaffCode like '%$user_search%' OR StaffName like '%$user_search%' OR EMail like '%$user_search%' OR MobileNo like '%$user_search%')"; 

		}
	   echo  $query = "SELECT `StaffCode`,`StaffName`,`EMail`,`MobileNo`,`StaffStatus`,`role`, CMSStaffStockCode, LoginID FROM local_user_mapping WHERE 1 $cond $userCondi ORDER BY $order";   
	
		return $result = $db->fetchAll($query);

	}

	

	public function removeWipeOutUser($staffcode,$deactive='')

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$query = "select * from local_user_mapping where md5(concat(StaffCode,'@@@@@',role))='$staffcode'";

		$fetch = $db->fetchRow($query,array($staffcode));

		$deviceToken = $fetch['device_token'];

		$deviceType = $fetch['device_type'];

		$staffcodenew = $fetch['StaffCode'];

		if($fetch['id'])

		{

			if($deactive == 'deactive' ||  $deactive == 'activate')

			{

				if($deactive == 'deactive')

				{

					$db->query("update local_user_mapping set StaffStatus='DAC', current_latitude='', current_longitude='', device_type='', device_token='', device_id='' where StaffCode=?", array($staffcodenew));

				}

				else if($deactive == 'activate')

				{

					$db->query("update local_user_mapping set StaffStatus='AC' where StaffCode=?", array($staffcodenew));

					}

			}

			else

			{

				$db->query("update local_user_mapping set device_id='99999999999abcd99999999999', device_token='' where StaffCode=?", array($staffcodenew));			

			}

			if($deviceToken)

			{

				if($deactive == 'deactive')

				{

					$payload['aps'] = array('alert' =>'deactivated', 'push_type' =>'deactivate');

				}

				else

				{

					$payload['aps'] = array('alert' =>'remote wipeout', 'push_type' =>'remote_wipeout');

				}
				$pushobj = new Application_Model_Sendpush();

				$pushobj->sendPush($deviceToken,$deviceType,$payload);

			}	

		}

	}

	

	public function mappingStructureUser()

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$select = $db->select()->from('local_user_mapping');

		return $result = $db->fetchAll($select);

	}
	
	
///////////////////////////////////// count jobstatus ahsan ///////////////////////////// ////////////////////////
/*public function getEngchangejobTime($LoginID)
	{ 
	//json_encode($LoginID);exit;

		$db =  Zend_Db_Table::getDefaultAdapter();
		$date=date('y-m-d');

	   $query = "select * from notification where mob_user_staff_code=$LoginID AND DATE(noti_date)=$date order by id desc limit 1;";  

		return $result = $db->fetchRow($query);
	}*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	
		function getCountOfVisitedSite($staffCode,$date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$type = 'Job';
		$statustype = 'at_site';
		//$date = date("Y-m-d");
		$query = "SELECT * FROM  `notification` WHERE mob_user_staff_code = '".$staffCode."' AND TYPE =  '".$type."' AND statustype =  '".$statustype."' AND DATE( noti_date ) = '".$date."'";
		$result = $db->fetchAll($query, array());
		if(count($result)>0)
		{
			$cnt = count($result)+1;
		}
		else
		{
			$cnt = 1;
		}
		
		return $cnt;
	}
	
public function getCIListByCircleCode($circleCode)

	{

	    $db =  Zend_Db_Table::getDefaultAdapter();
		$rolename = 'cluster_incharge';
		$circleCodesArr = explode(',',$circleCode);

		$cond =  "";

		if($circleCodesArr)

		{

			$i = 1;

			if($rolename=="national_head" or $rolename=="super_admin")

			{

				$cond =  "";

			}

			else if($rolename=="zone_head")

			{

				foreach($circleCodesArr as $val)

				{

					if($i == 1)

					{

						$cond .= " AND (concat(',',zoneCircleCodes,',') like '%,$val,%'";

					}

					else

					{

						$cond .= " OR concat(',',zoneCircleCodes,',') like '%,$val,%'";

					}

					$i++;

				}

				$cond .= ")";

			}

			else if($rolename=="regional_head")

			{

				foreach($circleCodesArr as $val)

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

			}

			else if($rolename=="circle_head")

			{

				foreach($circleCodesArr as $val)

				{

					if($i == 1)

					{

						$cond .= " AND (concat(',',circleCodes,',') like '%,$val,%'";

					}

					else

					{

						$cond .= " OR concat(',',circleCodes,',') like '%,$val,%'";

					}

					$i++;

				}

				$cond .= ")";

			}

			else if($rolename=="service_manager")

			{

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

			}

			else if($rolename=="cluster_incharge")

			{

				foreach($circleCodesArr as $val)

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

			}

		}

		$db->query("SET NAMES 'utf8'");

		//echo "<pre>"; print_r($authStorage->read()); 

		  $query = "select StaffName, md5(concat(StaffCode,'@@@@@',role)) as StaffCode,StaffCode AS user_staff_code from  local_user_mapping where role='$rolename' $cond";



		return $result = $db->fetchAll($query);	

	}
        
        
        public function getEnggNameByClusterID($cluster_id)
        {
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select StaffCode, StaffName , fieldUserParent from local_user_mapping where fieldUserParent='".$cluster_id."' order by StaffName asc";
            return $result = $db->fetchAll($query);
        }
        
        public function getCustomerByCircle($circle_code)
        {
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = " select distinct(Customer_Name), Circle_Code from call_log_completed where Circle_Code='".$circle_code."' ";
            return $result = $db->fetchAll($query);
        }
        
        public function getSitesByCustomer($customer){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = " select distinct(Site_ID), Customer_Name from call_log_completed where Customer_Name='".$customer."'";
            return $result = $db->fetchAll($query);
        }
        
        
        public function getContactInformationDetail($loginId){
            $db =  Zend_Db_Table::getDefaultAdapter();

                $query = " select * from contact_info where md5(user_id)='".$loginId."' order by id desc";
  


           
          
            return $result = $db->fetchAll($query);
        }
        
        
          public function getPlaceInformationDetail($loginId){
            $db =  Zend_Db_Table::getDefaultAdapter();

            $query = " select id,user_id, place_description, consenred_person, designation, contact, address, date(offline_sink_datetime) from place_info where md5(user_id)='".$loginId."' order by id desc";
 
            return $result = $db->fetchAll($query);
        }
        
        
        public function getContactAllInformation($name, $mobile, $fuser){
            $db =  Zend_Db_Table::getDefaultAdapter();
            
            if($name){
                $cond .="and name like '%".$name."%'";
            }
            
            if($mobile){
                $cond .="and mobile like '%".$mobile."%'";
            }
            
            
            if($fuser){
                $cond .="and user_id = '".$fuser."'";
            }
               $query = " select * from contact_info where 1 and project !='' $cond  order by id desc";     
               return $result = $db->fetchAll($query);
        }
        
        public function getPlacedInformation($loginId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            if($loginId){
               $query = "select * from place_info where md5(user_id)='".$loginId."'";
            }
            else{
                $query = "select * from place_info  order by id desc"; 
            }
    
            return $result = $db->fetchAll($query);
        }
        
        
         public function getAllPlacedInformation($name, $mobile, $fuser){
            $db =  Zend_Db_Table::getDefaultAdapter();
            
            if($name){
                $cond .="and consenred_person like '%".$name."%'";
            }
            
            if($mobile){
                $cond .="and contact like '%".$mobile."%'";
            }
            
            
            if($fuser){
                $cond .="and user_id = '".$fuser."'";
            }
           
            $query = "select * from place_info where 1 and project !=' '  $cond  order by id desc"; 
            return $result = $db->fetchAll($query);
        }
        
        
        public function getLoginUserInformationByID($Id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from contact_info where md5(id)='".$Id."'";
    
     
            return $result = $db->fetchRow($query);
        }
        
        public function getLoginPlaceUserInformationByID($Id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from place_info where md5(id)='".$Id."'";
    
     
            return $result = $db->fetchRow($query);
        }
        
        
         public function getLoginUserInformation($loginId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from logi_field_users where md5(LoginID)='".$loginId."'";
            return $result = $db->fetchRow($query);
        }
        
        
         public function getAllUserInfo(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from logi_field_users where Role !='service_manager' and project !=''";
            return $result = $db->fetchAll($query);
        }
        
        
        
        public function getTotalCountCapturedInfoDetail(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) as info_count from contact_info where project !=' '";           
            return $result = $db->fetchRow($query);
        }
        
        
        public function getTotalCountCapturedPlaceDetail(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) as place_count from place_info where project != ' ' ";
     
            return $result = $db->fetchRow($query);
        }
        
        
        public function viewLocation($id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from contact_info where id  ='".$id."'";
            return $result = $db->fetchRow($query);
        }
        
         public function viewLocationPlace($id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from place_info where id  ='".$id."'";
            return $result = $db->fetchRow($query);
        }
        
        
        
        
        
        public function getContactAllInformationCount( $fuser){
            $db =  Zend_Db_Table::getDefaultAdapter();
            
          
            
            if($fuser){
                $cond .="and md5(user_id) = '".$fuser."'";
            }
               $query = " select count(id) as count_info from contact_info and project !='' where 1 $cond";     
               return $result = $db->fetchRow($query);
        }
        
        
        
        
        public function getAllPlacedInformationCount($fuser){
            $db =  Zend_Db_Table::getDefaultAdapter();
            
          
            
            if($fuser){
                $cond .="and md5(user_id) = '".$fuser."'";
            }
           
               $query = "select count(id) as count_place_info from place_info and !=' ' where 1  $cond"; 
             
         
           
    
            return $result = $db->fetchRow($query);
        }
        
        
        
        
          public function getUserNameByUserId($id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select StaffName from logi_field_users where LoginID  ='".$id."'";
            return $result = $db->fetchRow($query);
        }
        
        
        
        
        
        
        
        
        
        
       
        
        
        
   
}
