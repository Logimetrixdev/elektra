<?php
class Application_Model_Admin extends Zend_Db_Table_Abstract
{
	var $userCondi = "";
	/******* add by abhishek *************/
	function __construct($params) 
	{ 	  	
		$cond = " and (role='field_user' OR role='zone_head') and is_deleted='0' and access_status!='1' and StaffStatus='AC'";
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
	  	$staffUserSes = $authStorage->read()->StaffCodeMd5;
		$role = $authStorage->read()->role;
		$staffUser = $params['staffUser']?$params['staffUser']:$staffUserSes;
		if($params['staffUser'])
		{
			$role = $params['role']?$params['role']:$role;
		}
		
		if($staffUser)
		{
			
			if($role == 'cluster_incharge')
			{
				$cond .= " and md5(concat(fieldUserParent,'@@@@@','cluster_incharge'))= '".$staffUser."'";
			}
			else
			{
				$circles = $this->getUserCircleByRole($role,$staffUser);
				if($circles)
				{
					$cond .= " and fieldUserCodes in ($circles)";
				}
			}			
			
			
		}
		
		
		
		//echo $cond;die;
		return $this->userCondi = $cond;
   }
   
   /// get user circle by role and staffcode
	public function getUserCircleByRole($role,$staffUser)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		if($role == 'national_head' || $role == '')
		{
			return false;
		}
		else if($role == 'zone_head')
		{
			$col = 'zoneCircleCodes';
		}
		else if($role == 'regional_head')
		{
			$col = 'regionalCircleCodes';
		}
		else if($role == 'circle_head')
		{
			$col = 'circleCodes';
		}
		else if($role == 'service_manager')
		{
			$col = 'serviceManagerCircleCodes';
		}
		else if($role == 'cluster_incharge')
		{
			$col = 'clusterInchargeCircleCodes';
		}
		else
		{
			return false;
		}
		 $query = "select $col as circles  from local_user_mapping where md5(concat(StaffCode,'@@@@@',role))='$staffUser'";
		
