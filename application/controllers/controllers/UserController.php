<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : FqbController.php
 * File Description  : Fqb Controller
 * Created By : Abhishek Kumar Mishra<piyush@logimetrix.in>
 * Created Date: 24 July 2015
 ***************************************************************/
 
class UserController extends Zend_Controller_Action{
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
    }
    
    
     public function manageUserAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();    
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();
                $this->view->record = $record = $users->GetAllFEList($WebLoginID);
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($record);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
     }
     

    
     public function addFielduserAction(){
	$this->checklogin();
        $db = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
	    $authStorage = $auth->getStorage();       
        $CircleID =  $authStorage->read()->CircleID;
        $circle = new Application_Model_States();
  
        //$this->view->regionList = $regionList = $region->getAllRegionByCircleCode($CircleCode); 
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();
            
                        if(empty($params['staffname'])){
                             $this->view->errorMessage = "Kindly enter field executive name";
                        }
                        elseif(empty($params['phone'])){
                             $this->view->errorMessage = "Kindly enter field executive phone no.";
                        }
                        /*elseif(empty($params['circle_id'])){
                           $this->view->errorMessage = "Kindly select Circle Name"; 
                        }elseif(empty($params['district_id'])){
                           $this->view->errorMessage = "Kindly select District Name"; 
                        }elseif(empty($params['tehsil_id'])){
                           $this->view->errorMessage = "Kindly select Tehsil Name"; 
                        }elseif(empty($params['region_id'])){
                           $this->view->errorMessage = "Kindly select Region Name"; 
                        }elseif(count($params['vill'])<1){
                           $this->view->errorMessage = "Kindly select Village"; 
                        }*/
                        
                        else{
                           
                                $UserData['StaffName'] = $params['staffname'];
                                $UserData['Email'] = $params['email'];
                                $UserData['MobileNo'] = $params['phone'];
                                $UserData['DOB'] = $params['dob'];
                                $UserData['Address_Curr'] = $params['current_address'];
                                $UserData['Address_Per'] = $params['permanent_address'];
                                $UserData['Gender'] = $params['gender'];
                                //$UserData['CircleID'] = $params['circle_id'];
                               // $UserData['DistrictID'] = $params['district_id'];
                                //$UserData['TehsilID'] = $params['tehsil_id'];
                                //$UserData['ReigonID'] = $params['region_id'];
                                //$UserData['totalMappedVill'] = count($params['vill']);
                                $UserData['ParentWebLoginID'] = $authStorage->read()->WebLoginID;
                                $UserData['Role'] = 'field_user';     
                                $db->insert('logi_field_users', $UserData);
                                $UserInsertId = $db->lastInsertId();


                                $staff_name = strtoupper($params['staffname']);
                                $substr = substr($staff_name, 0, 3);
                                $uniqueno = 1000+$UserInsertId-1;
                                $LoginID = 'ELE'.$uniqueno;
                                $updateLoginData['LoginID']  = $LoginID;
                                $updateLoginData['identity'] = $substr.'00'.$UserInsertId;
                                $where = array('id = ? '=>$UserInsertId);
                                $db->update('logi_field_users', $updateLoginData , $where);
                                
                                /*foreach($params['vill'] as $villID){
                                    $insertVillMapping['vill_id'] = $villID;
                                    $insertVillMapping['user_id'] = $UserInsertId;
									
                                    $db->insert('logi_user_village_mapping', $insertVillMapping);
                                }*/
                                
                                $this->view->successMessage = "Field executive has been successfully created. App Login ID is ".$LoginID; 
                        }
        }
   }
   
   
    public function viewContactAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();    
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();
                $this->view->getContactInformationDetail= $getContactInformationDetail= $users->getContactInformationDetail($params['datacode']);              
                
                
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($getContactInformationDetail);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();


               
     }
     
     
      public function viewPlaceAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();    
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();
                $this->view->getPlaceInformationDetail= $getPlaceInformationDetail= $users->getPlaceInformationDetail($params['datacode']);              
                
                
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($getPlaceInformationDetail);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();


               
     }
     
     
         public function viewDetailAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams(); 
                       $layout = $this->_helper->layout();
                $layout->disableLayout('');
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();

               
                $this->view->getLoginUserInformationByID  = $getLoginUserInformationByID= $users->getLoginUserInformationByID($params['data']);

                $sql_get_parent = "select StaffName from logi_field_users where identity  = '".$params['under']."'";
                $this->view->parent_user = $parent_user =  $this->dbAdapter->fetchRow($sql_get_parent); 



        }
        
           public function viewDetailPlaceAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams(); 
                       $layout = $this->_helper->layout();
                $layout->disableLayout('');
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();
               
                $this->view->getLoginUserInformationByID  = $getLoginUserInformationByID= $users->getLoginPlaceUserInformationByID($params['data']); 

                $sql_get_parent = "select StaffName from logi_field_users where identity  = '".$params['under']."'";
                $this->view->parent_user = $parent_user =  $this->dbAdapter->fetchRow($sql_get_parent);     
        }
        
        
        
        public function locationAction(){
            $this->checklogin();
            $layout = $this->_helper->layout();
            $layout->disableLayout('');
            $Id = $this->getRequest()->getParam('data');          
            $user= new Application_Model_Users();    
	    $this->view->viewLocation = $viewLocation = $user->viewLocation($Id);
	}
   
   
   
      public function editFielduserAction(){
        $this->checklogin();
        $db = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage(); 
	$params = $this->view->params = $this->getRequest()->getParams();
        $user_id=$params['datacode'];
        $user= new Application_Model_Users();

         $this->view->userDetails=$user->getuser_id($params['datacode']);
		
        if($this->getRequest()->isPost()){
			
			
			
			
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();
            
                       if(empty($params['staffname'])){
                        $this->view->errorMessage = "Kindly enter field executive name";
                        }elseif(empty($params['phone'])){
                            $this->view->errorMessage = "Kindly enter field executive phone no.";
                        }
                        /*elseif(count($params['vill'])<1){
                           $this->view->errorMessage = "Kindly select Village"; 
                        }*/
                        else{
                           
                                $UserData['StaffName'] = $params['staffname'];
                                $UserData['Email'] = $params['email'];
                                $UserData['MobileNo'] = $params['phone'];
                                $UserData['DOB'] = $params['dob'];
                                $UserData['Address_Curr'] = $params['current_address'];
                                $UserData['Address_Per'] = $params['permanent_address'];
                                $UserData['Gender'] = $params['gender'];
                                //$UserData['CircleID'] = $params['circle_id'];
                               // $UserData['DistrictID'] = $params['district_id'];
                                //$UserData['TehsilID'] = $params['tehsil_id'];
                                //$UserData['ReigonID'] = $params['region_id'];
                               // $UserData['totalMappedVill'] = count($params['vill']);
                                $UserData['ParentWebLoginID'] = $authStorage->read()->WebLoginID;
                                $UserData['Role'] = 'field_user';     
                                $UserData['LoginID'] = $params['user_id']; 
                                $where = array('LoginID = ? '=>$params['user_id']);
                                $db->update('logi_field_users', $UserData , $where);
                                
                                //$user->deleteOldVill($UsersID['id'],$params['region_id']);
                               
								/*foreach($params['vill'] as $villID){
									
									$insertVillMapping['vill_id'] = $villID;
                                    $insertVillMapping['user_id'] = $UsersID['id'];
									$insertVillMapping['region_id'] = $params['region_id'];
									
                                    $db->insert('logi_user_village_mapping', $insertVillMapping);
                                }*/
                                
                               // $this->view->successMessage = "Field executive has been successfully updated"; 
                                $this->_redirect('user/manage-user/type/sucess');  
                        }
        }
   }
    
        public function deleteUserAction(){
        $this->checklogin();
        $db = $this->db;
        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();
        $params = $this->getRequest()->getParams();
        $users  = new Application_Model_Users();
        $userId = $params['userId'];
        $users->deleteUser($userId);
        $this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
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
