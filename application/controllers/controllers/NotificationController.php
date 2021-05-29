

<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : NotificationController.php
 * File Description  : Notification Controller
 * Created By : Neha Gupta
 * Created Date: 10 May 2018
 ***************************************************************/

class NotificationController extends Zend_Controller_Action{
	var $dbAdapter;
	
	public function init(){
		/* Initialize action controller here */
		$bootstrap = $this->getInvokeArg('bootstrap');
		$aConfig = $bootstrap->getOptions();
		$this->dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$this->WebLoginID = $authStorage->read()->WebLoginID;
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByWebLoginCode($this->WebLoginID);
		$this->view->last_login = $logout_details['login_time'];			
	}

	public function indexAction() {
		$this->checklogin();
		$db = $this->dbAdapter;
		$params = $this->view->params = $this->getRequest()->getParams();   
		$auth = Zend_Auth::getInstance();
		$sql = "SELECT * FROM tbl_notification WHERE status = '1'";
		$this->view->notification_data = $notification_data   = $db->fetchAll($sql);
		if ($params['id']) {
			$sql2 = "SELECT * FROM tbl_notification WHERE status = '1' AND id = '".$params['id']."'";
			$this->view->editNotification = $editNotification   = $db->fetchRow($sql2);
		}
		if($this->getRequest()->isPost()){

			if ($params['id']) {
				$UserData = array();
				$UserData['notification_content'] = $params['notification_content'];				
				$where = array('id = ? '=>$params['id']);
				$db->update('tbl_notification', $UserData , $where);
				$this->view->successMessage = "Notification Updated successfully";
				$this->_redirect('/notification');
			}
			else{
				$UserData = array();
				$UserData['notification_content'] = $params['notification_content'];
				$UserData['created_date']         = date('Y-m-d H:i:s');
				$db->insert('tbl_notification', $UserData);
				$this->view->successMessage = "Notification Added successfully";
				$this->_redirect('/notification');
			}

		}
		$page=$this->_getParam('page',1);
		$paginator = Zend_Paginator::factory($notification_data);      
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $perPage = $paginator->setItemCountPerPage(5); // number of items to show per page
        $this->view->paginator = $paginator;
        $this->view->totalrec = $paginator->getTotalItemCount();
    }


    
    public function deleteNotificationAction()
    {
    	$this->checklogin();
    	$this->db               = Zend_Db_Table::getDefaultAdapter();
    	if($this->getRequest()->isPost()) {
    		$requestParams = $this->getRequest()->getParams();
    		if($requestParams['status']!=''){
    			$mem_del = array();
    			$mem_del['status'] = 0;
    			$where_del['id = ?']   = $requestParams['id'];
    			$this->db->update('tbl_notification', $mem_del, $where_del);
    			$subject = array();
    			$subject['status'] = 0;
    			$where['id = ?']   = $requestParams['id'];
    			try{
    				$result = $this->db->update('tbl_notification', $subject, $where);
    				$msg= "Notification deleted successfully.";
    				$this->view->successMessage = $msg;
    				$this->_redirect('/notificationn/index');
    			}
    			catch (Exception $e) {
    				$this->view->errorMessage   = $e->getCode(); 
    			}
    		}
    		else{
    			$msg= "Deleted field missing";
    			$this->view->errorMessage   = $msg;
    		}
    		
    	} 
    }




    public function checklogin(){		
    	$auth = Zend_Auth::getInstance();	
    	$errorMessage = ""; 
    	/*************** check user identity ************/
    	if(!$auth->hasIdentity()){
    		$this->_redirect('admin/index');  
    	}		
    }




}
