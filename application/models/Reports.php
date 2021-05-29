<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : Users.php
 * File Description  : Users
 * Created By : Saurabh Singh Jadon
 * Created Date: 11 September 2013
 ***************************************************************/
 
class Application_Model_Reports extends Zend_Db_Table_Abstract
{
    public function getTravelDistanceByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		 $query = "SELECT  CONCAT(DATE(up.add_date_time), cl.Call_Log_No) AS groupbyfield, DATE(up.add_date_time) AS add_date, DATE_FORMAT(up.add_date_time,'%H:%i:%s') TIMEONLY, sum(up.travelled_distance) / 1000 as distance_travelled,
		lum.StaffCode, lum.StaffName, cl.Customer_Name, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1,  cl.Call_Log_No, cl.site_latitude, cl.site_longitude, cl.IndusTTNumber, cl.IndusTTNumber
		FROM 
		user_path AS up 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=up.mob_user_staff_code)
		INNER JOIN call_log AS cl on (up.Call_Log_No=cl.Call_Log_No)
		WHERE up.mob_user_staff_code=? and DATE(up.add_date_time) BETWEEN ? and ? and up.Call_Log_No!='0' group by groupbyfield order by add_date asc";
		return $result = $db->fetchAll($query, array($params['user_dis'], $params['distance_from'], $params['distance_to']));
	}
	
    public function getTimeSpendByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$resultArr = array();
		// Start query for at site
	    $query = "SELECT 
		CONCAT(DATE(up.add_date_time), cl.Call_Log_No,up.random_string) AS groupbyfield, DATE(up.add_date_time) AS add_date, DATE_FORMAT(up.add_date_time,'%H:%i:%s') TIMEONLY, 
		sum(up.travelled_distance) as distance_travelled,
		
		lum.StaffCode, lum.StaffName, cl.Customer_Name, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.Call_Log_No, cl.site_latitude, cl.site_longitude, cl.IndusTTNumber,

		(select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No=up.Call_Log_No and move_status='at_site' and random_string = up.random_string and DATE(add_date_time)=DATE(up.add_date_time) order by id asc limit 1) as atsite_startlatlong,

		(select SUM(time_spend) as time_spend from user_path where Call_Log_No=up.Call_Log_No and move_status='at_site' and random_string = up.random_string and DATE(add_date_time)=DATE(up.add_date_time) ) as atsite_time_spend,
		
		(select (SUM(time_spend) / 60) as total_time_spend from user_path where mob_user_staff_code=up.mob_user_staff_code  and  move_status='at_site' and DATE(add_date_time)=DATE(up.add_date_time)) as atsite_total_time_spend,

		(select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No=up.Call_Log_No and random_string = up.random_string and move_status='notmoving' and DATE(add_date_time)=DATE(up.add_date_time) order by id asc limit 1) as notmoving_startlatlong,

		(select SUM(time_spend) as time_spend from user_path where Call_Log_No=up.Call_Log_No and  random_string = up.random_string and move_status='notmoving' and DATE(add_date_time)=DATE(up.add_date_time) ) as notmoving_time_spend,	
		
        (select (SUM(time_spend) / 60) as total_time_spend from user_path where mob_user_staff_code=up.mob_user_staff_code  and  move_status='notmoving' and DATE(add_date_time)=DATE(up.add_date_time)) as notmoving_total_time_spend,		
		
		(select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No=up.Call_Log_No and random_string = up.random_string and move_status='moving' and DATE(add_date_time)=DATE(up.add_date_time) order by id asc limit 1) as moving_startlatlong,

		(select SUM(time_spend) as time_spend from user_path where Call_Log_No=up.Call_Log_No and random_string = up.random_string and move_status='moving' and DATE(add_date_time)=DATE(up.add_date_time) ) as moving_time_spend,
		
        (select (SUM(time_spend) / 60) as total_time_spend from user_path where mob_user_staff_code=up.mob_user_staff_code and   move_status='moving' and DATE(add_date_time)=DATE(up.add_date_time)) as moving_total_time_spend,
		up.random_string
		
		
		FROM 
		user_path AS up 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=up.mob_user_staff_code)
		INNER JOIN call_log AS cl on (up.Call_Log_No=cl.Call_Log_No)
		WHERE up.mob_user_staff_code='".$params['user_spend']."' and DATE(up.add_date_time) BETWEEN '".$params['time_spend_from']."' and '".$params['time_spend_to']."' and up.Call_Log_No!='0' and up.random_string !='' group by groupbyfield order by add_date asc";
		
		$result = $db->fetchAll($query);
		return $result;
	}
	
    public function getTicketReportByDate($params)
	{
/* 		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT  DATE(track_date) AS add_date, Call_Log_No, mob_user_cmsstaff_code FROM user_site_track WHERE DATE(track_date) BETWEEN ? and ? and status='left_site' group by add_date,Call_Log_No order by add_date asc";  
		return $result = $db->fetchAll($query, array($params['user_attend'], $params['ticket_from'], $params['ticket_to'])); */
	}
	
    public function getAttendanceByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
  $query = "SELECT  DATE(a.attend_date) AS add_date, 
						DATE_FORMAT(a.attend_date,'%H:%i:%s') TIMEONLY,
						lum.StaffCode, lum.StaffName, a.attend_date, a.latitude, a.longitude, a.logout_date, 
						a.end_latitude,a.end_longitude, if(a.status=2,'Present',
						if(a.status=3,'Absent','Present'))	as daystatus, 
						TIMESTAMPDIFF(SECOND, attend_date, logout_date)/3600 as total_time
		FROM 
		attendance AS a 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=a.mob_user_staff_code)

		WHERE a.mob_user_staff_code='".$params['user_attend']."' and DATE(a.attend_date) BETWEEN '".$params['attendance_from']."' and '".$params['attendance_to']."' order by attend_date desc";
		return $result = $db->fetchAll($query, array($params['user_attend'], $params['attendance_from'], $params['attendance_to']));
	}
	
	public function getSiteVisitByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT  DATE(track_date) as add_date, count(DISTINCT Call_Log_No) as total_visit  FROM user_site_track WHERE status='at_site'  and mob_user_staff_code=? and DATE(track_date) BETWEEN ? and ? group by add_date  order by add_date asc";  
		return $result = $db->fetchAll($query, array($params['user_visit'], $params['sitevisit_from'], $params['sitevisit_to']));
	}
	
	public function getTotalTravelledDistance($user_visit,$date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select (sum(travelled_distance) / 1000) AS total_distance_travelled from user_path  WHERE 
				mob_user_staff_code=? and DATE(add_date_time)=?
				group by DATE(add_date_time)";
			$result = $db->fetchRow($query, array($user_visit,$date));
			return $result['total_distance_travelled'];		
	}
	
	public function getStartLatLong($user_visit,$date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select lat,longitude from user_path  WHERE 
			mob_user_staff_code=? and DATE(add_date_time)=?
			order by id asc limit 1";
			return $result = $db->fetchRow($query, array($user_visit,$date));
	}

	public function getEndLatLong($user_visit,$date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select lat,longitude from user_path  WHERE 
			mob_user_staff_code=? and DATE(add_date_time)=?
			order by id desc limit 1";
			return $result = $db->fetchRow($query, array($user_visit,$date));
	}
	
	public function getTotalSiteVisitCountByEmp($staffcode, $datefrom, $dateto)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select count(Call_Log_No) as totalvisit from user_site_track where status='at_site' and mob_user_staff_code=? and track_date BETWEEN ? and ?";
		$result = $db->fetchRow($query, array($staffcode, $datefrom, $dateto));
		return $result['totalvisit'];
	}
	
	public function getTotalSiteCloseByEmp($staffcode, $date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select count(Call_Log_No) as totalclose from day_wise_ticket_status where job_process_status='Closed' and mob_user_staff_code='".$staffcode."' and track_date='".$date."'";
		$result = $db->fetchRow($query, array($staffcode, $datefrom, $dateto));
		return $result['totalclose'];
	}

	public function getTotalSitePendingByEmp($staffcode, $date)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select count(Distinct Call_Log_No) as totalpending from day_wise_ticket_status where job_process_status='Pending' and mob_user_staff_code='".$staffcode."' and track_date='".$date."'";
		$result = $db->fetchRow($query, array($staffcode, $datefrom, $dateto));
		return $result['totalpending'];
	}
	
    public function getTravelSiteCountByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
	
		$query = "SELECT  CONCAT(DATE(ust.track_date), cl.Call_Log_No) AS groupbyfield, DATE(ust.track_date) AS add_date, DATE_FORMAT(ust.track_date,'%H:%i:%s') TIMEONLY,
		lum.StaffCode, lum.StaffName,a.status,a.attend_date,cl.Call_Log_No,cl.Customer_Name,cl.Call_Type_Code, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1,  cl.site_latitude, cl.site_longitude, cl.IndusTTNumber,pt.Descr AS product 
		FROM 
		user_site_track AS ust 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=ust.mob_user_staff_code)
		INNER JOIN attendance AS a ON(a.mob_user_staff_code=ust.mob_user_staff_code)
		INNER JOIN call_log AS cl on (ust.Call_Log_No=cl.Call_Log_No)
		LEFT JOIN product_type AS pt ON(cl.DescrID=pt.DescrID)
		WHERE ust.mob_user_staff_code=? and DATE(ust.track_date) BETWEEN ? and ? and ust.Call_Log_No!='0' and ust.status='at_site' group by groupbyfield order by add_date asc";
  
		return $result = $db->fetchAll($query, array($params['user_visit'], $params['sitevisit_from'], $params['sitevisit_to']));
	}

	
    public function getFsrReportsByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT  DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,
		lum.StaffCode, lum.StaffName, cl.Customer_Name,cl.Call_Type_Code,pt.Descr AS product, cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.Call_Log_No, cl.site_latitude, cl.site_longitude, cl.IndusTTNumber, fd.fsr_closed_lat, fd.fsr_closed_long,fd.fsr_where_closed
		FROM 
		fsr_detail AS fd 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
		INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No)
		LEFT JOIN product_type AS pt ON (cl.DescrID=pt.DescrID)
		WHERE fd.mob_user_staff_code=? and DATE(fd.fsr_fill_date) BETWEEN ? and ? and fd.Call_Log_No!='0' order by add_date asc";

		return $result = $db->fetchAll($query, array($params['user_fsr'], $params['fsr_from'], $params['fsr_to']));
	}

	
    public function getTicketReportsByDate($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT DATE(fd.fsr_fill_date) AS add_date, DATE_FORMAT(fd.fsr_fill_date,'%H:%i:%s') TIMEONLY,
		lum.StaffName,lum.StaffCode, cl.Customer_Name, cl.Call_Type_Code,pt.Descr AS product,cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.site_latitude, 
		cl.site_longitude,
		if(cl.IndusTTDT!='' AND cl.IndusTTDT!='0000-00-00 00:00:00', cl.IndusTTDT,
		if(cl.Call_Log_DT!='' AND cl.Call_Log_DT!='0000-00-00 00:00:00',cl.Call_Log_DT,'N/A')) AS job_open_time,
		fd.tt_spare,fd.job_process_status,
		fd.job_process_status_time,cl.Call_Log_No, 
		cl.IndusTTNumber, fd.fsr_closed_lat, 
		fd.fsr_closed_long,fd.fsr_where_closed,
		cl.job_Scheduling_Status,fd.id AS fsr_no
		FROM 
		fsr_detail AS fd 
		INNER JOIN local_user_mapping AS lum on (lum.StaffCode=fd.mob_user_staff_code)
		INNER JOIN call_log AS cl on (fd.Call_Log_No=cl.Call_Log_No) LEFT JOIN product_type AS pt ON(pt.DescrID=cl.DescrID)
		WHERE fd.mob_user_staff_code ='".$params['user_dis']."' and DATE(fd.fsr_fill_date) BETWEEN '".$params['ticket_from']."' and '".$params['ticket_to']."' and fd.Call_Log_No!='0' order by add_date asc"; 

		return $result = $db->fetchAll($query);
	}
	
	public function getPickChooseData($params)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();

		// AND and DATE(attend_date)  between '".$params['pick_from']."' and '".$params['pick_to']."'
		$sqlparam = "";
		$groupby ="";
		if($params['totalloginhours'])
		{
			$sqlparam .= " ,total_login_hours";
		}
		if($params['fsrfillonsite'])
		{
			$sqlparam .= " , (
				select count(*) from fsr_detail where DATE(fsr_fill_date)=DATE(a.attend_date) and mob_user_staff_code=lum.StaffCode and fsr_where_closed='onsite'
			) as fsrfillonsite";
		}
		
		if($params['fsrfilloffsite'])
		{
			$sqlparam .= " , (
				select count(*) from fsr_detail where DATE(fsr_fill_date)=DATE(a.attend_date) and mob_user_staff_code=lum.StaffCode and fsr_where_closed='offsite'
			) as fsrfilloffsite";
		}
		
		if($params['ttclosedonsite'])
		{
			$sqlparam .= " , (
				select count(*) from call_log where DATE(tt_closed_date)=DATE(a.attend_date) and curr_Alloted_Eng_Code=lum.StaffCode and tt_closed_status='onsite'
			) as ttclosedonsite";
		}
		
		if($params['ttclosedoffsite'])
		{
			$sqlparam .= " , (
				select count(*) from call_log where DATE(tt_closed_date)=DATE(a.attend_date) and curr_Alloted_Eng_Code=lum.StaffCode and tt_closed_status='offsite'
			) as ttclosedoffsite";
		}
		
		if($params['sitevisitcount'])
		{
			$sqlparam .= " , (
				select count(*) from user_site_track where DATE(track_date)=DATE(a.attend_date) and mob_user_staff_code=lum.StaffCode and status='at_site'
			) as sitevisitcount";
		}
		
		$jointab = "";
		$cond = "";
		if(count($params['tttype']) || count($params['ttproducttype'])  || count($params['ttstatus']))
		{
			if(count($params['tttype']))
			{
				$str = implode(",",$params['tttype']);
				$str = "'".str_replace(",","','",$str)."'";
				$sqlparam .= " ,cl.Call_Type_Code";
				$conda = " cl.Call_Type_Code in ($str)";
			}
			if(count($params['ttproducttype']))
			{
				$str = implode(",",$params['ttproducttype']);
				$str = "'".str_replace(",","','",$str)."'";
				$sqlparam .= " ,pt.ComplaintDescrGroup";
				if($conda)
				{
					$condb = " and (".$conda." OR pt.ComplaintDescrGroup in ($str)";
				}
				else
				{
					$conda = " cl.product in ($str)";
				}	
			}
			
			if(count($params['ttstatus']))
			{
				$str = implode(",",$params['ttstatus']);
				$str = "'".str_replace(",","','",$str)."'";
				$sqlparam .= " ,d.job_process_status";
				
				if($condb)
				{
					$condc = $condb. " OR d.job_process_status in ($str))";
				}
				else if($conda)	
				{
					$condb = " and (".$conda." OR d.job_process_status in ($str)";
				}
				else
				{
					$conda = " d.job_process_status in ($str)";
				}
			}
			
			if($condc)
			{
				$cond = $condc;
			}
			else if($condb)
			{
				$cond = $condb.")";
			}
			else if($conda)	
			{
				$cond = " and ".$conda;
			}
			
			$sqlparam .= " ,CONCAT(cl.Call_Log_No,DATE(d.track_date)) as unique_date,cl.Call_Log_No,cl.Customer_Name, cl.Call_Type_Code,cl.Site_ID, cl.Customer_ID, cl.SiteAdd1, cl.site_latitude, cl.site_longitude,if(cl.IndusTTDT, cl.IndusTTDT,cl.Call_Log_DT) AS job_open_time, cl.IndusTTNumber,d.track_date,DATE_FORMAT(d.track_date,'%H:%i:%s') JOB_TIMEONLY,d.real_job_process_status,pt.ComplaintDescrGroup";
			
			$jointab = " INNER JOIN day_wise_ticket_status as d on (d.mob_user_staff_code=lum.StaffCode and d.track_date=DATE(a.attend_date)) 
			INNER JOIN call_log as cl on (cl.Call_Log_No=d.Call_Log_No)
			LEFT JOIN product_type as pt on (pt.DescrID=cl.DescrID)
			";
			$groupby = " GROUP BY unique_date";
		}
		if($params['absent'] && !$params['present'])
		{
			$cond .= " and a.status=3";
		}	
		else if(!$params['absent'] && $params['present'])
		{
			$cond .= " and (a.status=1 or a.status=2)";
		}
		$query = "select 
		DATE(a.attend_date) AS add_date, DATE_FORMAT(a.attend_date,'%H:%i:%s') TIMEONLY,
		lum.StaffCode, lum.StaffName,if(a.status=3,'Absent','Present') as attend
		$sqlparam
		from local_user_mapping as lum 
		INNER join attendance as a on 
		(lum.StaffCode=a.mob_user_staff_code) 
		$jointab
		where 
		lum.StaffCode='".$params['pick_user']."' and DATE(a.attend_date) BETWEEN '".$params['pick_from']."' and '".$params['pick_to']."'
		$cond $groupby
		
		order by a.attend_date asc
		";

		return $result = $db->fetchAll($query);
	}
	
	public function getAllAcceptedJob($StaffCode,$date='')
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
		 $query = "select count(*) as total_accepted from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Accepted' and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' ";  
		//die;
		$result = $db->fetchRow($query);
		$openjobstatusArr['acceptedcount'] = $result['total_accepted']?$result['total_accepted']:0;
		return $openjobstatusArr;
	}

	public function getAllUnacceptedcountJob($StaffCode,$date='')
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
		 $query = "select count(*) as total_unaccepted from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Unaccepted' and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' ";  
		//die;
		$result = $db->fetchRow($query);
		$openjobstatusArr['unacceptedcount'] = $result['total_unaccepted']?$result['total_unaccepted']:0;
		return $openjobstatusArr;
	}
	
	public function getAllReallocatedcountJob($StaffCode,$date='')
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
		 $query = "select count(*) as total_reallocated from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Unaccepted' and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' ";  
		//die;
		$result = $db->fetchRow($query);
		$openjobstatusArr['reallocatedcount'] = $result['total_reallocated']?$result['total_reallocated']:0;
		return $openjobstatusArr;
	}
	
	
	public function getAllTotalOpenJob($StaffCode,$date='')
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
		 $query = "select count(*) as total_processing from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Open' and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' ";  
		//die;
		$result = $db->fetchRow($query);
		$openjobstatusArr['opencount'] = $result['total_processing']?$result['total_processing']:0;
		return $openjobstatusArr;
	}
	
	
	public function getAllTotalPendingJob($StaffCode,$date='')
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
	     $query = "select count(*) as total_pending from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where (cl.job_process_status!='Closed' and cl.job_process_status!='Open') and schedule_status!=0 and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' "; 
	
		$result = $db->fetchRow($query);
		$pendingjobstatusArr['pendingcount'] = $result['total_pending']?$result['total_pending']:0;
		return $pendingjobstatusArr;
	}
	
	
	public function getAllTotalCloseJob($StaffCode,$date='')
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
		if(!$date)
		{
			$date = date("Y-m-d");
		}
	    $query = "select count(*) as total_complete from day_wise_ticket_status as cl left join local_user_mapping as lu on (cl.mob_user_cmsstaff_code=lu.CMSStaffStockCode) where cl.job_process_status='Closed' and schedule_status!=0
		and track_date = '".$date."' and lu.StaffCode ='".$StaffCode."' "; 
		$result = $db->fetchRow($query);
		$closejobstatusArr['closedcount'] = $result['total_complete']?$result['total_complete']:0;
		return $closejobstatusArr;
	}
	
	
	public function getAtSiteTime($StaffCode,$job_id,$date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select track_date AS at_site_time from user_site_track where mob_user_staff_code='".$StaffCode."' and Call_Log_No='".$job_id."' and DATE(track_date)='".$track_date."' and status='at_site'"; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	public function getLeftSiteTime($StaffCode,$job_id,$date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select track_date AS left_site_time from user_site_track where mob_user_staff_code='".$StaffCode."' and Call_Log_No='".$job_id."' and DATE(track_date)='".$track_date."' and status='left_site'"; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	
	public function getAttendanceStartTime($StaffCode,$date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select attend_date AS start_attend_date from attendance where mob_user_staff_code='".$StaffCode."' and DATE(attend_date)='".$track_date."' and status='1'"; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	public function getAttendanceEndTime($StaffCode,$date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select attend_date AS end_attend_date from attendance where mob_user_staff_code='".$StaffCode."' and DATE(attend_date)='".$track_date."' and status='2'"; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	
	public function getTotalSiteVisitCount($StaffCode,$date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $date = date("Y-m-d");
	    $query = "select count(*) as total_site_visit from user_site_track where mob_user_staff_code='".$StaffCode."' and DATE(track_date)='".$track_date."' and status='at_site' "; 
		$result = $db->fetchRow($query);
		$totalvisitArr['total_visit_site'] = $result['total_site_visit']?$result['total_site_visit']:0;
		return $totalvisitArr;
	}
	
	
	public function getAllGpsOnUserJob($StaffCode)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select gps_status from local_user_mapping where StaffCode='".$StaffCode."' "; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	
	public function getServiceRunningUserJob($StaffCode,$add_date)
	{   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select COUNT(*) as gps_status_count from local_user_mapping where StaffCode='".$StaffCode."' and DATE(attendance_time)='".$add_date."' "; 
		$result = $db->fetchRow($query);
		return $result;
	}
	
	public function moving_endlatlong($Call_Log_No,$random_string, $add_date_time)
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No='".$Call_Log_No."' and random_string = '".$random_string."' and move_status='moving' and DATE(add_date_time)=DATE('".$add_date_time."') order by id desc limit 1";

		$result = $db->fetchRow($query);
		return $result['latlong'];
	}

	public function notmoving_endlatlong($Call_Log_No,$random_string, $add_date_time)
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No='".$Call_Log_No."' and random_string = '".$random_string."' and move_status='notmoving' and DATE(add_date_time)=DATE('".$add_date_time."') order by id desc limit 1";

		$result = $db->fetchRow($query);
		return $result['latlong'];
	}

	public function atsite_endlatlong($Call_Log_No,$random_string, $add_date_time)
	{
	    $db =  Zend_Db_Table::getDefaultAdapter();
		$query = "select concat(TRUNCATE(lat,3),'/',TRUNCATE(longitude,3)) as latlong from user_path where Call_Log_No='".$Call_Log_No."' and move_status='at_site' and random_string = '".$random_string."' and DATE(add_date_time)=DATE('".$add_date_time."') order by id desc limit 1";

		$result = $db->fetchRow($query);
		return $result['latlong'];
	}	
	
	
	function getAllEnggList($params)
	{
		//print_r($params);
		//exit();
		$db =  Zend_Db_Table::getDefaultAdapter();
		if($params['rep_for']=='day_close' or $params['rep_for']=='day_start')
		{
			$params['type'] = 'Attendance';
			$start = $params['selected_date'].' '.$params['starttime'];
		   $end = $params['selected_date'].' '.$params['endtime'];
		    $role = 'field_user';
                $query = "SELECT lum.LoginID,lum.StaffCode,lum.StaffName,lum.fieldUserCodes,lum.fieldUserParent,lum.role,nft.type,nft.statustype,nft.mob_user_staff_code,nft.noti_date,lum2.StaffCode,lum2.StaffCode,lum2.StaffName AS cluster_incharge FROM
		local_user_mapping AS lum
                LEFT JOIN local_user_mapping AS lum2 on (lum.fieldUserParent=lum2.StaffCode)
		INNER JOIN  notification AS nft on (lum.StaffCode = nft.mob_user_staff_code)
		
		WHERE lum.fieldUserCodes='".$params['circle']."' and lum.role='".$role."' and nft.type='".$params['type']."' and  nft.statustype='".$params['rep_for']."' and nft.noti_date BETWEEN '".$start."' and '".$end."'  group by nft.mob_user_staff_code order by nft.noti_date asc";
		
		  return $result = $db->fetchAll($query, array());
		}
		else
		{
			   
			$params['type'] = 'Job';
			$start = $params['selected_date'].' '.$params['starttime'];
			$end = $params['selected_date'].' '.$params['endtime'];
			$role = 'field_user';
			$params['statustype'] = 'at_site';
			$query = "SELECT  lum.LoginID,lum.StaffCode,lum.StaffName,lum.fieldUserCodes,lum.fieldUserParent,lum.role,nft.type,nft.statustype,nft.mob_user_staff_code,nft.noti_date,nft.count_visit,lum2.StaffCode,lum2.StaffCode,lum2.StaffName AS cluster_incharge FROM
		local_user_mapping AS lum
                LEFT JOIN local_user_mapping AS lum2 on (lum.fieldUserParent=lum2.StaffCode)
		INNER JOIN  notification AS nft on (lum.StaffCode = nft.mob_user_staff_code)
		
		WHERE lum.fieldUserCodes='".$params['circle']."' and lum.role='".$role."' and nft.type='".$params['type']."' and  nft.statustype='".$params['statustype']."'  and nft.count_visit ='".$params['rep_for']."'  and nft.noti_date BETWEEN '".$start."' and '".$end."'  order by lum.LoginID asc, nft.noti_date asc ";
		  
		 
		  return $result = $db->fetchAll($query, array());
		   
		}
	
		}
		
		function getFsrDetailsForReport($params)
		{
			$db =  Zend_Db_Table::getDefaultAdapter();
			//print_r($params);
                        //exit();
			$all_call_completed_with_pdf = "SELECT  cl.Call_Log_DT AS CALL_DATE,cl.Call_Log_No AS ACME_TT_NUMBER,cl.IndusTTNumber 
                            AS CUSTOMER_TT_NUMBER,cl.Circle_Code AS CIRCLE,cl.Call_Type_Code AS TYPE_OF_CALL,cl.Customer_Name AS CUSTOMER,
                            cl.Site_ID AS SITE, cl.Allot_To_Engg_Name AS ENGG,cl.Call_Status_ID AS CALL_STATUS,pdt.ComplaintDescrGroup AS PRODUCT,
                            fsr.id,fsr.fsr_type,fsr.fsr_fill_date AS FSR_FILLED_DATE,fsrpu.id AS FPUID,fsrac.id AS FACID
			FROM
			call_log_completed AS cl
			INNER JOIN  fsr_detail AS fsr on (cl.Call_Log_No = fsr.Call_Log_No)
			LEFT JOIN fsr_piu AS fsrpu on (fsr.id = fsrpu.fsr_detail_id) 
			LEFT JOIN fsr_ac AS fsrac on (fsr.id = fsrac.fsr_detail_id)
                         LEFT JOIN product_type AS pdt on (cl.DescrID = pdt.DescrID) 
                        where 1";
			if($params['circle']){
				$all_call_completed_with_pdf .= " and cl.Circle_Code  ='".$params['circle']."'";
			}
			if($params['call_type']){
				$all_call_completed_with_pdf .= " and cl.Call_Type_Code  ='".$params['call_type']."'";
			}
                        if($params['customer_name']){
                        $all_call_completed_with_pdf .= " and cl.Customer_Name  ='".$params['customer_name']."'";
                        }
                        if($params['siteID']){
                        $all_call_completed_with_pdf .= " and cl.Site_ID  ='".$params['siteID']."'";
                        }
                        if($params['engg_name']){
                        $all_call_completed_with_pdf .= " and cl.Allot_To_Engg_Name  ='".$params['engg_name']."'";
                        }
                       if($params['complain_no']){
				$all_call_completed_with_pdf .= " and cl.Call_Log_No ='".$params['complain_no']."'";
			}
			if($params['date_from'] && $params['date_to']){
				$params['date_from'].' '.'00:00:00';
				$params['date_to'] = $params['date_to'].' '.'23:59:00';
			    $all_call_completed_with_pdf .= " and fsr.fsr_fill_date BETWEEN '".$params['date_from']."' and '".$params['date_to']."'";
			}
			
			 $all_call_completed_with_pdf .= " and fsr_type != 'Other' and fsr_filled!='no'";
			 
			return  $result = $db->fetchAll($all_call_completed_with_pdf, array()); 
			  
		}
		
		function getCirclewiseReports($params)
		{
			 $db =  Zend_Db_Table::getDefaultAdapter();

//SELECT  cl.Call_Log_DT AS CALL_DATE,cl.Call_Log_No AS ACME_TT_NUMBER,cl.IndusTTNumber  AS CUSTOMER_TT_NUMBER,cl.Circle_Code AS CIRCLE,cl.Call_Type_Code AS TYPE_OF_CALL,cl.Customer_Name AS CUSTOMER, cl.Site_ID AS SITE, LEFT JOIN  notification AS nft_attendence on (lum.StaffCode = nft.mob_user_staff_code), nft_attendence.noti_date as day_start  nft_attendence.statustype='day_start' AND
/*			select notification.noti_date as site_reached_time, fsr_detail.tt_spare, fsr_detail.job_process_status, fsr_detail.fsr_filled as fsr_filled, fsr_detail.fsr_fill_date, CASE WHEN date_format(notification.noti_date,'%Y-%m-%d') = '".$params['date_to']."' THEN 1 ELSE 0 END as display_site, lum.staffcode as ENGG_CODE, lum.staffname as ENGG_NAME, lum.mobileno as ENGG_PHONE, lum1.staffname as CI_NAME, lum1.mobileno as CI_PHONE, notification.mob_user_staff_code, notification.Call_Log_No from notification left join fsr_detail on (fsr_detail.Call_Log_No=notification.Call_Log_No) left join local_user_mapping lum on (lum.StaffCode=notification.mob_user_staff_code) left join local_user_mapping lum1 on (lum.fieldUserParent=lum1.StaffCode)  LEFT JOIN call_log as cl on (cl.Call_Log_No = fsr_detail.Call_Log_No) where cl.Circle_Code ='".$params['circle']."' AND notification.mob_user_staff_code in(select staffcode from local_user_mapping where fieldUserParent in(".$eng_list.")) and statustype='at_site' and date_format(noti_date,'%Y-%m-%d') >= '".$params['date_from']."' and date_format(noti_date,'%Y-%m-%d') <= '".$params['date_to']."' AND lum2.StaffCode = '".$params['cluster']."' group by fsr_detail.call_log_no   
 */
	$eng_list = implode("','",$params['CI_list']);
	$eng_list = "'".$eng_list."'"; 
 if($params['cluster']!='-' && $params['cluster']!='' ){

	$all_call_completed_with_pdf = "select fsr_detail.id as fsr_id, notification.noti_date as site_reached_time, fsr_detail.tt_spare, fsr_detail.job_process_status, fsr_detail.fsr_filled as fsr_filled, fsr_detail.fsr_fill_date, CASE WHEN date_format(notification.noti_date,'%Y-%m-%d') = '".$params['date_to']."' THEN 1 ELSE 0 END as display_site, lum.staffcode as ENGG_CODE, lum.staffname as ENGG_NAME, lum.mobileno as ENGG_PHONE, lum1.staffname as CI_NAME, lum1.mobileno as CI_PHONE, notification.mob_user_staff_code, notification.Call_Log_No as call_log_id from notification left join fsr_detail on (fsr_detail.Call_Log_No=notification.Call_Log_No) left join local_user_mapping lum on (lum.StaffCode=notification.mob_user_staff_code) left join local_user_mapping lum1 on (lum.fieldUserParent=lum1.StaffCode)  LEFT JOIN call_log as cl on (cl.Call_Log_No = notification.Call_Log_No) where cl.Circle_Code ='".$params['circle']."' AND notification.mob_user_staff_code in(select staffcode from local_user_mapping where fieldUserParent in('".$params['cluster']."')) and statustype='at_site' and date_format(noti_date,'%Y-%m-%d') >= '".$params['date_from']."' and date_format(noti_date,'%Y-%m-%d') <= '".$params['date_to']."' order by notification.noti_date ASC, fsr_detail.fsr_fill_date desc";
 }else{
	$all_call_completed_with_pdf = "select fsr_detail.id as fsr_id, notification.noti_date as site_reached_time, fsr_detail.tt_spare, fsr_detail.job_process_status, fsr_detail.fsr_filled as fsr_filled, fsr_detail.fsr_fill_date, CASE WHEN date_format(notification.noti_date,'%Y-%m-%d') = '".$params['date_to']."' THEN 1 ELSE 0 END as display_site, lum.staffcode as ENGG_CODE, lum.staffname as ENGG_NAME, lum.mobileno as ENGG_PHONE, lum1.staffname as CI_NAME, lum1.mobileno as CI_PHONE, notification.mob_user_staff_code, notification.Call_Log_No as call_log_id from notification left join fsr_detail on (fsr_detail.Call_Log_No=notification.Call_Log_No) left join local_user_mapping lum on (lum.StaffCode=notification.mob_user_staff_code) left join local_user_mapping lum1 on (lum.fieldUserParent=lum1.StaffCode)  LEFT JOIN call_log as cl on (cl.Call_Log_No = notification.Call_Log_No) where cl.Circle_Code ='".$params['circle']."' AND notification.mob_user_staff_code in(select staffcode from local_user_mapping where fieldUserParent in(".$eng_list.")) and statustype='at_site' and date_format(noti_date,'%Y-%m-%d') >= '".$params['date_from']."' and date_format(noti_date,'%Y-%m-%d') <= '".$params['date_to']."' order by notification.noti_date ASC, fsr_detail.fsr_fill_date desc";

//$fsr_detail_query = "select fsr_detail.Call_Log_No, fsr_detail.id as fsr_id, fsr_detail.tt_spare, fsr_detail.job_process_status, fsr_detail.fsr_filled as fsr_filled, fsr_detail.fsr_fill_date from fsr_detail where call_log_no in(select noti.call_log_no from notification noti  LEFT JOIN call_log as cl on (cl.Call_Log_No = noti.Call_Log_No) where cl.Circle_Code ='DEL' AND noti.mob_user_staff_code in(select staffcode from local_user_mapping where fieldUserParent in('EI0015','EI0098','EI2781')) AND noti.statustype='at_site' and date_format(noti.noti_date,'%Y-%m-%d') >= '2014-11-11' and date_format(noti.noti_date,'%Y-%m-%d') <= '2014-11-11') order by fsr_fill_date desc";	 
	 }
/*			if($params['circle']){
				$all_call_completed_with_pdf .= " cl.Circle_Code  ='".$params['circle']."'";
			}
			if($params['date_from'] && $params['date_to']){
			    $all_call_completed_with_pdf .= " and cl.Call_Log_DT BETWEEN '".$params['date_from']."' and '".$params['date_to']."' order by nft_time.noti_date DESC";
			}
*/			//echo $all_call_completed_with_pdf;
				//$all_call_completed_with_pdf .= " and fsr_type != 'Other'";
				$result = $db->fetchAll($all_call_completed_with_pdf);
$result_filter = array();
$call_log_found_array = array();
foreach($result as $single){
if(!in_array($single['call_log_id'],$call_log_found_array) ){
$result_filter[] = $single;	
$call_log_found_array[] = $single['call_log_id'];
}
	}
				return $result_filter ;
		    
		
		}

