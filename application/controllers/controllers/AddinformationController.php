<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : AddInformation.php
 * File Description  : AddInformation Controller
 * Created By : Puneet Mishra
 * Created Date: 07 Nov 2016
 ***************************************************************/
 
class AddinformationController extends Zend_Controller_Action{
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
    

    
    public function addContactInformationAction(){
	    $this->checklogin();
        $db = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
	    $authStorage = $auth->getStorage();       
        $CircleID =  $authStorage->read()->CircleID;
        $circle = new Application_Model_States();
        $params = $this->view->params = $this->getRequest()->getParams();
        if($this->getRequest()->isPost()){

         // echo '<pre>';
         // print_r($params);
         // exit();
                       
                           
            if(isset($_FILES['photograph_ci']['tmp_name']) AND !empty($_FILES['photograph_ci']['tmp_name'])){
                $tempName = $_FILES['photograph_ci']['tmp_name'];
                $imageName = time().$_FILES['photograph_ci']['name']; 
                $uploads = 'uploads/user_photograph/';
                if(!file_exists($uploads)){
                        mkdir($uploads);    
                }
                $pathComplete = $uploads.$imageName;
                @move_uploaded_file($tempName,$pathComplete);
            } 

            $insertData = array();
            $insertData['name'] = $params['name'];
            $insertData['mobile'] = $params['mobile'];
            $insertData['whatsapp'] = $params['whatsapp'];
            $insertData['profession'] = $params['profession'];
            $insertData['designation'] = $params['designation'];
            $insertData['cast'] = $params['cast'];
            $insertData['address'] = $params['address'];
            $insertData['polling_both'] = $params['polling_both'];
            $insertData['party_affilitation'] = $params['Affiliation'];
            $insertData['offline_sink_datetime'] = $params['name'];
            if($params['Affiliation'] =='yes'){
              $insertData['party_name'] = $params['party_name'];
            }
            $insertData['photo'] = $pathComplete;
            $insertData['availiability'] = $params['availiability'];
            $insertData['add_by'] = 'web';
            $insertData['offline_sink_datetime'] = date("Y-m-d h:i:s");
            $db->insert('contact_info',$insertData);
           
            $this->view->successMessage = "Contact Information add successfully"; 
                        
        }
    }







    public function addPlaceInformationAction(){
        $this->checklogin();
        $db = $this->dbAdapter;
        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();       
        $CircleID =  $authStorage->read()->CircleID;
        $circle = new Application_Model_States();
        $params = $this->view->params = $this->getRequest()->getParams();
        if($this->getRequest()->isPost()){

         // echo '<pre>';
         // print_r($params);
         // exit();
                       
                           
                if(isset($_FILES['photograph_pi']['tmp_name']) AND !empty($_FILES['photograph_pi']['tmp_name'])){
                    $tempName = $_FILES['photograph_pi']['tmp_name'];
                    $imageName = time().$_FILES['photograph_pi']['name']; 
                    $uploads = 'uploads/user_photograph/';
                    if(!file_exists($uploads)){
                        mkdir($uploads);    
                    }
                    $pathComplete = $uploads.$imageName;
                    @move_uploaded_file($tempName,$pathComplete);
                } 

                $insertcapturedData = array();
                $insertcapturedData['place_description'] = $params['description'];
                $insertcapturedData['consenred_person'] = $params['person'];
                $insertcapturedData['designation'] = $params['designation'];
                $insertcapturedData['contact'] = $params['contact'];
                $insertcapturedData['address'] = $params['address'];

                $insertcapturedData['offline_sink_datetime'] = date("Y-m-d h:i:s");
                $insertcapturedData['photo'] = $pathComplete;
                $insertcapturedData['add_by'] = 'web';
                $db->insert('place_info',$insertcapturedData);
           
            $this->view->successMessage = "Place Information add successfully"; 
                        
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
