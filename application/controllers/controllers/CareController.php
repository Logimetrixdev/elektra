<?php
/***************************************************************
 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : IndexController.php
 * File Description  : Index Controller
 * Created By : Abhishek Kumar Mishra
 * Created Date: 24 July 2015
 ***************************************************************/
 
class CareController extends Zend_Controller_Action
{

    var $dbAdapter;
	
    public function init()
    {
        /* Initialize action controller here */
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
		$this->currdate = date("Y-m-d H:i:s");//$date->toString('Y-m-d H:m:s');
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                if(isset($authStorage->read()->WebLoginID)){
                    $this->WebLoginID = $authStorage->read()->WebLoginID;
                    $users = new Application_Model_Care();
                    $logout_details = $users->getUserLoginDetailByWebLoginCode($this->WebLoginID);
                    $this->view->last_login = $logout_details['login_time'];
                }
		
			
    }

	
    public function indexAction(){
       // get adapter
        $dbAdapter = $this->dbAdapter;
        $users = new Application_Model_Care();
        $form = new Application_Form_IndexLogin();
        $auth = Zend_Auth::getInstance();

//		$errorMessage = ""; 
//		/*************** check user identity ************/
        if($auth->hasIdentity()){  
            $this->_redirect('/care/dashboard');  
        } 
        $this->view->form = $form;
        if($this->getRequest()->isPost()){
            if($form->isValid($_POST)){
                    $data = $form->getValues();
                    $loginid = $data['loginid'];
                    $password = $data['password'];
                    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
           // Set the input credential values
                    $authAdapter->setTableName('logi_field_users')  
                                            ->setIdentityColumn('WebLoginID')  
                                            ->setCredentialColumn('Password')
                                            ->setCredentialTreatment("? AND Role != 'field_user' AND StaffStatus='AC'");
                    $authAdapter->setIdentity($loginid)  
                                            ->setCredential($password);
                    $result = $auth->authenticate($authAdapter);

                    if($result->isValid()){
                        $storage = new Zend_Auth_Storage_Session();
                        $storage->write($authAdapter->getResultRowObject());
                        $auth = Zend_Auth::getInstance();
                        $authStorage = $auth->getStorage();

                        $WebLoginID = $authStorage->read()->WebLoginID;
                        $role = $authStorage->read()->Role;
                        $newWebLoginID = md5($WebLoginID.'@@@@@'.$role);
                        Zend_Auth::getInstance()->getStorage()->write(
                        $authStorage->read(),
                        array('WebLoginIDMd5' => $newWebLoginID)
                        );
                        $WebLoginIDMd5 = $authStorage->read()->WebLoginIDMd5 = $newWebLoginID;                                       
                        $login_details = $users->getUserLoginDetailByWebLoginCode($WebLoginID);
//                                       
//					// lastlogin date update in admin table
                        $user_login_data = array();
                        $user_login_data['WebLoginID'] = $WebLoginID;
                        $user_login_data['login_time'] = $this->currdate;
                        $user_login_data['server_detail'] = json_encode($_SERVER);
                        if($login_details['WebLoginID'] !=""){
                            $users->updateUserLoginDetailsByLoginID($WebLoginID,$user_login_data['login_time']);			
                        }
                        else{
                            $users->insertUserLoginDetailsByWebLoginID($user_login_data);				
                        }

                        $this->_redirect('/care/dashboard');
                    } 
                    else {
                        $this->view->errorMessage = "Invalid login id or password. Please try again.";
                    }
            }
        } 
    }



