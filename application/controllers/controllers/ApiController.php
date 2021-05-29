<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.

 * File Name   : ApiController.php

 * File Description  : All Api Method

 * Created By : abhishek kumar mishra

 * Created Date: 01 june 2015

 ***************************************************************/



class ApiController extends Zend_Controller_Action

{

	var $phpNative;

	

	var $secretkey;

	var $db;

	var $currdate;

	var $siteurl;



	public function init()

	{

        //Initialize action controller here 

		$this->_helper->viewRenderer->setNoRender(true);
		$bootstrap = $this->getInvokeArg('bootstrap');

		$aConfig = $bootstrap->getOptions();

		$this->secretkey = $aConfig['api']['searchuser']['secret'];

		$this->siteurl = $aConfig['api']['site']['url'];

		$this->db = Zend_Db_Table::getDefaultAdapter();

		$this->currdate = date("Y-m-d H:i:s",strtotime('+330 minutes'));//$date->toString('Y-m-d H:m:s');

		$this->day = date("d");

		$this->month = date("m");

		$this->year = date("Y");



	}



	

	/**

	* index() method is used to api index

	* @param NULL

	* @return True 

	*/	

	public function indexAction(){
		echo "Welcome malt api controller";
		exit;
	}


	public function serverUrlAction(){
		echo json_encode(array("error_code"=>'0', 'response_string'=>'success.', 'serverurl'=>'http://elektra.logimetrix.me/api'));
		exit;
	}

	


	/**

	* getnotification() is used to get notification

	* @param NULL

	* @return JSON 

	*/	

	public function getNotificationAction(){
	    // Get the Request parameters
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
		// Genaral Information of the user login details
		// $appkey	    = isset($params['appkey'])?$params['appkey']:"" ;
		// $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		// $device_id	= isset($params['device_id'])?$params['device_id']:"";

		$loginData 			= json_decode(file_get_contents('php://input'), true);		
		$appkey	 		    = isset($loginData['appkey'])?$loginData['appkey']:"";		
		$LoginID	 		= isset($loginData['LoginID'])?$loginData['LoginID']:"";		
		$device_id	 		= isset($loginData['device_id'])?$loginData['device_id']:"";
		$error_code = 1;
		$succes = FALSE;

		try{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id ){
				throw new Exception("Required parameter missing.");
			}	

			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			$count =  count($alldata);

			if(!empty($alldata['LoginID']) != $LoginID)
			{
				throw new Exception('Invalid user.');
			}
			if(strtoupper($alldata['StaffStatus']) != 'AC'){
				throw new Exception('Your account is not active.');
			}	

			$sql_notification_list = "select id, notification_content from tbl_notification where status = '1'";
			$notification_data   = $db->fetchAll($sql_notification_list);
			$db->commit();
			$succes = TRUE;	
		}

		catch(Exception $e){
			//Rollback transaction
			$db->rollBack();
			$error= $e->getMessage();
		}

		if($succes == TRUE ){
			echo json_encode(array("error_code"=>'0', "notification_data"=>$notification_data));
			exit;
		}

		else{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}

	/**

	* login() method is used to android user login

	* @param String

	* @return JSON 

	*/	



	public function loginAction(){ 
        // Get the Request parameters
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;              
		// Genaral Information of the user login details

		//$appkey = "elektra@123";
		$appkey	= isset($params['appkey'])?$params['appkey']:"";
		$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		$device_type	= isset($params['device_type'])?$params['device_type']:"";
		$device_id	= isset($params['device_id'])?$params['device_id']:"";

		$error_code = 1;
		$succes = FALSE;
		try{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id){
				throw new Exception("Required parameter missing.");
			}		
			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			if(!empty($alldata['LoginID']) != $LoginID){
				throw new Exception('Invalid login id.');
			}

			if(!empty($alldata['DeviceID'])){			
				if($alldata['DeviceID'] != $device_id){
					$error_code = 3;
					throw new Exception('You are not athorized for this device.');
				}
			}
			if(strtolower($alldata['StaffStatus']) != 'ac'){
				$error_code = 3;
				throw new Exception('Your account is not active.');
			}	

			if($alldata['App_Access_Status'] == 2){
				$error_code = 2;
				throw new Exception('This account deactivated by admin.');
			} 

			$update = array();
			$update['Device_type'] = $device_type;
			$update['DeviceID'] = $device_id;   // IMEI Code
			$update['Attendance_time'] = $this->currdate;
			//$update['gps_status'] = 'on';
			$user->updateSingleTableData('logi_field_users', $update, 'LoginID', $alldata['LoginID']);

			$LoginID = $alldata['LoginID'];
			$StaffName = $alldata['StaffName'];
			$UserID =  $alldata['userId'];
                     // $region =$user->getRegionIdData($alldata['userId']);
                   //   $regionID = $region['region_id'];


			$user_login_data = array();
			$user_login_data['mob_user_staff_code'] = $LoginID;
			$user_login_data['login_time'] = $this->currdate;
			$user_login_data['server_detail'] = json_encode($_SERVER);
			$user->insertUserLoginDetailsByLoginID($user_login_data);


			// Notification message insert notification table

			$notificationData = array();
			$notificationData['message'] = $StaffName.' ('.$LoginID.') <br> Status: login';
			$notificationData['mob_user_loginID'] = $LoginID;
			$notificationData['type'] = "Login";
			$notificationData['statustype'] = "login";
			$notificationData['noti_date'] = date('Y-m-d H:i:s');
			$db->insert('logi_notification',$notificationData);

            // All master data

			$sql_user_list = "select identity, StaffName from logi_field_users where LoginID !='".$LoginID."' and identity !=''";
			$result_user   = $db->fetchAll($sql_user_list);


			$db->commit();                      
			$succes = TRUE;	
		}

		catch(Exception $e){
			//Rollback transaction
			$db->rollBack();
			$error= $e->getMessage();
		}
		if($succes == TRUE ){
			//'MappedVillageLsit'=>$villageList,
			
			echo json_encode(array("error_code"=>'0', 'response_string'=>'login success.', 'user_list'=>$result_user));
			exit;
		}

