<?php
mysql_connect("10.10.2.16","mobilitysql","mobility3344");
mysql_select_db("acme");
$sql = mysql_query("select * from local_user_mapping") or die(mysql_error());
?>
<style>
th{
  background-color: #000000;
    border: 1px solid #000000;
    color: #FFFFFF;
    font-size: 11px;
}
td{
	border: 1px solid #000000;
	border: 1px solid #000000;
    font-size: 11px;
    padding: 5px;
}
</style>
<table cellspacing="0">
<tr>
	<th>S.N</th>
	<th>LoginID</th>
	<th>Staff Code</th>
	<th>Staff Name</th>
	<th>Email</th>
	<th>Password</th>
	<th>Zone Circle Code</th>
	<th>Regional Circle Code</th>
	<th>Circle Code</th>
	<th>Service Manager Circle Code</th>
	<th>Cluster Circle Code</th>
	<th>Field User Code</th>
	<th>Field User Parent</th>
	<th>Role</th>
	</tr>

<?php
$i= 0;
while($fetch = mysql_fetch_assoc($sql))
{
$i++;
?>
<tr>
	<td><?php echo $i;?></td>
	<td><?php echo $fetch['LoginID'];?>&nbsp;</td>
	<td><?php echo $fetch['StaffCode'];?>&nbsp;</td>
	<td><?php echo $fetch['StaffName'];?>&nbsp;</td>
	<td><?php echo $fetch['EMail'];?>&nbsp;</td>
	<td><?php echo $fetch['Password'];?>&nbsp;</td>
	<td><?php echo $fetch['zoneCircleCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['regionalCircleCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['circleCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['serviceManagerCircleCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['clusterInchargeCircleCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['fieldUserCodes'];?>&nbsp;</td>
	<td><?php echo $fetch['fieldUserParent'];?>&nbsp;</td>
	<td><?php echo $fetch['role'];?>&nbsp;</td>
	
</tr>
<?php
}
?>
</table>