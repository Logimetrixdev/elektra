<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : DistrictsController.php
 * File Description  : DistrictsController
 * Created By : Abhishek Kumar Mishra<abhishek@logimetrix.in>
 * Created Date: 31 July 2015
 ***************************************************************/
 
class DistrictsController extends Zend_Controller_Action
{
    var $dbAdapter;
	
    public function init()
    {
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
    }
    
    public function manageDistrictAction(){
       
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams(); 
        $auth = Zend_Auth::getInstance();
	$authStorage = $auth->getStorage();
        $WebLoginID = $authStorage->read()->WebLoginID;
        $district = new Application_Model_Districts();
        $this->view->record = $record = $district->getAllDistrict();
        $page=$this->_getParam('page',1);
        $paginator = Zend_Paginator::factory($record);      
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $perPage = $paginator->setItemCountPerPage(5); // number of items to show per page
        $this->view->paginator = $paginator;
        $this->view->totalrec = $paginator->getTotalItemCount();
     }
    
     public function addDistrictAction(){
       
	$this->checklogin();
        $db = $this->dbAdapter;        
        $district = new Application_Model_Districts();
        $this->view->AllState = $allstate = $district->getAllState();
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();            
                       if(empty($params['circle_id'])){
                        $this->view->errorMessage = "Kindly select state name";
                        }elseif(empty($params['districtname'])){
                            $this->view->errorMessage = "Kindly enter district name";
                        }else{
                                $District['circle_id'] = $params['circle_id'];
                                $District['district_name'] = $params['districtname'];
                                $db->insert('logi_district', $District);                              
                                $this->view->successMessage = "District has been created."; 
                        }
        }
   }
   
   
    public function editDistrictAction(){
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams();  
        $districtId = $this->getRequest()->getParam('districtId'); 
        $db = $this->dbAdapter;        
        $district = new Application_Model_Districts();
        $this->view->AllState = $allstate = $district->getAllState();
        $record = $district->getDistrictDetails($districtId);
        $successMessage="";
        $errorMessage="";
        if(isset($record['id']) && isset($record['district_name'])){
            $this->view->DistrictDetails = $record;
        }else{
           $this->view->errorMessage = "Sorry record not found";  
        }
        
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();            
                      if(empty($params['circle_id'])){
                        $this->view->errorMessage = "Kindly select state name";
                        }elseif(empty($params['districtname'])){
                            $this->view->errorMessage = "Kindly enter district name";
                        }else{
                                $District['circle_id'] = $params['circle_id'];
                                $District['districtname'] = $params['districtname'];                                
                                $district->UpdateDistrictData($District, $params['districtID']);
                                $this->_redirect('districts/manage-district/type/success');
                        }
        }
       
   }
    
    public function deletedistrictAction(){
			
			$this->checklogin();
			$district = new Application_Model_Districts();
			$DistrictId = $this->getRequest()->getParam('districtId'); 
			
			
			if($DistrictId !="")
			{ 
				$result = $district->deleteDistrict($DistrictId);
				 $this->_redirect('districts/manage-district/'); 
			  
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
