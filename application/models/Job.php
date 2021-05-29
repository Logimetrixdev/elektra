<?php

class Application_Model_Job extends Zend_Db_Table_Abstract

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

	

	

	public function getAllJobsWithFilter($get,$params,$title,$sortby)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		

		$auth = Zend_Auth::getInstance();

		$authStorage = $auth->getStorage();

		$role = $authStorage->read()->role;

		$StaffCodeMd5 = $authStorage->read()->StaffCodeMd5;

		$staffUser = $params['staffUser']?$params['staffUser']:$StaffCodeMd5;

		if($staffUser)

		{

			$role = $params['role']?$params['role']:$role;

		}

		$admin = new Application_Model_Admin();

				

		/* if($title=="tt")

		{

		   $sort_title = 'id';

		}

		else if($title=="aging")

		{

		   $sort_title = 'Call_Log_DT_Aging';

		}

		else if($title=="customer")

		{

		   $sort_title = 'Customer_ID';

		}

		else if($title=="site")

		{

		   $sort_title = 'Site_ID';

		}

		else if($title=="status")

		{

		   $sort_title = 'job_process_status';

		}

		else if($title=="scheduling")

		{

		   $sort_title = 'job_Scheduling_Status';

		}

		else if($title=="priority")

		{

		   $sort_title = 'Call_Priority_ID';

		}

		

	    if($sort_title !="" && $sortby !="")

		{

		   $order = $sort_title.' '.$sortby;

		}

		else

		{

		    $order = ' id DESC';

		} */

		

		

		//$cond = "";

	    /* if($get['staffUser'])

		{

			$staffUser = $get['staffUser'];

			$cond = " AND lum.CMSStaffStockCode='".$staffUser."'";

		} */

		/* if(($role=="national_head" || $role=="super_admin"))

		{

		    $cond .= "";

		}

		else if($role=="service_manager")

        {

			$cond .= $admin->__construct($params);

			//echo  $sqlQuery = "SELECT * FROM local_user_mapping WHERE 1 $cond"; die;	

		}

        else if($role=="cluster_incharge")

        {

			$cond .=" and (md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='".$staffUser."' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))  ='".$staffUser."'))";

        }	

		

		if($get['search'])

		{

			$search = $get['search'];

			$cond .= " AND (cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%')"; 

		} */

		

		$curDate = date("Y-m-d");

		//$cond .=" and cl.job_status='accepted' ";

	

