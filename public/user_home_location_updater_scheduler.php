<?php
error_reporting(E_ALL);
ini_set("display_errors","on");

//$con_mysql = mysql_connect('10.10.2.16','mobilitysql','mobility3344') or die('unable to connect');

$con_mysql = mysql_connect('acmecleantech.db.11816574.hostedresource.com','acmecleantech','Acme@1234') or die('unable to connect');

$mysqldb = mysql_select_db('acmecleantech',$con_mysql);
$trackdata = mysql_query("select count(*) as count, mob_user_staff_code, CONCAT(lat, ':', longitude) as latlong from user_path where 
date_format(add_date_time,'%Y-%m-%d')=CURDATE() and  
date_format(add_date_time,'%H:%i:%s') >'00:00:00' and  
date_format(add_date_time,'%H:%i:%s') <'04:00:00' group by mob_user_staff_code, latlong
order by count desc");

$final_track_grouped = array();
while($tracksingle = mysql_fetch_assoc($trackdata))
{
if(!isset($final_track_grouped[$tracksingle['mob_user_staff_code']])){
	$final_track_grouped[$tracksingle['mob_user_staff_code']] = $tracksingle['latlong'];
}

}

foreach($final_track_grouped as $user_id=>$latlong){

if(user_home_location_update_need($user_id)){
	echo 'update:'.$user_id.'<br>';
update_home_location($user_id, $latlong);	
}
	
}

function update_home_location($user_id, $latlong){
		$latlong_array = explode(':',$latlong);
	    $query = "INSERT INTO user_home_location SET 
		mob_user_staff_code='".$user_id."',
		latitude='".$latlong_array[0]."',
		longitude='".$latlong_array[1]."',
		updated_date='".date('Y-m-d H:i:s')."'
		"; 
		mysql_query($query);	
}
	
function user_home_location_update_need($user_id){
	$query = mysql_query("select count(*) as count from user_home_location where 
mob_user_staff_code='".$user_id."' ");
    $result = mysql_fetch_assoc($query);
	if($result['count']>0){
		return false;
		}else{
		return true;
		}
	}
//echo '<pre>';
//print_r($final_track_grouped);
//echo '</pre>';

?>