         /**
    * changePassword() method is used to admin chnage the password
    * @param NULL
	* @return True 
    */	
	public function changePasswordAction()
	{ 
		$this->checklogin();
		// get adapter
		$dbAdapter = $this->dbAdapter;
		$users = new Application_Model_Care();
		$form = new Application_Form_Password();
		$errorMessage = ""; 
		$successMessage = ""; 
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
		    if($form->isValid($_POST)){
				$data = $form->getValues();
				$old_pass = $data['old_pass']; 
				$password = $data['password']; 
				$cpassword = $data['cpassword']; 
				
				//Admin session data
				$auth = Zend_Auth::getInstance();
				$authStorage = $auth->getStorage();
                                $id = $authStorage->read()->id;
				$WebLoginID = $authStorage->read()->WebLoginID;
			        $user_password = $authStorage->read()->Password; 
		
				if($old_pass != $user_password)
				{
				  $this->view->errorMessage = "Old password does not match";
				}
                                if($password != $cpassword)
				{
				  $this->view->errorMessage = "Confirm password does not match with new password";
				}
				
				if($password == $cpassword && $old_pass == $user_password) 
				{
                                        $updateData = array();
					$updateData['Password'] = $password;
					$where = array('LoginID = ?'=>$LoginID);
					
					$result = $users->updateChangePasswordByStaffCode($updateData,$where);
					Zend_Auth::getInstance()->getStorage()->write(
					$authStorage->read(),
					array('Password' => $password)
					);
					$authStorage->read()->Password = $password;						
					$this->view->successMessage = "Password has been changed successfully.";
				}	
			}
		} 
		
	}        

	
	
	/**
	* forgotpassword() method is used to all users forgot password
	* @param String 
	* @return True 
	*/	
    public function forgotpasswordAction()
    {
		// get adapter
		$dbAdapter = $this->dbAdapter;
		$users = new Application_Model_Care();
		$form = new Application_Form_IndexForgot();
		
		$errorMessage = ""; 
		
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();
				$phone = $data['phone'];  
				$result = $users->getAllUserDetailsByPhone($phone);
				if($result){
					$StaffName = $result['StaffName'];
					$EMail = trim($result['EMail']);
					$LoginID = $result['LoginID'];
					$role = $result['role'];
					$from_email = $StaffName."@acme.com";
					
                                        if($role=="national_head")
                                        {
                                                                $roletype_name = "National head";
                                        }
                                        else if($role=="zone_head")
                                        {
                                                                $roletype_name = "Zone head";
                                        }
                                        else if($role=="regional_head")
                                        {
                                                                $roletype_name = "Regional head";
                                        }	
                                        else if($role=="circle_head")
                                        {
                                                                $roletype_name = "Circle head";
                                        }	
                                        else if($role=="service_manager"){ 
                                                                $roletype_name = "Service manager";
                                        }	
                                        else if($role=="cluster_incharge"){
                                                            $roletype_name = "Cluster incharge";
                                        }						
 					$subject = "$roletype_name forgot password email";
					$message .= "Here is your $roletype_name login details:<br><br>";
					$message .= "Email: $EMail <br>";
					$message .= "Username: $StaffName <br>";
					$message .= "Login ID: $LoginID <br><br>";
					$message .= "Thanks,<br>";
					$message .= "ACME Team";
			
					$confobj = new Application_Model_Fsr();
					
					$confArr = $confobj->getSmtpMailServerSettings();
					
					$config = $confArr['config'];
					$server = $confArr['server'];
					$fromemailconf = $confArr['fromemailconf'];
					
	
					$transport = new Zend_Mail_Transport_Smtp($server, $config);
					Zend_Mail::setDefaultTransport($transport);
					//echo $EMail;die;
					try{
						$mail = new Zend_Mail();
						$mail->setSubject($subject);
						$mail->setFrom($fromemailconf['fromemail'], $fromemailconf['fromname']);
						$mail->addTo($EMail, $StaffName);
						$mail->setBodyHtml($message);
						
						$mail->send($transport);
						$this->view->errorMessage = "Email has been sent successfully.";
					}
					catch (Exception $e)
					{
						$this->view->errorMessage = "Error in sending email.";
					//	echo '<pre>';
					//echo $e->getMessage();die;
						//print_r($e);
					}		
					//echo 'dsfds';die;
					
					
				
				} else {
					$this->view->errorMessage = "Invalid mobile number. Please try again.";
				}
			}
		} 
	}
        
        
            public function dashboardAction(){
		$this->checklogin();
		$params = $this->getRequest()->getParams();
                 $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		//echo "<pre>";print_r($authStorage->read());die;
		$WebLoginID = $authStorage->read()->WebLoginID;
		$role = $authStorage->read()->Role;
		$user = new Application_Model_Care();
                $this->view->getTotalCountCapturedInfoDetail = $getTotalCountCapturedInfoDetail = $user->getTotalCountCapturedInfoDetail();
                $this->view->getTotalCountCapturedPlaceDetail = $getTotalCountCapturedPlaceDetail = $user->getTotalCountCapturedPlaceDetail();

                //$farmer->totalNonRegisteredfarmer();
           }
           
            public function viewContactAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();  
                //print_r($params);  
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Care();
                $this->view->getContactInformation = $reportscome= $users->getContactAllInformation($params['name'], $params['mobile'], $params['user']);              
                $this->view->getAllUserInfo  = $getAllUserInfo= $users->getAllUserInfo();
                
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($reportscome);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
                
                if($params['type'] == 'generate_contact'){
                    // echo '<pre />';
                    //  print_r($record);
                    //  exit();
                    // output headers so that the file is downloaded rather than displayed
                    header('Content-type: text/csv');
                    header('Content-Disposition: attachment; filename="contact_information_detail.csv"');
                    // do not cache the file
                    header('Pragma: no-cache');
                    header('Expires: 0');

                    // create a file pointer connected to the output stream
                    $file = fopen('php://output', 'w');

                    // send the column headers
                    fputcsv($file, array('Sr. No.', 'Name', 'Mobile No', 'Whatsapp No','Profession', 'Cast', 'Address','Polling Both Id','Party Affiliation','Party Name','Availability', 'Date'));

                    // Sample data. This can be fetched from mysql too
                    // output each row of the data
                    $content = array();
                    $i = 2;	
                    foreach ($getContactInformation as $rs) {
                        $row = array();
                        $row[] = stripslashes($i-1);
                        $row[] = stripslashes($rs["name"]);
                        $row[] = stripslashes($rs["mobile"]);
                        $row[] = stripslashes($rs["whatsapp"]);
                        $row[] = stripslashes($rs["profession"]);
                        $row[] = stripslashes($rs["cast"]);
                        $row[] = stripslashes($rs['address']);
                        $row[] = stripslashes($rs["polling_both"]);
                        $row[] = stripslashes($rs["party_affilitation"]);
                        $row[] = stripslashes($rs["party_name"]);
                        $row[] = stripslashes($rs["availiability"]);
                        $row[] = stripslashes($rs["offline_sink_datetime"]);
                        $content[] = $row;
                        $i++;
                    }

                    foreach ($content as $res){
                        fputcsv($file, $res);
                    }
                    exit;

                 }        
            }
         
            public function viewPlaceAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();    
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Care();
                $this->view->getPlacedInformation  = $getPlacedInformation= $users->getAllPlacedInformation($params['name'], $params['mobile'], $params['user']);              
                $this->view->getAllUserInfo  = $getAllUserInfo= $users->getAllUserInfo();
                
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($getPlacedInformation);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
                if($params['type'] == 'generate_place'){
                    // echo '<pre />';
                    //  print_r($record);
                    //  exit();
                    // output headers so that the file is downloaded rather than displayed
                    header('Content-type: text/csv');
                    header('Content-Disposition: attachment; filename="place_information_detail.csv"');
                    // do not cache the file
                    header('Pragma: no-cache');
                    header('Expires: 0');
                    // create a file pointer connected to the output stream
                    $file = fopen('php://output', 'w');
                    // send the column headers
                    fputcsv($file, array('Sr. No.', 'Place Description', 'Concerned Person', 'Designation','Contact', 'Address', 'Date'));
                    // Sample data. This can be fetched from mysql too
                    // output each row of the data
                    $content = array();
                    $i = 2;	
                    foreach ($getPlacedInformation as $rs) {
                        $row = array();
                        $row[] = stripslashes($i-1);
                        $row[] = stripslashes($rs["place_description"]);
                        $row[] = stripslashes($rs["consenred_person"]);
                        $row[] = stripslashes($rs["designation"]);
                        $row[] = stripslashes($rs["contact"]);
                        $row[] = stripslashes($rs["address"]);
                        $row[] = stripslashes($rs['offline_sink_datetime']);
                        $content[] = $row;

                        $i++;
                    }
                    foreach ($content as $res){
                       fputcsv($file, $res);
                    }
                    exit;

                    }         
            }
    
    public function trackAction(){
	$this->checklogin();
	$user = new Application_Model_Care();
	$this->view->userdetails = $user->getuserData();		
    }

    public function legendHelpAction(){
	$this->_helper->layout->disableLayout();
    }
    
    
    public function getlocationAction(){
		
	$this->checklogin();
	$params = $this->getRequest()->getParams();
	if(trim($params['user_staff_code'])!=''){
	    $params['user'] = $params['user_staff_code'];
	}
		
	if(trim($params['user_staffcode'])!=''){
	    $params['user'] = $params['user_staffcode'];
	}		
		
	if($params['user_date']!=''){
	    $date =$params['user_date'];
	}
        else{
	    $date =date("Y-m-d");	
	}
        
        echo $sqlQuery = $this->db->select() ->from('logi_field_users',array('*')) ->where('LoginID =?',$params['user']);          
        $result = $this->db->fetchAll($sqlQuery);
        $this->view->current_coord = $result;
        $this->view->user = $params['user'];
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
    
    
    public function logoutAction(){
        $db = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();
        $WebLoginID = $authStorage->read()->WebLoginID;
        $role = $authStorage->read()->Role; 
        $users = new Application_Model_Users();
        $logout_details = $users->getUserLoginDetailByWebLoginCode($WebLoginID);

        // logout time date update in user_login_detail
        if($logout_details['WebLoginID'] !=""){
            $logout_time= $this->currdate;
            $users->updateUserLogoutDetailsByLoginID($WebLoginID,$logout_time);			
        }

        $storage = new Zend_Auth_Storage_Session();
        $storage->clear();
        if($role == "national_head" || $role == "zone_head" || $role == "regional_head" || $role == "circle_head" || $role == "service_manager" || $role == "cluster_incharge"){
            $this->_redirect('/care');
        }
        else{
            $this->_redirect('/admin/index');
        }	   
    }
    
 

	
	
    public function checklogin(){
        $auth = Zend_Auth::getInstance();
        $errorMessage = ""; 
        /*************** check user identity ************/
        if(!$auth->hasIdentity()){  
           $this->_redirect('index');  
        } 
    }

    
}