		else{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
			exit;
		}

	}


     /**

	* farmerRegisterAction() method is register a farmer

	* @param NULL

	* @return JSON 

	*/




	public function insertCapturedInformationAction(){

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();

		$secretkey = $this->secretkey;

		$db = $this->db;

		// Genaral Information of the user login details

		$appkey	= isset($params['appkey'])?$params['appkey']:"";
		$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		$device_id	= isset($params['device_id'])?$params['device_id']:""; 
		$captured_info_type	= isset($params['captured_info_type'])?$params['captured_info_type']:"";

                 //For Contact Information Params//


		$name	= isset($params['name'])?$params['name']:"";
		$mobile = isset($params['mobile'])?$params['mobile']:"";
		$whatsapp	= isset($params['whatsapp'])?$params['whatsapp']:"";
		$profession	= isset($params['profession'])?$params['profession']:"";
		$cast	= isset($params['cast'])?$params['cast']:"";
		$address	= isset($params['address'])?$params['address']:"";
		$polling_both	= isset($params['polling_both'])?$params['polling_both']:"";
		$party_affilitation	= isset($params['party_affilitation'])?$params['party_affilitation']:"";
		$party_name	        = isset($params['party_name'])?$params['party_name']:"";
		$offline_sink_datetime         = isset($params['offline_sink_datetime'])?$params['offline_sink_datetime']:"";
		$availiability         = isset($params['availiability'])?$params['availiability']:"";
		$lat	= isset($params['lat'])?$params['lat']:""; 
		$long	= isset($params['long'])?$params['long']:""; 
		$project	= isset($params['project'])?$params['project']:""; 

                 // End of Contact Information Params //


                 //For Placed Information Params//

		$place_description	= isset($params['place_description'])?$params['place_description']:"";
		$consenred_person= isset($params['consenred_person'])?$params['consenred_person']:"";
		$designation = isset($params['designation'])?$params['designation']:"";

		$contact	= isset($params['contact'])?$params['contact']:"";
		$address	= isset($params['address'])?$params['address']:"";
		$lat	= isset($params['lat'])?$params['lat']:"";
		$long	= isset($params['long'])?$params['long']:"";
		$offline_sink_datetime	= isset($params['offline_sink_datetime'])?$params['offline_sink_datetime']:"";

		$under	= isset($params['under'])?$params['under']:""; 

                 // End of Placed Information Params //

		$error_code = 1;

		$succes = FALSE;

		try

		{

			$db->beginTransaction();

			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id){
				throw new Exception("Required parameter missing.");
			}			
			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			if(!empty($alldata['LoginID']) != $LoginID){
				throw new Exception('Invalid login id.');
			}

			if(!empty($alldata['DeviceID']))
			{			
				if($alldata['DeviceID'] != $device_id)
				{
					$error_code = 3;
					throw new Exception('You are not athorized for this device.');
				}

			}
			if(strtolower($alldata['StaffStatus']) != 'ac')
			{
				$error_code = 3;
				throw new Exception('Your account is not active.');
			}	

			if($alldata['App_Access_Status'] == 2)
			{
				$error_code = 2;
				throw new Exception('This account deactivated by admin.');
			}


			// Add Basic Information of farmer
			if($captured_info_type ==1){            
				if(isset($_FILES['photograph_pi']['tmp_name']) AND !empty($_FILES['photograph_pi']['tmp_name'])){
					$tempName = $_FILES['photograph_pi']['tmp_name'];
					$imageName = time().$_FILES['photograph_pi']['name']; 
					$uploads = 'uploads/user_photograph/';
					if(!file_exists($uploads)){
						mkdir($uploads);	
					}
					$pathComplete = $uploads.$imageName;
					@move_uploaded_file($tempName,$pathComplete);
				}                        
				$insertcapturedData = array();
				$insertcapturedData['place_description'] = $place_description;
				$insertcapturedData['consenred_person'] = $consenred_person;
				$insertcapturedData['designation'] = $designation;
				$insertcapturedData['contact'] = $contact;
				$insertcapturedData['address'] = $address;
				$insertcapturedData['lat'] = $lat;
				$insertcapturedData['long'] = $long;
				$insertcapturedData['offline_sink_datetime'] = $offline_sink_datetime;
				$insertcapturedData['photo'] = $pathComplete;
				$insertcapturedData['user_id'] = $LoginID;
				$insertcapturedData['project'] = $project;
				$insertcapturedData['under']   = $under;
				$db->insert('place_info',$insertcapturedData);
			}
			else{

				if(isset($_FILES['photograph_ci']['tmp_name']) AND !empty($_FILES['photograph_ci']['tmp_name'])){
					$tempName = $_FILES['photograph_ci']['tmp_name'];
					$imageName = time().$_FILES['photograph_ci']['name']; 
					$uploads = 'uploads/user_photograph/';
					if(!file_exists($uploads)){
						mkdir($uploads);	
					}
					$pathComplete = $uploads.$imageName;
					@move_uploaded_file($tempName,$pathComplete);
				} 

				$insertData = array();
				$insertData['name'] = $name;
				$insertData['mobile'] = $mobile;
				$insertData['whatsapp'] = $whatsapp;
				$insertData['profession'] = $profession;
				$insertData['cast'] = $cast;
				$insertData['address'] = $address;
				$insertData['polling_both'] = $polling_both;
				$insertData['party_affilitation'] = $party_affilitation;
				$insertData['offline_sink_datetime'] = $offline_sink_datetime;
				$insertData['party_name'] = $party_name;
				$insertData['availiability'] = $availiability;
				$insertData['lat'] = $lat;
				$insertData['long'] = $long;
				$insertData['photo'] = $pathComplete;
				$insertData['user_id'] = $LoginID;
				$insertData['project'] = $project;
				$insertData['under']   = $under;
				$db->insert('contact_info',$insertData);
			}

			$update['Last_location_service_hit_time'] = $offline_sink_datetime;

			$user->updateSingleTableData('logi_field_users',$update,'LoginID',$alldata['LoginID']);



			$db->commit();

			$succes = TRUE;	

		}

		catch(Exception $e)

		{

			//Rollback transaction

			$db->rollBack();

			$error= $e->getMessage();

		}



		if($succes == TRUE )

		{

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Captured Information added Successfully.'));

			exit;

		}

		else

		{

			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));

			exit;

		}

	}





      /**

	* seedOrderAction() method is register a farmer

	* @param NULL

	* @return JSON 

	*/

































      /**

	* secondaryFramerRegistrationAction() method is register a farmer second time

	* @param NULL

	* @return JSON 

	*/


	
	
	
	/**

	* locationTrack() method is used to get all jobs

	* @param NULL

	* @return JSON 

	*/	

	public function locationTrackAction(){
	    // Get the Request parameters
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
		// Genaral Information of the user login details
		$appkey	= isset($params['appkey'])?$params['appkey']:"" ;
		$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		$device_id	= isset($params['device_id'])?$params['device_id']:"";
		$lat	= isset($params['lat'])?$params['lat']:"";
		$long	= isset($params['long'])?$params['long']:"";
		$offline_sink_datetime	= isset($params['offline_sink_datetime'])?$params['offline_sink_datetime']:"";
		$curr_date	= isset($params['curr_date'])?$params['curr_date']:"";
		$error_code = 1;
		$succes = FALSE;

		try{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id || !$lat || !$long){
				throw new Exception("Required parameter missing.");
			}			
			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			$count =  count($alldata);
			if(!empty($alldata['LoginID']) != $LoginID)
			{
				throw new Exception('Invalid user.');
			}
			if(strtoupper($alldata['StaffStatus']) != 'AC'){
				throw new Exception('Your account is not active.');
			}	

			

			$StaffName = $alldata['StaffName'];
			$insert = array();
			$insert['mob_user_staff_code'] = $alldata['LoginID'];
			$insert['lat'] = $lat;
			$insert['longitude'] = $long;
			$insert['add_date_time'] = $offline_sink_datetime;
			$insertId  = $user->insertLocationTrackByStaffCode($insert);

			// update user table

			if($alldata['live_on_date'] != $curr_date){
				$update['Current_latitude'] = $lat;
				$update['Current_longitude'] = $long;                           
				$update['live_on_date'] = $curr_date;
				$user->updateSingleTableData('logi_field_users',$update,'LoginID',$alldata['LoginID']);
			}	

			$db->commit();
			$succes = TRUE;	
		}

		catch(Exception $e){

			//Rollback transaction

			$db->rollBack();
			$error= $e->getMessage();
		}
		if($succes == TRUE ){
			echo json_encode(array("error_code"=>'0', 'response_string'=>'Location track added successfully.'));
			exit;
		}

		else{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}
	


	/* ---- Function for all chemical master to through on device*/
	
	
	function chemicalMasterAction(){
		
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
		$appkey	= isset($params['appkey'])?$params['appkey']:"";
		$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		$device_id	= isset($params['device_id'])?$params['device_id']:"";  
		$error_code = 1;
		$succes = FALSE;
		try	{
			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id){
				throw new Exception("Required parameter missing.");
			}			
			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			if(!empty($alldata['LoginID']) != $LoginID){
				throw new Exception('Invalid login id.');
			}                       
			if(!empty($alldata['DeviceID'])){			
				if($alldata['DeviceID'] != $device_id)
				{
					$error_code = 3;
					throw new Exception('You are not athorized for this device.');
				}

			}
			if(strtolower($alldata['StaffStatus']) != 'ac'){
				$error_code = 3;
				throw new Exception('Your account is not active.');
			}	

			if($alldata['App_Access_Status'] == 2){
				$error_code = 2;
				throw new Exception('This account deactivated by admin.');
			}

			$chemical = new Application_Model_States();
			$chemical_type = $chemical->getAllChemicalTypeForApi(); 
			$chemical = $chemical->getAllChemicalsForApi(); 
			$db->commit();
			$succes = TRUE;	
		}

		catch(Exception $e){
			//Rollback transaction
			$db->rollBack();
			$error= $e->getMessage();
		}

		if($succes == TRUE )
		{
			echo json_encode(array("error_code"=>'0', 'response_string'=>'List of chemical and chemical type','chemical_type'=>$chemical_type,'chemical'=>$chemical));
			exit;

		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}
	
	
	public function chemicalOrderAction(){
		$db = $this->db;
		$params = $this->getRequest()->getParams();
		$appkey	= isset($params['appkey'])?$params['appkey']:"";
		$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
		$device_id	= isset($params['device_id'])?$params['device_id']:"";

      //  $farmerId	= isset($params['farmerId'])?$params['farmerId']:"";
		$farmerCode	= isset($params['farmerCode'])?$params['farmerCode']:"";
		$date_of_order	= isset($params['date_of_order'])?$params['date_of_order']:"";
		$expected_date_delivery		= isset($params['expected_date_delivery'])?$params['expected_date_delivery']:"";
		$total_amount	= isset($params['total_amount'])?$params['total_amount']:"";
		$paid_amount	= isset($params['paid_amount'])?$params['paid_amount']:"";
		$vat	= isset($params['vat'])?$params['vat']:"";
		$grand_total	= isset($params['grand_total'])?$params['grand_total']:"";


		if(get_magic_quotes_gpc()){
			$chemcial_list = stripslashes($params['chemical_data']);
		}else{
			$chemcial_list = $params['chemical_data'];
		}
		$chemcial_list = json_decode($chemcial_list,true);


		$secretkey = $this->secretkey;
		$error_code = 1;
		$succes = FALSE;
		try	{
			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id){
				throw new Exception("Required parameter missing.");
			}			
			$user = new Application_Model_Users();
			$alldata = $user->getUserDataByLoginId($LoginID);
			if(!empty($alldata['LoginID']) != $LoginID){
				throw new Exception('Invalid login id.');
			}                       
			if(!empty($alldata['DeviceID'])){			
				if($alldata['DeviceID'] != $device_id)
				{
					$error_code = 3;
					throw new Exception('You are not athorized for this device.');
				}

			}
			if(strtolower($alldata['StaffStatus']) != 'ac'){
				$error_code = 3;
				throw new Exception('Your account is not active.');
			}	

			if($alldata['App_Access_Status'] == 2){
				$error_code = 2;
				throw new Exception('This account deactivated by admin.');
			}

			$farmerMaster = new Application_Model_Farmermasters();
					//db operation
			$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmerCode);          

			$farmerID = $allFarmerData['id'];
			$farmerCode = $allFarmerData['FarmerCode'];

			$insertChemicalData = array();
			$insertChemicalData['farmerId'] = $farmerID;
			$insertChemicalData['farmerCode'] = $farmerCode;
			$insertChemicalData['allotedFieldEngg'] = $LoginID;
			$insertChemicalData['date_of_order'] = $date_of_order;
			$insertChemicalData['expected_date_delivery'] = $expected_date_delivery;
			$insertChemicalData['total_amount'] = $total_amount;
			$insertChemicalData['paid_amount'] = $paid_amount;
					$insertChemicalData['finalpaid_amount'] = $grand_total-$paid_amount; // remaining AMount
					$insertChemicalData['vat'] = $vat;
					$insertChemicalData['grand_total'] = $grand_total;
					$insertChemicalData['created'] = $this->currdate;

					$db->insert('logi_chemical_orders',$insertChemicalData);	
					$rawOrderId = $db->lastInsertId();
					$finalOrderId = str_pad($rawOrderId, 6, '0', STR_PAD_LEFT);
					$finalOrderId = 'C'.''.$finalOrderId;
					// update OrderID
					
					$update = array();
					$update['orderId'] = $finalOrderId;
					$farmerMaster->updateChemicalOrderId($update, $rawOrderId);
					
					
					/*$paymentrecord = array();
				    $paymentrecord['order_id'] = $finalOrderId;
				    $paymentrecord['received_amount'] = $paid_amount;
				    $paymentrecord['date'] = $date_of_order;
				    $db->insert('logi_part_payment', $paymentrecord);*/


					// final multiple data for chemical list

				    $k=0;
				    if(count($chemcial_list)>0){
				    	foreach($chemcial_list as $k){
				    		$insertData_fland = array();
				    		$insertData_fland['orderId'] = $finalOrderId;
				    		$insertData_fland['chemicalType_id'] = $k['chemicalType_id'];
				    		$insertData_fland['chemicalId'] = $k['chemicalId'];
				    		$insertData_fland['qty'] = $k['qty'];
				    		$insertData_fland['allotedFieldEngg'] = $LoginID;
				    		$insertData_fland['rate'] = $k['rate'];
				    		$insertData_fland['total'] = $k['total'];
				    		$insertData_fland['created'] = $this->currdate;
				    		$db->insert('logi_chemical_order_list', $insertData_fland);
				    		$k++;
				    	} }

				    	$db->commit();
				    	$succes = TRUE;	 

				    }

				    catch(Exception $e){
				//Rollback transaction
				    	$db->rollBack();
				    	$error= $e->getMessage();
				    }

				    if($succes == TRUE )
				    {
				    	echo json_encode(array("error_code"=>'0', 'response_string'=>'Chemical Order Successfully done','orderId'=>$finalOrderId));
				    	exit;

				    }

				    else
				    {
				    	echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
				    	exit;
				    }
				}


				function chemicalOrderListAction(){

					$params = $this->getRequest()->getParams();
					$secretkey = $this->secretkey;
					$db = $this->db;
					$appkey	= isset($params['appkey'])?$params['appkey']:"";
					$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
					$device_id	= isset($params['device_id'])?$params['device_id']:"";   
					$error_code = 1;
					$succes = FALSE;
					try	{
						$db->beginTransaction();
						if($secretkey != $appkey){
							throw new Exception("Invalid request.");
						}	
						if(!$LoginID || !$device_id){
							throw new Exception("Required parameter missing.");
						}			
						$user = new Application_Model_Users();
						$alldata = $user->getUserDataByLoginId($LoginID);
						if(!empty($alldata['LoginID']) != $LoginID){
							throw new Exception('Invalid login id.');
						}                       
						if(!empty($alldata['DeviceID'])){			
							if($alldata['DeviceID'] != $device_id)
							{
								$error_code = 3;
								throw new Exception('You are not athorized for this device.');
							}

						}
						if(strtolower($alldata['StaffStatus']) != 'ac'){
							$error_code = 3;
							throw new Exception('Your account is not active.');
						}	

						if($alldata['App_Access_Status'] == 2){
							$error_code = 2;
							throw new Exception('This account deactivated by admin.');
						}

						$chemical = new Application_Model_Chemicals();
						$chemical_order = $chemical->getAllChemicalOrderApi($LoginID); 
						$chemical_list = $chemical->getAllChemicalsOrderListApi($LoginID); 
						$db->commit();
						$succes = TRUE;	
					}

					catch(Exception $e){
			//Rollback transaction
						$db->rollBack();
						$error= $e->getMessage();
					}

					if($succes == TRUE )
					{
						echo json_encode(array("error_code"=>'0', 'response_string'=>'List of chemical orders','chemical_order'=>$chemical_order,'chemical_order_list'=>$chemical_list));
						exit;

					}

					else
					{
						echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
						exit;
					}

				}

				function seedOrderListAction(){

					$params = $this->getRequest()->getParams();
					$secretkey = $this->secretkey;
					$db = $this->db;
					$appkey	= isset($params['appkey'])?$params['appkey']:"";
					$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
					$device_id	= isset($params['device_id'])?$params['device_id']:"";   
					$error_code = 1;
					$succes = FALSE;
					try	{
						$db->beginTransaction();
						if($secretkey != $appkey){
							throw new Exception("Invalid request.");
						}	
						if(!$LoginID || !$device_id){
							throw new Exception("Required parameter missing.");
						}			
						$user = new Application_Model_Users();
						$alldata = $user->getUserDataByLoginId($LoginID);
						if(!empty($alldata['LoginID']) != $LoginID){
							throw new Exception('Invalid login id.');
						}                       
						if(!empty($alldata['DeviceID'])){			
							if($alldata['DeviceID'] != $device_id)
							{
								$error_code = 3;
								throw new Exception('You are not athorized for this device.');
							}

						}
						if(strtolower($alldata['StaffStatus']) != 'ac'){
							$error_code = 3;
							throw new Exception('Your account is not active.');
						}	

						if($alldata['App_Access_Status'] == 2){
							$error_code = 2;
							throw new Exception('This account deactivated by admin.');
						}

						$seed = new Application_Model_Seeds();
						$seed_order_list = $seed->getAllSeedOrderListApi($LoginID); 

						$db->commit();
						$succes = TRUE;	
					}

					catch(Exception $e){
			//Rollback transaction
						$db->rollBack();
						$error= $e->getMessage();
					}

					if($succes == TRUE )
					{
						echo json_encode(array("error_code"=>'0', 'response_string'=>'List of Seed orders','seed_order_list'=>$seed_order_list));
						exit;

					}

					else
					{
						echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
						exit;
					}

				}

				function chemicalDeliveryAction(){

					$params = $this->getRequest()->getParams();
					$secretkey = $this->secretkey;
					$db = $this->db;
					$appkey	= isset($params['appkey'])?$params['appkey']:"";
					$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
					$device_id	= isset($params['device_id'])?$params['device_id']:"";   
					$orderId	= isset($params['orderId'])?$params['orderId']:"";
					$paid_amount	= isset($params['paid_amount'])?$params['paid_amount']:"";
					$date_delivery	= isset($params['date_delivery'])?$params['date_delivery']:"";

					$error_code = 1;
					$succes = FALSE;
					try	{
						$db->beginTransaction();
						if($secretkey != $appkey){
							throw new Exception("Invalid request.");
						}	
						if(!$LoginID || !$device_id || !$orderId){
							throw new Exception("Required parameter missing.");
						}			
						$user = new Application_Model_Users();

						$alldata = $user->getUserDataByLoginId($LoginID);
						if(!empty($alldata['LoginID']) != $LoginID){
							throw new Exception('Invalid login id.');
						}                       
						if(!empty($alldata['DeviceID'])){			
							if($alldata['DeviceID'] != $device_id)
							{
								$error_code = 3;
								throw new Exception('You are not athorized for this device.');
							}

						}

						if(strtolower($alldata['StaffStatus']) != 'ac'){
							$error_code = 3;
							throw new Exception('Your account is not active.');
						}	

						if($alldata['App_Access_Status'] == 2){
							$error_code = 2;
							throw new Exception('This account deactivated by admin.');
						}

						$chemicalMaster = new Application_Model_Chemicals();
						$seedMaster = new Application_Model_Seeds();

						$partPaymentCount=$seedMaster->getpartPaymentCount($orderId); 

						$GetlastPaidAmountForChemical = $chemicalMaster->GetlastPaidAmountForChemical($orderId);
						$PaidFinalAmount= $paid_amount + $GetlastPaidAmountForChemical['paid_amount'];
						$OutStandingBalWithFarmer = $GetlastPaidAmountForChemical['finalpaid_amount']-$paid_amount;
					//db operation
						$updateData = array();


						if($partPaymentCount['val']>0){      
							$updateData['paid_amount'] = $PaidFinalAmount;
						$updateData['finalpaid_amount'] = $OutStandingBalWithFarmer; // Remaining Amount with Farmer
					}  
					else {
						$updateData['paid_amount'] = $GetlastPaidAmountForChemical['paid_amount'];
						$updateData['finalpaid_amount'] = $GetlastPaidAmountForChemical['finalpaid_amount'];
					}
					
					if($OutStandingBalWithFarmer ==0){
						$updateData['date_delivery'] = $date_delivery;
					}    
					$chemicalMaster->updateChemicalDeliveryApi($updateData,$orderId);
					
					$paymentrecord = array();
					$paymentrecord['order_id'] = $orderId;
					$paymentrecord['received_amount'] = $paid_amount;
					$paymentrecord['date'] = $date_delivery;
					$db->insert('logi_part_payment', $paymentrecord); 

					$db->commit();
					$succes = TRUE;	
				}

				catch(Exception $e){
			//Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE )
				{
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Chemical Delivery Success','params'=>$params));
					exit;

				}

				else
				{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
					exit;
				}

			}	

			function seedDeliveryAction(){

				$params = $this->getRequest()->getParams();
				$secretkey = $this->secretkey;
				$db = $this->db;
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";   
				$orderId	= isset($params['orderId'])?$params['orderId']:"";
				$PaidAmount	= isset($params['PaidAmount'])?$params['PaidAmount']:"";
				$date_delivery	= isset($params['date_delivery'])?$params['date_delivery']:"";
				$part_payment_date	= isset($params['part_payment_date'])?$params['part_payment_date']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$orderId){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();

					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id)
						{
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}

					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$seedMaster = new Application_Model_Seeds();
                                                //db operation

					$partPaymentCount=$seedMaster->getpartPaymentCount($orderId);  

					$GetlastPaidAmountForseed = $seedMaster->GetlastPaidAmountForseed($orderId);	

					$PaidFinalAmount= $PaidAmount + $GetlastPaidAmountForseed['PaidAmount'];

					$PaidOutStandingAmount = $GetlastPaidAmountForseed['balance_amount']-$PaidAmount;

					$updateData = array();					
					$updateData['paid_amount'] = $PaidFinalAmount;
					if($partPaymentCount['val']>0){      
						$updateData['balance_amount'] = $PaidOutStandingAmount;
					}  
					else{
						$updateData['balance_amount'] = $GetlastPaidAmountForseed['balance_amount'];
					} 
					if($PaidOutStandingAmount ==0){
						$updateData['DeliveryDate'] = $date_delivery;
					}   

					$seedMaster->updateSeedDeliveryApi($updateData,$orderId);

					$paymentrecord = array();
					$paymentrecord['order_id'] = $orderId;
					$paymentrecord['received_amount'] = $PaidAmount;
					$paymentrecord['date'] = $part_payment_date;


					$seedMaster->insertPartPaymentData($paymentrecord);


					$db->commit();
					$succes = TRUE;	
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE )
				{
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Seed Delivery Successfull'));
					exit;

				}

				else
				{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
					exit;
				}

			}


			/******************************** Api For Adding Master Data**********************************/            

			function getMasterdataAction(){
				$params = $this->getRequest()->getParams();
				$secretkey = $this->secretkey;
				$db = $this->db;
                    // Genaral Information of the user login details
				$appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"SUF1034";
                    //$device_type	= isset($params['device_type'])?$params['device_type']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"354101061119960";
				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}

					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}

					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}		
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);


					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$GetAllSoilTypeData = $farmerMaster->GetAllSoilTypeData();

					$GetAllTopographyData = $farmerMaster->GetAllChemicslData();

					$GetCompanyMasterData= $farmerMaster->GetCompanyMasterData();
					$GetMasterIngredienData = $farmerMaster->GetMasterIngredienData();
					$GetMasterSourceData = $farmerMaster->GetMasterSourceData();
					$GetApplicationData = $farmerMaster->GetApplicationData();
					$GetLastCropData = $farmerMaster->GetLastCropData();

					$GetShowingData = $farmerMaster->GetShowingData();
					$GetIrregationData = $farmerMaster->GetWayofirregationData();
					$GetWaterSourceData = $farmerMaster->GetWaterSourceData();
					$GetSeedTypeData = $farmerMaster->GetSeedTypeData();

					$GetRowData = $farmerMaster->GetRowData();
					$GetSoilfertilityTypeData = $farmerMaster->GetSoilfertilityTypeData();
					$GetCropirrigationNumberData = $farmerMaster->GetCropirrigationNumberData();
					$GetLandpreprationData = $farmerMaster->GetLandpreprationData();
					$GetFertilitytypeData = $farmerMaster->GetFertilitytypeData();
					$GetSoilmoistureData = $farmerMaster->GetSoilmoistureData();


					$GetSowingmoistureSourceData = $farmerMaster->GetSowingmoistureSourceData();
					$GetStageofCropData = $farmerMaster->GetStageofCropData();
					$GetDiseasescoringData = $farmerMaster->GetDiseasescoringData();
					$GetLodgingscoringData = $farmerMaster->GetLodgingscoringData();
					$GetQualityofCropData = $farmerMaster->GetQualityofCropData();
					$GetYieldcomponentTypeData = $farmerMaster->GetYieldcomponentTypeData();


					$GettopographyData = $farmerMaster->GettopographyData();
					$GetLandunitAcreageData = $farmerMaster->GetLandunitAcreageData();

					$GetLandunitKgData = $farmerMaster->GetLandunitKgData();
					$GetCropunitMlData = $farmerMaster->GetCropunitMlData();

					$GetPreviouscropData = $farmerMaster->GetPreviouscropData();

					$GetLandshowingDepthData = $farmerMaster->GetLandshowingDepthData();
					$GetCropunittypesData = $farmerMaster->GetCropunittypesData();

					$db->commit();                      
					$succes = TRUE;	
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}
				if($succes == TRUE ){
                        //'MappedVillageLsit'=>$villageList,

					echo json_encode(array("error_code"=>'0', 'response_string'=>'Master Data Listed Successfully',  'Soil_type'=>$GetAllSoilTypeData, 'Soil_Chemical_data'=>$GetAllTopographyData,  'Company_data'=>$GetCompanyMasterData, 'Seed_Ingredian_data'=>$GetMasterIngredienData, 'Seed_Source_data'=>$GetMasterSourceData, 'Application_data'=>$GetApplicationData, 'Last_crop_data'=>$GetLastCropData, 'Last_Showing_data'=>$GetShowingData, 'Irregation_data'=>$GetIrregationData, 'irrigation_facility'=>$GetWaterSourceData, 'Seed_type_data'=>$GetSeedTypeData, 'Row_data'=>$GetRowData, 'Soil_fertility_type_data'=>$GetSoilfertilityTypeData, 'Crop_irrigation_number_data'=>$GetCropirrigationNumberData, 'Quality_Land_prepration_data'=>$GetLandpreprationData, 'Fertility_type_data'=>$GetFertilitytypeData, 'Soil_moisture_data'=>$GetSoilmoistureData, 'Sowing_moisture_source_data'=>$GetSowingmoistureSourceData, 'Stage_of_crop_data'=>$GetStageofCropData, 'Disease_scoring_data'=>$GetDiseasescoringData, 'Lodging_scoring_data'=>$GetLodgingscoringData, 'Quality_of_crop_data'=>$GetQualityofCropData, 'Yield_component_type_data'=>$GetYieldcomponentTypeData, 'Topography_data'=>$GettopographyData, 'Unit_acreage_data'=>$GetLandunitAcreageData, 'Unit_kg_data'=>$GetLandunitKgData, 'Previous_crop_data'=>$GetPreviouscropData, 'Land_showing_depth_data'=>$GetLandshowingDepthData, 'Sowing_moisture_source_data'=>$GetSowingmoistureSourceData,'Stage_of_crop_data'=>$GetStageofCropData,'Disease_scoring_data'=>$GetDiseasescoringData,'Lodging_scoring_data'=>$GetLodgingscoringData,'Quality_of_crop_data'=>$GetQualityofCropData,'Yield_component_type_data'=>$GetYieldcomponentTypeData,'Crop_unit_ml_data'=>$GetCropunitMlData));
					exit;
				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
					exit;
				}

			}



			/********************************End of Api For Adding Master Data**********************************/        



			/******************************** Api For  Land Detail**********************************/

			public function addLandDetailsAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;


				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_name	= isset($params['land_name'])?$params['land_name']:"";
				$land_area	= isset($params['land_area'])?$params['land_area']:"";
				$land_unit	= isset($params['land_unit'])?$params['land_unit']:"";
				$irrigation_facility_id	= isset($params['irrigation_facility_id'])?$params['irrigation_facility_id']:"";
				$soil_type_id	= isset($params['soil_type_id'])?$params['soil_type_id']:"";
				$soil_fertility_id	= isset($params['soil_fertility_id'])?$params['soil_fertility_id']:"";
				$previous_crop_id	= isset($params['previous_crop_id'])?$params['previous_crop_id']:"";
				$newly_cultivated_field	= isset($params['newly_cultivated_field'])?$params['newly_cultivated_field']:"";
				$rocky_field	= isset($params['rocky_field'])?$params['rocky_field']:"";
				$topography_id	= isset($params['topography_id'])?$params['topography_id']:"";
				$land_lat	= isset($params['land_lat'])?$params['land_lat']:"";
				$land_long	= isset($params['land_long'])?$params['land_long']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";



				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_data = array();                                            
					$land_data['land_name'] = $land_name;
					$land_data['land_area'] = $land_area;
					$land_data['land_unit'] = $land_unit;
					$land_data['irrigation_facility_id'] = $irrigation_facility_id;
					$land_data['soil_type_id'] = $soil_type_id;
					$land_data['soil_fertility_id'] = $soil_fertility_id;
					$land_data['previous_crop_id'] = $previous_crop_id;
					$land_data['newly_cultivated_field'] = $newly_cultivated_field;
					$land_data['rocky_field'] = $rocky_field;
					$land_data['topography_id'] = $topography_id;
					$land_data['land_lat'] = $land_lat;
					$land_data['land_long'] = $land_long;
					$land_data['farmer_code'] = $farmer_code;
					$land_data['farmer_name'] = $allFarmerData['FarmerName'];
                                //$land_data['fe_name'] = $alldata['$alldata'];
					$land_data['farmer_id'] = $farmerID;
					$land_data['fe_code'] = $LoginID;
					$land_data['sink_date_time'] = $sink_date_time;
					$land_data['created'] = $this->currdate; 
					$db->insert('logi_land_information', $land_data);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land Detail Inserted successfully.', 'land_detail_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}



			public function updateLandDetailsAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_detail_id	= isset($params['land_detail_id'])?$params['land_detail_id']:"";
				$land_name	= isset($params['land_name'])?$params['land_name']:"";
				$land_area	= isset($params['land_area'])?$params['land_area']:"";
				$land_unit	= isset($params['land_unit'])?$params['land_unit']:"";
				$irrigation_facility_id	= isset($params['irrigation_facility_id'])?$params['irrigation_facility_id']:"";
				$soil_type_id	= isset($params['soil_type_id'])?$params['soil_type_id']:"";
				$soil_fertility_id	= isset($params['soil_fertility_id'])?$params['soil_fertility_id']:"";
				$previous_crop_id	= isset($params['previous_crop_id'])?$params['previous_crop_id']:"";
				$newly_cultivated_field	= isset($params['newly_cultivated_field'])?$params['newly_cultivated_field']:"";
				$rocky_field	= isset($params['rocky_field'])?$params['rocky_field']:"";
				$topography_id	= isset($params['topography_id'])?$params['topography_id']:"";
				$land_lat	= isset($params['land_lat'])?$params['land_lat']:"";
				$land_long	= isset($params['land_long'])?$params['land_long']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_data = array();                                            
					$land_data['land_name'] = $land_name;
					$land_data['land_area'] = $land_area;
					$land_data['land_unit'] = $land_unit;
					$land_data['irrigation_facility_id'] = $irrigation_facility_id;
					$land_data['soil_type_id'] = $soil_type_id;
					$land_data['soil_fertility_id'] = $soil_fertility_id;
					$land_data['previous_crop_id'] = $previous_crop_id;
					$land_data['newly_cultivated_field'] = $newly_cultivated_field;
					$land_data['rocky_field'] = $rocky_field;
					$land_data['topography_id'] = $topography_id;
					$land_data['land_lat'] = $land_lat;
					$land_data['land_long'] = $land_long;
					$land_data['farmer_code'] = $farmer_code;
					$land_data['farmer_name'] = $allFarmerData['FarmerName'];
					$land_data['farmer_id'] = $farmerID;
					$land_data['fe_code'] = $LoginID;
					$land_data['sink_date_time'] = $sink_date_time;
					$land_data['created'] = $this->currdate;

					$db->update('logi_land_information',$land_data,array('land_id=?'=>$land_detail_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        

			/******************************** End of Api For Land Detail**********************************/









			/**********************************Land Prepration Api*************************************************/




			public function addLandPreprationAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;              
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$harrow_no	= isset($params['harrow_no'])?$params['harrow_no']:"";
				$cultivator_no	= isset($params['cultivator_no'])?$params['cultivator_no']:"";
				$plowing	= isset($params['plowing'])?$params['plowing']:"";
				$rotorator_no	= isset($params['rotorator_no'])?$params['rotorator_no']:"";
				$niveling_no	= isset($params['niveling_no'])?$params['niveling_no']:"";
				$date_of_preshowing_irrigation	= isset($params['date_of_preshowing_irrigation'])?$params['date_of_preshowing_irrigation']:"";
				$quality_of_land_prepration_id	= isset($params['quality_of_land_prepration_id'])?$params['quality_of_land_prepration_id']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";       



				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_pre = array();                                            
					$land_pre['land_id'] = $land_id;
					$land_pre['harrow_no'] = $harrow_no;
					$land_pre['cultivator_no'] = $cultivator_no;
					$land_pre['plowing'] = $plowing;
					$land_pre['rotorator_no'] = $rotorator_no;
					$land_pre['niveling_no'] = $niveling_no;
					$land_pre['date_of_preshowing_irrigation'] = $date_of_preshowing_irrigation;
					$land_pre['quality_of_land_prepration_id'] = $quality_of_land_prepration_id;                             
					$land_pre['farmer_code'] = $farmer_code;
					$land_pre['farmer_id'] = $farmerID;
					$land_pre['fe_code'] = $LoginID;
					$land_pre['sink_date_time'] = $sink_date_time;
					$land_pre['created'] = $this->currdate; 
					$db->insert('logi_land_prepration', $land_pre);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land Prepration Detail Inserted successfully.', 'land_prepration_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}






			public function updateLandPreprationAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$land_prepration_id	= isset($params['land_prepration_id'])?$params['land_prepration_id']:"";
				$harrow_no	= isset($params['harrow_no'])?$params['harrow_no']:"";
				$cultivator_no	= isset($params['cultivator_no'])?$params['cultivator_no']:"";
				$plowing	= isset($params['plowing'])?$params['plowing']:"";
				$rotorator_no	= isset($params['rotorator_no'])?$params['rotorator_no']:"";
				$niveling_no	= isset($params['niveling_no'])?$params['niveling_no']:"";
				$date_of_preshowing_irrigation	= isset($params['date_of_preshowing_irrigation'])?$params['date_of_preshowing_irrigation']:"";
				$quality_of_land_prepration_id	= isset($params['quality_of_land_prepration_id'])?$params['quality_of_land_prepration_id']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";   

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_pre = array();                                            
					$land_pre['land_id'] = $land_id;
					$land_pre['harrow_no'] = $harrow_no;
					$land_pre['cultivator_no'] = $cultivator_no;
					$land_pre['plowing'] = $plowing;
					$land_pre['rotorator_no'] = $rotorator_no;
					$land_pre['niveling_no'] = $niveling_no;
					$land_pre['date_of_preshowing_irrigation'] = $date_of_preshowing_irrigation;
					$land_pre['quality_of_land_prepration_id'] = $quality_of_land_prepration_id;                             
					$land_pre['farmer_code'] = $farmer_code;
					$land_pre['farmer_id'] = $farmerID;
					$land_pre['fe_code'] = $LoginID;
					$land_pre['sink_date_time'] = $sink_date_time;
					$land_pre['created'] = $this->currdate; 
					$db->update('logi_land_prepration',$land_pre,array('land_prepration_id=?'=>$land_prepration_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        

			/************************************End of Land Prepration Api**************************************************/






			/**********************************************Land Showing Api*****************************************************************/


			public function landShowingDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$mou_type_id	= isset($params['mou_type_id'])?$params['mou_type_id']:"";
				$variety_id	= isset($params['variety_id'])?$params['variety_id']:"";
				$category_id	= isset($params['category_id'])?$params['category_id']:"";
				$lot_no	= isset($params['lot_no'])?$params['lot_no']:"";
				$sowing_date	= isset($params['sowing_date'])?$params['sowing_date']:"";
				$tcw_of_seed	= isset($params['tcw_of_seed'])?$params['tcw_of_seed']:"";
				$sowing_depth	= isset($params['sowing_depth'])?$params['sowing_depth']:"";
                        //$sowing_depth_unit_id	= isset($params['sowing_depth_unit_id'])?$params['sowing_depth_unit_id']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$seed_rate_kg_per_acre	= isset($params['seed_rate_kg_per_acre'])?$params['seed_rate_kg_per_acre']:"";
				$seed_rate_per_m2	= isset($params['seed_rate_per_m2'])?$params['seed_rate_per_m2']:"";
				$sowing_type_id	= isset($params['sowing_type_id'])?$params['sowing_type_id']:"";
				$sowing_moisture_source	= isset($params['sowing_moisture_source'])?$params['sowing_moisture_source']:"";
				$row_to_row_diff	= isset($params['row_to_row_diff'])?$params['row_to_row_diff']:"";
                       // $row_to_row_diff_unit_id	= isset($params['row_to_row_diff_unit_id'])?$params['row_to_row_diff_unit_id']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_showing = array();                                            
					$land_showing['land_id'] = $land_id;
					$land_showing['mou_type_id'] = $mou_type_id;
					$land_showing['variety_id'] = $variety_id;
					$land_showing['category_id'] = $category_id;
					$land_showing['lot_no'] = $lot_no;
					$land_showing['tcw_of_seed'] = $tcw_of_seed;
					$land_showing['sowing_depth'] = $sowing_depth;
					$land_showing['sowing_date'] = $sowing_date;
                                       // $land_showing['sowing_depth_unit_id'] = $sowing_depth_unit_id;
					$land_showing['seed_rate_kg_per_acre'] = $seed_rate_kg_per_acre;
					$land_showing['seed_rate_per_m2'] = $seed_rate_per_m2;
					$land_showing['sowing_type_id'] = $sowing_type_id;
					$land_showing['sowing_moisture_source'] = $sowing_moisture_source;
					$land_showing['row_to_row_diff'] = $row_to_row_diff;
                                       // $land_showing['row_to_row_diff_unit_id'] = $row_to_row_diff_unit_id;
					$land_showing['farmer_code'] = $farmer_code;
					$land_showing['farmer_id'] = $farmerID;
					$land_showing['fe_code'] = $LoginID;
					$land_showing['sink_date_time'] = $sink_date_time;
					$land_showing['created'] = $this->currdate; 
					$db->insert('logi_land_showing', $land_showing);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land Showing Detail Inserted successfully.', 'showing_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}





			public function updateLandShowingDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$land_showing_id	= isset($params['land_showing_id'])?$params['land_showing_id']:"";
				$mou_type_id	= isset($params['mou_type_id'])?$params['mou_type_id']:"";
				$variety_id	= isset($params['variety_id'])?$params['variety_id']:"";
				$category_id	= isset($params['category_id'])?$params['category_id']:"";
				$lot_no	= isset($params['lot_no'])?$params['lot_no']:"";
				$sowing_date	= isset($params['sowing_date'])?$params['sowing_date']:"";
				$tcw_of_seed	= isset($params['tcw_of_seed'])?$params['tcw_of_seed']:"";
				$sowing_depth	= isset($params['sowing_depth'])?$params['sowing_depth']:"";
				$sowing_depth_unit_id	= isset($params['sowing_depth_unit_id'])?$params['sowing_depth_unit_id']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$seed_rate_kg_per_acre	= isset($params['seed_rate_kg_per_acre'])?$params['seed_rate_kg_per_acre']:"";
				$seed_rate_per_m2	= isset($params['seed_rate_per_m2'])?$params['seed_rate_per_m2']:"";
				$sowing_type_id	= isset($params['sowing_type_id'])?$params['sowing_type_id']:"";
				$sowing_moisture_source	= isset($params['sowing_moisture_source'])?$params['sowing_moisture_source']:"";
				$row_to_row_diff	= isset($params['row_to_row_diff'])?$params['row_to_row_diff']:"";
				$row_to_row_diff_unit_id	= isset($params['row_to_row_diff_unit_id'])?$params['row_to_row_diff_unit_id']:"";  

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_showing = array();                                            
					$land_showing['land_id'] = $land_id;
					$land_showing['mou_type_id'] = $mou_type_id;
					$land_showing['variety_id'] = $variety_id;
					$land_showing['category_id'] = $category_id;
					$land_showing['lot_no'] = $lot_no;
					$land_showing['tcw_of_seed'] = $tcw_of_seed;
					$land_showing['sowing_depth'] = $sowing_depth;
					$land_showing['sowing_date'] = $sowing_date;
					$land_showing['sowing_depth_unit_id'] = $sowing_depth_unit_id;
					$land_showing['seed_rate_kg_per_acre'] = $seed_rate_kg_per_acre;
					$land_showing['seed_rate_per_m2'] = $seed_rate_per_m2;
					$land_showing['sowing_type_id'] = $sowing_type_id;
					$land_showing['sowing_moisture_source'] = $sowing_moisture_source;
					$land_showing['row_to_row_diff'] = $row_to_row_diff;
					$land_showing['row_to_row_diff_unit_id'] = $row_to_row_diff_unit_id;
					$land_showing['farmer_code'] = $farmer_code;
					$land_showing['farmer_id'] = $farmerID;
					$land_showing['fe_code'] = $LoginID;
					$land_showing['sink_date_time'] = $sink_date_time;
					$land_showing['created'] = $this->currdate; 
					$farmerMaster->updateLandShowingDetailData($land_showing, $land_showing_id);
					$db->update('logi_land_showing',$land_showing,array('land_sowing_id=?'=>$land_showing_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}



			/*****************************************End of Land Showing Api**********************************************/




			/**************************************Api For Fertilizer******************************************/

			public function addFertilizerDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$time_of_application_id 	= isset($params['time_of_application_id'])?$params['time_of_application_id']:"";
				$fertilizer_type_id	= isset($params['fertilizer_type_id'])?$params['fertilizer_type_id']:"";
				$quantity	= isset($params['quantity'])?$params['quantity']:"";
				$quantity_unit	= isset($params['quantity_unit'])?$params['quantity_unit']:"";
				$organic_mannure_before_rabi	= isset($params['organic_mannure_before_rabi'])?$params['organic_mannure_before_rabi']:"";
				$organic_mannure_before_kharif	= isset($params['organic_mannure_before_kharif'])?$params['organic_mannure_before_kharif']:"";
				$farmer_used_date_time	= isset($params['farmer_used_date_time'])?$params['farmer_used_date_time']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";
				$total_n	= isset($params['total_n'])?$params['total_n']:"";
				$total_p	= isset($params['total_p'])?$params['total_p']:"";


				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}
					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];

					$getNPCountAgainestFarmer = $farmerMaster->getNPCountAgainestFarmer($farmerCode);

					if($getNPCountAgainestFarmer['number']=='0'){
						$fe_report = array();                                        
						$fe_report['total_n'] = $total_n;
						$fe_report['total_p'] = $total_p;
						$fe_report['farmer_code'] = $farmer_code;
						$fe_report['fe_code'] = $LoginID;
						$fe_report['sink_date_time'] = $sink_date_time;
						$fe_report['created'] = $this->currdate; 
						$db->insert('logi_farmer_fertilizer_detail', $fe_report);
					}    

					$land_fertilizer = array();                                            
					$land_fertilizer['land_id'] = $land_id;
					$land_fertilizer['time_of_application_id'] = $time_of_application_id;
					$land_fertilizer['fertilizer_type_id'] = $fertilizer_type_id;
					$land_fertilizer['quantity'] = $quantity;
					$land_fertilizer['quantity_unit'] = $quantity_unit;
					$land_fertilizer['organic_mannure_before_rabi'] = $organic_mannure_before_rabi;
					$land_fertilizer['organic_mannure_before_kharif'] = $organic_mannure_before_kharif;
					$land_fertilizer['farmer_used_date_time'] = $farmer_used_date_time;
					$land_fertilizer['farmer_code'] = $farmer_code;
					$land_fertilizer['farmer_id'] = $farmerID;
					$land_fertilizer['fe_code'] = $LoginID;
					$land_fertilizer['sink_date_time'] = $sink_date_time;
					$land_fertilizer['created'] = $this->currdate; 
					$db->insert('logi_land_fertilizer', $land_fertilizer);

					$FertilizerId = $db->lastInsertId();



					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Fertilizer Detail Inserted successfully.', 'fertilizer_id'=>$FertilizerId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}







			public function updateFertilizerDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$fertilizer_id	= isset($params['fertilizer_id'])?$params['fertilizer_id']:"";
				$time_of_application_id 	= isset($params['time_of_application_id'])?$params['time_of_application_id']:"";
				$fertilizer_type_id	= isset($params['fertilizer_type_id'])?$params['fertilizer_type_id']:"";
				$quantity	= isset($params['quantity'])?$params['quantity']:"";
				$quantity_unit	= isset($params['quantity_unit'])?$params['quantity_unit']:"";
				$organic_mannure_before_rabi	= isset($params['organic_mannure_before_rabi'])?$params['organic_mannure_before_rabi']:"";
				$organic_mannure_before_kharif	= isset($params['organic_mannure_before_kharif'])?$params['organic_mannure_before_kharif']:"";
				$farmer_used_date_time	= isset($params['farmer_used_date_time'])?$params['farmer_used_date_time']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";   

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_fertilizer = array();                                            
					$land_fertilizer['land_id'] = $land_id;
					$land_fertilizer['time_of_application_id'] = $time_of_application_id;
					$land_fertilizer['fertilizer_type_id'] = $fertilizer_type_id;
					$land_fertilizer['quantity'] = $quantity;
					$land_fertilizer['quantity_unit'] = $quantity_unit;
					$land_fertilizer['organic_mannure_before_rabi'] = $organic_mannure_before_rabi;
					$land_fertilizer['organic_mannure_before_kharif'] = $organic_mannure_before_kharif;
					$land_fertilizer['farmer_used_date_time'] = $farmer_used_date_time;
					$land_fertilizer['farmer_code'] = $farmer_code;
					$land_fertilizer['farmer_id'] = $farmerID;
					$land_fertilizer['fe_code'] = $LoginID;
					$land_fertilizer['sink_date_time'] = $sink_date_time;
					$land_fertilizer['created'] = $this->currdate;  
					$db->update('logi_land_fertilizer',$land_fertilizer,array('land_farmer_id=?'=>$fertilizer_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        








			/**************************************End of Api For Fertilizer******************************************/



			/****************************************Api for Land Irrigation******************************************************************/

			public function landIrrigationDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$irri_type_id 	= isset($params['irri_type_id'])?$params['irri_type_id']:"";
				$irri_no_id	= isset($params['irri_no_id'])?$params['irri_no_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";
				$irri_date	= isset($params['irri_date'])?$params['irri_date']:"";
				$days_after_sowing	= isset($params['days_after_sowing'])?$params['days_after_sowing']:"";  
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_irrigation = array();                                            
					$land_irrigation['land_id'] = $land_id;
					$land_irrigation['irri_type_id'] = $irri_type_id;
					$land_irrigation['irri_no_id'] = $irri_no_id;
					$land_irrigation['stage_id'] = $stage_id;
					$land_irrigation['irri_date'] = $irri_date;
					$land_irrigation['days_after_sowing'] = $days_after_sowing;
					$land_irrigation['farmer_code'] = $farmer_code;
					$land_irrigation['farmer_id'] = $farmerID;
					$land_irrigation['fe_code'] = $LoginID;
					$land_irrigation['sink_date_time'] = $sink_date_time;
					$land_irrigation['created'] = $this->currdate; 
					$db->insert('logi_land_irrigation', $land_irrigation);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                    //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land irrigation  Detail Inserted successfully.', 'land_irrigation_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}






			public function updateLandIrrigationDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_irrigation_id	= isset($params['land_irrigation_id'])?$params['land_irrigation_id']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$irri_type_id 	= isset($params['irri_type_id'])?$params['irri_type_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";
				$irri_date	= isset($params['irri_date'])?$params['irri_date']:"";
				$days_after_sowing	= isset($params['days_after_sowing'])?$params['days_after_sowing']:"";  
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";  

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_irrigation = array();                                            
					$land_irrigation['land_id'] = $land_id;
					$land_irrigation['irri_type_id'] = $irri_type_id;
					$land_irrigation['stage_id'] = $stage_id;
					$land_irrigation['irri_date'] = $irri_date;
					$land_irrigation['days_after_sowing'] = $days_after_sowing;
					$land_irrigation['farmer_code'] = $farmer_code;
					$land_irrigation['farmer_id'] = $farmerID;
					$land_irrigation['fe_code'] = $LoginID;
					$land_irrigation['sink_date_time'] = $sink_date_time;
					$land_irrigation['created'] = $this->currdate; 

					$db->update('logi_land_irrigation',$land_irrigation,array('land_irri_id=?'=>$land_irrigation_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        


			/********************************************End of Api for Land Irrigation*************************************************************/




			/******************************************** Api for Land Cropprotection*************************************************************/




			public function landCropprotectionDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$source_id 	= isset($params['source_id'])?$params['source_id']:"";
				$chemical_type_id	= isset($params['chemical_type_id'])?$params['chemical_type_id']:"";
				$chemical_type	= isset($params['chemical_type'])?$params['chemical_type']:"";
				$company	= isset($params['company'])?$params['company']:"";
				$active_ingredient	= isset($params['active_ingredient'])?$params['active_ingredient']:"";
				$dose	= isset($params['dose'])?$params['dose']:"";
				$dose_unit_id	= isset($params['dose_unit_id'])?$params['dose_unit_id']:"";
				$area_unit_id	= isset($params['area_unit_id'])?$params['area_unit_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";                       
				$protection_date	= isset($params['protection_date'])?$params['protection_date']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$error_code = 1;
				$succes = FALSE;    
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_cropprotection = array();                                            
					$land_cropprotection['land_id'] = $land_id;
					$land_cropprotection['source_id'] = $source_id;
					$land_cropprotection['chemical_type_id'] = $chemical_type_id;
					$land_cropprotection['chemical_type'] = $chemical_type;
					$land_cropprotection['company'] = $company;
					$land_cropprotection['active_ingredient'] = $active_ingredient;
					$land_cropprotection['dose'] = $dose;


					$land_cropprotection['dose_unit_id'] = $dose_unit_id;
					$land_cropprotection['area_unit_id'] = $area_unit_id;
					$land_cropprotection['stage_id'] = $stage_id;
					$land_cropprotection['protection_date'] = $protection_date;
					$land_cropprotection['farmer_code'] = $farmer_code;
					$land_cropprotection['farmer_id'] = $farmerID;
					$land_cropprotection['fe_code'] = $LoginID;
					$land_cropprotection['sink_date_time'] = $sink_date_time;
					$land_cropprotection['created'] = $this->currdate; 
					$db->insert('logi_land_cropprotection', $land_cropprotection);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land Cropprotection  Detail Inserted successfully.', 'land_cropprotection_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}



			public function updateCropprotectionDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$land_cropprotection_id	= isset($params['land_cropprotection_id'])?$params['land_cropprotection_id']:"";
				$source_id 	= isset($params['source_id'])?$params['source_id']:"";
				$chemical_type_id	= isset($params['chemical_type_id'])?$params['chemical_type_id']:"";
				$chemical_type	= isset($params['chemical_type'])?$params['chemical_type']:"";
				$company	= isset($params['company'])?$params['company']:"";
				$active_ingredient	= isset($params['active_ingredient'])?$params['active_ingredient']:"";
				$dose	= isset($params['dose'])?$params['dose']:"";
				$dose_unit_id	= isset($params['dose_unit_id'])?$params['dose_unit_id']:"";
				$area_unit_id	= isset($params['area_unit_id'])?$params['area_unit_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";                       
				$protection_date	= isset($params['protection_date'])?$params['protection_date']:"";
				$sink_date_time	= isset($params['sink_date_time'])?$params['sink_date_time']:"";  

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_cropprotection = array();                                            
					$land_cropprotection['land_id'] = $land_id;
					$land_cropprotection['source_id'] = $source_id;
					$land_cropprotection['chemical_type_id'] = $chemical_type_id;
					$land_cropprotection['chemical_type'] = $chemical_type;
					$land_cropprotection['company'] = $company;
					$land_cropprotection['active_ingredient'] = $active_ingredient;
					$land_cropprotection['dose'] = $dose;


					$land_cropprotection['dose_unit_id'] = $dose_unit_id;
					$land_cropprotection['area_unit_id'] = $area_unit_id;
					$land_cropprotection['stage_id'] = $stage_id;
					$land_cropprotection['protection_date'] = $protection_date;
					$land_cropprotection['farmer_code'] = $farmer_code;
					$land_cropprotection['farmer_id'] = $farmerID;
					$land_cropprotection['fe_code'] = $LoginID;
					$land_cropprotection['sink_date_time'] = $sink_date_time;
					$land_cropprotection['created'] = $this->currdate; 
					$db->update('logi_land_cropprotection',$land_cropprotection,array('land_protection_id=?'=>$land_cropprotection_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        



			/********************************************End of Api for Land Cropprotection*************************************************************/



			/******************************************** Api for Land Yield component*************************************************************/   

			public function landYieldComponentDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";

				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$observation_id	= isset($params['observation_id'])?$params['observation_id']:"";
				$average_no	= isset($params['average_no'])?$params['average_no']:"";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"";







				$error_code = 1;
				$succes = FALSE;    
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);       

					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];

					$land_yield_component = array(); 

					$land_yield_component['land_id'] = $land_id;
					$land_yield_component['observation_id'] = $observation_id;
					$land_yield_component['average_no'] = $average_no;   
					$land_yield_component['farmer_code'] = $farmer_code;
					$land_yield_component['farmer_id'] = $farmerID;
					$land_yield_component['fe_code'] = $LoginID;
					$land_yield_component['sink_date_time'] = $sink_date_time;
					$land_yield_component['created'] = $this->currdate; 
					$db->insert('logi_land_yield_component', $land_yield_component);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land Yield Component  Detail Inserted successfully.', 'land_yield_component_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}









			public function updateYieldComponentDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"suf1050";
				$device_id	= isset($params['device_id'])?$params['device_id']:"354101061265987";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"SRATOBAI0941";
				$land_id	= isset($params['land_id'])?$params['land_id']:"171";
				$land_yield_component_id	= isset($params['land_yield_component_id'])?$params['land_yield_component_id']:"75";
				$observation_id	= isset($params['observation_id'])?$params['observation_id']:"1";
				$average_no	= isset($params['average_no'])?$params['average_no']:"110";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"2016-05-10 11:57:20";


				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$land_yield_component = array(); 

					$land_yield_component['land_id'] = $land_id;
					$land_yield_component['observation_id'] = $observation_id;
					$land_yield_component['average_no'] = $average_no;   
					$land_yield_component['farmer_code'] = $farmer_code;
					$land_yield_component['farmer_id'] = $farmerID;
					$land_yield_component['fe_code'] = $LoginID;
					$land_yield_component['sink_date_time'] = $sink_date_time;
					$land_yield_component['created'] = $this->currdate; 
					$db->update('logi_land_yield_component',$land_yield_component,array('yield_id=?'=>$land_yield_component_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}

			/********************************************End of Api for Land Yield component*************************************************************/ 



			/********************************************End of Api for Land Visit*************************************************************/ 


			public function landVisitDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";
				$disease_score	= isset($params['disease_score'])?$params['disease_score']:"";
				$logging_score	= isset($params['logging_score'])?$params['logging_score']:"";
				$presemption_of_insect	= isset($params['presemption_of_insect'])?$params['presemption_of_insect']:"";
				$soil_moisture_id	= isset($params['soil_moisture_id'])?$params['soil_moisture_id']:"";
				$quality_of_crop	= isset($params['quality_of_crop'])?$params['quality_of_crop']:"";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$error_code = 1;
				$succes = FALSE;    
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					if(isset($_FILES['image']['tmp_name']) AND !empty($_FILES['image']['name'])){
						$tempName = $_FILES['image']['tmp_name'];
						$imageName = time().$_FILES['image']['name']; 
						$uploads = 'uploads/visit_photograph/';
						if(!file_exists($uploads)){
							mkdir($uploads);	
						}
						$pathComplete = $uploads.$imageName;
						@move_uploaded_file($tempName,$pathComplete);

					}

					$land_visit = array(); 

					$land_visit['land_id'] = $land_id;
					$land_visit['stage_id'] = $stage_id;
					$land_visit['disease_score'] = $disease_score;
					$land_visit['logging_score'] = $logging_score;
					$land_visit['presemption_of_insect'] = $presemption_of_insect;
					$land_visit['soil_moisture_id'] = $soil_moisture_id;
					$land_visit['quality_of_crop'] = $quality_of_crop;
					$land_visit['image'] = $pathComplete;
					$land_visit['farmer_code'] = $farmer_code;
					$land_visit['farmer_id'] = $farmerID;
					$land_visit['fe_code'] = $LoginID;
					$land_visit['sink_date_time'] = $sink_date_time;
					$land_visit['created'] = $this->currdate; 
					$db->insert('logi_land_visit', $land_visit);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Land visit  Detail Inserted successfully.', 'land_visit_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}    


			public function updateLandVisitDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$land_id	= isset($params['land_id'])?$params['land_id']:"";
				$land_visit_id	= isset($params['land_visit_id'])?$params['land_visit_id']:"";
				$stage_id	= isset($params['stage_id'])?$params['stage_id']:"";
				$disease_score	= isset($params['disease_score'])?$params['disease_score']:"";
				$logging_score	= isset($params['logging_score'])?$params['logging_score']:"";
				$presemption_of_insect	= isset($params['presemption_of_insect'])?$params['presemption_of_insect']:"";
				$soil_moisture_id	= isset($params['soil_moisture_id'])?$params['soil_moisture_id']:"";
				$quality_of_crop	= isset($params['quality_of_crop'])?$params['quality_of_crop']:"";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$land_visit['land_id'] = $land_id;
					$land_visit['stage_id'] = $stage_id;
					$land_visit['disease_score'] = $disease_score;
					$land_visit['logging_score'] = $logging_score;
					$land_visit['presemption_of_insect'] = $presemption_of_insect;
					$land_visit['soil_moisture_id'] = $soil_moisture_id;
					$land_visit['quality_of_crop'] = $quality_of_crop;
					$land_visit['image'] = $pathComplete;
					$land_visit['farmer_code'] = $farmer_code;
					$land_visit['farmer_id'] = $farmerID;
					$land_visit['fe_code'] = $LoginID;
					$land_visit['sink_date_time'] = $sink_date_time;
					$land_visit['created'] = $this->currdate; 

					$db->update('logi_land_visit',$land_visit,array('visit_id=?'=>$land_visit_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        


			/********************************************End of Api for Land Visit*************************************************************/ 




			/********************************************FE visit report Api*************************************************************/

			public function feVisitReportAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$comment	= isset($params['comment'])?$params['comment']:"";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"";


				$error_code = 1;
				$succes = FALSE;    
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$fe_report = array(); 

					$fe_report['comment'] = $comment;

					$fe_report['farmer_code'] = $farmer_code;
					$fe_report['farmer_id'] = $farmerID;
					$fe_report['fe_code'] = $LoginID;
					$fe_report['sink_date_time'] = $sink_date_time;
					$fe_report['created'] = $this->currdate; 
					$db->insert('logi_fevisit_report', $fe_report);

					$landdetailId = $db->lastInsertId();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                            //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Fe report Detail Inserted successfully.', 'fe_report_id'=>$landdetailId));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}




			public function updateFeVisitReportAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$fe_report_id	= isset($params['fe_report_id'])?$params['fe_report_id']:"";
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";
				$comment	= isset($params['comment'])?$params['comment']:"";
				$sink_date_time 	= isset($params['sink_date_time'])?$params['sink_date_time']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id || !$farmer_code){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$farmerMaster = new Application_Model_Farmermasters();
					$allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);          	
					$farmerID = $allFarmerData['id'];
					$farmerCode = $allFarmerData['FarmerCode'];


					$fe_report = array(); 

					$fe_report['comment'] = $comment;

					$fe_report['farmer_code'] = $farmer_code;
					$fe_report['farmer_id'] = $farmerID;
					$fe_report['fe_code'] = $LoginID;
					$fe_report['sink_date_time'] = $sink_date_time;
					$fe_report['created'] = $this->currdate; 
					$db->update('logi_fevisit_report',updateFeVisitReport,array('id=?'=>$fe_report_id));

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data updated successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			}        






			/********************************************End of FE visit report Api*************************************************************/




			public function deleteDetailsAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$deletion_id	= isset($params['deletion_id'])?$params['deletion_id']:"";
				$type	= isset($params['type'])?$params['type']:"";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					switch($type){
						case  'sowing':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_showing',$deleteDetail,array('land_sowing_id=?'=>$deletion_id));
						break;                                                                  

						case  'prepration':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_prepration',$deleteDetail,array('land_prepration_id=?'=>$deletion_id));
						break;

						case  'fertilizer':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_fertilizer',$deleteDetail,array('land_farmer_id=?'=>$deletion_id));
						break;

						case  'cropprotection':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_cropprotection',$deleteDetail,array('land_protection_id=?'=>$deletion_id));
						break;

						case  'irrigation':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_irrigation',$deleteDetail,array('land_irri_id=?'=>$deletion_id));
						break;

						case 'yield':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_yield_component',$deleteDetail,array('yield_id=?'=>$deletion_id));
						break;

						case 'visit':
						$deleteDetail['status'] = '1';
						$db->update('logi_land_visit',$deleteDetail,array('visit_id=?'=>$deletion_id));
						break;

						case 'land':
						$deleteDetail['status'] = '1';

						$db->update('logi_land_showing',$deleteDetail,array('land_id=?'=>$deletion_id));                                         
						$db->update('logi_land_prepration',$deleteDetail,array('land_id=?'=>$deletion_id));
						$db->update('logi_land_fertilizer',$deleteDetail,array('land_id=?'=>$deletion_id));                                        
						$db->update('logi_land_cropprotection',$deleteDetail,array('land_id=?'=>$deletion_id));                                       
						$db->update('logi_land_irrigation',$deleteDetail,array('land_id=?'=>$deletion_id));                                        
						$db->update('logi_land_yield_component',$deleteDetail,array('land_id=?'=>$deletion_id));                                                                                
						$db->update('logi_land_visit',$deleteDetail,array('land_id=?'=>$deletion_id));
						$db->update('logi_land_information',$deleteDetail,array('land_id=?'=>$deletion_id));

						break;

						case  '':
						default:

						break;
					}

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data Deleted successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			} 






			public function getmandipriseAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;

				$appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"SUF1001";
				$device_type	= isset($params['device_type'])?$params['device_type']:"android";
				$device_token	= isset($params['device_token'])?$params['device_token']:"123456";
				$device_id	= isset($params['device_id'])?$params['device_id']:"354101061391494";

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}
					$GetAllTehsilMandiRateList = $user->GetAllTehsilMandiRateList();

					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                        //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Data Listed successfully.', 'mandi_price'=>$GetAllTehsilMandiRateList));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}
			} 


	/////
			function getHarvestListAction(){

				$params = $this->getRequest()->getParams();
				$secretkey = $this->secretkey;
				$db = $this->db;
				$appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"SUF1069";
				$device_id	= isset($params['device_id'])?$params['device_id']:"354101061265987"; 
				$error_code = 1;
				$succes = FALSE;
				try	{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
                        // if(!empty($alldata['LoginID']) != $LoginID){
                        // throw new Exception('Invalid login id.');
                        // }                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id)
						{
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}



					$StaffName = $alldata['StaffName'];
					$LoginID = $alldata['LoginID'];
					$farmerMaster = new Application_Model_Farmermasters();

					$allHarvestData = $farmerMaster->getAllHarvestList(); 
					$allHarvestFarmerStichingData = $farmerMaster->getAllHarvestFarmerStichingList(); 



					$db->commit();
					$succes = TRUE;	
				}

				catch(Exception $e){

			//Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}



				if($succes == TRUE )
				{
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Harvest listed Successfully.','HarvestList'=>$allHarvestData,'HarvestFarmerStichingData'=>$allHarvestFarmerStichingData));
					exit;

				}

				else
				{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
					exit;
				}

			}






			function insertHarvestDetailAction(){

				$params = $this->getRequest()->getParams();
				$secretkey = $this->secretkey;
				$db = $this->db;
               // $params = array('appkey'=>'teamlogimetrix@abhishek@naveen', 'LoginID'=>'SUF1081', 'device_id'=>'354101061387971','fe_code'=>'SUF1069','farmer_code'=>'SUF1069' ,'harvest_detail'=>'[{"net_price":"1","delivery":"1","harvest_name":"Moisture"}]');
               //array('harvest_detail'=>'[{"net_price":"1","delivery":"1","harvest_name":"Moisture"}]');
				if(get_magic_quotes_gpc()){
					$harvest_data = stripslashes($params['harvest_detail']);
				}
				else{
					$harvest_data = $params['harvest_detail'];
				}
				$harvest_data = json_decode($harvest_data,true);	
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:""; 
				$fe_code	= isset($params['fe_code'])?$params['fe_code']:"";                    
				$farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:"";


				$name	= isset($params['name'])?$params['name']:"q";  
				$weight_per_quintal	= isset($params['weight_per_quintal'])?$params['weight_per_quintal']:"";

				$allowances_per_quintal	= isset($params['allowances_per_quintal'])?$params['allowances_per_quintal']:""; 
				$rate	= isset($params['rate'])?$params['rate']:"1";  
				$unloading_charges	= isset($params['unloading_charges'])?$params['unloading_charges']:"";  
				$price_per_quintal	= isset($params['price_per_quintal'])?$params['price_per_quintal']:""; 


				$price_per_ten	= isset($params['price_per_ten'])?$params['price_per_ten']:"";  
				$delivery	= isset($params['delivery'])?$params['delivery']:"";  

				$error_code = 1;
				$succes = FALSE;
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}
					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}

					$farmer_harvest_data_array = array();                             
					$farmer_harvest_data_array['name']   = $name;
					$farmer_harvest_data_array['weight_per_quintal']          = $weight_per_quintal;
					$farmer_harvest_data_array['allowances_per_quintal']         = $weight_per_quintal;
					$farmer_harvest_data_array['rate']       = $rate;
					$farmer_harvest_data_array['unloading_charges']     = $unloading_charges;
					$farmer_harvest_data_array['price_per_quintal']  = $price_per_quintal;                    
					$farmer_harvest_data_array['price_per_ten']     = $price_per_ten;
					$farmer_harvest_data_array['delivered']  = $delivery;
					$farmer_harvest_data_array['farmer_code']  = $farmer_code;
					$farmer_harvest_data_array['fe_code']  = $fe_code;
					$db->insert('logi_harvest_farmer_stiching', $farmer_harvest_data_array);  
					$lastfarmerharvestInsertId = $db->lastInsertId();  



					$k = 0;

					foreach($harvest_data as $k){

						$harvest_data_array['name']   = $k['harvest_name'];
						$harvest_data_array['net_price']   = $k['net_price'];
						$harvest_data_array['delivery']  = $k['delivery'];
						$harvest_data_array['farmer_harvest_id']  = $lastfarmerharvestInsertId;
						$db->insert('tbl_harvest_detail', $harvest_data_array);                            
						$k++;
					}
					$db->commit();
					$succes = TRUE;	
				}

				catch(Exception $e){
                    //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}
				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Harvest deatil inserted successfully.'));
					exit;
				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
					exit;
				}
			}






			public function insertFeedbackDetailAction(){

				$params = $this->getRequest()->getParams();     
				$db = $this->db;
				$secretkey = $this->secretkey;                
				$appkey	= isset($params['appkey'])?$params['appkey']:"";
				$LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				$device_id	= isset($params['device_id'])?$params['device_id']:"";
				$issue	= isset($params['issue_type'])?$params['issue_type']:"";                        
				$remark	= isset($params['remark'])?$params['remark']:"";
				$resolution	= isset($params['resolution'])?$params['resolution']:"";
				$feedback_date	= isset($params['feedback_date'])?$params['feedback_date']:"";

				$error_code = 1;
				$succes = FALSE;    
				try{
					$db->beginTransaction();
					if($secretkey != $appkey){
						throw new Exception("Invalid request.");
					}	
					if(!$LoginID || !$device_id){
						throw new Exception("Required parameter missing.");
					}			
					$user = new Application_Model_Users();
					$alldata = $user->getUserDataByLoginId($LoginID);
					if(!empty($alldata['LoginID']) != $LoginID){
						throw new Exception('Invalid login id.');
					}                       
					if(!empty($alldata['DeviceID'])){			
						if($alldata['DeviceID'] != $device_id){
							$error_code = 3;
							throw new Exception('You are not athorized for this device.');
						}

					}
					if(strtolower($alldata['StaffStatus']) != 'ac'){
						$error_code = 3;
						throw new Exception('Your account is not active.');
					}	

					if($alldata['App_Access_Status'] == 2){
						$error_code = 2;
						throw new Exception('This account deactivated by admin.');
					}


					$feed_detail_array = array(); 

					$feed_detail_array['RelatedIssue'] = $issue;
					$feed_detail_array['FarmerRemark'] = $remark;
					$feed_detail_array['FEResolution'] = $resolution;   
					$feed_detail_array['FeedbackDateTime'] = $feedback_date;
					$db->insert('logi_order_feedback', $feed_detail_array);



					$db->commit();
					$succes = TRUE;	 
				}

				catch(Exception $e){
                                //Rollback transaction
					$db->rollBack();
					$error= $e->getMessage();
				}

				if($succes == TRUE ){
					echo json_encode(array("error_code"=>'0', 'response_string'=>'Feedback Detail Inserted successfully.'));
					exit;

				}

				else{
					echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error));
					exit;
				}

			}


		}