/* 	   $query = "SELECT cl.EntrySrNo,cl.id,cl.Call_Log_No,cl.Call_Log_DT,cl.Customer_ID,cl.Customer_Name,cl.Circle_Code,cl.Circle_Name,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Call_Log_Desrc,cl.Call_Priority_ID,cl.Call_Status_ID,cl.Logged_User_Code,cl.job_status,cl.job_process_status,

	   if(IndusTTDT!='NULL' and IndusTTDT!='', IndusTTDT, Call_Log_DT) as Call_Log_DT_Aging,if(DATE(job_Scheduling_Date) = ?,cl.job_Scheduling_Status,'noschedule') as job_Schedule_number,job_Scheduling_Date,

	   cl.curr_Alloted_Eng_Code,cl.job_Scheduling_Status,lum.StaffName,lum.StaffCode,pt.ComplaintDescrGroup AS product_fsr_type FROM local_user_mapping AS lum INNER JOIN call_log AS cl ON(cl.curr_Alloted_Eng_Code=lum.CMSStaffStockCode) INNER JOIN  product_type AS pt ON (pt.DescrID=cl.DescrID) WHERE 1 $cond GROUP BY cl.id ORDER BY $order "; */

	   

	   

		$select = $db->select()

					 ->from(array('lum'=>'local_user_mapping'),array('StaffName', 'StaffCode'))

			         ->join(array('cl' => 'call_log'),

                    'cl.curr_Alloted_Eng_Code=lum.CMSStaffStockCode',

                    array('EntrySrNo','id','Call_Log_No','Customer_ID','Customer_Name','Circle_Code','Circle_Name','Site_ID','SiteDescr','SiteAdd1','Call_Log_Desrc','Call_Priority_ID','Call_Status_ID','Logged_User_Code','job_status','job_process_status','if(IndusTTDT!=\'NULL\' and IndusTTDT!=\'\', IndusTTDT, Call_Log_DT) as Call_Log_DT_Aging','if(DATE(job_Scheduling_Date) = \'$curDate\',cl.job_Scheduling_Status,\'noschedule\') as job_Schedule_number','job_Scheduling_Date','curr_Alloted_Eng_Code','job_Scheduling_Status') );

					

		$select->where("job_status='accepted'");

		

		 if(($role=="national_head" || $role=="super_admin"))

		{

		    //$cond .= ""; 

		}

		else if($role=="service_manager")

        {

			//$cond .= $admin->__construct($params);

			$comoncond = $admin->__construct($params);

			$prefix = ' and ';

			$str = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $comoncond);

			$select->where($str);

		}

       else if($role=="cluster_incharge")

        {

			//$cond .=" and (md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='".$staffUser."' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))  ='".$staffUser."'))";

			

			$select->where("(md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='$staffUser' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))='$staffUser'))");

        } 		

		

		if($get['search'])

		{

			$search = $get['search'];

			//$cond .= " AND (cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%')"; 

			$select->where("(cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%' OR cl.Call_Log_No like '%$search%'  OR cl.IndusTTNumber like '%$search%')");

		}

		

		if($params['fromdate'] && $params['todate'])

		{

			$fromdate = $params['fromdate'];

			$todate = $params['todate'];

			$select->where("DATE(job_accepted_time)>='$fromdate' AND DATE(job_accepted_time)<='$todate'");

		}



		if($un_title=="tt")

		{

		   $sort_title = 'id';

		}

		else if($un_title=="aging")

		{

		   $sort_title = 'Call_Log_DT_Aging';

		}

		else if($un_title=="customer")

		{

		   $sort_title = 'Customer_ID';

		}

		else if($un_title=="site")

		{

		   $sort_title = 'Site_ID';

		}

		else if($un_title=="status")

		{

		   $sort_title = 'job_process_status';

		}

		else if($un_title=="scheduling")

		{

		   $sort_title = 'job_Scheduling_Status';

		}

		else if($un_title=="priority")

		{

		   $sort_title = 'Call_Priority_ID';

		}

		

	     if($sort_title !="" && $un_sortby !="")

		{

		   $order = $sort_title.' '.$un_sortby;

		   $select->order($order);

		}

		else

		{

		    $select->order('id DESC');

		} 

	   return  $select;

	   

		//return $result = $db->fetchAll($query,array($curDate));

	}

	

	

	public function getAllUnacceptedJobsWithFilter($get,$params,$un_title,$un_sortby)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$auth = Zend_Auth::getInstance();

		$authStorage = $auth->getStorage();

		$role = $authStorage->read()->role;

		$StaffCodeMd5 = $authStorage->read()->StaffCodeMd5;

		$staffUser = $params['staffUser']?$params['staffUser']:$StaffCodeMd5;

		if($staffUser)

		{

			$role = $params['role']?$params['role']:$role;

		}

		$admin = new Application_Model_Admin();

		



		

		$cond = "";

		/* if($get['staffUser'])

		{

			$staffUser = $get['staffUser'];

			$cond = " AND lum.CMSStaffStockCode='".$staffUser."'";

		} */

		if(($role=="national_head" || $role=="super_admin"))

		{

		    $cond .= ""; 

		}

		else if($role=="service_manager")

        {

			$cond .= $admin->__construct($params);

		}

       else if($role=="cluster_incharge")

        {

			$cond .=" and (md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='".$staffUser."' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))  ='".$staffUser."'))";

        }		

		

		if($get['search'])

		{

			$search = $get['search'];

			$cond .= " AND (cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%') OR cl.Call_Log_No like '%$search%'  OR cl.IndusTTNumber like '%$search%')"; 

		}

		

		$curDate = date("Y-m-d");

		//$cond .=" and job_status=' ' ";

		