function getEnggDayTimeList($engg_code_list,$from_date,$to_date)
{   
	$eng_list = implode("','",$engg_code_list);
	$eng_list = "'".$eng_list."'";
	$db =  Zend_Db_Table::getDefaultAdapter();

	$all_engg_list = "select lum.staffcode as staffcode, lum.staffcode as ENGG_CODE, lum.mobileno as ENGG_PHONE, lum.staffname as ENGG_NAME, lum1.staffname as CI_NAME, lum1.mobileno as CI_PHONE from local_user_mapping lum join local_user_mapping lum1 on (lum.fieldUserParent = lum1.staffcode) where lum.fieldUserParent IN(".$eng_list.")  AND lum.staffstatus='AC'";
	//print_r($all_engg_list);die;
	$resultall = $db->fetchAll($all_engg_list);
	
	$all_engg_result = array();
	foreach($resultall as $single){
		$all_engg_result[$single['staffcode']] = $single;
		}

	$engg_start_day = "SELECT ntf_day_start.mob_user_staff_code as engg_code, ntf_day_start.noti_date as day_start
	FROM notification AS ntf_day_start where  ntf_day_start.statustype='day_start' AND ntf_day_start.mob_user_staff_code IN(select staffcode from local_user_mapping where fieldUserParent IN(".$eng_list.")) AND DATE_FORMAT( ntf_day_start.noti_date, '%Y-%m-%d' ) ='".$to_date."' group by ntf_day_start.mob_user_staff_code order by noti_date ASC ";
	//print_r($engg_start_day);
	$result = $db->fetchAll($engg_start_day);
	
	$start_day = array();
	foreach($result as $single){
		$start_day[$single['engg_code']]['day_start'] = $single['day_start'];
		}
	//echo '<pre>';print_r($start_day);die;
	$engg_end_day = "SELECT ntf_day_start.mob_user_staff_code as engg_code, ntf_day_start.noti_date as day_end
	FROM notification AS ntf_day_start where  ntf_day_start.statustype='day_close' AND ntf_day_start.mob_user_staff_code IN(select staffcode from local_user_mapping where fieldUserParent IN(".$eng_list.")) AND DATE_FORMAT( ntf_day_start.noti_date, '%Y-%m-%d' ) ='".$to_date."' group by ntf_day_start.mob_user_staff_code order by noti_date DESC ";
	//print_r($engg_end_day);
	$resulte = $db->fetchAll($engg_end_day);
	
	$end_day = array();
	foreach($resulte as $single){
		$end_day[$single['engg_code']]['day_end'] = $single['day_end'];
		}

	$engg_visit_count = "SELECT mob_user_staff_code as engg_code, max(count_visit) as visit_count,  date_format(noti_date,'%Y-%m-%d') as for_date FROM notification where mob_user_staff_code in(select staffcode from local_user_mapping where fieldUserParent IN(".$eng_list.")) and date_format(noti_date,'%Y-%m-%d') >='".$from_date."' AND date_format(noti_date,'%Y-%m-%d') <='".$to_date."' and statustype='at_site' group by mob_user_staff_code, for_date";
	$resultvisit_count = $db->fetchAll($engg_visit_count);
	
	$user_visit_count = array();
	foreach($resultvisit_count as $single){
		if(!empty($user_visit_count[$single['engg_code']])){
		$user_visit_count[$single['engg_code']] += $single['visit_count'];
		}else{
		$user_visit_count[$single['engg_code']] = $single['visit_count'];			
			}
		}

//echo '<pre>'; print_r(array('start_time'=>$start_day,'end_time'=>$end_day));
	return array('all_engg_result'=>$all_engg_result,'start_time'=>$start_day,'end_time'=>$end_day,'visit_count'=>$user_visit_count);	
}

