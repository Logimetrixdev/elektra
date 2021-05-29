<?php
//die;
error_reporting(E_ALL);
ini_set("display_errors","on");

$con_mssql = mssql_connect('10.10.0.9','mobility','mobility1234') or die('unable to connect');
$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$mssqldb = mssql_select_db('ACME_Misc_DB',$con_mssql);
$mysqldb = mysql_select_db('acme',$con_mysql);
  
mssql_query("SET ANSI_NULLS ON");
mssql_query("SET ANSI_WARNINGS ON");

//$query = mssql_query("select TOP 20 * from View_New_Engr_Stock");
$query = mssql_query("select TOP 50 * from View_New_Engr_Stock where EnggCode='SAP127'") or die('error');
echo '<pre>';
while($fetch = mssql_fetch_assoc($query))
{
print_r($fetch);
continue;
	$sql = mysql_query("select * from spare_parts where Comp_Code='".trim($fetch['Comp_Code'])."' and LOT='".trim($fetch['LOT'])."' and EnggCode='".trim($fetch['EnggCode'])."'");
	$num = mysql_num_rows($sql);
	if($num)
	{
		$queryspare = "update spare_parts set 
	  `Comp_Descr`='".trim($fetch['Comp_Descr'])."',
	  `QTY`='".trim($fetch['QTY'])."',
	  `UOM`='".trim($fetch['UOM'])."',
	  `STATUS`='".trim($fetch['STATUS'])."',
	  `ItemStatus`='".trim($fetch['ItemStatus'])."',
	  `WH`='".trim($fetch['WH'])."',
	  `SpareCategory`='".trim($fetch['SpareCategory'])."' 
		where Comp_Code='".trim($fetch['Comp_Code'])."' and LOT='".trim($fetch['LOT'])."' and EnggCode='".trim($fetch['EnggCode'])."'";
	}
	else
	{
		$queryspare = "insert into  spare_parts set 
		`Comp_Code`='".trim($fetch['Comp_Descr'])."',
	  `Comp_Descr`='".trim($fetch['Comp_Descr'])."',
	  `LOT`='".trim($fetch['LOT'])."',
	  `QTY`='".trim($fetch['QTY'])."',
	  `UOM`='".trim($fetch['UOM'])."',
	  `STATUS`='".trim($fetch['STATUS'])."',
	  `EnggCode`='".trim($fetch['EnggCode'])."',
	  `ItemStatus`='".trim($fetch['ItemStatus'])."',
	  `WH`='".trim($fetch['WH'])."',
	  `SpareCategory`='".trim($fetch['SpareCategory'])."'"; 
	}
	
	mysql_query($queryspare) or die(mysql_error());
	//print_r($fetch); 
}
?>