<?php

/**
 * File Name: Admin.php
 * Description: Admin Controller 
 * Created By : Praveen Kumar <praveen.kumar@appstudioz>
 * Created date: 06-Sept-2013
 */
	 
class AdminController extends Zend_Controller_Action
{
   var $dbAdapter;
	
    public function init()
    {
        /* Initialize action controller here */
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$this->StaffCode = $authStorage->read()->StaffCode;
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByStaffCode($this->StaffCode);
		$this->view->last_login = $logout_details['login_time'];	
    }

	
	/**
    * index() method is used to admin login
    * @param Username and password
	* @return True 
    */	
    public function indexAction()
    {
		// get adapter
		$dbAdapter = $this->dbAdapter;
		$admin = new Application_Model_Admin();
		$form = new Application_Form_Login();
		$auth = Zend_Auth::getInstance();
		
		$errorMessage = ""; 
		/*************** check user identity ************/
		if($auth->hasIdentity())  
		{  
		    $this->_redirect('/dashboard');  
		} 
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();
				
				$username = $data['username'];
				$password = $data['password'];
				
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		       // Set the input credential values
				$authAdapter->setTableName('admin')  
							->setIdentityColumn('username')  
							->setCredentialColumn('password')  
							->setCredentialTreatment('MD5(?)'); 
				$authAdapter->setIdentity($username)  
							->setCredential($password);
							//print_r($authAdapter);
							//die;
				$result = $auth->authenticate($authAdapter); 
				
				if($result->isValid()){
					$storage = new Zend_Auth_Storage_Session();
					$storage->write($authAdapter->getResultRowObject());
					$auth = Zend_Auth::getInstance();
					$authStorage = $auth->getStorage();
					
					// lastlogin date update in admin table 
					$lastlogin  = date('Y-m-d H:i:s');
					$admin_id = $authStorage->read()->id;
					$admin->updateAdminLastLoginDate($lastlogin,$admin_id);
					$this->_redirect('/dashboard');
				} else {
					$this->view->errorMessage = "Invalid username or password. Please try again.";
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
		$admin = new Application_Model_Admin();
		$form = new Application_Form_Password();
		$errorMessage = ""; 
		$successMessage = ""; 
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
		    if($form->isValid($_POST)){
				$data = $form->getValues();
				$old_pass = md5($data['old_pass']); 
				$password = $data['password']; 
				$cpassword = $data['cpassword']; 
				
				//Admin session data
				$auth = Zend_Auth::getInstance();
				$authStorage = $auth->getStorage();
			    $admin_id = $authStorage->read()->id;
			    $admin_password = $authStorage->read()->password; 
		
				if($old_pass != $admin_password)
				{
				  $this->view->errorMessage = "Old password does not match";
				}
			    if($password != $cpassword)
				{
				  $this->view->errorMessage = "Confirm password does not match with new password";
				}
				
				if($password == $cpassword && $old_pass == $admin_password) 
				{
					$result = $admin->updateChangePasswordByAdminId($password,$admin_id);
					Zend_Auth::getInstance()->getStorage()->write(
					$authStorage->read(),
					array('password' => md5($password))
					);
					$authStorage->read()->password = md5($password);
										
					$this->view->successMessage = "Password has been changed successfully.";
				}	
			}
		} 
		
		
	}
	
	
	/**
	* forgotpassword() method is used to admin forgot password
	* @param String 
	* @return True 
	*/	
    public function forgotpasswordAction()
    {
		// get adapter
		$dbAdapter = $this->dbAdapter;
		$admin = new Application_Model_Admin();
		$form = new Application_Form_Forgot();
		
		$errorMessage = ""; 
		
		$this->view->form = $form;
		if($this->getRequest()->isPost()){
			if($form->isValid($_POST)){
				$data = $form->getValues();
				$email = $data['email']; 
				$result = $admin->getAdminUserListByEmail($email);
				if($result){
					$admin_username = $result['username'];
					$admin_email = $result['email'];
					$password = $result['password2'];
					$from_email = $admin_username."@acme.com";
								
					$subject = "Forgot Password Email";
					$message .= "Here is your admin login details:<br><br>";
					$message .= "Username: $admin_username <br>";
					$message .= "Password: $password <br><br>";
					$message .= "Thanks,<br>";
			
					// To send HTML mail, the Content-type header must be set
					$headers  = 'MIME-Version: 1.0' . "\r\n";
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
					$headers .= 'From: '.$admin_username.' <'.$from_email.'>' . "\r\n";
					
					// Mail it
					@mail($admin_email, $subject, $message, $headers);
					$this->view->errorMessage = "Email has been sent successfully.";
				} else {
					$this->view->errorMessage = "Invalid email. Please try again.";
				}
			}
		} 
		
	}


	/************ by saurabh singh **********/
	public function fielduserlocationAction()
	{
		$this->_helper->layout()->disableLayout();
		$dbAdapter = $this->dbAdapter;
		$params = $this->getRequest()->getParams();
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$role = $authStorage->read()->role;
         $admin = new Application_Model_Admin($params);
		$this->view->latlongArr = $admin->getAllUserCrrentLatitudeLongitude($params['user_search']);
                //print_r($this->view->latlongArr);
                //exit();
		$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	

	/**** logout **********/
	public function logoutAction()
	{
		$storage = new Zend_Auth_Storage_Session();
		$storage->clear();
		$this->_redirect('/admin/index');
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
	
	
}
