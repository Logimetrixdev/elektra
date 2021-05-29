<?php
/***************************************************************
 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : IndexController.php
 * File Description  : Index Controller
 * Created By : Abhishek Kumar Mishra
 * Created Date: 24 July 2015
 ***************************************************************/
 
class IndexController extends Zend_Controller_Action
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
                    $users = new Application_Model_Users();
                    $logout_details = $users->getUserLoginDetailByWebLoginCode($this->WebLoginID);
                    $this->view->last_login = $logout_details['login_time'];
                }
		
			
    }

	
    public function indexAction()
    {
       // get adapter
		$dbAdapter = $this->dbAdapter;
		$users = new Application_Model_Users();
		$form = new Application_Form_IndexLogin();
		$auth = Zend_Auth::getInstance();
		
//		$errorMessage = ""; 
//		/*************** check user identity ************/
		if($auth->hasIdentity()){  
		    $this->_redirect('/dashboard');  
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
							->setCredentialTreatment("? AND Role != 'field_user' AND StaffStatus='AC' ");
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
					if($login_details['WebLoginID'] !="")
					{
					    $users->updateUserLoginDetailsByLoginID($WebLoginID,$user_login_data['login_time']);			
					}
					else
					{
					    $users->insertUserLoginDetailsByWebLoginID($user_login_data);				
					}
                                        
					$this->_redirect('/dashboard');
				} else {
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
		$users = new Application_Model_Users();
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
		$users = new Application_Model_Users();
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
                    else if($role=="service_manager")
                    { 
					    $roletype_name = "Service manager";
                    }	
                    else if($role=="cluster_incharge")
                    {
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
	
	
	public function checklogin()
	{
		$auth = Zend_Auth::getInstance();
		
		$errorMessage = ""; 
		/*************** check user identity ************/
		if(!$auth->hasIdentity())  
        {  
            $this->_redirect('index');  
        } 
	}

    
}