		$result = $db->fetchRow($query);
		if(count($result))
		{
			$circleArr = explode(",",$result['circles']);
			$circles = implode("','",$circleArr);
			$circles = "'".$circles."'";
			return $circles;
		}
		else
		{
			return '';
		}
		
	}
	/******* add by saurabh *************/
	
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
	
	
	public function updateChangePasswordByAdminId($data, $id){  
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update admin set password='".md5($data)."',password2='".$data."' where id='".$id."' ");
	}
	

	
	public function getAdminUserListByEmail($email)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select * from admin where email='".$email."'";
		$result = $db->fetchRow($query);
		if($result){
			return $result;
		}
		return false;
	}
	
	
	public function getAdminUserListById($id)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select * from admin where id='".$id."'";
		$result = $db->fetchRow($query);
		if($result){
			return $result;
		}
		return false;
	}

	public function updateAdminLastLoginDate($data, $id){  
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update admin set lastlogin='".$data."' where id='".$id."' ");
	}
	
	public function getUserCurrentStatus($get)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select user_id, access_status from users where md5(concat('idsalt',user_id))=?";
		return $result = $db->fetchRow($query,array($get['rowid']));
		
	}
	
	public function updateUserCurrentStatus($get,$currstatus)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$data = array($currstatus,$get['rowid']);
		$db->query("update users set access_status=? where md5(concat('idsalt',user_id))=?", $data);
		
	}

	
	/****************** send PushBy user_id ************/
	public function sendPushByUserId($get)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select * from users where md5(concat('idsalt',user_id))=?";
		$eArr = $db->fetchRow($query,array($get['rowid']));
		$deviceToken = $eArr['device_token'];
		$deviceType = $eArr['device_type'];	
		if($deviceToken)
		{
			$pushobj = new Application_Model_Sendpush();
			$payload['aps'] = array('alert' =>"This account deactivated by admin", 'push_type' =>8, 'sound' => "default", 'badge'=>1);	
			$pushobj->sendPush($deviceToken,$deviceType,$payload);
		}
	}	
	
	/*********** clear Device Token When Inactive ***********/
	public function clearDeviceTokenWhenInactive($get)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update users set  device_type='', device_token='' where md5(concat('idsalt',user_id))=?",array($get['rowid']));
	}

     /************* code By saurabh **********/
	public function getPresentUserCount()
	{
		$userCondi = $this->userCondi;
		$db =  Zend_Db_Table::getDefaultAdapter();
		 $query = "select count(*) as totalattend from local_user_mapping where DATE(attendance_time)=? $userCondi";
              
		$date = date("Y-m-d");
              
		$result = $db->fetchRow($query,array($date));
		$presentuser = $result['totalattend']?$result['totalattend']:0;
		return $presentuser;
	}
	
	public function getAllUserCount($filtorbyheadlisting,$circles='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = " and fieldUserCodes!=''";
		if($filtorbyheadlisting and $circles)
		{
			$cond .= " and fieldUserCodes in ($circles)";
		}
	 	$query = "select count(*) as total from local_user_mapping where 1 $userCondi $cond";
                
		$result = $db->fetchRow($query);
	    $totaluser = $result['total']?$result['total']:0;
		return $totaluser;
	}
	
	public function getAllUserCountCluster($parentstaff)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = " and fieldUserCodes!='' and fieldUserParent='".$parentstaff."'";

	 	$query = "select count(*) as total from local_user_mapping where 1 $userCondi $cond";
		$result = $db->fetchRow($query);
	    $totaluser = $result['total']?$result['total']:0;
		return $totaluser;
	}	
	
	
	public function getOpenJobStatusCount($filtorbyheadlisting,$circles='', $StaffCode='')
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = '';
		if($filtorbyheadlisting == 'getopenjobcount')
		{
			$cond = " and lum.StaffCode='$StaffCode'";
		}
		else if($filtorbyheadlisting and $circles)
		{
			$cond = " and lum.fieldUserCodes IN ($circles)";
		}
	 	$query = "select count(cl.Call_Log_No) as total_open_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and cl.job_process_status='processing' $userCondi $cond"; 
		$result = $db->fetchRow($query);
	    $total_open_job = $result['total_open_job']?$result['total_open_job']:0;
		return $total_open_job;
	}

	public function getOpenJobStatusCountCluster($parentStaff)
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;

		$cond = " and lum.fieldUserParent='".$parentStaff."'";
	 	$query = "select count(cl.Call_Log_No) as total_open_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and cl.job_process_status='processing' $userCondi $cond"; 
		$result = $db->fetchRow($query);
	    $total_open_job = $result['total_open_job']?$result['total_open_job']:0;
		return $total_open_job;
	}	
	
	public function getPendingJobStatusCount($filtorbyheadlisting,$circles='', $StaffCode='')
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = '';
		
		if($filtorbyheadlisting == 'getpendingjobcount')
		{
			$cond = " and lum.StaffCode='$StaffCode'";
		}
		else 
		if($filtorbyheadlisting and $circles)
		{
			$cond = " and lum.fieldUserCodes IN ($circles)";
		}
	   $query = "select count(cl.Call_Log_No) as total_pending_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and (cl.job_process_status='' OR cl.job_process_status='Close_with_pending') $userCondi $cond";
		$result = $db->fetchRow($query);
	    $total_pending_job = $result['total_pending_job']?$result['total_pending_job']:0;
		return $total_pending_job;
	}
	
	public function getPendingJobStatusCountCluster($parentStaff)
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = " and lum.fieldUserParent='$parentStaff'";

	   $query = "select count(cl.Call_Log_No) as total_pending_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and (cl.job_process_status='' OR cl.job_process_status='Close_with_pending') $userCondi $cond";
		$result = $db->fetchRow($query);
	    $total_pending_job = $result['total_pending_job']?$result['total_pending_job']:0;
		return $total_pending_job;
	}
	
	public function getCompleteJobStatusCount($filtorbyheadlisting,$circles='', $StaffCode='')
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = '';
		if($filtorbyheadlisting == 'getcompletejobcount')
		{
			$cond = " and lum.StaffCode='$StaffCode'";
		}
		else 
		if($filtorbyheadlisting and $circles)
		{
			$cond = " and lum.fieldUserCodes IN ($circles)";
		}
	    $query = "select count(cl.Call_Log_No) as total_complete_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and (cl.job_process_status='Complete') $userCondi $cond"; 
		$result = $db->fetchRow($query);
	    $total_complete_job = $result['total_complete_job']?$result['total_complete_job']:0;
		return $total_complete_job;
	}
	
	public function getCompleteJobStatusCountCluster($parentStaff)
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = " and lum.fieldUserParent='$parentStaff'";
	    $query = "select count(cl.Call_Log_No) as total_complete_job from local_user_mapping AS lum INNER JOIN call_log AS cl ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) where 1 and (cl.job_process_status='Complete') $userCondi $cond"; 
		$result = $db->fetchRow($query);
	    $total_complete_job = $result['total_complete_job']?$result['total_complete_job']:0;
		return $total_complete_job;
	}
	
	
	public function getJobWithUserStatus()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d");
		$userCondi = $this->userCondi;
		$query = "select count(*) as total_toward_site from local_user_mapping where user_curr_job_status='toward_site' and DATE(user_curr_job_status_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$jobstatusArr['total_toward_site'] = $result['total_toward_site']?$result['total_toward_site']:0;
		unset($result);
		$query = "select count(*) as total_at_site from local_user_mapping where user_curr_job_status='at_site' and DATE(user_curr_job_status_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$jobstatusArr['total_at_site'] = $result['total_at_site']?$result['total_at_site']:0;
		unset($result);
		$query = "select count(*) as total_left_site from local_user_mapping where user_curr_job_status='left_site' and DATE(user_curr_job_status_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$jobstatusArr['total_left_site'] = $result['total_left_site']?$result['total_left_site']:0;
		return $jobstatusArr;
	}
	
	public function getJobStatusArr($filter_date = '')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		if(!$filter_date)
		{
			$date = date("Y-m-d");
		}
		else
		{
		   $date = $filter_date; 
		}
		
