<?php
//error_reporting(E_ALL);
//ini_set("display_errors","on");

$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);
  


$query = mssql_query("select TOP 20 * from dbo.Complaint_Descr");

while($fetch = mssql_fetch_assoc($query))
{
	$sql = mysql_query("select * from product_type where DescrID='".trim($fetch['DescrID'])."'");
	$num = mysql_num_rows($sql);
	if($num)
	{
		$queryspare = "update product_type set 
	  `Descr`='".trim($fetch['Descr'])."',
	  `ComplaintDescrGroup`='".trim($fetch['ComplaintDescrGroup'])."',
	  `status`='".trim($fetch['status'])."'
	   where DescrID='".trim($fetch['DescrID'])."'
	  ";
	}
	else
	{
		$queryspare = "insert into  product_type set 
		DescrID='".trim($fetch['DescrID'])."',
	  `Descr`='".trim($fetch['Descr'])."',
	  `ComplaintDescrGroup`='".trim($fetch['ComplaintDescrGroup'])."',
	  `status`='".trim($fetch['status'])."'"; 
	}
	mysql_query($queryspare) or die(mysql_error());
	//print_r($fetch); 
}
?>