<?php
error_reporting(E_ALL ^ E_NOTICE);
require_once 'excel_reader2.php';
$data = new Spreadsheet_Excel_Reader("example.xls");
?>
<html>
<head>
<style>
table.excel {
	border-style:ridge;
	border-width:1;
	border-collapse:collapse;
	font-family:sans-serif;
	font-size:12px;
}
table.excel thead th, table.excel tbody th {
	background:#CCCCCC;
	border-style:ridge;
	border-width:1;
	text-align: center;
	vertical-align:bottom;
}
table.excel tbody th {
	text-align:center;
	width:20px;
}
table.excel tbody td {
	vertical-align:bottom;
}
table.excel tbody td {
    padding: 0 3px;
	border: 1px solid #EEEEEE;
}
</style>
</head>

<body>
<?php 
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'sjslayjy_malt');
define('DB_PASSWORD', 'Abhishek@987');
define('DB_DATABASE', 'sjslayjy_finalMalt');
mysql_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD);
mysql_select_db(DB_DATABASE);

// check the file data is exist
//echo $data->dump(true,true); 

// echo "Total Sheets in this xls file: ".count($data->sheets)."<br /><br />";

// $html="<table border='1'>";
// for($i=0;$i<count($data->sheets);$i++) // Loop to get all sheets in a file.
// {	
// 	if(count($data->sheets[$i][cells])>0) // checking sheet not empty
// 	{
// 		echo "Sheet $i:<br /><br />Total rows in sheet $i  ".count($data->sheets[$i][cells])."<br />";
// 		for($j=1;$j<=count($data->sheets[$i][cells]);$j++) // loop used to get each row of the sheet
// 		{ 
// 			$html.="<tr>";
// 			for($k=1;$k<=count($data->sheets[$i][cells][$j]);$k++) // This loop is created to get data in a table format.
// 			{
// 				$html.="<td>";
// 				$html.=$data->sheets[$i][cells][$j][$k];
// 				$html.="</td>";
// 			}
// 			//echo '<pre/>';
// 			//print_r($data->sheets[$i][cells][$j]);
// 			$recordID = mysql_real_escape_string($data->sheets[$i][cells][$j][1]);
// 			$staffCode = mysql_real_escape_string($data->sheets[$i][cells][$j][2]);
// 			$regionId = mysql_real_escape_string($data->sheets[$i][cells][$j][3]);
// 			$phone = mysql_real_escape_string($data->sheets[$i][cells][$j][4]);
// 			$fname = mysql_real_escape_string($data->sheets[$i][cells][$j][5]);
// 			$village = mysql_real_escape_string($data->sheets[$i][cells][$j][6]);

// 			// Start Getting Mapped Village
// 			$query = "SELECT * FROM `logi_villages` WHERE `village_name` LIKE '".$village."'";
// 			$result = mysql_query($query) or die("error in getting village data");
// 			$row = mysql_fetch_assoc($result);
//             if($row['id']){
//             	// mapped villege are inserted in farmer table
// 				$farmerType = 1;
// 				$FarmerName = $fname;
// 				$MobileNo = $phone;
// 				$stateId = $row['circle_id'];
// 				$districtId = $row['district_id'];
// 				$tehsilId  = $row['tehsil_id'];
// 				$regionId = $row['region_id'];
// 				$villageId = $row['id'];
// 				$AllotedFieldEngg = $staffCode;
// 				$RegisterDate = date("Y-m-d");
// 				$SuffletBGP = 1;		
// 				$query = "insert into logi_my_farmers(farmerType,FarmerName,MobileNo,stateId,districtId,tehsilId,regionId,villageId,AllotedFieldEngg,RegisterDate,SuffletBGP)
// 				values('".$farmerType."','".$FarmerName."','".$MobileNo."','".$stateId."','".$districtId."','".$tehsilId."','".$regionId."','".$villageId."','".$AllotedFieldEngg."','".$RegisterDate."','".$SuffletBGP."')";
// 				mysql_query($query) or die("Error in inserting farmer data");	
// 			}else{
// 				// not mapped villege are inserted in log table
// 				$query = "insert into logi_log_missing_village_map(recordId)  values('".$recordID."')";
// 				mysql_query($query) or die("Error in inserting farmer data");	
// 			}
			
// 		}
// 	}

// }

// echo "Data Inserted in dababase";

$query = "SELECT `id`,`farmerType`,`stateId`,`districtId`,`tehsilId`,`villageId` FROM `logi_my_farmers` WHERE 1";
$result = mysql_query($query) or die("error in getting village data");
while($row = mysql_fetch_assoc($result)){
$companyId 		= $row['id'];
$farmerType  	= $row['farmerType'];
$stateId  		= $row['stateId'];
$districtId  	= $row['districtId'];
$tehsilId  		= $row['tehsilId'];
$villageId 		= $row['villageId'];
	echo $villageId;  		
			// Get State Code
			  $query_state = "SELECT state FROM logi_circle where  id='".$stateId."'";  
              $result_state = mysql_query($query_state) or die("error in getting state data");
              $row_state = mysql_fetch_assoc($result_state)
              print_r($row_state);
            //echo $stateName = strtoupper(substr($row_state['state'],0,2));
            exit();
}
?>

</body>
</html>  