/* 		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
	    $rolename = $authStorage->read()->role; 
		if($rolename == "cluster_incharge")
		{ 
			$userCondi = " and (role='field_user' OR role='cluster_incharge') and is_deleted='0' and access_status!='1' and StaffStatus='AC' and CMSStaffStockCode!='' "; 
		}
		else{
		   $userCondi = $this->userCondi;
		} */
		$userCondi = $this->userCondi;
	     $query = "select count(id) as total_processing from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) 
		where cl.job_process_status='Open' and schedule_status!=0 and track_date = '".$date."' $userCondi ";  
		
		
		//die;
		$result = $db->fetchRow($query);
		$jobstatusArr['total_processing'] = $result['total_processing']?$result['total_processing']:0;
		unset($result);
		
		
		$query = "select count(id) as total_pending from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where (cl.job_process_status!='Closed' and cl.job_process_status!='Open') and schedule_status!=0
		and track_date = '".$date."'
		$userCondi"; 
		
		$result = $db->fetchRow($query);
		$jobstatusArr['total_pending'] = $result['total_pending']?$result['total_pending']:0;
		unset($result);
		$query = "select count(id) as total_complete from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Closed' and schedule_status!=0 and track_date = '".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$jobstatusArr['total_complete'] = $result['total_complete']?$result['total_complete']:0;
		
		return $jobstatusArr;
	}
	
	
	public function getFsrClosedCountArr()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
		$query = "select count(*) as totalonsite from fsr_detail AS fd INNER JOIN local_user_mapping AS lum ON (fd.mob_user_staff_code=lum.StaffCode) where fd.fsr_where_closed='onsite' and DATE(fd.fsr_fill_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$fsrcountArr['totalonsite'] = $result['totalonsite']?$result['totalonsite']:0;
		unset($result);
		$query = "select count(*) as totaloffsite from fsr_detail AS fd INNER JOIN local_user_mapping AS lum ON (fd.mob_user_staff_code=lum.StaffCode) where fd.fsr_where_closed='offsite' and DATE(fd.fsr_fill_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		$fsrcountArr['totaloffsite'] = $result['totaloffsite']?$result['totaloffsite']:0;
		return $fsrcountArr;
	}
	
	
	public function getTtClosedCountArr()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
		$query = "select count(*) as totalonsite from call_log AS cl INNER JOIN local_user_mapping AS lum ON (cl.	curr_Alloted_Eng_Code=lum.CMSStaffStockCode) where cl.tt_closed_status='onsite' and DATE(cl.tt_closed_date)='".$date."' and cl.job_process_status = 'Complete' $userCondi"; 
		$result = $db->fetchRow($query);
		$ttcountArr['totalonsite'] = $result['totalonsite']?$result['totalonsite']:0;
		unset($result);
		$query = "select count(*) as totaloffsite from call_log AS cl INNER JOIN local_user_mapping AS lum ON (cl.	curr_Alloted_Eng_Code=lum.CMSStaffStockCode) where cl.tt_closed_status='offsite' and DATE(cl.tt_closed_date)='".$date."' and cl.job_process_status = 'Complete' $userCondi";
		$result = $db->fetchRow($query);
		$ttcountArr['totaloffsite'] = $result['totaloffsite']?$result['totaloffsite']:0;
		return $ttcountArr;
	}
	
	public function getTotalSiteVisitCount($date='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		if(!$date)
		{
			$date = date("Y-m-d");
		}
	  $query = "select count(ust.Call_Log_No) as totalvisit from user_site_track AS ust INNER JOIN local_user_mapping AS lum ON (ust.mob_user_staff_code=lum.StaffCode) where ust.status='at_site' and DATE(ust.track_date)='".$date."' $userCondi";  
		$result = $db->fetchRow($query);
		return $result['totalvisit'];
	}
	
	public function getLowBatteryCount()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d");
		$query = "select count(*) as totalbettarylow from local_user_mapping where battery_status='low' and DATE(battery_status_date)='".$date."' $userCondi";
		$result = $db->fetchRow($query);
		return $result['totalbettarylow'];	
	}
	
	public function getOnlineUser($filtorbyheadlisting,$circles='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d H:i:s");
		$userCondi = $this->userCondi;
		$cond = " and fieldUserCodes!=''";
		if($filtorbyheadlisting and $circles)
		{
			$cond .= " and fieldUserCodes in ($circles)";
		}
		$query = "select count(*) as totalonline  from local_user_mapping where TIMESTAMPDIFF(MINUTE,last_location_service_hit_time,'".$date."') < 20 $userCondi $cond";
		$result = $db->fetchRow($query);
		return $result['totalonline'];
	}
	
	public function getOnlineUserCluster($parentStaff)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d H:i:s");
		$userCondi = $this->userCondi;
		$cond = " and fieldUserCodes!='' and fieldUserParent='".$parentStaff."'";

		$query = "select count(*) as totalonline  from local_user_mapping where TIMESTAMPDIFF(MINUTE,last_location_service_hit_time,'".$date."') < 20 $userCondi $cond";
		$result = $db->fetchRow($query);
		return $result['totalonline'];
	}
      /* End code by saurabh */ 
	
	/* new add by saurabh */
	public function getAllUserCrrentLatitudeLongitude($user_search = '')
	{
		$date = date('Y-m-d');
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		$userCondi = $this->userCondi;
		
		$newcond = '';
		
		if($user_search)
		{
				$newcond = " and (lu.StaffCode like '%$user_search%' || lu.StaffName like '%$user_search%' || cl.Call_Log_No like '%$user_search%' || cl.IndusTTNumber like '%$user_search%')";
		}	
		  $query = "select lu.StaffCode, lu.StaffName,lu.current_latitude, lu.current_longitude, lu.last_location_service_hit_time, cl.Call_Log_No, cl.IndusTTNumber,
				if(DATE(user_curr_job_status_date) = '$date',user_curr_job_status,'N/A')
				AS curr_job_status
		  from local_user_mapping as lu
					LEFT JOIN call_log as cl on (lu.CMSStaffStockCode=cl.curr_Alloted_Eng_Code AND  job_process_status='processing')	
				where 1 and lu.current_latitude!='' and lu.current_longitude  $newcond   $userCondi";
				$result = $db->fetchAll($query);	
				foreach($result as $key=>$val)
				{
					$message = $db->fetchOne("select message from notification  where mob_user_staff_code=? order by id desc limit 1",array($val['StaffCode']));
					$msArr = explode('<br>',$message);
					 $laststat = end($msArr);
					 $lastArr = explode(':',$laststat);
					 $currstatususer = trim(end($lastArr));
					$result[$key]['currstatususer'] = $currstatususer;
					
				}
		return $result;		
	}
	
	public function getGpsInfoArr()
	{
		$date = date('Y-m-d');
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		   $query = "select count(*) as gps_status_count from local_user_mapping where 1 and fieldUserCodes!='' $userCondi and DATE(attendance_time)='$date' group by gps_status";
		return $result = $db->fetchAll($query);		
	}
	
	public function getGpsInfoArrOnOff()
	{
		$date = date('Y-m-d');
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		    $query = "select count(*) as gps_status_count from local_user_mapping where 1 and fieldUserCodes!='' $userCondi and DATE(attendance_time)='$date' and gps_status='on'";
		$fetchOn = $db->fetchRow($query);	
		
		$queryOff = "select count(*) as gps_status_count from local_user_mapping where 1 and fieldUserCodes!='' $userCondi and DATE(attendance_time)='$date' and ( gps_status='' or gps_status='off')";
		$fetchOff = $db->fetchRow($queryOff);
		
		$fetchOn['gps_status_count'] = $fetchOn['gps_status_count']?$fetchOn['gps_status_count']:0;
		$fetchOff['gps_status_count'] = $fetchOff['gps_status_count']?$fetchOff['gps_status_count']:0;
		$result[0] = $fetchOff;
		$result[1] = $fetchOn;
		
		
		return $result;	
	}
	
  
	public function getMovingAndNotMoving()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
		$query = "select count(*) as total_move from local_user_mapping where 1 and move_status='moving' and DATE(attendance_time)='".$date."'  and fieldUserCodes!=''   $userCondi ";
		$result = $db->fetchRow($query);
		$moveStatusArr['total_move'] = 	$result['total_move'];
		unset($result);
		$query = "select count(*) as total_notmove from local_user_mapping where 1 and move_status!='moving' and DATE(attendance_time)='".$date."'  and fieldUserCodes!=''   $userCondi ";
		$result = $db->fetchRow($query);
		$moveStatusArr['total_notmove'] = 	$result['total_notmove'];	
		return $moveStatusArr;	
	}
	
	
	public function getAllNotificationByRoleName($role_name,$staffUser='',$fromdate='',$todate='',$fromhistory='',$user_search='')
	{   
		$db =  Zend_Db_Table::getDefaultAdapter();
	/* 	$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode; 

		if($role_name =="zone_head" || $role_name =="regional_head" || $role_name =="circle_head" || $role_name =="service_manager" || $role_name =="cluster_incharge")
		{
		    $cond .= $this->userCondi;
		}
		else if($role_name =="super_admin" || $role_name =="national_head")
		{
		    //$cond .= " AND mob_user_staff_code IS NULL";
			$cond .= " ";
		}
		 */
		$limit = '';
		if($fromdate)
		{
			$cond = " AND (DATE(noti_date) between '$fromdate' and '$todate')";
		}
		else
		{
			if($fromhistory != 'fromhistory')
			{
				$cond = " AND DATE(noti_date)='".date('Y-m-d')."'";
				$limit = " limit 15";
			}
		}
		$newcond = '';
		if($user_search)
		{
				$newcond = " and (lum.StaffCode like '%$user_search%' || lum.StaffName like '%$user_search%')";
		}
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
	    $query = "select * from notification AS nt INNER JOIN local_user_mapping AS lum ON(nt.mob_user_staff_code=lum.StaffCode) where 1 AND nt.is_read='0' $cond $newcond $userCondi ORDER BY nt.noti_date DESC $limit"; 

		$result = $db->fetchAll($query);
		return $result;	
	}
	
	public function getAllUserByAttendance($attendence,$role)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode; 
		$date = date("Y-m-d");
		$userCondi = $this->userCondi;
		if($attendence == 'present')
		{ 
			 $query = "select distinct(a.mob_user_staff_code) as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from attendance as a left join local_user_mapping as lum on (a.mob_user_staff_code=lum.StaffCode) where a.status='1' and DATE(a.attend_date)=? and lum.fieldUserCodes!='' $userCondi";  
			
		}
		else
		{
			// 
			$array = array();
			$querynew = "select distinct mob_user_staff_code from attendance where status='1' and DATE(attend_date)=?";
			$newresult = $db->fetchAll($querynew, array($date));
			if(count($newresult))
			{
				foreach($newresult as $key=>$val)
				{
					$array[] = "'".$val['mob_user_staff_code']."'";
					}
				}
				$incond = '';
				if(count($array))
				{
					$str = implode(",",$array);
					$incond = " and StaffCode not in ( $str )";
					}
			
		    $query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude  from  local_user_mapping as lum where 1 and role in ('cluster_incharge','field_user') $incond  and lum.fieldUserCodes!='' $userCondi";  
			  
		}
		$result = $db->fetchAll($query, array($date));
		return $result;	
	}
	
	public function getMovingNotmovingUserList($movetype)
	{

		$db =  Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		//$StaffCode = $authStorage->read()->StaffCode; 
		$date = date("Y-m-d");
		$userCondi = $this->userCondi;
		if($movetype == 'moving')
		{ 
			$query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from local_user_mapping as lum where 1 and move_status='moving' and DATE(attendance_time)='".$date."'  and lum.fieldUserCodes!='' $userCondi ";
		}
		else
		{ 
			$query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from local_user_mapping as lum where 1 and (move_status!='moving' OR move_status='') and DATE(attendance_time)='".$date."'  and lum.fieldUserCodes!='' $userCondi ";
		}
		$result = $db->fetchAll($query, array($date));
		return $result;	
	}
	
	
	public function getNotmovingUserList($movetype)
	{

		$db =  Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		//$StaffCode = $authStorage->read()->StaffCode; 
		$date = date("Y-m-d");
		$userCondi = $this->userCondi;
		if($movetype == 'notmoving')
		{ 
			$query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from local_user_mapping as lum where 1 and (move_status!='moving' OR move_status='') and DATE(attendance_time)='".$date."' and fieldUserCodes!=''  $userCondi ";
		}
		
		$result = $db->fetchAll($query, array($date));
		return $result;	
	}
	
	
	public function getCurrworkingJobDetail($staffcode,$job_status='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d");
		$cond = "";
		if($job_status == 'at_site')
		{
			$cond = " and ust.status='at_site'";
		}
		 $query = "select cl.* from user_site_track as ust
					inner join call_log as cl on (cl.curr_Alloted_Eng_Code=ust.mob_user_cmsstaff_code)
		where ust.mob_user_staff_code=? and DATE(ust.track_date)=? 
		$cond
		order by ust.id desc limit 1";

		return $db->fetchRow($query,array($staffcode,$date));
	}
	
	public function getUserStatusWithJobListStatus($moveatsitetype)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode; 
		$date = date("Y-m-d");
		$userCondi = $this->userCondi;
		if($moveatsitetype == 'towardsite')
		{
			$query = "select StaffCode as staffcode, StaffName, EMail, MobileNo, current_latitude, current_longitude from local_user_mapping where user_curr_job_status='toward_site' and fieldUserCodes!='' and DATE(user_curr_job_status_date)=? $userCondi";
		}
		else if($moveatsitetype == 'atsite')
		{
			$query = "select StaffCode as staffcode, StaffName, EMail, MobileNo, current_latitude, current_longitude from local_user_mapping where user_curr_job_status='at_site' and fieldUserCodes!='' and DATE(user_curr_job_status_date)=? $userCondi";
		}
		else
		{
			$query = "select StaffCode as staffcode, StaffName, EMail, MobileNo, current_latitude, current_longitude from local_user_mapping where user_curr_job_status='left_site'
			and fieldUserCodes!=''	and DATE(user_curr_job_status_date)=? $userCondi";
		}
		$result = $db->fetchAll($query, array($date));
		return $result;		
		
	}
	
	public function getTicketListByStatus($filter_date = '',$statustype)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		
		if(!$filter_date)
		{
			$date = date("Y-m-d");
		}
		else
		{
		   $date = $filter_date; 
		}
		
