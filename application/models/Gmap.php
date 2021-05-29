<?php
class Application_Model_Gmap extends Zend_Db_Table_Abstract
{

	public function getCustomerAllJobs($params,$days='')
	{
		$db =  Zend_Db_Table::getDefaultAdapter();
		$cond = "";
		$condarray = "";

		$date = date('Y-m-d');
		//print_r($params); 
		if($params['customer']!='')
		{
			$condarray[] = "cl.Customer_Name='".$params['customer']."'";
		}

		if($params['circle']!='')
		{
			$condarray[] = "cl.Circle_Code='".$params['circle']."'";
		}

		if($params['call_type']!='')
		{
			$condarray[] = "cl.Call_Type_Code='".$params['call_type']."'";
		}

		if($params['product_type']!='')
		{
			$condarray[] = "cl.DescrID='".$params['product_type']."'";
		}
				
		if($days!='')
		{
			$condarray[] = "cl.Call_log_DT >=(NOW() - INTERVAL ".$days." DAY)";
		}
		
		$condarray[] = "(cl.site_latitude!='' OR slm.latitude!='')";
		
	    $cond = implode(' AND ', $condarray);
		 $query = "SELECT slm.latitude as slm_latitude, slm.longitude as slm_longitude, cl.id,cl.EntrySrNo,cl.Call_Log_No,cl.Call_Log_No as job_id,
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
		FROM call_log AS cl LEFT JOIN product_type AS pt ON (cl.DescrID=pt.DescrID) LEFT JOIN site_location_mapping as slm ON (cl.site_id=slm.site_id) 
		where $cond
		 group by cl.site_id order by cl.id desc";  
		//echo '<br>Q:'.$query;die;
		return $result = $db->fetchAll($query,array($date, $date));
	}
	
public function productTypeList(){
	
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT * from product_type"; 
		return $result = $db->fetchAll($query);
	}

public function callTypeList(){
	
return array(
"PM",
"BD",
"I&C",
"OTR",
"CC",
"MISC",
"EMS",
"I&CO",
"SB",
"OD"
		);
	}

public function circleList(){
	
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT CircleCode, CircleName FROM acme.circle_master order by CircleName asc;"; 
		return $result = $db->fetchAll($query);
	}
public function customerList(){
	
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT distinct Customer_Name FROM acme.call_log order by Customer_Name ASC	
"; 
		return $result = $db->fetchAll($query);
	}


public function getUsersCurrentLoc($users){
	$user_keys = array_keys($users);
	$db = Zend_Db_Table::getDefaultAdapter();	
	$select = $db->select()->from(array("lum"=>"local_user_mapping"));
	$select->where('cmsstaffstockcode IN(?)', $user_keys)->where('StaffStatus=?','AC');
	//echo '<br>uc:'.$select->__toString();
	$stmt = $db->query($select);
	return $stmt->fetchAll();

	}	
		
}
