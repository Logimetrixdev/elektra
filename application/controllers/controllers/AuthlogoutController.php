<?php
/**
* Logimetrix Techsolution Pvt. Ltd.
 * File Name   : IndexController.php
 * File Description  : Index Controller
 * Created By : Abhishek Kumar Mishra
 * Created Date: 24 July 2015
 */
	 

	 
class AuthlogoutController extends Zend_Controller_Action
{
   var $dbAdapter;
	
    public function init()
    {
        /* Initialize action controller here */
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
		$this->currdate = date("Y-m-d H:i:s");//$date->toString('Y-m-d H:m:s');
    }

	
	/**
    * index() method is used to admin login
    * @param Username and password
	* @return True 
    */	
    public function indexAction()
    {
		// Action Body
    }

	
	/**** logout **********/
	public function logoutAction()
	{
                $db = $this->dbAdapter;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$WebLoginID = $authStorage->read()->WebLoginID;
		$role = $authStorage->read()->Role; 
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByWebLoginCode($WebLoginID);
                $front = Zend_Controller_Front::getInstance();
                $controller = $front->getRequest()->getControllerName(); 
		
		// logout time date update in user_login_detail
		if($logout_details['WebLoginID'] !="")
		{
		    $logout_time= $this->currdate;
		    $users->updateUserLogoutDetailsByLoginID($WebLoginID,$logout_time);			
		}
		
		$storage = new Zend_Auth_Storage_Session();
		$storage->clear();
		if($role == "national_head" || $role == "zone_head" || $role == "regional_head" || $role == "circle_head" || $role == "service_manager" || $role == "cluster_incharge")
		{
		    $this->_redirect('/index');
		}
              
                
		else
		{
		    $this->_redirect('/admin/index');
	    }
		
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
