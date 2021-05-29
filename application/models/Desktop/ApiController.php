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
        echo json_encode(array("error_code"=>'0', 'response_string'=>'success.', 'serverurl'=>'http://soufflet.runapp.me/api'));
        exit;
        }

	

	/**

	* login() method is used to android user login

	* @param String

	* @return JSON 

	*/	

    public function loginAction()

    { 

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();
                $secretkey = $this->secretkey;
                $db = $this->db;
              
		// Genaral Information of the user login details

		$appkey	= isset($params['appkey'])?$params['appkey']:"";
                $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
                $device_type	= isset($params['device_type'])?$params['device_type']:"";
                $device_token	= isset($params['device_token'])?$params['device_token']:"";
                $device_id	= isset($params['device_id'])?$params['device_id']:"";

		

		$error_code = 1;

		$succes = FALSE;

		try

		{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_type || !$device_id){
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
                        
                        if($alldata['TotalVillage'] == 0)
                        {
                            $error_code = 4;
                            throw new Exception('Sorry no village mapped contact admin');
                        }
                        
                        $update = array();
			$update['Device_type'] = $device_type;
			$update['Device_token'] = $device_token;
			$update['DeviceID'] = $device_id;   // IMEI Code
			//$update['gps_status'] = 'on';
			$user->updateSingleTableData('logi_field_users', $update, 'LoginID', $alldata['LoginID']);

			$LoginID = $alldata['LoginID'];
			$StaffName = $alldata['StaffName'];
                        $UserID =  $alldata['userId'];
                        $regionID =  $alldata['ReigonID'];
                        

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
                        $farmerMaster = new Application_Model_Farmermasters();
                        $seedMaster   = new Application_Model_Seedmasters();
                        $farmerType = $farmerMaster->getFarmerType();
                        $farmerPriority = $farmerMaster->getFarmerPriority();
                        $farmerRef = $farmerMaster->getAllReferenceType();
                        $LastCrop = $farmerMaster->getLastCrops();
                        $RabiCrop = $farmerMaster->getRabiCrop();
                        $KharifCrop = $farmerMaster->getKharifCrop();
                        $SoilType = $farmerMaster->getSoilType();
                        $WayOfIrrigation = $farmerMaster->getWayOfIrrigation();
                        $WayOfShowing = $farmerMaster->getWayOfShowing();
                        $SourceofWater = $farmerMaster->getSourceofWater();
                        $units = $farmerMaster->getAllunits();
                        // staff record
                        $StaffData = $user->getUserRecordByLoginID($LoginID);
                        //$villageList = $user->staffMappedVillageListByLoginID($UserID);
                        
                        
                        $circleData = $user->getcircleData($UserID);
                        $districtData = $user->getdistrictData($UserID);
                        $tehsilData = $user->gettehsilData($UserID);
                        $regionData = $user->getregionData($UserID);
                        $villData = $user->getvillData($UserID);
                        
                        
                        
                        // Seed Master
                        $seedType = $seedMaster->getSeedType();
                        $seedSubType = $seedMaster->getSeedSubAPIType();
                        $seedRowType = $seedMaster->getAllRowType();
                        $seedList = $seedMaster->getAllseedsforAPI();
                        
                        // Dc list
                        $dc_list = $user->getAllUserDCByRegionID($regionID);
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
			//'MappedVillageLsit'=>$villageList,
			
			echo json_encode(array("error_code"=>'0', 'response_string'=>'login success.','LastCrop'=>$LastCrop,'RabiCrop'=>$RabiCrop,
                            'KharifCrop'=>$KharifCrop,'SoilType'=>$SoilType,'WayofIrrigation'=>$WayOfIrrigation,'WayofShowing'=>$WayOfShowing,
                            'SourceofWater'=>$SourceofWater,'FarmerType'=>$farmerType,'FarmerPriority'=>$farmerPriority,'FarmerRef'=>$farmerRef,
                            'UserData'=>$StaffData,'CircleData'=>$circleData,'DistrictData'=>$districtData,'TehsilData'=>$tehsilData,'RegionData'=>$regionData,'VillData'=>$villData,'AllUnits'=>$units,'SeedType'=>$seedType,'SeedSubType'=>$seedSubType,
                            'SeedRowType'=>$seedRowType,'SeedList'=>$seedList,'DcList'=>$dc_list));
			exit;
		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

    }

   
     /**

	* farmerRegisterAction() method is register a farmer

	* @param NULL

	* @return JSON 

	*/
   
    
    
    
   	public function farmerRegisterAction()

    {

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();

		$secretkey = $this->secretkey;

		$db = $this->db;
               
		// Genaral Information of the user login details

                 $appkey	= isset($params['appkey'])?$params['appkey']:"";
                 $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
                 $device_id	= isset($params['device_id'])?$params['device_id']:""; 
                 
                 $farmerType	= isset($params['farmerType'])?$params['farmerType']:"";
                 $farmerPriority = isset($params['farmerPriority'])?$params['farmerPriority']:"";
                 $farmer_name	= isset($params['farmer_name'])?$params['farmer_name']:"";
                 $father_name	= isset($params['father_name'])?$params['father_name']:"";
                 $mobile_no	= isset($params['mobile_no'])?$params['mobile_no']:"";
                 $secondayNo	= isset($params['secondayNo'])?$params['secondayNo']:"";
                 $farmer_state	= isset($params['farmer_state'])?$params['farmer_state']:"";
                 $farmer_district	= isset($params['farmer_district'])?$params['farmer_district']:"";
                 $farmer_tehsil	        = isset($params['farmer_tehsil'])?$params['farmer_tehsil']:"";
                 $farmer_region         = isset($params['farmer_region'])?$params['farmer_region']:"";
                 $farmer_village	= isset($params['farmer_village'])?$params['farmer_village']:"";                
                 $reference	= isset($params['reference'])?$params['reference']:"";
                 $refd	= isset($params['refd'])?$params['refd']:"";
                 $otherInfo	= isset($params['otherInfo'])?$params['otherInfo']:"";
                                  
                 $total_acrage	= isset($params['total_acrage'])?$params['total_acrage']:"";
                 $total_land	= isset($params['total_land'])?$params['total_land']:"";

		 $wayofshowing	= isset($params['wayofshowing'])?$params['wayofshowing']:"";
                 $wayofIrrigation	= isset($params['wayofIrrigation'])?$params['wayofIrrigation']:"";
                 $sourceOfWater	= isset($params['sourceOfWater'])?$params['sourceOfWater']:"";
                 $suffletBGP	= isset($params['suffletBGP'])?$params['suffletBGP']:"";
                 $tractorOwner	= isset($params['tractorOwner'])?$params['tractorOwner']:"";
                 
                 $accountHolder = isset($params['accountHolder'])?$params['accountHolder']:"";
                 $bankName	= isset($params['bankName'])?$params['bankName']:"";
                 $ifscCode	= isset($params['ifscCode'])?$params['ifscCode']:"";
                 $accountNumber	= isset($params['accountNumber'])?$params['accountNumber']:"";
                 $panNo	= isset($params['panNo'])?$params['panNo']:"";
                       
                if(get_magic_quotes_gpc()){
                $d = stripslashes($params['land_details']);
                }else{
                $d = $params['land_details'];
                }
                $landDetails = json_decode($d,true);
                
                       
                        

		$error_code = 1;

		$succes = FALSE;

		try

		{

			$db->beginTransaction();

			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id || !$farmer_name){
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

				

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
                        
                       
			// Add Basic Information of farmer
                        
			$insertFarmerData = array();
                        $insertFarmerData['farmerType'] = $farmerType;
                        $insertFarmerData['farmerPriority'] = $farmerPriority;
                        $insertFarmerData['farmerPriority'] = $farmerPriority;
			$insertFarmerData['FarmerName'] = $farmer_name;
			$insertFarmerData['FatherName'] = $father_name;
			$insertFarmerData['MobileNo'] = $mobile_no;
                        $insertFarmerData['secondayNo'] = $secondayNo;
			$insertFarmerData['stateId'] = $farmer_state;
                        $insertFarmerData['districtId'] = $farmer_district;
                        $insertFarmerData['tehsilId'] = $farmer_tehsil;
                        $insertFarmerData['regionId'] = $farmer_region;
			$insertFarmerData['villageId'] = $farmer_village;
			$insertFarmerData['refType'] = $reference;
                        $insertFarmerData['refDetails'] = $refd;
                        $insertFarmerData['otherInfo'] = $otherInfo;
			$insertFarmerData['TotalAcrage'] = $total_acrage;
                        $insertFarmerData['TotalLand'] = $total_land;
                        $insertFarmerData['AllotedFieldEngg'] = $LoginID;
                        $insertFarmerData['AllotedFieldEnggName'] = $StaffName;
                        
                        if(isset($_FILES['photograph1']['tmp_name']) AND !empty($_FILES['photograph1']['tmp_name']))
			{
				$tempName = $_FILES['photograph1']['tmp_name'];
				$imageName = time().$_FILES['photograph1']['name']; 
				$uploads = 'uploads/user_photograph/';
				if(!file_exists($uploads)){
					mkdir($uploads);	
				}
				$pathComplete = $uploads.$imageName;
				@move_uploaded_file($tempName,$pathComplete);
				$insertFarmerData['Farmer_Image']= $imageName;
			}
                        
                        $insertFarmerData['SuffletBGP'] = $suffletBGP;
                       
                        $insertFarmerData['RegisterDate'] = $this->currdate;
	
                        $db->insert('logi_my_farmers', $insertFarmerData);	
                        $farmerInsertId = $db->lastInsertId();

			// Add land Information of farmer
                        
                        $k=0;
                        if(count($landDetails)>0){
                        foreach($landDetails as $k){
                            $insertData_fland = array();
                            $insertData_fland['FarmerID'] = $farmerInsertId;
                            $insertData_fland['KhasraNumber'] = $k['KhasraNumber'];
                            $insertData_fland['TotalAcrage'] = $k['TotalAcrage'];
                            $insertData_fland['LastCrop'] = $k['LastCrop'];
                            $insertData_fland['LastRabi'] = $k['LastRabi'];
                            $insertData_fland['LastKharif'] = $k['LastKharif'];
                            $insertData_fland['SoilType'] = $k['SoilType'];
                            $insertData_fland['latitude'] = $k['latitude'];
                            $insertData_fland['longitude'] = $k['longitude'];
                            $insertData_fland['Created'] = $this->currdate;
                            $db->insert('logi_farmer_lands', $insertData_fland);
                            $k++;
                        } }
                        
                        // Add farming Information of farmer                       
                        $insertFarmerDataShowing = array();
                        $insertFarmerDataShowing['FarmerID'] = $farmerInsertId;
                        $insertFarmerDataShowing['WayOfShowing'] = $wayofshowing;
                        $insertFarmerDataShowing['SourceOfWater'] = $sourceOfWater;
                        $insertFarmerDataShowing['WayofIrrigation'] = $wayofIrrigation;
                        $insertFarmerDataShowing['tractorOwner'] = $tractorOwner;
                        $insertFarmerDataShowing['Created'] = $this->currdate;
                        $db->insert('logi_farmer_showing', $insertFarmerDataShowing);
                        
                        
                        // generate farmer code                     
                        $farmer = new Application_Model_Farmermasters();
                        $generateFarmerCode = array();
                        $generateFarmerCode['stateId'] = $farmer_state;
                        $generateFarmerCode['districtId'] = $farmer_district;
                        $generateFarmerCode['tehsilId'] = $farmer_tehsil;
                        $generateFarmerCode['villageId'] = $farmer_village;
                        $generateFarmerCode['farmerType'] = $farmerType;
                        $generateFarmerCode['suffletBGP'] = $suffletBGP;
                        $generateFarmerCode['farmerID'] = $farmerInsertId;
                        $farmerCode = $farmer->GetFarmerCode($generateFarmerCode);
                        
                        // Add farmer bank information
                        if(isset($suffletBGP) && $suffletBGP==1){
                        $insertFarmerBankInfo = array();
                        $insertFarmerBankInfo['FarmerID'] = $farmerInsertId;
                        $insertFarmerBankInfo['AccountHolderName'] = $accountHolder;
                        $insertFarmerBankInfo['BankName'] = $bankName;
                        $insertFarmerBankInfo['ifscCode'] = $ifscCode;
                        $insertFarmerBankInfo['AccountNumber'] = $accountNumber;
                        $insertFarmerBankInfo['panNo'] = $panNo;
                        if(isset($_FILES['passbook_image']['tmp_name']) AND !empty($_FILES['passbook_image']['tmp_name']))
			{
				$tempName = $_FILES['passbook_image']['tmp_name'];
				$imageName = time().$_FILES['passbook_image']['name']; 
				$uploads = 'uploads/pass_book/';
				if(!file_exists($uploads)){
					mkdir($uploads);	
				}
				$pathComplete = $uploads.$imageName;
				@move_uploaded_file($tempName,$pathComplete);
				$insertFarmerBankInfo['pass_book_image']= $imageName;
			}
                        $db->insert('logi_farmer_bankdeails', $insertFarmerBankInfo);
                        }
                        
                        // update farmer code
                        $update = array();
                        $update['FarmerCode'] = $farmerCode;
                        $farmer->updateSingleTableData('logi_my_farmers', $update, 'id', $farmerInsertId);
                        
                        
                        // generate notification
			$notificationData = array();
			$notificationData['message'] = $StaffName.' ('.$LoginID.') <br> Status: Farmer Register successfully. Farmer Code <br>'.$farmerCode;	
			$notificationData['statustype'] = 'Farmer Register';	
			$notificationData['mob_user_loginID'] = $LoginID;
			$notificationData['type'] = "Farmer";	
                        $notificationData['noti_date'] = $this->currdate;   
                        $db->insert('logi_notification',$notificationData);	


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

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Farmer Register Successfully.','FarmerCode'=>$farmerCode));

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
   
    
    
    
   	public function seedOrderAction()

    {

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();

		$secretkey = $this->secretkey;

		$db = $this->db;
               
		// Genaral Information of the user login details

                 $appkey	= isset($params['appkey'])?$params['appkey']:"";
                $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
                 $device_id	= isset($params['device_id'])?$params['device_id']:""; 
                 $farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:""; 
                 $seedId        = isset($params['seedId'])?$params['seedId']:"";
                   
                   
                 $seedTypeId    = isset($params['seedTypeId'])?$params['seedTypeId']:"";
                 $seedSubTypeId = isset($params['seedSubTypeId'])?$params['seedSubTypeId']:"";
                 $rowTypeId     = isset($params['rowTypeId'])?$params['rowTypeId']:"";              
                 $seedName      = isset($params['seedName'])?$params['seedName']:"";
                 $qty           = isset($params['qty'])?$params['qty']:"";
                 $unit          = isset($params['unit'])?$params['unit']:"";
                 $rate          = isset($params['rate'])?$params['rate']:"";
                 $advance_to_be_pay	    = isset($params['advance_to_be_pay'])?$params['advance_to_be_pay']:"";
                 $payment_recevied          = isset($params['payment_recevied'])?$params['payment_recevied']:"";
                 $balance_amount            = isset($params['balance_amount'])?$params['balance_amount']:"";                
                 $dc_name                   = isset($params['dc_name'])?$params['dc_name']:"";
                 $expected_date_of_delivery = isset($params['expected_date_of_delivery'])?$params['expected_date_of_delivery']:"";
                  $date_of_order = isset($params['date_of_order'])?$params['date_of_order']:"";
                        

		$error_code = 1;

		$succes = FALSE;

		try

		{

			$db->beginTransaction();

			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id || !$seedId || !$farmer_code){
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

				

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
                        
                       
			// Get Farmer Details Here 
                        $farmerMaster = new Application_Model_Farmermasters();
                        $allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);
                        
                        $farmerID = $allFarmerData['id'];
                        $farmerCode = $allFarmerData['FarmerCode'];
                        
			$insertFarmerData = array();
                        $insertFarmerData['FarmerID'] = $farmerID;
                        $insertFarmerData['SeedTypeId'] = $seedTypeId;
                        $insertFarmerData['SubTypeId'] = $seedSubTypeId;
			$insertFarmerData['RowTypeId'] = $rowTypeId;
			$insertFarmerData['SeedId'] = $seedId;
			$insertFarmerData['SeedName'] = $seedName;
                        $insertFarmerData['Qty'] = $qty;
                        $insertFarmerData['unit'] = $unit;
			$insertFarmerData['Rate'] = $rate;
                        $insertFarmerData['advance_to_be_pay'] = $advance_to_be_pay;
                        $insertFarmerData['payment_recevied'] = $payment_recevied;
                        $insertFarmerData['balance_amount'] = $balance_amount;
			$insertFarmerData['expected_date_of_delivery'] = $expected_date_of_delivery;
			$insertFarmerData['dc_name'] = $dc_name;
                        $insertFarmerData['OrderDate'] = $date_of_order;
                        $insertFarmerData['AllotedFieldEngg'] = $LoginID;
                        
                        
                        
                        if(isset($_FILES['photograph1']['tmp_name']) AND !empty($_FILES['photograph1']['tmp_name']))
			{
				$tempName = $_FILES['photograph1']['tmp_name'];
				$imageName = time().$_FILES['photograph1']['name']; 
				$uploads = 'uploads/seed_orders/';
				if(!file_exists($uploads)){
					mkdir($uploads);	
				}
				$pathComplete = $uploads.$imageName;
				@move_uploaded_file($tempName,$pathComplete);
				$insertFarmerData['FeSignatureOrder']= $imageName;
			}
                        
                         if(isset($_FILES['photograph2']['tmp_name']) AND !empty($_FILES['photograph2']['tmp_name']))
			{
				$tempName = $_FILES['photograph2']['tmp_name'];
				$imageName = time().$_FILES['photograph2']['name']; 
				$uploads = 'uploads/seed_orders/';
				if(!file_exists($uploads)){
					mkdir($uploads);	
				}
				$pathComplete = $uploads.$imageName;
				@move_uploaded_file($tempName,$pathComplete);
				$insertFarmerData['FarmerSignatureOrder']= $imageName;
			}
                        
                        $db->insert('logi_farmer_seed_orders', $insertFarmerData);	
                        
                        
                    $rawOrderId = $db->lastInsertId();
					$finalOrderId = str_pad($rawOrderId, 6, '0', STR_PAD_LEFT);
					$finalOrderId = 'S'.''.$finalOrderId;
					// update OrderID
					
					$update = array();
					$update['orderId'] = $finalOrderId;
					$farmerMaster->updateOrderId('logi_farmer_seed_orders', $update, 'id', $rawOrderId);
                        
                        
                        
                        // generate notification
			$notificationData = array();
			$notificationData['message'] = $StaffName.' ('.$LoginID.') <br> Status: New farmer seed order received<br>'.$farmerCode;	
			$notificationData['statustype'] = 'Farmer Seed Order';	
			$notificationData['mob_user_loginID'] = $LoginID;
			$notificationData['type'] = "Farmer";	
                        $notificationData['noti_date'] = $this->currdate;   
                        $db->insert('logi_notification',$notificationData);	


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

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Seed Order succuessfully done.'));

			exit;

		}

		else

		{

			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));

			exit;

		}

    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
      /**

	* secondaryFramerRegistrationAction() method is register a farmer second time

	* @param NULL

	* @return JSON 

	*/
   
    
    
    
   	public function secondaryFramerRegistrationAction()

    {

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();

		$secretkey = $this->secretkey;

		$db = $this->db;
               
		// Genaral Information of the user login details

                 $appkey	= isset($params['appkey'])?$params['appkey']:"";
                 $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
                 $device_id	= isset($params['device_id'])?$params['device_id']:""; 
                 $farmer_code	= isset($params['farmer_code'])?$params['farmer_code']:""; 
                   
                 $accountHolder = isset($params['accountHolder'])?$params['accountHolder']:"";
                 $bankName	= isset($params['bankName'])?$params['bankName']:"";
                 $ifscCode	= isset($params['ifscCode'])?$params['ifscCode']:"";
                 $accountNumber	= isset($params['accountNumber'])?$params['accountNumber']:"";
                 $panNo	= isset($params['panNo'])?$params['panNo']:"";
                 $suffletBGP = isset($params['suffletBGP'])?$params['suffletBGP']:"";    

		$error_code = 1;

		$succes = FALSE;

		try

		{

			$db->beginTransaction();

			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id  || !$farmer_code || !$suffletBGP){
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

				

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
                        
                       
			// Get Farmer Details Here 
                        $farmerMaster = new Application_Model_Farmermasters();
                        $allFarmerData = $farmerMaster->getFarmerDetailsByFarmerCode($farmer_code);
                        
                        $farmerID = $allFarmerData['id'];
                        $farmerCode = $allFarmerData['FarmerCode'];
                        
			 if(isset($suffletBGP) && $suffletBGP==1){
                        $insertFarmerBankInfo = array();
                        $insertFarmerBankInfo['FarmerID'] = $farmerID;
                        $insertFarmerBankInfo['AccountHolderName'] = $accountHolder;
                        $insertFarmerBankInfo['BankName'] = $bankName;
                        $insertFarmerBankInfo['ifscCode'] = $ifscCode;
                        $insertFarmerBankInfo['AccountNumber'] = $accountNumber;
                        $insertFarmerBankInfo['panNo'] = $panNo;
                        if(isset($_FILES['passbook_image']['tmp_name']) AND !empty($_FILES['passbook_image']['tmp_name']))
			{
				$tempName = $_FILES['passbook_image']['tmp_name'];
				$imageName = time().$_FILES['passbook_image']['name']; 
				$uploads = 'uploads/pass_book/';
				if(!file_exists($uploads)){
					mkdir($uploads);	
				}
				$pathComplete = $uploads.$imageName;
				@move_uploaded_file($tempName,$pathComplete);
				$insertFarmerBankInfo['pass_book_image']= $imageName;
			}
                        $db->insert('logi_farmer_bankdeails', $insertFarmerBankInfo);
                           // update BGP Status
                        $update = array();
                        $updated_farmerCode =  'S'.$farmerCode;
                        $update['SuffletBGP'] = $suffletBGP;
                        $update['FarmerCode'] = $updated_farmerCode;
                        $farmerMaster->updateSingleTableData('logi_my_farmers', $update, 'id', $farmerID);
                        
                        }
                        
                       
                        
                        
                           // generate notification
			$notificationData = array();
			$notificationData['message'] = $StaffName.' ('.$LoginID.') <br> Status: Farmer Register successfully. Farmer Code <br>'.$farmerCode;	
			$notificationData['statustype'] = 'Farmer Register';	
			$notificationData['mob_user_loginID'] = $LoginID;
			$notificationData['type'] = "Farmer";	
                        $notificationData['noti_date'] = $this->currdate;   
                        $db->insert('logi_notification',$notificationData);	


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

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Farmer Registered Successfully.','New_Farmer_Code'=>$updated_farmerCode));

			exit;

		}

		else

		{

			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));

			exit;

		}

    }
   

	function getFarmerListAction(){
		
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
		$appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
        $LoginID	= isset($params['LoginID'])?$params['LoginID']:"SUF1003";
        $device_id	= isset($params['device_id'])?$params['device_id']:"354101061392229"; 
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

			

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
            $farmerMaster = new Application_Model_Farmermasters();
            $allFarmerData = $farmerMaster->getAllFarmerForApi($LoginID); 
            $allSeedOrderData = $farmerMaster->getallseedOrderForApi($LoginID);           
                       
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
			echo json_encode(array("error_code"=>'0', 'response_string'=>'Farmer listed Successfully.','ImageUrl'=>'uploads/user_photograph/dummy_pic.png','Farmer_List'=>$allFarmerData,'Seed_Order_List'=>$allSeedOrderData));
			exit;

		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}

	
	
	
	
	   	public function fieldExectivePaymentAction()
    {

	    // Get the Request parameters

		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
               
		// Genaral Information of the user login details

                 $appkey	= isset($params['appkey'])?$params['appkey']:"";
				 $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
				 $device_id	= isset($params['device_id'])?$params['device_id']:""; 
                  
                 $paymentAmount = isset($params['paymentAmount'])?$params['paymentAmount']:"";
                 $receiptNumber	= isset($params['receiptNumber'])?$params['receiptNumber']:"";
                 $paymentDate	= isset($params['paymentDate'])?$params['paymentDate']:"";
                 $bank	= isset($params['bank'])?$params['bank']:"";
                    

		$error_code = 1;
		$succes = FALSE;

		try
		{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id  || !$paymentAmount || !$paymentDate || !$bank){
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

				

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
              
            $paymentData = array();
			$paymentData['paymentAmount'] = $paymentAmount;
			$paymentData['receiptNumber'] =  $receiptNumber;	
			$paymentData['paymentDate'] = $paymentDate;
                        $paymentData['bank'] = $bank;
			$paymentData['LoginID'] = $LoginID;	
			$paymentData['created'] = $this->currdate;   
			$db->insert('logi_field_executive_payments',$paymentData);	
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

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Payment Information is successfully submitted.'));
			exit;

		}

		else

		{

			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));

			exit;

		}

    }
   
   
   
   
   function getDepositePaymentAction(){
		
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

			

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
            $farmerMaster = new Application_Model_Farmermasters();
            $allDepositePayment = $farmerMaster->getAllDepositePayment($LoginID); 
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
			echo json_encode(array("error_code"=>'0', 'response_string'=>'Field Executive Deposite List.','Deposite_data'=>$allDepositePayment));
			exit;

		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}

	
	
	
	function getMisSupportAction(){
		
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

			

			$StaffName = $alldata['StaffName'];
			$LoginID = $alldata['LoginID'];
            $farmerMaster = new Application_Model_Farmermasters();
            $misData = $farmerMaster->getMisSupportData(); 
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
			echo json_encode(array("error_code"=>'0', 'response_string'=>'Mis Support List.','Mis_support_data'=>$misData));
			exit;

		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

	}
	
	
	
	/**

	* locationTrack() method is used to get all jobs

	* @param NULL

	* @return JSON 

	*/	

	public function locationTrackAction()
    {
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
		$battery_status = isset($params['battery_status'])?$params['battery_status']:"";
		$travelled_distance = isset($params['travelled_distance'])?$params['travelled_distance']:"";
	    $time_spend = isset($params['time_spend'])?$params['time_spend']:"";
		$error_code = 1;
		$succes = FALSE;

		try{

			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$LoginID || !$device_id || !$lat || !$long || !$battery_status){
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
			$insert['battery_status'] = $battery_status;
			$insert['add_date_time'] = $this->currdate;
			$insert['travelled_distance'] = $travelled_distance; 
			$insert['time_spend'] = $time_spend;
			$insertId  = $user->insertLocationTrackByStaffCode($insert);

			// update user table

			$update['Current_latitude'] = $lat;
			$update['Current_longitude'] = $long;
			if(isset($offline_timestamp) and  $offline_timestamp!=''){
                            $update['Last_location_service_hit_time'] = $offline_timestamp;
                         }else{
                             $update['Last_location_service_hit_time'] = date("Y-m-d H:i:s");
            }
			$user->updateSingleTableData('logi_field_users',$update,'LoginID',$alldata['LoginID']);
			if($battery_status == 'mediaum'){
			$bstatus = 'battery_mediaum';
			}else if($battery_status == 'very low'){
			$bstatus = 'battery_low';
			}else if($battery_status == 'full'){
			$bstatus = 'battery_full';
			}else if($battery_status == 'low'){
			$bstatus = 'battery_low';
			}

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

			echo json_encode(array("error_code"=>'0', 'response_string'=>'Location track added successfully.'));

			exit;
		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}

    }
	

	  
	/* ---- Function for all chemical master to through on device*/
	
	
	function chemicalMasterAction(){
		
		$params = $this->getRequest()->getParams();
		$secretkey = $this->secretkey;
		$db = $this->db;
	    $appkey	= isset($params['appkey'])?$params['appkey']:"teamlogimetrix@abhishek@naveen";
        $LoginID	= isset($params['LoginID'])?$params['LoginID']:"";
        $device_id	= isset($params['device_id'])?$params['device_id']:"352840070836245";  
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
        
        $farmerId	= isset($params['farmerId'])?$params['farmerId']:"1";
        $farmerCode	= isset($params['farmerCode'])?$params['farmerCode']:"";
        $date_of_order	= isset($params['date_of_order'])?$params['date_of_order']:"";
        $expected_date_delivery		= isset($params['expected_date_delivery'])?$params['expected_date_delivery']:"";
        $total_amount	= isset($params['total_amount'])?$params['total_amount']:"";
        $paid_amount	= isset($params['paid_amount'])?$params['paid_amount']:"";
        
        
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
					$insertChemicalData['created'] = $this->currdate;
				
					$db->insert('logi_chemical_orders',$insertChemicalData);	
					$rawOrderId = $db->lastInsertId();
					$finalOrderId = str_pad($rawOrderId, 6, '0', STR_PAD_LEFT);
					$finalOrderId = 'C'.''.$finalOrderId;
					// update OrderID
					
					$update = array();
					$update['orderId'] = $finalOrderId;
					$farmerMaster->updateOrderId('logi_chemical_orders', $update, 'id', $rawOrderId);
					
					
					// final multiple data for chemical list
					
						$k=0;
                        if(count($chemcial_list)>0){
                        foreach($chemcial_list as $k){
                            $insertData_fland = array();
                            $insertData_fland['orderId'] = $finalOrderId;
                            $insertData_fland['chemicalType_id'] = $k['chemicalTypeID'];
                            $insertData_fland['chemicalId'] = $k['chemicalId'];
                            $insertData_fland['qty'] = $k['qty'];
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
			echo json_encode(array("error_code"=>'0', 'response_string'=>'Chemical Order Successfully done'));
			exit;

		}

		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}
	}
		

}