/* 	   $select = "SELECT cl.EntrySrNo,cl.id,cl.Call_Log_No,cl.Customer_ID,cl.Customer_Name,cl.Circle_Code,cl.Circle_Name,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Call_Log_Desrc,cl.Call_Priority_ID,cl.Call_Status_ID,cl.Logged_User_Code,cl.job_status,cl.job_process_status,if(IndusTTDT!='NULL' and IndusTTDT!='', IndusTTDT, Call_Log_DT) as Call_Log_DT_Aging,if(DATE(job_Scheduling_Date) = '$curDate',cl.job_Scheduling_Status,'noschedule') as job_Schedule_number,job_Scheduling_Date,

	   cl.curr_Alloted_Eng_Code,cl.job_Scheduling_Status,lum.StaffName,lum.StaffCode,pt.ComplaintDescrGroup AS product_fsr_type FROM local_user_mapping AS lum INNER JOIN call_log AS cl ON(cl.curr_Alloted_Eng_Code=lum.CMSStaffStockCode) INNER JOIN  product_type AS pt ON (pt.DescrID=cl.DescrID) WHERE 1 $cond GROUP BY cl.id ORDER BY $order ";  */

	   

		//$select = $db->select()->from('local_user_mapping')->joinInner('call_log');

		$select = $db->select()

					 ->from(array('lum'=>'local_user_mapping'),array('StaffName', 'StaffCode'))

			         ->join(array('cl' => 'call_log'),

                    'cl.curr_Alloted_Eng_Code=lum.CMSStaffStockCode',

                    array('EntrySrNo','id','Call_Log_No','Customer_ID','Customer_Name','Circle_Code','Circle_Name','Site_ID','SiteDescr','SiteAdd1','Call_Log_Desrc','Call_Priority_ID','Call_Status_ID','Logged_User_Code','job_status','job_process_status','if(IndusTTDT!=\'NULL\' and IndusTTDT!=\'\', IndusTTDT, Call_Log_DT) as Call_Log_DT_Aging','if(DATE(job_Scheduling_Date) = \'$curDate\',cl.job_Scheduling_Status,\'noschedule\') as job_Schedule_number','job_Scheduling_Date','curr_Alloted_Eng_Code','job_Scheduling_Status') );

					

		$select->where("job_status=''");

		

		 if(($role=="national_head" || $role=="super_admin"))

		{

		    //$cond .= ""; 

		}

		else if($role=="service_manager")

        {

			//$cond .= $admin->__construct($params);

			$comoncond = $admin->__construct($params);

			$prefix = ' and ';

			$str = preg_replace('/^' . preg_quote($prefix, '/') . '/', '', $comoncond);

			$select->where($str);

		}

       else if($role=="cluster_incharge")

        {

			//$cond .=" and (md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='".$staffUser."' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))  ='".$staffUser."'))";

			

			$select->where("(md5(concat(lum.fieldUserParent,'@@@@@','cluster_incharge')) ='$staffUser' OR (md5(concat(lum.StaffCode,'@@@@@','cluster_incharge'))='$staffUser'))");

        } 		

		

		if($get['search'])

		{

			$search = $get['search'];

			//$cond .= " AND (cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%')"; 

			$select->where("(cl.Call_Log_Desrc like '%$search%' OR cl.Customer_ID like '%$search%' OR cl.SiteAdd1 like '%$search%' OR cl.SiteDescr like '%$search%' OR cl.Site_ID like '%$search%' OR cl.Call_Log_No like '%$search%'  OR cl.IndusTTNumber like '%$search%')");

		}

		

		



		if($un_title=="tt")

		{

		   $sort_title = 'id';

		}

		else if($un_title=="aging")

		{

		   $sort_title = 'Call_Log_DT_Aging';

		}

		else if($un_title=="customer")

		{

		   $sort_title = 'Customer_ID';

		}

		else if($un_title=="site")

		{

		   $sort_title = 'Site_ID';

		}

		else if($un_title=="status")

		{

		   $sort_title = 'job_process_status';

		}

		else if($un_title=="scheduling")

		{

		   $sort_title = 'job_Scheduling_Status';

		}

		else if($un_title=="priority")

		{

		   $sort_title = 'Call_Priority_ID';

		}

		

	     if($sort_title !="" && $un_sortby !="")

		{

		   $order = $sort_title.' '.$un_sortby;

		   $select->order($sort_title.' '.$un_sortby);

		}

		else

		{

		    $select->order('id DESC');

		} 

		

		return $select;

		//return $result = $db->fetchAll($query,array($curDate));

	}

	

	public function getProductTypeVal($DescrID)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$select = $db->select()->from('product_type',array('ComplaintDescrGroup AS product_fsr_type'))->where('DescrID=>',$DescrID);

		return $type = $db->fetchOne($select);

	}

	

	public function getAppJobDetailsByJobId($job_id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

	    $query = "SELECT cl.*, cl.Call_Log_No as job_id,

			if(DATE(cl.job_track_status_time) = ? and (cl.job_track_status='toward_site' OR cl.job_track_status='at_site' OR cl.job_track_status='verify_site'), cl.job_track_status, '') as job_track_status,

			if(DATE(cl.job_Scheduling_Date)='".$date."', cl.job_Scheduling_Status, 0) as job_Scheduling_Status,pt.ComplaintDescrGroup as product_type,pt.Descr as product_descr

			FROM call_log AS cl LEFT JOIN product_type AS pt ON (cl.DescrID=pt.DescrID) WHERE Call_Log_No =?"; 

		return $result = $db->fetchRow($query, array($date,$job_id));

	}
	
	
	
	

	

	

	public function getJobDetailsByJobId($job_id)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

	   $query = "SELECT cl.*, cl.Call_Log_No as job_id,

			if(DATE(cl.job_track_status_time) = ? and (cl.job_track_status='toward_site' OR cl.job_track_status='at_site' OR cl.job_track_status='verify_site'), cl.job_track_status, '') as job_track_status,pt.Descr as product_descr,

			lu.StaffCode, lu.StaffName

					FROM call_log AS cl LEFT JOIN product_type AS pt ON (cl.DescrID=pt.DescrID)

					INNER JOIN local_user_mapping as lu on (lu.CMSStaffStockCode=cl.curr_Alloted_Eng_Code)

					WHERE md5(concat(cl.Call_Log_No,'sendid'))=?"; 

				

		return $result = $db->fetchRow($query, array($date,$job_id));

	}

	

	

	public function updateJobPriorityByJobId($priority, $job_id){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("UPDATE call_log SET Call_Priority_ID=? WHERE md5(concat(Call_Log_No,'sendid')) =?", array($priority,$job_id));

	}

	

	

	public function updateJobScheduleByJobId($schedule, $job_id){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date = date('Y-m-d H:i:s');

		$db->query("UPDATE call_log SET job_Scheduling_Status=?,job_Scheduling_Date=? WHERE md5(concat(Call_Log_No,'sendid'))=?", array($schedule,$date,$job_id));

	}

	

	

	public function getAllTicketsByUserId($get,$user_id,$un_title,$un_sortby)

	{ 

		$db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		

		if($get['search'])

		{

			$search = $get['search'];

			$cond = " AND (cl.Call_Log_Desrc like '%$search%')";

		}

		

		if($un_title=="tt")

		{

		   $sort_title = 'cl.Call_Log_No';

		}

		else if($un_title=="customer")

		{

		   $sort_title = 'Customer_Name';

		}

		else if($un_title=="site")

		{

		   $sort_title = 'Site_ID';

		}

		else if($un_title=="status")

		{

		   $sort_title = 'job_process_status';

		}

		else if($un_title=="call_type")

		{

		   $sort_title = 'Call_Type_Code';

		}



		

	    if($sort_title !="" && $un_sortby !="")

		{

		   $order = $sort_title.' '.$un_sortby;

		}

		else

		{

		    $order = ' job_Schedule_number asc';

		}

	

		

		$curDate = date("Y-m-d");

		

		
		$cond .="";

	     $query = "SELECT cl.EntrySrNo,cl.Call_Log_No,cl.Customer_ID,cl.Customer_Name,cl.Circle_Code,cl.Circle_Name,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Call_Log_Desrc,cl.Call_Priority_ID,cl.Call_Status_ID,cl.Logged_User_Code,cl.job_status, cl.job_process_status, if(DATE(job_Scheduling_Date) = ?,cl.job_Scheduling_Status,'noschedule') as job_Schedule_number, cl.job_Scheduling_Status, cl.Call_Type_Code, pt.ComplaintDescrGroup AS product_fsr_type FROM  call_log AS cl 

		LEFT JOIN  product_type AS pt ON (pt.DescrID=cl.DescrID)

		WHERE 1  AND cl.curr_Alloted_Eng_Code =? $cond order by $order";   

		return $result = $db->fetchAll($query,array($curDate,$user_id));

	}

	

	

	public function getAllJobs($CMSStaffStockCode)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$cond = "";

		$date = date('Y-m-d');

		/*$cond = " and (

		(cl.job_process_status!='Complete') OR 

		(cl.job_process_status='Complete' and DATE(cl.tt_closed_date)='".$date."')

		)";*/

		if($CMSStaffStockCode)

		{

			$cond .= " and cl.curr_Alloted_Eng_Code='".$CMSStaffStockCode."'";

		}



	     $query = "SELECT slm.latitude as slm_latitude, slm.longitude as slm_longitude,slm.update_status as slm_status_flag,cl.id,cl.EntrySrNo,cl.Call_Log_No,cl.Call_Log_No as job_id,

		if(cl.IndusTTDT!=null and cl.IndusTTDT!='', cl.IndusTTDT, cl.Call_Log_DT) as Call_Log_DT_Aging,

		if(cl.IndusTTDT, DAYOFWEEK(cl.IndusTTDT), DAYOFWEEK(cl.Call_Log_DT)) as Call_Log_Aging_Week,if(DATE(cl.job_Scheduling_Date)='".$date."', cl.job_Scheduling_Status, 10000) as job_Scheduling_Status_new,

		cl.Customer_ID,cl.Customer_Name,cl.Circle_Code,cl.Circle_Name,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Call_Log_Desrc,

		cl.Call_Priority_ID,cl.Call_Status_ID,cl.Logged_User_Code,cl.job_status,cl.IndusSiteID, cl.IndusTTNumber,

		cl.job_process_status,

		cl.site_latitude,cl.site_longitude,cl.Call_Type_Code,

		if(DATE(cl.job_Scheduling_Date)='".$date."', cl.job_Scheduling_Status, 0) as 

		job_Scheduling_Status,pt.ComplaintDescrGroup as product_type,pt.Descr as product_descr,

		if(cl.job_process_status='Complete' OR cl.job_process_status='Close_with_pending', cl.job_process_status,

		if(

		DATE(cl.job_track_status_time) = '".$date."' and (cl.job_track_status='toward_site' OR cl.job_track_status='at_site' OR cl.job_track_status='verify_site' ), cl.job_track_status, cl.job_process_status

		)

		) as job_track_status,

		cl.CallRaisedBy, cl.CallRaisedContactNO, cl.curr_Alloted_Eng_Code

		FROM call_log AS cl LEFT JOIN product_type AS pt ON (cl.DescrID=pt.DescrID) 
                LEFT JOIN site_location_mapping as slm ON (cl.site_id=slm.site_id)

		where 1 

		

		$cond 

		ORDER BY job_Scheduling_Status_new asc,Call_Log_DT_Aging desc,Call_Priority_ID asc";  

		//and (cl.job_process_status!='Complete' OR (cl.job_process_status='Complete' and DATE(job_accepted_time)='$date'))

		return $result = $db->fetchAll($query,array($date, $date));

	}

	

	

	public function updateJobStatusForAccept($user_id, $job_id, $offlineTime){  

		$db =  Zend_Db_Table::getDefaultAdapter();
                
                if(isset($offlineTime) and $offlineTime!=''){
                   $accept_date =  $offlineTime;
                }else{
                 $accept_date = date('Y-m-d H:i:s');   
                }
		

		$db->query("UPDATE call_log SET job_status='accepted',job_accepted_time='".$accept_date."' where curr_Alloted_Eng_Code=? and Call_Log_No =?", array($user_id,$job_id));

	}

	

	

	/**

	* updateJobSiteAddByUserUniqueId() method is used to update job site address

	* @param Integer

	* @return Array 

	*/	

	public function updateJobSiteAddByCMSStaffStockCode($userUniqueId, $job_id,$SiteAdd1,$SiteAdd2,$site_latitude,$site_longitude){  

		$db = Zend_Db_Table::getDefaultAdapter();

		$jobSiteAdd1 = addslashes($SiteAdd1); 

		$jobSiteAdd2 = addslashes($SiteAdd2);

		$db->query("UPDATE call_log SET SiteAdd1=?,SiteAdd2=?,site_latitude=?,site_longitude=? where curr_Alloted_Eng_Code=? and Call_Log_No =? ", array($jobSiteAdd1,$jobSiteAdd2,$site_latitude,$site_longitude,$userUniqueId,$job_id));

	}

	

	

	/**

	* updateJobOrderByJobId() method is used to update job order change

	* @param Integer

	* @return True 

	*/	

	public function updateJobOrderByJobId($key, $value){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("UPDATE call_log SET change_order='".$key."' WHERE EntrySrNo='".$value."' ");

	}

	



	/**

	* insertNewJob() method is used to insert new job

	* @param Array

	* @return True 

	*/	

	public function insertNewJob($insertData){   

		$db = Zend_Db_Table::getDefaultAdapter();

		 $db->insert("call_log",$insertData);

		return $result = $db->lastInsertId(); 

	}

	

	

	/**

	* insertNewJobAssign() method is used to insert new job assign data

	* @param Array

	* @return True 

	*/	

	public function insertNewJobAssign($insertData){   

		$db =  Zend_Db_Table::getDefaultAdapter();

		return $result = $db->insert("job_alloted_eng_history",$insertData);

	}

	

	

	/**

	* updateNewJobAssign() method is used to update new job assign of eng

	* @param Integer

	* @return True 

	*/	

	public function updateNewJobAssign($jobId, $staffCode,$date){  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$db->query("UPDATE call_log SET job_status='', curr_Alloted_Eng_Code=?,curr_Alloted_Eng_Date=? WHERE md5(concat(Call_Log_No,'sendid'))=?",array($staffCode,$date,$jobId));



	}

	

	

	/**

	* checkJobSiteIdBySiteId() method is used to check job site id already exists by site id

	* @param string

	* @return True 

	*/	

	public function checkJobSiteIdBySiteId($site_id){  

		$db =  Zend_Db_Table::getDefaultAdapter();

        $sqlQuery = "SELECT count(*) As total_record FROM call_log WHERE Site_ID ='".$site_id."' ";

		return $db->fetchOne($sqlQuery);

	}

	

	

	/**

	* getAllProductType() method is used to get all spare parts list by eng code

	* @param String

	* @return Array 

	*/	

    public function getAllProductType()

	{  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$PTArr = array();

		//$PTArr = array("AC","PIU");

	 	$query = $db->select()

						   ->from('product_type',array())

						   ->columns(array('DescrID','Descr'))

						   //->where('ComplaintDescrGroup IN (?)',$PTArr)

						    ->where('status =?','1');  

		return $result = $db->fetchAll($query);

	}

	

	

	/**

	* getLatLongDetailsByJobId() method is used to get all spare parts list by eng code

	* @param String

	* @return Array 

	*/	

    public function getLatLongDetailsByJobId($job_id)

	{  

		$db =  Zend_Db_Table::getDefaultAdapter();

	 	$query = $db->select()

						   ->from('call_log',array())

						   ->columns(array('Call_Log_No','site_latitude','site_longitude'))

						   ->where('Call_Log_No =?',$job_id);

		return $result = $db->fetchRow($query);

	}

	

	

	/**

	* distance() method is used to get distance between two lat long

	* @param String

	* @return Array 

	*/	

	public function distance($lat1, $lon1, $lat2, $lon2, $unit) {

	  $theta = $lon1 - $lon2;

	  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));

	  $dist = acos($dist);

	  $dist = rad2deg($dist);

	  $miles = $dist * 60 * 1.1515;

	  $unit = strtoupper($unit);

	 

	  if ($unit == "K") {

		$kilometers = ($miles * 1.609344);

		return $meters = ($kilometers * 1000);

	  } else if ($unit == "N") {

		  return ($miles * 0.8684);

		} else {

			return $miles;

		  }

	}

	

	

	/**

	* getFsrJobDetailsByJobId() method is used to get all fsr job details

	* @param String

	* @return Array 

	*/	

	public function getFsrJobDetailsByJobId($job_id)

	{  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

	    $query = "SELECT cl.Call_Log_No,pt.ComplaintDescrGroup AS fsr_type FROM call_log AS cl LEFT JOIN product_type AS pt ON (pt.DescrID = cl.DescrID) WHERE md5(concat(cl.Call_Log_No,'sendid'))=?";  

		return $result = $db->fetchRow($query, array($job_id));

	}

	

	

	/**

	* getJobAcFsrDetailsByJobId() method is used to get all fsr details

	* @param String

	* @return Array 

	*/	

	
	
	public function getJobAcFsrDetailsByJobId($job_id,$fsrId)

	{  

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

		$ac_fsr_query = "SELECT fa.*,fd.Call_Log_No,fd.fsr_where_closed,fd.job_process_status,fd.job_process_status_time,fd.job_track_status_time as AttendenetTime,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Customer_Name,cl.Call_Log_Desrc,cl.Call_Type_Code,cl.job_track_status_time,cl.Circle_Name,cl.Cluster,lum.StaffName FROM fsr_detail AS fd INNER JOIN fsr_ac AS fa ON (fd.id=fa.fsr_detail_id) INNER JOIN call_log AS cl ON(cl.Call_Log_No=fd.Call_Log_No) INNER JOIN local_user_mapping AS lum ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) WHERE fd.fsr_type='AC' and md5(concat(fd.Call_Log_No,'sendid'))=? and fd.id=? ORDER BY fd.id DESC"; 
		$fsr_result = $db->fetchRow($ac_fsr_query, array($job_id,$fsrId));

		//echo "<pre>";print_r($fsr_result);die; 

		return $fsr_result;

	}

	

	

	/**

	* getJobPiuFsrDetailsByJobId() method is used to get all fsr details

	* @param String

	* @return Array 

	*/	

	public function getJobPiuFsrDetailsByJobId($job_id,$fsrId)

	{  
        
		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

		 $piu_fsr_query = "SELECT fp.*,fd.Call_Log_No,fd.fsr_where_closed,fd.job_process_status,fd.job_process_status_time,fd.job_track_status_time as AttendenetTime,fd.fsr_fill_date,cl.Customer_Name,cl.IndusTTNumber,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Call_Type_Code,cl.Call_Log_Desrc,cl.job_track_status_time,cl.Circle_Name,cl.Cluster,lum.StaffName FROM fsr_detail AS fd INNER JOIN fsr_piu AS fp ON (fd.id=fp.fsr_detail_id) INNER JOIN call_log AS cl ON(cl.Call_Log_No=fd.Call_Log_No) INNER JOIN local_user_mapping AS lum ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) WHERE fd.fsr_type='PIU' and md5(concat(fd.Call_Log_No,'sendid'))=? and fd.id=? ORDER BY fd.id DESC"; 
		
		

		$fsr_result = $db->fetchRow($piu_fsr_query, array($job_id,$fsrId));

		return $fsr_result;

	}

	

	public function changeDayWiseJobStatus($job_id)

	{

		$db =  Zend_Db_Table::getDefaultAdapter();

		$date =date("Y-m-d");

	

				// set job status day wise

				$value = $db->fetchRow("select * from call_log where Call_Log_No=?", array($job_id));

				$CMSStaffStockCode = $value['curr_Alloted_Eng_Code'];

				if(count($value))

				{

					if(!$value['job_status'])

					{

						if($value['Allot_To_Engg_Code'] == $value['curr_Alloted_Eng_Code'])

						{

							$status = 'Unaccepted';

						}

						else

						{

							$status = 'Reallocated';

						}

					}

					else

					{

						if(!$value['job_process_status'] && $value['job_status'] == 'accepted')

						{

							$status = 'Accepted';

						}

						else if($value['job_process_status'] == 'processing')

						{

							$status = 'Open';

						}

						else if($value['job_process_status'] == 'Complete')

						{

							$status = 'Closed';

						}

						else

						{

							$status = 'Pending';

						}

					}

					$djob = $db->fetchRow("select * from day_wise_ticket_status where Call_Log_No=? and track_date=?", array($value['Call_Log_No'],$date));

					if($djob['Call_Log_No'])

					{

					

						$sqlQuery ="INSERT INTO day_wise_ticket_status

						(mob_user_staff_code, mob_user_cmsstaff_code, Call_Log_No, schedule_status,job_latitude, 	job_longitude,job_process_status,track_date,real_job_process_status)

						VALUES('".$userStaffCode."','".$CMSStaffStockCode."','".$value['Call_Log_No']."','".$value['job_Scheduling_Status']."','".$value['site_latitude']."','".$value['site_longitude']."','$status','".$date."','".$value['job_process_status']."')

						ON DUPLICATE KEY 

						

						UPDATE mob_user_staff_code= '".$userStaffCode."', mob_user_cmsstaff_code='".$CMSStaffStockCode."',schedule_status='".$value['job_Scheduling_Status']."',job_latitude='".$value['site_latitude']."',job_longitude='".$value['site_longitude']."',job_process_status= '$status',real_job_process_status='".$value['job_process_status']."' ";

			

						/*$db->query("update day_wise_ticket_status set job_process_status='$status',schedule_status='".$value['job_Scheduling_Status']."', job_latitude='".$value['site_latitude']."',job_longitude='".$value['site_longitude']."',real_job_process_status='".$value['job_process_status']."' where Call_Log_No='".$djob['Call_Log_No']."' and track_date='".$date."'");

						*/

					}

					else

					{

             			$user_data = $db->fetchRow("select StaffCode from local_user_mapping where CMSStaffStockCode=?", array($CMSStaffStockCode));	

                        $userStaffCode = $user_data['StaffCode'];	

						

						

						$sqlQuery ="INSERT INTO day_wise_ticket_status

						(mob_user_staff_code, mob_user_cmsstaff_code, Call_Log_No, schedule_status,job_latitude, 	job_longitude,job_process_status,track_date,real_job_process_status)

						VALUES('".$userStaffCode."','".$CMSStaffStockCode."','".$value['Call_Log_No']."','".$value['job_Scheduling_Status']."','".$value['site_latitude']."','".$value['site_longitude']."','$status','".$date."','".$value['job_process_status']."')

						ON DUPLICATE KEY 

						

						UPDATE mob_user_staff_code= '".$userStaffCode."', mob_user_cmsstaff_code='".$CMSStaffStockCode."',schedule_status='".$value['job_Scheduling_Status']."',job_latitude='".$value['site_latitude']."',job_longitude='".$value['site_longitude']."',job_process_status= '$status',real_job_process_status='".$value['job_process_status']."' ";

			

						/* $db->query("insert into day_wise_ticket_status set  

						mob_user_staff_code='".$userStaffCode."',

						mob_user_cmsstaff_code='".$CMSStaffStockCode."',

						job_latitude='".$value['site_latitude']."', 	

						job_longitude='".$value['site_longitude']."',

						schedule_status='".$value['job_Scheduling_Status']."',

						job_process_status='$status', 

						Call_Log_No='".$value['Call_Log_No']."', 

						track_date='".$date."',

						real_job_process_status='".$value['job_process_status']."'

						");

						*/

						

					}

				}

		

		

	}

	

	

	/**

	* getJobAcFsrDetailsByJobId() method is used to get all fsr details

	* @param String

	* @return Array 

	*/	

	public function getAllFsrByJobId($job_id)

	{  

		$db = Zend_Db_Table::getDefaultAdapter();

		$fsr_query = "SELECT fd.Call_Log_No,fd.fsr_where_closed,fd.job_process_status,fd.job_process_status_time,fd.fsr_type,fd.fsr_filled,fd.id FROM fsr_detail AS fd WHERE md5(concat(fd.Call_Log_No,'sendid'))=? ORDER BY fd.id DESC"; 

		$fsr_result = $db->fetchAll($fsr_query, array($job_id));

		//echo "<pre>";print_r($fsr_result);die;

		return $fsr_result;

	}

	
     /// function written by abhishek mishra
	 
	function getAllCompletedJob()
	{
			
		 	$db =  Zend_Db_Table::getDefaultAdapter();
			$day_value = 3;
			$this->Crd = date("Y-m-d").' 00:00:00';
		         $query = "SELECT * FROM `call_log` WHERE DATEDIFF('$this->Crd', job_process_status_time) >= ? and job_process_status='Complete' and pdf_status='yes' limit 4000";
                       
                        return $result = $db->fetchAll($query, array($day_value));
        }
	
	
	public function insertCompletedJob($insertData){   
            $db =  Zend_Db_Table::getDefaultAdapter();
            return $result = $db->insert("call_log_completed",$insertData);
          }
		  
		  
         public function removeCalllogfromTble($call_log_id)
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->delete('call_log', array('id = ?' => $call_log_id));
	}
	
	
	
	
	
	public function getAllCompletedFsrForPDF()
	{
//                        $getDate = date("Y-m-d"); 
//                        $CurrentDate = strtotime($getDate);
//                        $onedayPrevious = strtotime('-1 day', $CurrentDate);
//                        $RealDate = date('Y-m-d', $onedayPrevious);
                        $start = '2015-05-18';
                        $end = '2015-05-20';
                        //$cond = " and Date(fsr_fill_date) <= '".$RealDate."'"; 
                        $cond = " and Date(fsr_fill_date) between '".$start."' and '".$end."'";  
                        $db =  Zend_Db_Table::getDefaultAdapter();
                        $all_new_fsr = "SELECT * from fsr_detail WHERE fsr_type!='Other'  and fsr_filled!='no' and pdf_status!='yes'   $cond";
                        return $result = $db->fetchAll($all_new_fsr, array());
	}
        
        public function UpdateFsrConvertStatus($jobId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $db->query("UPDATE call_log SET pdf_status='yes' WHERE Call_Log_No =?", array($jobId));
        }
        public function UpdateFsrConvertStatusInfsrTable($fsrId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $db->query("UPDATE fsr_detail SET pdf_status='yes' WHERE id =?", array($fsrId));
        }
	
	
	public function getJobPiuFsrDetail($job_id,$fsrId){ 
	    $db =  Zend_Db_Table::getDefaultAdapter();
		$piu_fsr_query = "SELECT fp.*,fd.Call_Log_No,fd.fsr_where_closed,fd.job_process_status,fd.job_process_status_time,fd.job_track_status_time as AttendentTime,fd.fsr_fill_date,cl.Customer_Name,cl.Site_ID,cl.SiteAdd1,cl.SiteDescr,cl.Call_Type_Code,cl.Call_Log_Desrc,cl.job_track_status_time,cl.Circle_Name,cl.Cluster,cl.IndusTTNumber,lum.StaffName FROM fsr_detail AS fd INNER JOIN fsr_piu AS fp ON (fd.id=fp.fsr_detail_id) INNER JOIN call_log AS cl ON(cl.Call_Log_No=fd.Call_Log_No) INNER JOIN local_user_mapping AS lum ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code) WHERE fd.fsr_type='PIU' and fd.Call_Log_No=? and fd.id=? ORDER BY fd.id DESC"; 
		$fsr_result = $db->fetchRow($piu_fsr_query, array($job_id,$fsrId));
		return $fsr_result;
	}
	
	public function getJobAcFsrDetail($job_id,$fsrId){  
        $db =  Zend_Db_Table::getDefaultAdapter();
       $ac_fsr_query = "SELECT fa.*,fd.Call_Log_No,fd.fsr_where_closed,fd.job_process_status,fd.job_process_status_time,fd.job_track_status_time as Attendenttime,cl.Site_ID,cl.SiteDescr,cl.SiteAdd1,cl.Customer_Name,cl.Call_Log_Desrc,cl.Call_Type_Code,cl.job_track_status_time,cl.Circle_Name,cl.Cluster,lum.StaffName FROM fsr_detail AS fd INNER JOIN fsr_ac AS fa ON (fd.id=fa.fsr_detail_id) INNER JOIN call_log AS cl ON(cl.Call_Log_No=fd.Call_Log_No) INNER JOIN local_user_mapping AS lum ON (lum.CMSStaffStockCode=cl.curr_Alloted_Eng_Code)  WHERE fd.fsr_type='AC' and fd.Call_Log_No=? and fd.id=? ORDER BY fd.id DESC"; 
	   $fsr_result = $db->fetchRow($ac_fsr_query, array($job_id,$fsrId));
        return $fsr_result;

	}
        
        
        
         public function getEntryValidate($job_id,$timestamp)
        {
            $set = false;
                        if($timestamp == ''){
                            $timestamp = date("Y-m-d H:i:s");
                        }
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select fd.Call_Log_No,fd.fsr_fill_date from fsr_detail AS fd  WHERE fd.Call_Log_No=? and fd.fsr_fill_date=?";
            $result = $db->fetchAll($query, array($job_id,$timestamp));
            if($result){
              $set = true;  
            }
            return $set;
        }
	
	 public function getNotificationEntryValidator($job_id,$timestamp)
        {
          $set=false;
          
                        if($timestamp == ''){
                            $timestamp = date("Y-m-d H:i:s");
                        }
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select noti.noti_date,noti.Call_Log_No from notification AS noti  WHERE noti.Call_Log_No=? and noti.noti_date=?";
            $result = $db->fetchAll($query, array($job_id,$timestamp));
            if($result){
                $set=true;
            }
            return $set;
        } 
        
        public function UpdateDataSinkTime($StaffId)
        {
            $db =  Zend_Db_Table::getDefaultAdapter();
            $sinkTime = date('Y-m-d H:i:s');
            $db->query("UPDATE local_user_mapping SET last_data_sink=? WHERE StaffCode =?", array($sinkTime,$StaffId));
         }
         
         public function UpdateDataAvailable($StaffId,$dataAmount){
            $db =  Zend_Db_Table::getDefaultAdapter();
             $sql = "update local_user_mapping set pending_data='".$dataAmount."' where StaffCode='".$StaffId."'";
            $db->query($sql);
           
           // $db->query("UPDATE local_user_mapping SET pending_data=? WHERE StaffCode =?", array($dataAmount,$StaffId));
         }
                 
         
         
         
          public function UpdateSiteLatLongStatus($dataArray){
             $db =  Zend_Db_Table::getDefaultAdapter();
             $query = "SELECT * FROM  `site_location_mapping` WHERE site_id = '".$dataArray['site_id']."'";
	     $result = $db->fetchAll($query, array());
             if($result){
                 $querydata = "update site_location_mapping set latitude='".$dataArray['latitude']."', longitude='".$dataArray['longitude']."',staff_code='".$dataArray['staff_code']."',update_status='".$dataArray['update_status']."',last_updated_date='".$dataArray['last_updated_date']."' where site_id='".$dataArray['site_id']."'";
                 $db->query($querydata);
             }else{
                $querydata = "insert into  site_location_mapping set 
                `site_id`='".$dataArray['site_id']."',
                `latitude`='".$dataArray['latitude']."',
                `longitude`='".$dataArray['longitude']."',
                `staff_code`='".$dataArray['staff_code']."',
                `update_status`='".$dataArray['update_status']."',
                `last_updated_date`='".$dataArray['last_updated_date']."'"; 
                $db->query($querydata);
             }
         }
         
         public function UpdateTrackStatusTimeToFsr($call_log_no,$fsr_Id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $findSqlfortrackusercall = "select Call_Log_No,job_track_status_time from call_log where Call_Log_No='".$call_log_no."'";           
            $result = $db->fetchRow($findSqlfortrackusercall, array());
            $tracktime = $result['job_track_status_time'];
            $updateTrackTimeOnfsr = "update fsr_detail set job_track_status_time='".$tracktime."' where id='".$fsr_Id."'";
            $db->query($updateTrackTimeOnfsr);
         }
         
         public function InsertUserLog($dataArray){
              $db =  Zend_Db_Table::getDefaultAdapter();
              $timestamp = date("Y-m-d H:i:s");
                 $querydata = "insert into engg_log set 
                `Call_Log_No`='".$dataArray['Call_Log_No']."',
                `logged_status`='".$dataArray['logged_status']."',
                `logged_date_time`='".$dataArray['logged_date_time']."',
                `mob_user_staff_code`='".$dataArray['mob_user_staff_code']."',
                `datasinked`='".$timestamp."'"; 
                $db->query($querydata);
         }
}

