<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);

//mssql_query("SET ANSI_NULLS ON");
//mssql_query("SET ANSI_WARNINGS ON");

/* $query = mssql_query("select TOP 20 *
 from dbo.Complaint_Descr
 
 "); */
 
 $query = mssql_query("select TOP 20 *
 from Call_Log
 
 ");
 
echo '<pre>';
while($fetch = mssql_fetch_assoc($query))
{
	print_r($fetch); 
}	
?>