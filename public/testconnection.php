<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);

/* 
$Query_Staff_Master = "select TOP 20 * from Staff_Master";
$Staff_Master = mssql_query($Query_Staff_Master,$con_mssql);
echo '<pre>';
while($fetch_Staff_Master = mssql_fetch_assoc($Staff_Master))
{
  print_r($fetch_Staff_Master);
}
die;

$call_log = mssql_query("select TOP 20 * from Call_Log");
echo '<pre>';
while($fetch_call_log = mssql_fetch_assoc($call_log))
{
	//print_r($fetch_call_log); 
	$callnumber = $fetch_call_log['Call_Log_No'];  
	$calldata = mysql_query("select * from call_log where Call_Log_No='".$callnumber."'");
	$fetch = mysql_fetch_assoc($calldata);
	if($fetch['Call_Log_No']=="")
	{ 
	    $query = "INSERT INTO call_log SET 
		EntrySrNo='".$fetch_call_log['EntrySrNo']."',
		Call_Log_No='".$fetch_call_log['Call_Log_No']."',
		Call_Log_DT='".$fetch_call_log['Call_Log_DT']."',
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
		Call_Cancel_DT='".$fetch_call_log['Call_Cancel_DT']."',
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
		RemarksResolvedByPhoneDT='".$fetch_call_log['RemarksResolvedByPhoneDT']."',
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
		ActualEntryDT='".$fetch_call_log['ActualEntryDT']."',
		FSREnteredBy='".$fetch_call_log['FSREnteredBy']."',
		IndusTTNumber='".$fetch_call_log['IndusTTNumber']."',
		IndusTTDT='".$fetch_call_log['IndusTTDT']."',
		CallLoggedFrom='".$fetch_call_log['CallLoggedFrom']."',
		CostCenter='".$fetch_call_log['CostCenter']."',
		UPRNo='".$fetch_call_log['UPRNo']."',
		CDINo='".$fetch_call_log['CDINo']."'
		"; 
		mysql_query($query);
	}
	else
	{
		$query = "UPDATE call_log SET 
		EntrySrNo='".$fetch_call_log['EntrySrNo']."',
		Call_Log_No='".$fetch_call_log['Call_Log_No']."',
		Call_Log_DT='".$fetch_call_log['Call_Log_DT']."',
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
		Call_Cancel_DT='".$fetch_call_log['Call_Cancel_DT']."',
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
		RemarksResolvedByPhoneDT='".$fetch_call_log['RemarksResolvedByPhoneDT']."',
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
		ActualEntryDT='".$fetch_call_log['ActualEntryDT']."',
		FSREnteredBy='".$fetch_call_log['FSREnteredBy']."',
		IndusTTNumber='".$fetch_call_log['IndusTTNumber']."',
		IndusTTDT='".$fetch_call_log['IndusTTDT']."',
		CallLoggedFrom='".$fetch_call_log['CallLoggedFrom']."',
		CostCenter='".$fetch_call_log['CostCenter']."',
		UPRNo='".$fetch_call_log['UPRNo']."',
		CDINo='".$fetch_call_log['CDINo']."'
		WHERE Call_Log_No='".$fetch_call_log['Call_Log_No']."'
		";
		mysql_query($query);
	}
	//print_r($fetch); 
}
 */

$query = mssql_query("SELECT * FROM information_schema.tables");
echo '<pre>';
while($fetch = mssql_fetch_assoc($query))
{
	print_r($fetch);
}


die;


//phpinfo();
    //$dsn = 'dblib:dbname=ACME_Misc_DB;host=10.10.0.9';
    $user = 'mobility';
    $password = 'mobility';
    //$dbh = new PDO($dsn, $user, $password);
	$dbh = new PDO("dblib:host=10.10.0.9;dbname=ACME_Misc_DB",$user,$password);
	echo 'sdfsdfs';
  ?>