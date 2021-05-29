<?php
//error_reporting(E_ALL);
//ini_set("display_errors","on");
//echo '<pre>';
$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);

 
$Query_Staff_Master = "SELECT 
EntryNo, ParentStaffCode, LoginID, StaffCode, StaffName, StaffTypeCode, WorkCircle_Code, StaffStatus, CMSStaffStockCode, OwnConvType,
      ManagerCode, EMail, MobileNo, ApprovingLevel, IsCostCentreLock, Remarks,
      ACME_EmpID, EmpGrade,  InsertedBy, InsertedDT, UpdatedBy,UpdatedDT, WorkLocation, ClusterCode,   Gender,
      IsMarried, Password
 from RMS.Staff_Master
 where StaffStatus='AC'
 order by EntryNo asc
 ";
 
 
 
$Staff_Master = mssql_query($Query_Staff_Master,$con_mssql) or die(mssql_get_last_message()); 


$date = date('Y-m-d H:i:s');
$checknational = 'yes';
echo '<pre>';
while($fetch_Staff_Master = mssql_fetch_assoc($Staff_Master))
{	

	$circlecodecond = "";
	$role = '';
	$zonecirarr = array();
	$reginonarr = array();
	$circlearr = array();
	$sercircle = array();
	$clusterarr = array();
	$Circle = '';
	
	$StaffCode = trim($fetch_Staff_Master['StaffCode']);
	$CMSStaffStockCode = trim($fetch_Staff_Master['CMSStaffStockCode']);

	
	// national head
	if($checknational == 'yes')
	{
		$nationalsql = mssql_query("select TOP 1  sNo, Region, RD_StaffCode, RD_Email, Status from RMS.Region_Master where RD_StaffCode='".$StaffCode."'");
		$numnational = mssql_num_rows($nationalsql);
		if($numnational)
		{
			$role = 'national_head';
			$circlecodecond = " `nationalCircleCodes`='',";
			$checknational = 'no';
			
	
		// insert and update local user mapping table 
			// check user already register
			$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
			$usernum = mysql_num_rows($usersql);
			if(!$usernum)
			{
				 $fenalquery = "insert into local_user_mapping set 
							`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
							`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
							`StaffCode`='".trim($StaffCode)."', 
							`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
							`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
							`Password`='".trim($fetch_Staff_Master['Password'])."', 
							`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
							`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
							`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
							$circlecodecond
							`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
							`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
							`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
							`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
							`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
							`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
							`sink_register_date`='".$date."', 
							`role`='".$role."'
				
				";
			}
			else
			{
				$fenalquery = "update local_user_mapping set 
							`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
							`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
							`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
							`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
							`Password`='".trim($fetch_Staff_Master['Password'])."', 
							`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
							`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
							`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
							$circlecodecond
							`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
							`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
							`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
							`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
							`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
							`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
							`sink_register_date`='".$date."' 
							 where `StaffCode`='".$StaffCode."'  and `role`='".$role."'
				
				";
			}
			mysql_query($fenalquery);
			
		}
	}
	
	
	// zone head
	$zonesql = mssql_query("select *  from ZoneHeadMaster where ZoneHeadCode='".$StaffCode."'");
	$nnumzone = mssql_num_rows($zonesql);
	if($nnumzone)
	{
			while($zonefetch = mssql_fetch_assoc($zonesql))
			{
				$RegionCode = $zonefetch['RegionCode'];
				$getcirclsql = mssql_query("select * from Circle_Master where Region='".$RegionCode."'");
				while($getcirfetch = mssql_fetch_assoc($getcirclsql))
				{
					$zonecirarr[] = $getcirfetch['CircleCode'];
				}
			}
			
			$zonecirarrnew = array_unique($zonecirarr);
			$zonecirstr = implode(',',$zonecirarrnew);
		
			$circlecodecond = " `zoneCircleCodes`='".$zonecirstr."',";
			$role = 'zone_head';
	// insert and update local user mapping table 
		// check user already register
		$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
		$usernum = mysql_num_rows($usersql);
		if(!$usernum)
		{
			 $fenalquery = "insert into local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffCode`='".trim($StaffCode)."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."', 
						`role`='".$role."'
			
			";
		}
		else
		{
			$fenalquery = "update local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."'
						 where `StaffCode`='".$StaffCode."'  and `role`='".$role."'
			
			";
		}
		mysql_query($fenalquery);
	}

	$regionsql = mssql_query("select *  from RegionHeadMaster where RHead='".$StaffCode."'");
	$numregion = mssql_num_rows($regionsql);
	if($numregion)
	{
		while($fetchregion = mssql_fetch_assoc($regionsql))
		{
			$reginonarr[] = $fetchregion['CircleCode'];
		}	
		$reginonarrnew  =  array_unique($reginonarr);
		$role = 'regional_head';
		$reginonstr = implode(',',$reginonarrnew);	
		$circlecodecond = " `regionalCircleCodes`='".$reginonstr."',";
	// insert and update local user mapping table 
		// check user already register
		$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
		$usernum = mysql_num_rows($usersql);
		if(!$usernum)
		{
			 $fenalquery = "insert into local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffCode`='".trim($StaffCode)."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."', 
						`role`='".$role."'
			
			";
		}
		else
		{
			$fenalquery = "update local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."'
						  where `StaffCode`='".$StaffCode."'  and `role`='".$role."'
			
			";
		}
		mysql_query($fenalquery);
	}

	//circle head
	$cirsql = mssql_query("select *  from Circle_Master where COH_Only1='".$StaffCode."'");
	$numcircle = mssql_num_rows($cirsql);
	if($numcircle)
	{
		while($fetchcir = mssql_fetch_assoc($cirsql))
		{
			$circlearr[] = $fetchcir['CircleCode'];
		}	
		$circlearrnew  =  array_unique($circlearr);
		$role = 'circle_head';
		$circlestr = implode(',',$circlearrnew);	
		$circlecodecond = " `circleCodes`='".$circlestr."',";
	// insert and update local user mapping table 
		// check user already register
		$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
		$usernum = mysql_num_rows($usersql);
		if(!$usernum)
		{
			 $fenalquery = "insert into local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffCode`='".trim($StaffCode)."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."', 
						`role`='".$role."'
			
			";
		}
		else
		{
			$fenalquery = "update local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."',
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."'
						  where `StaffCode`='".$StaffCode."'  and `role`='".$role."'
			
			";
		}
		mysql_query($fenalquery);

	}

	//service manager
	$sersql = mssql_query("select *  from Service_Manager where ServiceManagerCode='".$StaffCode."'");
	$numserv = mssql_num_rows($sersql);
	if($numserv)
	{
		while($fetchser = mssql_fetch_assoc($sersql))
		{
			$sercircle[] = $fetchser['CircleCode'];
		}	
		$sercirclenew  =  array_unique($sercircle);
		$role = 'service_manager';
		$sercirstr = implode(',',$sercirclenew);	
		$circlecodecond = " `serviceManagerCircleCodes`='".$sercirstr."',";
	// insert and update local user mapping table 
		// check user already register
		$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
		$usernum = mysql_num_rows($usersql);
		if(!$usernum)
		{
			 $fenalquery = "insert into local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffCode`='".trim($StaffCode)."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."', 
						`role`='".$role."'
			
			";
		}
		else
		{
			$fenalquery = "update local_user_mapping set 
						`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
						`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
						`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
						`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
						`Password`='".trim($fetch_Staff_Master['Password'])."', 
						`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
						`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
						`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
						$circlecodecond
						`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
						`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
						`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
						`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
						`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
						`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
						`sink_register_date`='".$date."'
						 where `StaffCode`='".$StaffCode."'  and `role`='".$role."'
			
			";
		}
		mysql_query($fenalquery);
	}
	
	
	// cluster incharge 
	if(!$role)
	{
		if($CMSStaffStockCode)
		{
			$cisql = mssql_query("select *  from Acme_CMS_SIte_Engineer_mapping where CluserLeadCode='".$CMSStaffStockCode."'");
			$cinum = mssql_num_rows($cisql);
			if($cinum)
			{
				while($fetchci = mssql_fetch_assoc($cisql))
				{
					$clusterarr[] = $fetchci['Circle'];
				}	
				$clusterarrnew  =  array_unique($clusterarr);
				$role = 'cluster_incharge';
				$clusterstr = implode(',',$clusterarrnew);	
				$circlecodecond = " `clusterInchargeCircleCodes`='".$clusterstr."',";
			}
			
			// check field user
			
			$engsql = mssql_query("select TOP 1 *  from Acme_CMS_SIte_Engineer_mapping where Eng_code='".$CMSStaffStockCode."'");
			$engnum = mssql_num_rows($engsql);
			if($engnum)
			{
				$fetcheng = mssql_fetch_assoc($engsql);
				$Circle = $fetcheng['Circle'];
				if(!$role)
				{
					$role = 'field_user';
				}	
 
			$cmsquery = "SELECT StaffCode FROM RMS.Staff_Master where CMSStaffStockCode='".$fetcheng['CluserLeadCode']."'";
				$getcluster = mssql_query($cmsquery);
				$cmsfetch = mssql_fetch_assoc($getcluster);
				
				$circlecodecond .= " `fieldUserCodes`='".$Circle."', fieldUserParent='".$cmsfetch['StaffCode']."',";
			}	


		// insert and update local user mapping table 
		if($cinum || $engnum)
		{
			// check user already register
			$usersql = mysql_query("select id from local_user_mapping where StaffCode='".$StaffCode."' and role='$role'");
			$usernum = mysql_num_rows($usersql);
			if(!$usernum)
			{
				 $fenalquery = "insert into local_user_mapping set 
							`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
							`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
							`StaffCode`='".trim($StaffCode)."', 
							`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
							`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
							`Password`='".trim($fetch_Staff_Master['Password'])."', 
							`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
							`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
							`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
							$circlecodecond
							`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
							`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
							`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
							`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
							`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
							`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
							`sink_register_date`='".$date."', 
							`role`='".$role."'
				
				";
			}
			else
			{
				$fenalquery = "update local_user_mapping set 
							`EntryNo`='".trim($fetch_Staff_Master['EntryNo'])."', 
							`LoginID`='".trim($fetch_Staff_Master['LoginID'])."', 
							`StaffName`='".trim($fetch_Staff_Master['StaffName'])."', 
							`EMail`='".trim($fetch_Staff_Master['EMail'])."', 
							`Password`='".trim($fetch_Staff_Master['Password'])."', 
							`MobileNo`='".trim($fetch_Staff_Master['MobileNo'])."', 
							`CMSStaffStockCode`='".trim($fetch_Staff_Master['CMSStaffStockCode'])."', 
							`StaffStatus`='".trim($fetch_Staff_Master['StaffStatus'])."',
							$circlecodecond
							`Remarks`='".trim($fetch_Staff_Master['Remarks'])."', 
							`ACME_EmpID`='".trim($fetch_Staff_Master['ACME_EmpID'])."', 
							`EmpGrade`='".trim($fetch_Staff_Master['EmpGrade'])."', 
							`DOB`='".trim($fetch_Staff_Master['DOB'])."', 
							`Gender`='".trim($fetch_Staff_Master['Gender'])."', 
							`IsMarried`='".trim($fetch_Staff_Master['IsMarried'])."', 
							`sink_register_date`='".$date."'
							 where `StaffCode`='".$StaffCode."'  and `role`='".$role."' 
				
				";
			}
			mysql_query($fenalquery);
		}
			
		}
	}	
}
die;

  ?>