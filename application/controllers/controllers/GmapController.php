<?php

class GmapController extends Zend_Controller_Action
{
	var $db;

    public function init()
    {
		$this->db = Zend_Db_Table::getDefaultAdapter();

    }
	
public function trackAction(){
	$this->checklogin();
	$user = new Application_Model_Users();
	$this->view->userdetails = $user->getuserData();		
}

public function legendHelpAction(){
	$this->_helper->layout->disableLayout();
}
	
public function trackAllAction(){
	$this->checklogin();
	$gmapModel = new Application_Model_Gmap();
	$this->view->productTypeList = $gmapModel->productTypeList();
	$this->view->callTypeList = $gmapModel->callTypeList();
	$this->view->circleList = $gmapModel->circleList();
	$this->view->customerList = $gmapModel->customerList();	
}	
	public function customerAction(){
	
	$params = $this->getRequest()->getParams();
	
	$gmapModel = new Application_Model_Gmap();

	$joballdata = $gmapModel->getCustomerAllJobs($params);		
	$this->view->jobs = $joballdata; 
	
	foreach($joballdata as $single_job){
	$user_list[$single_job['curr_Alloted_Eng_Code']] = $single_job['curr_Alloted_Eng_Code'];
	}
	
	//print_r($user_list);die;	
	$allUsersLocation = $gmapModel->getUsersCurrentLoc($user_list);
	$this->view->users = $allUsersLocation;
	$this->view->params = $params; 

	$this->_helper->layout->disableLayout();
	
	}
	