public function circle_cluster_list($circle_code){
	$db =  Zend_Db_Table::getDefaultAdapter();
	$cluster_query = "SELECT circle_headcode from circle_master where  circlecode='".$circle_code."'";
	//print_r($engg_start_day);
	$result = $db->fetchOne($cluster_query);
	$cluster_codes = explode(',',$result);
	
	foreach($cluster_codes as $single_cluster){
	$cluster_array[] = array('text'=>$single_cluster,'value'=>$single_cluster);
		}
	
	return $cluster_array ;	
	}
        
        
        public function getAllsparepartsofuser($cmsCode){
       $db =  Zend_Db_Table::getDefaultAdapter();
          $all_spare_part_sql = "select * from spare_parts where EnggCode='".$cmsCode."' ";
          return $result = $db->fetchAll($all_spare_part_sql);
     }
        
        
          public function getUserDataByStaffCode($staffcode)
            { 
                $db =  Zend_Db_Table::getDefaultAdapter();

	      $query = "SELECT EntryNo,LoginID,device_id,access_status, StaffCode,StaffStatus, StaffName,CMSStaffStockCode,device_type,device_token,role,Call_Log_No FROM local_user_mapping WHERE StaffCode=? and StaffStatus='AC' and fieldUserCodes!=''

	 AND (role='field_user' OR role='cluster_incharge')"; 
           return $result = $db->fetchRow($query, array($staffcode));

	}


	//////////////////////////////////// Report  functions for current user site address location update /////////////////////////////////        

		     public function getcalllogdetails($params){
                                $db =  Zend_Db_Table::getDefaultAdapter();
                                $query="Select lu.StaffName,lu.CMSStaffStockCode,lu.current_latitude,lu.current_longitude,lu.user_curr_job_status_date,
                                cl.curr_Alloted_Eng_Code,cl.Call_Log_No,cl.Site_ID,cl.SiteAdd1,cl.SiteAdd2,
                                site.*
				From call_log as cl 
                                LEFT JOIN local_user_mapping as lu on(cl.curr_Alloted_Eng_Code=lu.CMSStaffStockCode)
                                LEFT JOIN site_location_mapping as site on(cl.Site_ID=site.site_id)
                                where cl.Call_Log_No='".$params['call_log']."'";
				return $result = $db->fetchRow($query, array($params));
                         }

         public function managecalllogdetails($data){
				$db =  Zend_Db_Table::getDefaultAdapter();
				$date=date('Y-m-d H:i:s');
                                $date1= date('Y-m-d');
				$query="Update call_log set site_longitude='".$data['current_longitude']."',site_latitude='".$data['current_latitude']."' where Call_Log_No='".$data['Call_Log_No']."'";
				$query2="INSERT INTO site_latlong_log(Site_ID,updated_Lat,updated_Long,staffcode,created)
				VALUES('".$data['Site_ID']."','".$data['current_latitude']."','".$data['current_longitude']."','".$data['CMSStaffStockCode']."','".$date."')";
				 $query3="Update site_location_mapping set longitude='".$data['current_longitude']."',latitude='".$data['current_latitude']."',last_updated_date='".$date1."' where site_id='".$data['Site_ID']."'";
                                 $db->query($query3);
                                $db->query($query);
				$db->query($query2);
				$querycall="Select * From site_latlong_log where Site_ID='".$data['Site_ID']."'";
				return $result = $db->fetchall($querycall);
         }
       
       		public function callhitorydeatils($data){
					$db =  Zend_Db_Table::getDefaultAdapter();
					$querycall="Select * From site_latlong_log where Site_ID='".$data."'";
					return  $result = $db->fetchall($querycall);
            }
                 
             
        
        
        function GetAllEnggListByClusterInchargesStaffCode($engg_code_list){
        $eng_list = implode("','",$engg_code_list);
	$eng_list = "'".$eng_list."'";
	$db =  Zend_Db_Table::getDefaultAdapter();
        $all_engg_list = "select lum.staffcode as ENGG_CODE from local_user_mapping lum where lum.fieldUserParent IN(".$eng_list.")  AND lum.staffstatus='AC'";
	return $resultall = $db->fetchAll($all_engg_list);
              
        }
        
        function GetAllEnggForACircle($circle){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $all_engg_list = "select lum.staffcode as ENGG_CODE from local_user_mapping lum where lum.fieldUserCodes ='".$circle."'  AND lum.staffstatus='AC' and role='field_user'";
            return $resultall = $db->fetchAll($all_engg_list);
        }
        
        function GetAllEnggForACMSCircle($circle){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $all_engg_list = "select lum.CMSStaffStockCode as ENGG_CODE from local_user_mapping lum where lum.fieldUserCodes ='".$circle."'  AND lum.staffstatus='AC' and role='field_user'";
            return $resultall = $db->fetchAll($all_engg_list);
        }
        
        public function GetAllFsrFilledByEngg($userData)
        {
        $eng_list = implode("','",$userData['Engg_List']);
        $eng_list = "'".$eng_list."'";
	$cond  = " and DATE(fsr.fsr_fill_date) BETWEEN '".$userData['Date_from']."' and '".$userData['Date_to']."'";
        $cond .= " and fsr.job_process_status = 'Complete'  and fsr.fsr_filled!='no'";
        $db =  Zend_Db_Table::getDefaultAdapter();
         $all_fsr_filled = "select fsr.id as fsrNo, fsr.Call_Log_No,fsr.mob_user_staff_code,fsr.fsr_type,fsr.job_process_status,fsr.fsr_fill_date,
        cl.Call_Log_DT, cl.Customer_Name, cl.Site_ID, cl.IndusTTNumber, cl.IndusTTDT, cl.Call_Type_Code, cl.tt_closed_date,cl.curr_Alloted_Eng_Code,
        cl.job_process_status,lum.StaffName,fsrac.reason as ReasonAc,fsrac.sme_remarks as RemarksAc,fsrpu.reason as ReasonPIU,fsrpu.sme_remarks as RemarksPIU
        from fsr_detail as fsr 
        LEFT JOIN fsr_piu AS fsrpu on (fsr.id = fsrpu.fsr_detail_id) 
        LEFT JOIN fsr_ac AS fsrac on (fsr.id = fsrac.fsr_detail_id)
        left join call_log as cl on (fsr.Call_Log_No = cl.Call_Log_No)
        left join local_user_mapping as lum on (cl.curr_Alloted_Eng_Code = lum.CMSStaffStockCode) where fsr.mob_user_staff_code IN(".$eng_list.") $cond";
       
        $result = $db->fetchAll($all_fsr_filled);
        return $result;
        }
        
        
        function GetAllEnggListByClusterCodes($engg_code_list){
        $eng_list = implode("','",$engg_code_list);
	$eng_list = "'".$eng_list."'";
	$db =  Zend_Db_Table::getDefaultAdapter();
        $all_engg_list = "select lum.staffcode as staffcode, lum.staffcode as ENGG_CODE, lum.mobileno as ENGG_PHONE, lum.staffname as ENGG_NAME, lum1.staffname as CI_NAME, lum1.mobileno as CI_PHONE from local_user_mapping lum join local_user_mapping lum1 on (lum.fieldUserParent = lum1.staffcode) where lum.fieldUserParent IN(".$eng_list.")  AND lum.staffstatus='AC'";
	return $resultall = $db->fetchAll($all_engg_list);
        }
        
        
        public function getattendancedetails($filterdata){
       
        $db =  Zend_Db_Table::getDefaultAdapter();
        $eng_list = implode("','",$filterdata['Engg_List']);
        $eng_list = "'".$eng_list."'";
        
	
	$resultall = $filterdata['Engg_List'];
	$all_engg_result = array();
	foreach($resultall as $single){
		$all_engg_result[$single] = $single;
		}
    
        $engg_start_day = "SELECT ntf_day_start.mob_user_staff_code as engg_code, ntf_day_start.noti_date as day_start
	FROM notification AS ntf_day_start where  ntf_day_start.statustype='day_start' AND ntf_day_start.mob_user_staff_code IN(select staffcode from local_user_mapping where staffcode IN(".$eng_list.")) AND DATE_FORMAT( ntf_day_start.noti_date, '%Y-%m-%d' ) BETWEEN '".$filterdata['Date_from']."' and '".$filterdata['Date_to']."' group by date(ntf_day_start.noti_date) order by ntf_day_start.mob_user_staff_code asc,ntf_day_start.noti_date ASC ";
	
	$result = $db->fetchAll($engg_start_day);
        //print_r($result);
	$start_day = array();
        $i=0;
	foreach($result as $single){
		$start_day[$single['engg_code']]['day_start'][$i] = $single['day_start'];
                $i++;
		}
                
	
        
    
        
        
        
	$engg_end_day = "SELECT ntf_day_start.mob_user_staff_code as engg_code, ntf_day_start.noti_date as day_end
	FROM notification AS ntf_day_start where  ntf_day_start.statustype='day_close' AND ntf_day_start.mob_user_staff_code IN(select staffcode from local_user_mapping where staffcode IN(".$eng_list.")) AND DATE_FORMAT( ntf_day_start.noti_date, '%Y-%m-%d' ) BETWEEN '".$filterdata['Date_from']."' and '".$filterdata['Date_to']."' group by date(ntf_day_start.noti_date) order by ntf_day_start.mob_user_staff_code asc,ntf_day_start.noti_date ASC ";
	//print_r($engg_end_day);
	$resulte = $db->fetchAll($engg_end_day);
        
        $end_day = array();
         $j=0;
	foreach($resulte as $single){
		$end_day[$single['engg_code']]['day_end'][$j] = $single['day_end'];
                $j++;
		}
                
       
        
        $visitfirstsite = "SELECT ntf_first_site.mob_user_staff_code as engg_code, ntf_first_site.noti_date as firstSiteVisit
	FROM notification AS ntf_first_site where ntf_first_site.statustype='at_site' and  ntf_first_site.count_visit='1'  AND ntf_first_site.mob_user_staff_code IN(select staffcode from local_user_mapping where staffcode IN(".$eng_list.")) AND DATE_FORMAT( ntf_first_site.noti_date, '%Y-%m-%d' ) BETWEEN '".$filterdata['Date_from']."' and '".$filterdata['Date_to']."'  order by ntf_first_site.mob_user_staff_code asc,ntf_first_site.noti_date asc ";
	//print_r($visitfirstsite);
        //exit();
	$resulted = $db->fetchAll($visitfirstsite);
        
        $end_day = array();
        $k=0;
	foreach($resulted as $single){
		$first_site[$single['engg_code']]['firstSiteVisit'][$k]= $single['firstSiteVisit'];
                $k++;
		}
                
       return array('all_engg_result'=>$all_engg_result,'start_time'=>$start_day,'end_time'=>$end_day,'firstSiteVisit'=>$first_site);

        }
        
        public function GetEnggAttendence($params){
             $db =  Zend_Db_Table::getDefaultAdapter();
//                if($params['cluster']!='-' and $params['engg_name']!=''){
//                $cluster_code_list[] =      $params['cluster'];
//                $engg_staff_code_list[] = $params['engg_name'];
//		}
//                
//                elseif($params['cluster']!='-'){
//                $cluster_code_list[] = $params['cluster'];
//                $engg_list_unformat = $this->GetAllEnggListByClusterInchargesStaffCode($cluster_code_list);
//                $engg_staff_code_list = array();
//                foreach($engg_list_unformat as $format){
//                    if(!in_array($format['ENGG_CODE'], $engg_staff_code_list)){
//                        $engg_staff_code_list[] = $format['ENGG_CODE'];
//                    }
//                }
//                }
//                
//                elseif($params['cluster']='-'){
//                    $circle_user = new Application_Model_Users();
//                    $cluster_list_unformat = $circle_user->getCIListByCircleCode($params['circle']);
//                     foreach($cluster_list_unformat as $single){
//                                if(!isset($cluster_code_list[$single['user_staff_code']])){
//                                if(!in_array($single['user_staff_code'],$cluster_code_list)){
//                                $cluster_code_list[] = $single['user_staff_code'];
//                                }
//                                }
//                                }
//                        $engg_list_unformat = $this->GetAllEnggListByClusterInchargesStaffCode($cluster_code_list);
//                        $engg_staff_code_list = array();
//
//                        foreach($engg_list_unformat as $format){
//                            if(!in_array($format['ENGG_CODE'], $engg_staff_code_list)){
//                                $engg_staff_code_list[] = $format['ENGG_CODE'];
//                            }
//                        }
//                }
             
                if($params['engg_name']!=''){
                $cluster_code_list[] = $params['cluster'];
                $engg_staff_code_list[] = $params['engg_name'];
		}elseif(isset($params['circle']) and isset($params['cluster']) and $params['cluster']!='-'){
                        $cluster_code_list[] = $params['cluster'];
                        $engg_list_unformat = $this->GetAllEnggListByClusterInchargesStaffCode($cluster_code_list);
                        $engg_staff_code_list = array();
                        foreach($engg_list_unformat as $format){
                            if(!in_array($format['ENGG_CODE'], $engg_staff_code_list)){
                                $engg_staff_code_list[] = $format['ENGG_CODE'];
                            }
                        }
                }else{
                        $engg_list_unformat = $this->GetAllEnggForACircle($params['circle']);
                        $engg_staff_code_list = array();
                        foreach($engg_list_unformat as $format){
                            if(!in_array($format['ENGG_CODE'], $engg_staff_code_list)){
                                $engg_staff_code_list[] = $format['ENGG_CODE'];
                            }
                        }
                }
                
                
                
                
                $params['CI_List'] = $cluster_code_list;
                $params['EnggList'] = $engg_staff_code_list;
                $userData = array(); 
                
                $userData['Engg_List'] = $engg_staff_code_list;
                $userData['Date_from'] = $params['date_from'];
                $userData['Date_to'] = $params['date_to'];
                
                $eng_list = implode("','",$userData['Engg_List']);
                $eng_list = "'".$eng_list."'";
                
                
                
                   $engg_start_day = "SELECT ntf_day_start.mob_user_staff_code as engg_code, ntf_day_start.noti_date as day_start FROM notification AS ntf_day_start 
where ntf_day_start.statustype='day_start' AND ntf_day_start.mob_user_staff_code 
IN(select staffcode from local_user_mapping where staffcode 
IN(".$eng_list."))
AND DATE_FORMAT( ntf_day_start.noti_date, '%Y-%m-%d' ) BETWEEN '".$userData['Date_from']."' and '".$userData['Date_to']."'  group by 
  ntf_day_start.noti_date
order by ntf_day_start.mob_user_staff_code asc,ntf_day_start.noti_date asc";
                   //exit();
                $resulttent = $db->fetchAll($engg_start_day); 
               return $resulttent;
        }
        
        public function getEnggDetailsByStaffCode($staffCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $engg = "select StaffName from local_user_mapping where StaffCode='".$staffCode."'";
            $resulttent = $db->fetchRow($engg); 
            return $resulttent['StaffName'];
        }
        
         public function GetEnggDayStart($mob_user_staff_code,$date){
            //$mob_user_staff_code='ACE0705';
            $db =  Zend_Db_Table::getDefaultAdapter();
              $daystart = "SELECT  ntf_day_close.noti_date as day_start_time
	FROM notification AS ntf_day_close where ntf_day_close.statustype='day_start'   AND 
        ntf_day_close.mob_user_staff_code='".$mob_user_staff_code."' and date(ntf_day_close.noti_date)='".$date."' order by ntf_day_close.noti_date asc;
	";
             //exit();
            $resulttent = $db->fetchAll($daystart);
        
             return $resulttent;
        }
        
        public function getEnggFirstVisitAtDate($mob_user_staff_code,$date){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $firstvisitTime = "SELECT  ntf_first_site.noti_date as firstSiteVisit
	FROM notification AS ntf_first_site where ntf_first_site.statustype='at_site' and  ntf_first_site.count_visit='1'  AND 
        ntf_first_site.mob_user_staff_code='".$mob_user_staff_code."' and date(ntf_first_site.noti_date)='".$date."';
	";
            $resulttent = $db->fetchRow($firstvisitTime); 
             return $resulttent['firstSiteVisit'];
        }
        public function GetEnggDayEnd($mob_user_staff_code,$date){
            //$mob_user_staff_code='ACE0579';
            $db =  Zend_Db_Table::getDefaultAdapter();
            $dayclose = "SELECT  ntf_day_close.noti_date as day_close_time
                          FROM notification AS ntf_day_close where ntf_day_close.statustype='day_close'   AND 
                          ntf_day_close.mob_user_staff_code='".$mob_user_staff_code."' and date(ntf_day_close.noti_date)='".$date."' order by ntf_day_close.noti_date desc;
                          ";
            $resulttent = $db->fetchAll($dayclose);
            return $resulttent;
        }
        
        
        function GetAllEnggListCMSCodeByClusterInchargesStaffCode($engg_code_list){
            $eng_list = implode("','",$engg_code_list);
            $eng_list = "'".$eng_list."'";
            $db =  Zend_Db_Table::getDefaultAdapter();
            $all_engg_list = "select lum.CMSStaffStockCode as ENGG_CODE from local_user_mapping lum where lum.fieldUserParent IN(".$eng_list.")  AND lum.staffstatus='AC'";
            return $resultall = $db->fetchAll($all_engg_list);    
        }
        
        
        
        
        function DayWorkReport($userData){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $eng_list = implode("','",$userData['Engg_List']);
            $eng_list = "'".$eng_list."'";

            $sql_collection ="select cl.Call_Log_No,cl.Call_Log_DT,cl.job_process_status_time,cl.job_track_status_time,cl.job_process_status,cl.curr_Alloted_Eng_Code,
                        lum.StaffName,lum.StaffCode,fd.id,fd.fsr_filled,fd.fsr_type,fd.reason,fd.fsr_fill_date,fdpiu.reason as PIU_REASON,
                        fdpiu.sme_remarks as PIU_SMEREMARKS,fdac.remarks as AC_REMARKS, fdac.sme_remarks as AC_SMEREMARKS, fdac.reason as AC_REASON
                        from call_log as cl 
                        left join local_user_mapping as lum on (cl.curr_Alloted_Eng_Code = lum.CMSStaffStockCode)
                        left join fsr_detail as fd on (cl.job_track_status_time=fd.job_track_status_time and cl.Call_Log_No=fd.Call_Log_No)
                        left join fsr_piu as fdpiu on (fd.id = fdpiu.fsr_detail_id)
                        left join fsr_ac as fdac on (fd.id = fdac.fsr_detail_id)
                        where cl.curr_Alloted_Eng_Code 
                        IN(".$eng_list.") 
                        AND DATE_FORMAT( cl.job_process_status_time, '%Y-%m-%d' ) BETWEEN '".$userData['Date_from']."' and '".$userData['Date_to']."' and lum.StaffStatus='AC'
                        order by cl.job_process_status_time asc";

            return $resulttent = $db->fetchAll($sql_collection); 
        }
        
        
        function DataSinkReport($userData){
            $eng_list = implode("','",$userData['Engg_List']);
            $eng_list = "'".$eng_list."'";    
            $db =  Zend_Db_Table::getDefaultAdapter();
            $sql ="select lum.staffcode as ENGG_CODE,lum.StaffName as staffname,lumcls.StaffName as clusterIncharge,lum.last_data_sink,lum.pending_data
                   from local_user_mapping as lum 
                   left join local_user_mapping as lumcls on (lum.fieldUserParent=lumcls.staffcode)
                   where lum.StaffCode IN(".$eng_list.")";
            
            return $result = $db->fetchAll($sql); 
        }

        public function getBankDepositReport($engineer){   
	    $db =  Zend_Db_Table::getDefaultAdapter();
            if($engineer){
               $cond = " AND LoginID='".$engineer."'";
            }
	    $query = "select * from logi_field_executive_payments where 1 $cond  order by id DESC "; 
            
	    $result = $db->fetchAll($query);
	    return $result;
	}

	public function getIssueList(){   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select * from logi_issue where deleted=0 order by id DESC ";
            
	    $result = $db->fetchAll($query);
	    return $result;
	}

	public function getIssueDetailById($issueId){   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select * from logi_issue where id='".$issueId."' "; 
            
            $result = $db->fetchRow($query);
            return $result;
	}
        
        
        public function getFieldEngineerDetailByLoginId($loginId){   
	    $db =  Zend_Db_Table::getDefaultAdapter();
	    $query = "select * from logi_field_users where LoginID='".$loginId."' ";  
         
            $result = $db->fetchRow($query);
            return $result;
	}
          
}

