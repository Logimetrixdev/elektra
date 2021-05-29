<?php

/**
* Logimetrix Techsolution Pvt. Ltd.
 * File Name   : IndexController.php
 * File Description  : Index Controller
 * Created By : Abhishek Kumar Mishra
 * Created Date: 24 July 2015
 */
	 
class DashboardController extends Zend_Controller_Action
{
   var $dbAdapter;
	
    public function init()
    {
        /* Initialize action controller here */
		$bootstrap = $this->getInvokeArg('bootstrap');
		$aConfig = $bootstrap->getOptions();
                $this->view->siteurl = $aConfig['site']['image']['url'];
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$this->WebLoginID = $authStorage->read()->WebLoginID;
		$this->Role = $authStorage->read()->Role;
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByWebLoginCode($this->WebLoginID);
                $this->view->last_login = $logout_details['login_time'];
    }

	
	/**
    * index() method is used to admin login
    * @param Username and password
	* @return True 
    */	
    public function indexAction(){
		$this->checklogin();
		$params = $this->getRequest()->getParams();
                 $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		//echo "<pre>";print_r($authStorage->read());die;
		$WebLoginID = $authStorage->read()->WebLoginID;
		$role = $authStorage->read()->Role;
		$user = new Application_Model_Users();
                $this->view->getTotalCountCapturedInfoDetail = $getTotalCountCapturedInfoDetail = $user->getTotalCountCapturedInfoDetail();
                $this->view->getTotalCountCapturedPlaceDetail = $getTotalCountCapturedPlaceDetail = $user->getTotalCountCapturedPlaceDetail();

                //$farmer->totalNonRegisteredfarmer();
    }

	
	/**
    * viewHistory() method is used to admin login
    * @param Username and password
	* @return True 
    */	
    public function viewHistoryAction()
    {
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		//print_r($params);die;
		$this->view->fromdate = $params['fromdate'];
		$this->view->todate = $params['todate'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getAllNotificationByRoleName($role,'',$params['fromdate'],$params['todate'],'fromhistory',$params['user_search']);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		$this->view->selectRole = $admin->getRoles($role,$params['role']);
		
	}	

	
	 
	public function presentUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getAllUserByAttendance('present',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
		
	}

	 
	public function absentUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getAllUserByAttendance('absent',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
		
	}
	
	public function movingUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getMovingNotmovingUserList('moving',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
		
	}
	
	public function notmovingUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getNotmovingUserList('notmoving',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
		
	}
	
	public function towardsiteUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getUserStatusWithJobListStatus('towardsite',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function atsiteUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getUserStatusWithJobListStatus('atsite',$role);
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function leftsiteUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getUserStatusWithJobListStatus('leftsite');
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function completeJobsAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getTicketListByStatus('','complete');
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function pendingJobsAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getTicketListByStatus('','pending');
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function processingJobsAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getTicketListByStatus('','processing');
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function gpsonUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getGpsOnOrOffUserList('on');
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function gpsoffUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getGpsOnOrOffUserList('');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function onlineUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getOnlineOfflineUserList('online');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);     
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function offlineUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getOnlineOfflineUserList('offline');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function lowbetteryUserAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getLowBetteryUserList();
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function fsrOnsiteListAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->fsrClosedOnsiteOffsiteList('onsite');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	
	public function fsrOffsiteListAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->fsrClosedOnsiteOffsiteList('');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	
	public function ttOnsiteListAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->ttClosedOnsiteOffsiteList('onsite');
	
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	
	public function ttOffsiteListAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		//$StaffCode = $authStorage->read()->StaffCode;
		//$role = $authStorage->read()->role;
		
		$result= $admin->ttsClosedOnsiteOffsiteList('offsite');
		
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	public function siteVisitCountAction()
    {
		$this->_helper->layout()->disableLayout();
		$this->checklogin();
		$params = $this->getRequest()->getParams();
		$this->view->totalnum = $params['page'];
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		
		$result= $admin->getTotalSiteVisitCountRecord();
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($result);      
		$paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $this->view->per_page_limit = $paginator->setItemCountPerPage(10); // number of items to show per page
        $this->view->paginator = $paginator;
		$this->view->totalrec = $paginator->getTotalItemCount();
		
		
		// get all role in drop down 
		//$this->view->selectRole = $admin->getRoles($role,$params['role']);
	}
	
	
	public function fieldfarmerlocationAction()
	{
		$this->_helper->layout()->disableLayout();
		$dbAdapter = $this->dbAdapter;
		$params = $this->getRequest()->getParams();
		
	
		
		$admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$role = $authStorage->read()->role;
		
		
		
		$this->view->Farmerdetails = $Farmerdetails = $admin->getFarmerdetails();
	
	
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
