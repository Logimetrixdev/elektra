<?php

/**
 * File Name: MyspaceController.php
 * Description: MyspaceController  
 * Created By : Abhishek Kumar Mishra 
 * Created date: 16-Oct-2015
 */
	 
class CourseController extends Zend_Controller_Action{
   var $dbAdapter;
    public function init(){
        /* Initialize action controller here */
		$bootstrap = $this->getInvokeArg('bootstrap');
		$aConfig = $bootstrap->getOptions();
	    $this->view->siteurl = $aConfig['site']['image']['url'];
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$this->LoginID = $authStorage->read()->LoginID;
		$this->role = $authStorage->read()->role;
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByStaffCode($this->LoginID);
		$this->view->last_login = $logout_details['login_time'];
		$this->currdate = date("Y-m-d H:i:s",strtotime('+330 minutes'));
    }

	
	/**
    * index() method is used to admin login
    * @param LoginID and password
	* @return True 
    */	
   
    


 	public function reportAction(){		
                $this->checklogin(); 
$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$layout = $this->_helper->layout();
                $layout->disableLayout('');

                                                                                                
	}
    

 
	
	
}
