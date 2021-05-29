<?php

/**
 * File Name: MyspaceController.php
 * Description: MyspaceController  
 * Created By : Abhishek Kumar Mishra 
 * Created date: 16-Oct-2015
 */
	 
class BatchController extends Zend_Controller_Action{
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
    public function indexAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->record = $record = $user->getAllbatchDetail();
                
			
    }


    
    public function addBatchAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$params = $this->getRequest()->getParams();
		 if($this->getRequest()->isPost()){
			 
          	
				$data1['batch_name'] = $params['batch_name'];
                                
				$data1['strength'] = $params['strength'];
			       
				$db->insert('tbl_batch',$data1);                  					
				$this->view->successMessage = "Batch Name Added successfully."; 
        }
    }
	
	public function editBatchAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$params = $this->view->params = $this->getRequest()->getParams();
		$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
        $this->view->gettaskDetails = $taskDetails = $user->getbatchDetails($params['batchId']);
                if($this->getRequest()->isPost()){
            	$data['batch_name'] = $params['batch_name'];
                $data['strength'] = $params['strength'];
        
                   
				$user->updatebatchData($data,$params['batchId']);                     					
				$this->view->successMessage = "Batch Detail Updated successfully."; 
				$this->_redirect('/batch/index/type/success'); 						 
		}
    }


    

    public function deleteTaskAction(){		
                $this->checklogin(); 
                $user = new Application_Model_Users();
				$batchId = $this->getRequest()->getParam('batchId');
				if($batchId!=""){ 
					 $user->delebatchById($batchId);                                        
					 $this->_redirect('/batch/index'); 
				}                                                                                    
	}
	
	
	public function checklogin(){
		$auth = Zend_Auth::getInstance();
		
		$errorMessage = ""; 
		/*************** check user identity ************/
        if(!$auth->hasIdentity())
        {
		     $this->_redirect('admin/index');  
        }		
	}
	
	
}
