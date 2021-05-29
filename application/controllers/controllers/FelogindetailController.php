<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : FelogindetailController.php
 * File Description  : Felogindetail Controller
 * Created By : Puneet Mishra
 * Created Date: 09 Nov 2015
 ***************************************************************/
 
class FelogindetailController extends Zend_Controller_Action{
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
    
    
    
    public function loginDetailAction(){
        $this->checklogin();
        $params = $this->view->params =$params = $this->getRequest()->getParams();    
        $auth = Zend_Auth::getInstance();
        $authStorage = $auth->getStorage();
        $WebLoginID = $authStorage->read()->WebLoginID;
        $users = new Application_Model_Users();
        $this->view->getAllUserInfo  = $getAllUserInfo= $users->getAllUserInfo();

        if($params['date']){
          $date  =$params['date'];
        }else{
          $date =  date("Y-m-d");
        }

        $this->view->record = $record = $users->GetAllFeUserList($params[datacode], $params['login_status'],$date);;
   
        $page=$this->_getParam('page',1);
        $paginator = Zend_Paginator::factory($record);      
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
        $this->view->paginator = $paginator;
        $this->view->totalrec = $paginator->getTotalItemCount();
    }

    public function checklogin(){		
        $auth = Zend_Auth::getInstance();	
        $errorMessage = ""; 
	/*************** check user identity ************/
        if(!$auth->hasIdentity()){
            $this->_redirect('/index');  
        }		
	}	
}
