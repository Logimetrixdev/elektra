<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);

// get last id in mysql call log table
$mclsql = mysql_query("select max(EntrySrNo) as topEntrySrNo from call_log");
$mcfetch = mysql_fetch_assoc($mclsql);
$topEntrySrNo = $mcfetch['topEntrySrNo']?$mcfetch['topEntrySrNo']:0;

//$call_log = mssql_query("select * from Call_Log where Call_Log_No='R001/15/03/2014/1337'");

//$call_log = mssql_query("select * from Call_Log where Call_Status_ID=2 ");

$call_log = mssql_query("select TOP 10000 * from Call_Log where Call_Status_ID=2 and EntrySrNo >'$topEntrySrNo' order by EntrySrNo asc");

//$call_log = mssql_query("select  * from Call_Log where Allot_To_Engg_Code='CDL107'") or die('query error');

while($fetch_call_log_old = mssql_fetch_assoc($call_log))
{
	/*echo '<pre>';
	$time = strtotime($fetch_call_log_old['Call_Log_DT']);
	echo date("Y-m-d H:i:s", $fetch_call_log_old['Call_Log_DT']);
	print_r($fetch_call_log_old); 
	die;*/
	
	$fetch_call_log = filtorWithEscape($fetch_call_log_old);
	
	$callnumber = $fetch_call_log['Call_Log_No'];  
	$calldata = mysql_query("select * from call_log where Call_Log_No='".$callnumber."'");
	
	
	$fetch = mysql_fetch_assoc($calldata);
	//print_r($fetch);
	//die;
	if($fetch['Call_Log_No']=="")
	{ 
	    $query = "INSERT INTO call_log SET 
		EntrySrNo='".$fetch_call_log['EntrySrNo']."',
		Call_Log_No='".$fetch_call_log['Call_Log_No']."',
		Call_Log_DT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['Call_Log_DT']))."',
		Call_Enterd_As_ID='".$fetch_call_log['Call_Enterd_As_ID']."',
		Customer_ID='".$fetch_call_log['Customer_ID']."',
		Customer_Name='".$fetch_call_log['Customer_Name']."',
		Circle_Code='".$fetch_call_log['Circle_Code']."',
		Circle_Name='".$fetch_call_log['Circle_Name']."',
		InternalSiteID='".$fetch_call_log['InternalSiteID']."',
		Site_ID='".$fetch_call_log['Site_ID']."',
		SiteDescr='".$fetch_call_log['SiteDescr']."',
		SiteAdd1='".$fetch_call_log['SiteAdd1']."',
		SiteAdd2='".$fetch_call_log['SiteAdd2']."',
		OperatorStatus='".$fetch_call_log['OperatorStatus']."',
		CallRaisedBy='".$fetch_call_log['CallRaisedBy']."',
		CallRaisedContactNO='".$fetch_call_log['CallRaisedContactNO']."',
		Call_Type_Code='".$fetch_call_log['Call_Type_Code']."',
		Cluster='".$fetch_call_log['Cluster']."',
		DescrID='".$fetch_call_log['DescrID']."',
		Call_Log_Desrc='".$fetch_call_log['Call_Log_Desrc']."',
		OfflineCallReson='".$fetch_call_log['OfflineCallReson']."',
		Call_Priority_ID='".$fetch_call_log['Call_Priority_ID']."',
		Call_Status_ID='".$fetch_call_log['Call_Status_ID']."',
		Allot_To_Engg_Code='".$fetch_call_log['Allot_To_Engg_Code']."',
		Allot_To_Engg_Name='".$fetch_call_log['Allot_To_Engg_Name']."',
		Allot_To_Engg_Phone='".$fetch_call_log['Allot_To_Engg_Phone']."',
		Sch_DT='".$fetch_call_log['Sch_DT']."',
		ResponseDT='".$fetch_call_log['ResponseDT']."',
		AttendingDT='".$fetch_call_log['AttendingDT']."',
		RemarksInAllot='".$fetch_call_log['RemarksInAllot']."',
		Call_Cancel_Reason='".$fetch_call_log['Call_Cancel_Reason']."',
		Call_Cancel_Approver='".$fetch_call_log['Call_Cancel_Approver']."',
		Call_Cancel_DT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['Call_Cancel_DT']))."',
		Call_Cancel_Log_User='".$fetch_call_log['Call_Cancel_Log_User']."',
		RemarksInCaseCancel='".$fetch_call_log['RemarksInCaseCancel']."',
		LastPMDate='".date("Y-m-d H:i:s", strtotime($fetch_call_log['LastPMDate']))."',
		PMDays='".$fetch_call_log['PMDays']."',
		DueDate='".date("Y-m-d H:i:s", strtotime($fetch_call_log['DueDate']))."',
		Logged_User_Code='".$fetch_call_log['Logged_User_Code']."',
		Delete_Status='".$fetch_call_log['Delete_Status']."',
		Logged_AllotUserCode='".$fetch_call_log['Logged_AllotUserCode']."',
		OpenCallReasonNo='".$fetch_call_log['OpenCallReasonNo']."',
		CallOpenRemarks='".$fetch_call_log['CallOpenRemarks']."',
		RemarksResolvedByPhone='".$fetch_call_log['RemarksResolvedByPhone']."',
		RemarksResolvedByPhoneDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['RemarksResolvedByPhoneDT']))."',
		RemarksResolvedByEngg='".$fetch_call_log['RemarksResolvedByEngg']."',
		RemarksResolvedByEnggDT='".$fetch_call_log['RemarksResolvedByEnggDT']."',
		FSRType='".$fetch_call_log['FSRType']."',
		FSREnterdDT='".$fetch_call_log['FSREnterdDT']."',
		FSRSignedBy='".$fetch_call_log['FSRSignedBy']."',
		FSRRemarks='".$fetch_call_log['FSRRemarks']."',
		AttachFilePath='".$fetch_call_log['AttachFilePath']."',
		PIUSrNo='".$fetch_call_log['PIUSrNo']."',
		GasFilledInNoOfAc='".$fetch_call_log['GasFilledInNoOfAc']."',
		MyComments='".$fetch_call_log['MyComments']."',
		Current_OpenCallDetailNo='".$fetch_call_log['Current_OpenCallDetailNo']."',
		IndusSiteID='".$fetch_call_log['IndusSiteID']."',
		FSRCopyRecd='".$fetch_call_log['FSRCopyRecd']."',
		ActualEntryDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['ActualEntryDT']))."',
		FSREnteredBy='".$fetch_call_log['FSREnteredBy']."',
		IndusTTNumber='".$fetch_call_log['IndusTTNumber']."',
		IndusTTDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['IndusTTDT']))."',
		CallLoggedFrom='".$fetch_call_log['CallLoggedFrom']."',
		CostCenter='".$fetch_call_log['CostCenter']."',
		UPRNo='".$fetch_call_log['UPRNo']."',
		CDINo='".$fetch_call_log['CDINo']."',
		
		curr_Alloted_Eng_Code='".$fetch_call_log['Allot_To_Engg_Code']."',
		curr_Alloted_Eng_Date='".date('Y-m-d H:i:s')."'
		"; 
		mysql_query($query) or die(mysql_error());
	}
	else
	{
		$query = "UPDATE call_log SET 
		EntrySrNo='".$fetch_call_log['EntrySrNo']."',
		Call_Log_No='".$fetch_call_log['Call_Log_No']."',
		Call_Log_DT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['Call_Log_DT']))."',
		Call_Enterd_As_ID='".$fetch_call_log['Call_Enterd_As_ID']."',
		Customer_ID='".$fetch_call_log['Customer_ID']."',
		Customer_Name='".$fetch_call_log['Customer_Name']."',
		Circle_Code='".$fetch_call_log['Circle_Code']."',
		Circle_Name='".$fetch_call_log['Circle_Name']."',
		InternalSiteID='".$fetch_call_log['InternalSiteID']."',
		Site_ID='".$fetch_call_log['Site_ID']."',
		SiteDescr='".$fetch_call_log['SiteDescr']."',
		SiteAdd1='".$fetch_call_log['SiteAdd1']."',
		SiteAdd2='".$fetch_call_log['SiteAdd2']."',
		OperatorStatus='".$fetch_call_log['OperatorStatus']."',
		CallRaisedBy='".$fetch_call_log['CallRaisedBy']."',
		CallRaisedContactNO='".$fetch_call_log['CallRaisedContactNO']."',
		Call_Type_Code='".$fetch_call_log['Call_Type_Code']."',
		Cluster='".$fetch_call_log['Cluster']."',
		DescrID='".$fetch_call_log['DescrID']."',
		Call_Log_Desrc='".$fetch_call_log['Call_Log_Desrc']."',
		OfflineCallReson='".$fetch_call_log['OfflineCallReson']."',
		Call_Priority_ID='".$fetch_call_log['Call_Priority_ID']."',
		Call_Status_ID='".$fetch_call_log['Call_Status_ID']."',
		Allot_To_Engg_Code='".$fetch_call_log['Allot_To_Engg_Code']."',
		Allot_To_Engg_Name='".$fetch_call_log['Allot_To_Engg_Name']."',
		Allot_To_Engg_Phone='".$fetch_call_log['Allot_To_Engg_Phone']."',
		Sch_DT='".$fetch_call_log['Sch_DT']."',
		ResponseDT='".$fetch_call_log['ResponseDT']."',
		AttendingDT='".$fetch_call_log['AttendingDT']."',
		RemarksInAllot='".$fetch_call_log['RemarksInAllot']."',
		Call_Cancel_Reason='".$fetch_call_log['Call_Cancel_Reason']."',
		Call_Cancel_Approver='".$fetch_call_log['Call_Cancel_Approver']."',
		Call_Cancel_DT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['Call_Cancel_DT']))."',
		Call_Cancel_Log_User='".$fetch_call_log['Call_Cancel_Log_User']."',
		RemarksInCaseCancel='".$fetch_call_log['RemarksInCaseCancel']."',
		LastPMDate='".$fetch_call_log['LastPMDate']."',
		PMDays='".$fetch_call_log['PMDays']."',
		DueDate='".$fetch_call_log['DueDate']."',
		Logged_User_Code='".$fetch_call_log['Logged_User_Code']."',
		Delete_Status='".$fetch_call_log['Delete_Status']."',
		Logged_AllotUserCode='".$fetch_call_log['Logged_AllotUserCode']."',
		OpenCallReasonNo='".$fetch_call_log['OpenCallReasonNo']."',
		CallOpenRemarks='".$fetch_call_log['CallOpenRemarks']."',
		RemarksResolvedByPhone='".$fetch_call_log['RemarksResolvedByPhone']."',
		RemarksResolvedByPhoneDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['RemarksResolvedByPhoneDT']))."',
		RemarksResolvedByEngg='".$fetch_call_log['RemarksResolvedByEngg']."',
		RemarksResolvedByEnggDT='".$fetch_call_log['RemarksResolvedByEnggDT']."',
		FSRType='".$fetch_call_log['FSRType']."',
		FSREnterdDT='".$fetch_call_log['FSREnterdDT']."',
		FSRSignedBy='".$fetch_call_log['FSRSignedBy']."',
		FSRRemarks='".$fetch_call_log['FSRRemarks']."',
		AttachFilePath='".$fetch_call_log['AttachFilePath']."',
		PIUSrNo='".$fetch_call_log['PIUSrNo']."',
		GasFilledInNoOfAc='".$fetch_call_log['GasFilledInNoOfAc']."',
		MyComments='".$fetch_call_log['MyComments']."',
		Current_OpenCallDetailNo='".$fetch_call_log['Current_OpenCallDetailNo']."',
		IndusSiteID='".$fetch_call_log['IndusSiteID']."',
		FSRCopyRecd='".$fetch_call_log['FSRCopyRecd']."',
		ActualEntryDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['ActualEntryDT']))."',
		FSREnteredBy='".$fetch_call_log['FSREnteredBy']."',
		IndusTTNumber='".$fetch_call_log['IndusTTNumber']."',
		IndusTTDT='".date("Y-m-d H:i:s", strtotime($fetch_call_log['IndusTTDT']))."',
		CallLoggedFrom='".$fetch_call_log['CallLoggedFrom']."',
		CostCenter='".$fetch_call_log['CostCenter']."',
		UPRNo='".$fetch_call_log['UPRNo']."',
		CDINo='".$fetch_call_log['CDINo']."',
		curr_Alloted_Eng_Code='".$fetch_call_log['Allot_To_Engg_Code']."',
		curr_Alloted_Eng_Date='".date('Y-m-d H:i:s')."'
		WHERE Call_Log_No='".$fetch_call_log['Call_Log_No']."'
		";
		mysql_query($query) or die(mysql_error());
	}
	//print_r($fetch); 
}



function filtorWithEscape($arr)
{
	foreach($arr as $key => $val)
	{
		$val = trim($val);
		$newArr[$key] = mysql_real_escape_string($val);
	}
	return $newArr;
}
echo 'success';
?>