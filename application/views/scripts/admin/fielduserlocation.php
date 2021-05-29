<?php
$latlongArr = $this->latlongArr;
$params = $this->params;
$auth = Zend_Auth::getInstance();
$authStorage = $auth->getStorage();
//echo "<pre>"; print_r($authStorage->read()); die;
$role = $authStorage->read()->role;
?>
<html> 
<head> 
  <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
  <!-- <meta http-equiv="refresh" content="60" > -->
  <title>Google Maps Multiple Markers</title> 
  <style>
  .select-menu-map, #selectRoleHere {
	width:200px;
	border:1px solid #abadb3;
	padding:4px;
	margin-right:10px;
	margin-bottom:10px;
}
.reset-button{
	cursor:pointer;
	line-height:20px;
	padding:4px 12px;
	color:#fff;
	background: #4D5B76;
}
  </style>
  <script src="https://maps.google.com/maps/api/js?sensor=false" 
type="text/javascript"></script>
    <?php
     echo $this->headScript()->prependFile('/js/jquery-1.7.2.min.js')
                                               ->appendFile('/js/bootstrap.js');
    ?>
	<script>
	function getFiltorUsingAjax(role)
	{
		if(role == 'selectRoleHere')
		{
			alert('Please select any Role');
			return false;
		}
		else
		{
			$.ajax({
				  url: "/ajaxrequest/get-user-by-role/rolename/"+role,
				  cache: false
				})
				  .done(function( myJSONObject ) {
				 
				  var obj = eval ("(" +  myJSONObject  + ")");
				  var user_data = obj.user_data;
				  var len = user_data.length;
				  var userList = '<option value="">Select User</option>';
				  if(len)
				  {
					  for(var i=0;i<len;i++)
					  {
						var staffName = user_data[i].StaffName;
						var StaffCode = user_data[i].StaffCode;
						var user_staff_code = user_data[i].user_staff_code;
						var user_detail = staffName+'   '+'('+"StaffCode : "+user_staff_code+')';
						
						if(StaffCode == '<?php echo $params['staffUser'];?>')
						{
							userList += '<option value="'+StaffCode+'" selected="selected">'+user_detail+'</option>';
						}
						else
						{
							userList += '<option value="'+StaffCode+'">'+user_detail+'</option>';
						}
					  }
				  }
					$("#selectUserList").html(userList);	
					
				  }).fail(function( jqXHR, textStatus ) {
				  alert( "Request failed: " + textStatus );
				});
		}
	}
	</script>
<script>
$(function(){
	$('#selectRoleHere').bind('click',function(){
		var role = $(this).val();
		getFiltorUsingAjax(role);
	});
	
	$("#selectUserList").bind('change',function(){
		var statffid = $(this).val();
		var role = $("#selectRoleHere").val();
		window.location = '/admin/fielduserlocation/role/'+role+'/staffUser/'+statffid;
	})
});

<?php
if($role !="cluster_incharge")
{
	if($params['role'])
	{
	?>
	getFiltorUsingAjax('<?php echo $params['role'];?>');
	<?php
	}
}	
?>
</script>
</head> 
<body>

<?php if($role !="cluster_incharge") { ?>
<div>
<!--- user filtor by role --->
<?php 
echo $this->selectRole;
?>
<select id="selectUserList" class="select-menu-map" style="width:218px;">
<option value="">Select User</option>	
</select>
<a href="/admin/fielduserlocation" style="text-decoration:none;" class="reset-button">Reset</a>
<!--- user filtor by role end --->
<select>
	<?php
		foreach($latlongArr  as $data)
		{
		?>
		<option value="<?php echo $data['StaffCode'];?>">
		<?php echo $data['StaffName'];?>(<?php echo $data['StaffCode'];?>)</option>
		<?php
		}
	?>
<select>
</div>
<?php } ?>

<div id="map" style="width: 100%; height: 100%;"></div>

  <script type="text/javascript">
    var locations = [
	<?php
		foreach($latlongArr  as $data)
		{
	?>
      ['Staff Code : <strong><?php echo $data['StaffCode'];?></strong> <br />Staff Name : <strong><?php echo $data['StaffName'];?></strong>, Last Poll Time: <strong><?php echo $data['last_location_service_hit_time'];?></strong> <?php if($data['Call_Log_No']){?><br />Call Log No. <strong><?php echo $data['Call_Log_No'];?></strong>, <?php } if($data['IndusTTNumber']){?>Indus Call Log No: <strong><?php echo $data['IndusTTNumber'];?></strong><?php }?>', <?php echo $data['current_latitude'];?>, <?php echo $data['current_longitude'];?>, 4],
	  <?php }?>
    ];
	// Enable the visual refresh
	google.maps.visualRefresh = true;
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 10,
      center: new google.maps.LatLng(28.62594150, 77.07827360),
      mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {  
      marker = new google.maps.Marker({
        position: new google.maps.LatLng(locations[i][1], locations[i][2]),
        map: map
      });

      google.maps.event.addListener(marker, 'click', (function(marker, i) {
        return function() {
          infowindow.setContent(locations[i][0]);
          infowindow.open(map, marker);
        }
      })(marker, i));
    }
  </script>
</body>
</html>