/* 		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
	    $rolename = $authStorage->read()->role; 
		if($rolename == "cluster_incharge")
		{ 
		 $userCondi = "and (role='field_user' OR role='cluster_incharge') and is_deleted='0' and access_status!='1' and StaffStatus='AC' and CMSStaffStockCode!='' "; 
		}
		else{
		   $userCondi = $this->userCondi;
		}
		 */
		 
		$userCondi = $this->userCondi;
		
		if($statustype == 'processing')
		{
			$query = "select lu.StaffName, lu.StaffCode,lu.MobileNo,lu.EMail,tttab.Call_Log_No, tttab.Call_Log_DT, tttab.Customer_ID, tttab.Customer_Name, tttab.Site_ID, tttab.SiteDescr, tttab.Call_Type_Code, tttab.IndusTTNumber, tttab.IndusTTDT,tttab.site_latitude, tttab.site_longitude,tttab.job_process_status_time from day_wise_ticket_status as cl
			inner join call_log as tttab on (cl.Call_Log_No = tttab.Call_Log_No)
			left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) 
			 where cl.job_process_status='Open' and schedule_status!=0
			 and track_date = '".$date."'
			 $userCondi
			 "; 
		}
		else if($statustype == 'pending')
		{
			$query = "select lu.StaffName, lu.StaffCode,lu.MobileNo,lu.EMail,tttab.Call_Log_No, tttab.Call_Log_DT, tttab.Customer_ID, tttab.Customer_Name, tttab.Site_ID, tttab.SiteDescr, tttab.Call_Type_Code, tttab.IndusTTNumber, tttab.IndusTTDT,tttab.site_latitude, tttab.site_longitude,tttab.job_process_status_time from day_wise_ticket_status as cl 
			inner join call_log as tttab on (cl.Call_Log_No = tttab.Call_Log_No)
			left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where (cl.job_process_status!='Closed' and cl.job_process_status!='Open') and schedule_status!=0
			and track_date = '".$date."'
			$userCondi";
		}
		else
		{
			$query = "select lu.StaffName, lu.StaffCode,lu.MobileNo,lu.EMail,tttab.Call_Log_No, tttab.Call_Log_DT, tttab.Customer_ID, tttab.Customer_Name, tttab.Site_ID, tttab.SiteDescr, tttab.Call_Type_Code, tttab.IndusTTNumber, tttab.IndusTTDT, tttab.site_latitude, tttab.site_longitude,tttab.job_process_status_time from day_wise_ticket_status as cl
			inner join call_log as tttab on (cl.Call_Log_No = tttab.Call_Log_No)
			left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Closed' and schedule_status!=0
			and track_date = '".$date."'
			$userCondi";
		}
		//echo $query;die;
		$result = $db->fetchAll($query);
		
		return $result;
	}
	
	public function getGpsOnOrOffUserList($gpsstatus)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$cond = "";
		if($gpsstatus == 'on')
		{
			$cond = "on";
		}
		    $query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from local_user_mapping as lum where 1 
			and fieldUserCodes!='' and lum.gps_status='$cond'
		   $userCondi ";
		return $result = $db->fetchAll($query);		
	}
	
	public function getOnlineOfflineUserList($visibletype='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d H:i:s");
		$userCondi = $this->userCondi;
		$cond = '';

		if($visibletype == 'online')
		{
			$vcond = " and TIMESTAMPDIFF(MINUTE,last_location_service_hit_time,'".$date."') < 20";
		}
		else
		{
			$vcond = " AND 
			(TIMESTAMPDIFF(
			MINUTE , last_location_service_hit_time, '$date' ) >20 OR last_location_service_hit_time='0000-00-00 00:00:00')";
		}
		 $query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude  from local_user_mapping as lum where 1 
		 and lum.fieldUserCodes!=''
		$vcond
		$userCondi";
		$result = $db->fetchAll($query);
		return $result;
	}	

	public function getLowBetteryUserList()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = date("Y-m-d H:i:s");
		$userCondi = $this->userCondi;
		$cond = '';

				$query = "select StaffCode as staffcode, lum.StaffName, lum.EMail, lum.MobileNo, lum.current_latitude, lum.current_longitude from local_user_mapping as lum where battery_status='low' and DATE(battery_status_date)='".$date."' and lum.fieldUserCodes!='' $userCondi";
		$result = $db->fetchAll($query);
		return $result;
	}
	
	
	public function fsrClosedOnsiteOffsiteList($fsrfilled='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
		
		if($fsrfilled == 'onsite')
		{
			$query = "SELECT  DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,
			lum.StaffCode, lum.StaffName, cl.Customer_Name,cl.Call_Type_Code,pt.Descr AS product, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.Call_Log_No, cl.site_latitude, cl.site_longitude, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed
			FROM 
			fsr_detail AS fd 
			INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
			INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No)
			INNER JOIN product_type AS pt ON (cl.DescrID=pt.DescrID)
			WHERE fd.fsr_where_closed='onsite' and DATE(fd.fsr_fill_date)='".$date."' $userCondi";
		
		}
		else
		{
			$query = "SELECT  DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,
			lum.StaffCode, lum.StaffName, cl.Customer_Name,cl.Call_Type_Code,pt.Descr AS product, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.Call_Log_No, cl.site_latitude, cl.site_longitude, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed
			FROM 
			fsr_detail AS fd 
			INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
			INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No)
			INNER JOIN product_type AS pt ON (cl.DescrID=pt.DescrID)
			WHERE fd.fsr_where_closed='offsite' and DATE(fd.fsr_fill_date)='".$date."' $userCondi";
		}
		//echo $query;die;
		$result = $db->fetchAll($query);
		return $result;
	}


	public function ttClosedOnsiteOffsiteList($ttfilled='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");
		
		if($ttfilled == "onsite")
		{ 
		    $query = "SELECT DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,fd.fsr_fill_date,
			lum.StaffName,lum.StaffCode, cl.Customer_Name, cl.Call_Type_Code,pt.Descr AS product,cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.site_latitude, cl.site_longitude,if(cl.IndusTTDT, cl.IndusTTDT,cl.Call_Log_DT) AS  job_open_time,fd.tt_spare,cl.job_process_status,cl.Call_Log_No, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed, cl.job_Scheduling_Status,fd.id AS fsr_no
			FROM 
			fsr_detail AS fd 
			INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
			INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No) INNER JOIN product_type AS pt ON(pt.DescrID=cl.DescrID)
			WHERE cl.tt_closed_status='onsite' and DATE(cl.tt_closed_date)='".$date."' and cl.job_process_status = 'Complete' $userCondi GROUP BY cl.Call_Log_No ORDER BY fd.id DESC";  
		
		}
		else 
		{    
			$query = "SELECT DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,fd.fsr_fill_date,
			lum.StaffName,lum.StaffCode, cl.Customer_Name, cl.Call_Type_Code,pt.Descr AS product,cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.site_latitude, cl.site_longitude,if(cl.IndusTTDT, cl.IndusTTDT,cl.Call_Log_DT) AS  job_open_time,fd.tt_spare,fd.job_process_status,cl.Call_Log_No, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed, cl.job_Scheduling_Status,fd.id AS fsr_no
			FROM 
			fsr_detail AS fd 
			INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
			INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No) INNER JOIN product_type AS pt ON(pt.DescrID=cl.DescrID)
			WHERE cl.tt_closed_status='offsite' and DATE(cl.tt_closed_date)='".$date."' and cl.job_process_status = 'Complete' $userCondi GROUP BY cl.Call_Log_No";  
		}
		//echo $query;die;
		$result = $db->fetchAll($query);
		return $result;
	}	
	
	
	
	public function ttsClosedOnsiteOffsiteList($ttofffilled='')
	{ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		$date = date("Y-m-d");

		if($ttofffilled == "offsite")
		{  
			$query = "SELECT DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,
			lum.StaffName,lum.StaffCode, cl.Customer_Name, cl.Call_Type_Code,pt.Descr AS product,cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.site_latitude, cl.site_longitude,if(cl.IndusTTDT, cl.IndusTTDT,cl.Call_Log_DT) AS  job_open_time,fd.tt_spare,cl.job_process_status,cl.Call_Log_No, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed, cl.job_Scheduling_Status,fd.id AS fsr_no
			FROM 
			fsr_detail AS fd 
			INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
			INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No) INNER JOIN product_type AS pt ON(pt.DescrID=cl.DescrID)
			WHERE cl.tt_closed_status='offsite' and DATE(cl.tt_closed_date)='".$date."' and cl.job_process_status = 'Complete' $userCondi GROUP BY cl.Call_Log_No ORDER BY fd.id DESC";  
		}
		//echo $query;die;
		$result = $db->fetchAll($query);
		return $result;
	}	
	

	public function getRoles($role = '', $roletype = '')
	{
		if($role == 'super_admin')
		{
			$selected = '';
			if($roletype == 'national_head')
			{
				$selected = 'selected="selected"';
			}
			$dropdown .= '<option value="national_head" '. $selected .'>National Head</option>';
		}
		if($role == 'national_head' || $role == 'super_admin')
		{
			$selected = '';
			if($roletype == 'zone_head')
			{
				$selected = 'selected="selected"';
			}
			$dropdown .= '<option value="zone_head" '. $selected .'>Zone Head</option>';
			$selectRole = '<select id="selectRoleHere"><option value="selecthead">Select Role</option>'.$dropdown.'</select>';
		}
		return $selectRole;
	}
	
	
	/**
	* getAllUserDetails() method is used to get all users detail 
	* @param Null
	* @return Array 
	*/	
	public function getAllUserDetails()
	{ 
		$userCondi = $this->userCondi;	
		$db =  Zend_Db_Table::getDefaultAdapter();
	     $query = "SELECT StaffCode,StaffName FROM local_user_mapping WHERE StaffStatus='AC' $userCondi ORDER BY StaffName"; 
	  
		return $result = $db->fetchAll($query);
	}
	
	public function getTotalSiteVisitCountRecord($date='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$userCondi = $this->userCondi;
		if(!$date)
		{
			$date = date("Y-m-d");
		}
	    $query = "select lum.* from user_site_track AS ust INNER JOIN local_user_mapping AS lum ON (ust.mob_user_staff_code=lum.StaffCode) where ust.status='at_site' and DATE(ust.track_date)='".$date."' $userCondi";  
		$result = $db->fetchAll($query);
		return $result;
	}
	
	public function getTotalLoginTime($staffcode,$date='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$date = $date?$date:date("Y-m-d");
		$query = "SELECT SUM(UNIX_TIMESTAMP(IF(logout_date!='0000-00-00 00:00:00',logout_date,NOW())) - UNIX_TIMESTAMP(attend_date)) AS totallogin FROM attendance WHERE mob_user_staff_code='$staffcode'
			and DATE(attend_date)='$date'
		";
		$logtime = $db->fetchRow($query);
		return $logtime['totallogin'];
	}
	
	
	public function getFarmerdetails()
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT fl.FarmerID, fl.KhasraNumber, fl.TotalAcrage, fl.latitude, fl.longitude, fn.FarmerName,fn.FarmerCode
FROM logi_farmer_lands fl, logi_my_farmers fn
WHERE fl.FarmerID = fn.id;"; 
		 
		return $db->fetchAll($query);
		
	}
  
}