    public function indexAction()
    {
		
	$this->checklogin();
	$params = $this->getRequest()->getParams();
	
	//print_r($params);
	//exit();
	
	if(trim($params['user_staff_code'])!=''){
		$params['user'] = $params['user_staff_code'];
		}
		
	if(trim($params['user_staffcode'])!=''){
		$params['user'] = $params['user_staffcode'];
		}		
		
	if($params['user_date']!=''){
	$date =	$params['user_date'];
	}else{
	$date =	date("Y-m-d");
		
	}
	//$sqlQuery = $this->db->select() ->from('user_path',array('*')) ->where('mob_user_staff_code =?',$params['user'])->where(new Zend_Db_Expr('date_format(add_date_time,"%Y-%m-%d") ="'. $date.'"'))->order('add_date_time asc')->limit(1);  
	//echo 'q:'.$sqlQuery->__toString();die;
        //$result = $this->db->fetchRow($sqlQuery);
	//$this->view->center_coord = $result; 		
	
		$sqlQuery = $this->db->select() ->from('logi_field_users',array('*')) ->where('LoginID =?',$params['user']);  
		$result = $this->db->fetchAll($sqlQuery);
		
		$this->view->current_coord = $result; 
	
    	
		$this->view->user = $params['user'];
                
        //$user = new Application_Model_Users();
		//$alldata = $user->getUserAllDataByStaffCode($params['user']);
		//print_r($alldata);
		
		//$jobs = new Application_Model_Job();		
		//$joballdata = $jobs->getAllJobs($alldata['CMSStaffStockCode'],1);		
		//$this->view->jobs = $joballdata; 
		
		
		
		
		$sqlQuery = $this->db->select() ->from('user_path',array('*')) ->where('mob_user_staff_code =?',$params['user'])->where(new Zend_Db_Expr('date_format(add_date_time,"%Y-%m-%d") ="'. $date.'"'))->order('add_date_time asc')->limit(2000);  
		$result = $this->db->fetchAll($sqlQuery);
		
		

		
		$single_coord_array = array();
		foreach($result as $single_path_coord){
		$single_coord_array[] = 'new google.maps.LatLng('.$single_path_coord['lat'].', '.$single_path_coord['longitude'].')';
		}
		$single_coord_array[] = $single_coord_array[0];
		$path_coords = implode(',',$single_coord_array);
		$this->view->path_coords = $path_coords; 
                
                $this->view->single_path_coord = $result; 
                
                
		$this->_helper->layout->disableLayout();
    }

public function loadGeoJsonAction(){

	$params = $this->getRequest()->getParams();
	
	$gmapModel = new Application_Model_Gmap();

	$joballdata = $gmapModel->getCustomerAllJobs($params);		
	$jobs = $joballdata; 
$customer_tower_code = array('Indus Towers Limited'=>'cust-i', 'Bharti Airtel Limited'=> 'cust-b' );
$customer_call_type_code = array('BD'=>'call-b', 'PM'=>'call-p');
$customer_product_type_code = array('AC'=>'product-a','PIU'=>'product-p');	
$jobs_json = array();
$site_counts = 0;
foreach($jobs as $single_job){ 
	$single_json_entry = array();
	$image = '';
	if(isset($customer_tower_code[$single_job['Customer_Name']]) && $customer_tower_code[$single_job['Customer_Name']]!=''){ $image .= $customer_tower_code[$single_job['Customer_Name']]; }else{ $image .= 'cust-o'; }
	$image .= '-';
	if(isset($customer_call_type_code[$single_job['Call_Type_Code']]) && $customer_call_type_code[$single_job['Call_Type_Code']]!=''){ $image .= $customer_call_type_code[$single_job['Call_Type_Code']]; }else{ $image .= 'call-o'; }
	$image .= '-';
	if(isset($customer_product_type_code[$single_job['product_type']]) && $customer_product_type_code[$single_job['product_type']]!=''){ $image .= $customer_product_type_code[$single_job['product_type']]; }else{ $image .= 'product-o'; }
	$image .= '.gif';
	$single_json_entry['icon'] = 'http://apps.acme.in:9030/images/map-icons/'.$image;
	$single_json_entry['cust_name'] = $single_job['Customer_Name'];
	$single_json_entry['site_id'] = $single_job['Site_ID'];
	$single_json_entry['call_type'] = $single_job['Call_Type_Code'];
	$single_json_entry['product_type'] = $single_job['product_type'];
	$single_json_entry['call_log_no'] = $single_job['Call_Log_No'];

if($single_job['slm_latitude'] !='' && $single_job['slm_longitude'] !='' && $single_job['slm_longitude'] !='0.0'  && $single_job['slm_latitude'] !='0.0'){

$single_json_entry['lat'] = $single_job['slm_latitude'];	 
$single_json_entry['long'] = $single_job['slm_longitude'];

}else if($single_job['site_latitude'] !='' && $single_job['site_longitude'] !='' && $single_job['site_latitude'] !='0.0'  && $single_job['site_longitude'] !='0.0'){ 

$single_json_entry['lat'] = $single_job['site_latitude'];	 
$single_json_entry['long'] = $single_job['site_longitude'];

}
if($single_json_entry['lat']!='' && $single_json_entry['long']!=''){
$jobs_json[] = $single_json_entry;
$site_counts++;
}
}

	foreach($joballdata as $single_job){
	$user_list[$single_job['curr_Alloted_Eng_Code']] = $single_job['curr_Alloted_Eng_Code'];
	}
	
	//print_r($user_list);die;	
	$allUsersLocation = $gmapModel->getUsersCurrentLoc($user_list);
	$this->view->users = $allUsersLocation; 
print 'var data = '.json_encode(array('site_count'=>$site_counts,'site_list'=>$jobs_json));
/*echo 'var data = {
 "sites": [{"photo_id": 27932, "photo_title": "Atardecer en Embalse", "photo_url": "http://www.panoramio.com/photo/27932", "photo_file_url": "http://mw2.google.com/mw-panoramio/photos/medium/27932.jpg", "longitude": -64.404945, "latitude": -32.202924, "width": 500, "height": 375, "upload_date": "25 June 2006", "owner_id": 4483, "owner_name": "Miguel Coranti", "owner_url": "http://www.panoramio.com/user/4483"}
],
"users": [{"photo_id": 27932, "photo_title": "Atardecer en Embalse", "photo_url": "http://www.panoramio.com/photo/27932", "photo_file_url": "http://mw2.google.com/mw-panoramio/photos/medium/27932.jpg", "longitude": -64.404945, "latitude": -32.202924, "width": 500, "height": 375, "upload_date": "25 June 2006", "owner_id": 4483, "owner_name": "Miguel Coranti", "owner_url": "http://www.panoramio.com/user/4483"}
]';*/
die;	
	}

public function checklogin()
{
	$auth = Zend_Auth::getInstance();
	
	$errorMessage = ""; 
	/*************** check user identity ************/
	if(!$auth->hasIdentity())  
	{  
		$this->_redirect('admin/index');  
	} 
}

public function getUserByClusterAction(){ 

$cluster_id =  $this->_getParam('cluster_id'); 
$users = new Application_Model_Users();

$user_list_unformat = $users->getEnggNameByClusterID($cluster_id);
$user_list[] = array("value"=>"-",'text'=>"Staff Code");
foreach($user_list_unformat as $single){
	if($single['StaffCode']==$cluster_id){
		continue;
		}
	$user_list[] = array("value"=>$single['StaffCode'],"text"=>$single['StaffName']."(StaffCode : ".$single['StaffCode'].")");
	
	}
    $this->getHelper('Layout')->disableLayout();

    $this->getHelper('ViewRenderer')->setNoRender();

    $this->getResponse()->setHeader('Content-Type', 'application/json');
echo json_encode(array('options'=>$user_list));
return;	
}
}



