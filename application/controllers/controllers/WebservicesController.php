<?php

class WebservicesController extends Zend_Controller_Action
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
		$body = $this->getRequest()->getRawBody();
		/* $body = '{"appkey":"mys3cr3tk3y","message":"fffg","trip_id":"417","device_id":"911239258302209","user_id":"8"}
'; */
		$request = Zend_Json::decode($body);
		$this->phpNative = $request;
		
		$bootstrap = $this->getInvokeArg('bootstrap');
		$aConfig = $bootstrap->getOptions();
		$this->secretkey = $aConfig['api']['searchuser']['secret'];
		$this->siteurl = $aConfig['api']['site']['url'];
		$this->db = Zend_Db_Table::getDefaultAdapter();
		//$date = new Zend_Date();
		$this->currdate = date("Y-m-d H:i:s");//$date->toString('Y-m-d H:m:s');
	
    }


    public function indexAction()
    {
        // action body
    }

	
	public function loginAction()
    {
		$data = $this->phpNative;
		$secretkey = $this->secretkey;
		$db = $this->db;
		$appkey = $data['appkey'];
		$insert = array();
		$insert['email'] = $data['email'];
		$insert['pin'] = $data['pin'];
		$insert['device_type'] = $data['device_type'];
		$insert['device_token'] = $data['device_token'];
		$insert['device_id'] = $data['device_id'];
		$confirm_login = $data['confirm_login'];
	 	//$appkey = 'mys3cr3tk3y';
		//$insert['email'] = 'sanjoo@appstudioz.com';
		//$insert['pin'] = '1234';
		//$insert['device_type'] = 'iPhone';
		//$insert['device_token'] = '123'; 
		$error_code = 1;
		$succes = FALSE;
		try
		{
			$db->beginTransaction();
			if($secretkey != $appkey){
				throw new Exception("Invalid request.");
			}	
			if(!$insert['email'] || !$insert['pin'] || !$insert['device_type'] || !$insert['device_token']){
				throw new Exception("Required parameter missing.");
			}		

			$user = new Application_Model_Users();
			$alldata = $user->getAllData('users', 'email',$insert['email']);
			$count =  count($alldata);
			if(!$count)
			{
				throw new Exception('Invalid email or pin.');
			}
			if($alldata['pin'] != $insert['pin'])
			{
				throw new Exception('Wrong pin.');
			}
			if($alldata['access_status'] == 0)
			{
				throw new Exception('Please activate your account.');
			}
			
			if($alldata['access_status'] == 2)
			{
				$error_code = 2;
				throw new Exception('This account deactivated by admin.');
			}
			//// check user trip
			$checkTrip = $user->checkUserTripById($alldata['user_id']);
			if($checkTrip['driver_id'])
			{
				$error_code = 2;
				throw new Exception('User is on trip at another device.');
			}
			
			if($alldata['device_id'] and $confirm_login != 'ok' and $alldata['device_id']!=$insert['device_id'])
			{
				$error_code = 3;
				throw new Exception('Curently you are login on another device. Do you want login with this device?');
			}
			/***********  check already login *************/
	/* 		if($alldata['device_token'])
			{
				throw new Exception('You are already login on another device.');
			} */
			
			
			
			$update = array();
			$update['last_login'] = $this->currdate;
			$update['device_type'] = $insert['device_type'];
			$update['device_token'] = $insert['device_token'];
			$update['device_id'] = $insert['device_id'];
			$user->updateSingleTableData('users', $update, 'user_id', $alldata['user_id']);
			
			$fromfieldArr = array('user_id', 'name', 'email', 'phone_number', 'user_photo', 'pin', 'access_status', 'register_date', 'IFNULL(register_as,0) as register_as', 'IFNULL(driver_status,0) as driver_status', 'IFNULL(passenger_status,0) as passenger_status', 'if(braintree_customer_id,"yes","no") as payment_done','current_role');
			$allNewdata = $user->getAllData('users', 'user_id',$alldata['user_id'], $fromfieldArr);
	
			$userData['user'] = $allNewdata;
			
			
			$userData['driverDoc'] = new stdClass();
			$fromfieldArr = array('id', 've_driver_id', 'IFNULL(vehicle_make,0) as vehicle_make', 'IFNULL(vehicle_model,0) as vehicle_model', 'IFNULL(year,0) as year', 'IFNULL(number_of_doors,0) as number_of_doors', 'IFNULL(max_no_of_passenger,0) as max_no_of_passenger', 'IFNULL(allow_pets,0) as allow_pets', 'IFNULL(current_license_no,0) as current_license_no', 'IFNULL(add_date,0) as add_date', 'tcp_license_no', 'how_soon_start', 'mostlikaly_drive', 'driver_license_photo', 'car_insurance_photo', 'car_registration_photo', 'admin_apporve', 'car_photo');
			$driverDoc = array();
			$driverDoc = $user->getAllData('vehicleinfo', 've_driver_id',$alldata['user_id'], $fromfieldArr);
			if($driverDoc)
			{
				$userData['driverDoc'] = $driverDoc;
			}	
			
			$fromTripfieldArr = array('source', 'source_latitude', 'source_longitude', 'destination', 'destination_latitude', 'destination_longitude', 'trip_time', 'pickup_distance', 'pickup_estimated_time');
			$driverRoot = $user->getAllData('setdrivertrip', 'driver_id',$alldata['user_id'], $fromTripfieldArr);
			
			if($driverRoot)
			{
				$userData['driverRoot'] = $driverRoot;
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
			echo json_encode(array("error_code"=>'0', 'response_string'=>'login success.', 'user_data'=>$userData));
			exit;
		}
		else
		{
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}
    }

	
}

