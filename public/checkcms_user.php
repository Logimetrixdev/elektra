<?php
$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
echo $Query_Staff_Master = "SELECT EntryNo, ParentStaffCode, LoginID, StaffCode, StaffName, StaffTypeCode, WorkCircle_Code, StaffStatus, CMSStaffStockCode, OwnConvType,
 ManagerCode, EMail, MobileNo, ApprovingLevel, IsCostCentreLock, Remarks, ACME_EmpID, EmpGrade,  InsertedBy, InsertedDT, UpdatedBy,UpdatedDT, WorkLocation, ClusterCode,
 Gender,IsMarried, Password from RMS.Staff_Master where StaffStatus='AC' and  CMSStaffStockCode = 'CUW269' order by EntryNo asc";
$Staff_Master = mssql_query($Query_Staff_Master,$con_mssql) or die(mssql_get_last_message()); 
$fetch_Staff_Master = mssql_fetch_assoc($Staff_Master);

    $StaffCode = trim($fetch_Staff_Master['StaffCode']);
    $CMSStaffStockCode = trim($fetch_Staff_Master['CMSStaffStockCode']);
       
       echo 'Query from Staff Master Table';
       echo  '<pre>';
       print_r($fetch_Staff_Master);
       echo  '</pre>';
       echo 'Query from Acme Staff Engg mapping Table';
       echo '<br/>';
       echo  $sql1 = "select TOP 1 *  from Acme_CMS_SIte_Engineer_mapping where Eng_code='".$CMSStaffStockCode."'";
       $engsql = mssql_query("select TOP 1 *  from Acme_CMS_SIte_Engineer_mapping where Eng_code='".$CMSStaffStockCode."'");
       $engnum = mssql_num_rows($engsql);
       $fetcheng = mssql_fetch_assoc($engsql);
       echo  '<pre>';
       print_r($fetcheng);
       echo  '</pre>';
       echo 'Query from Acme Staff Master table again for getting the CI name where CMSStaffStockCode equals to upper fetch data [CluserLeadCode]';
       echo '<br/>';
       echo $cmsquery = "SELECT * FROM RMS.Staff_Master where CMSStaffStockCode='".$fetcheng['CluserLeadCode']."'";
       $getcluster = mssql_query($cmsquery);
       $cmsfetch = mssql_fetch_assoc($getcluster);
       echo  '<pre>';
       print_r($cmsfetch);
       echo  '</pre>';
       
?>