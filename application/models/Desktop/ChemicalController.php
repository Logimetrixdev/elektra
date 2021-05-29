<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : StatesController.php
 * File Description  : StatesController
 * Created By : Abhishek Kumar Mishra
 * Created Date: 16 Aug 2015
 ***************************************************************/
 
class UnitController extends Zend_Controller_Action
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
    
    public function manageUnitAction(){
	$this->checklogin();
        $auth = Zend_Auth::getInstance();
	$authStorage = $auth->getStorage();
        $WebLoginID = $authStorage->read()->WebLoginID;
        $state = new Application_Model_States();
        $params = $this->view->params = $this->getRequest()->getParams();
        $this->view->record = $record = $state->getAllUnits();
        $page=$this->_getParam('page',1);
        $paginator = Zend_Paginator::factory($record);      
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $perPage = $paginator->setItemCountPerPage(8); // number of items to show per page
        $this->view->paginator = $paginator;
        $this->view->totalrec = $paginator->getTotalItemCount();
     }
    
     public function addUnitAction(){
	$this->checklogin();
        $db = $this->dbAdapter;        
        $state = new Application_Model_States(); 
        $district = new Application_Model_Districts();
        $this->view->AllState = $allstate = $district->getAllState();
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();            
                       if(empty($params['stateID'])){
                        $this->view->errorMessage = "Kindly select state name";
                        }elseif(empty($params['unittype'])){
                            $this->view->errorMessage = "Kindly select unit type";
                        }elseif(empty($params['unit'])){
                           $this->view->errorMessage = "Kindly enter unit name"; 
                        }elseif(empty($params['disp'])){
                           $this->view->errorMessage = "Kindly enter unit app display code"; 
                        }
                        else{
                                $UnitData['stateId'] = $params['stateID'];
                                $UnitData['unittype'] = $params['unittype'];
                                $UnitData['unit'] = $params['unit'];
                                $UnitData['disp'] = $params['disp'];
                                $UnitData['acr_val'] = $params['acr_val'];
                                $db->insert('logi_units', $UnitData);                              
                                $this->view->successMessage = "New unit has been created."; 
                        }
        }
   }
   
   
    public function editUnitAction(){
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams();  
        $unitId = $this->getRequest()->getParam('code'); 
        $db = $this->dbAdapter;  
        $district = new Application_Model_Districts();
        $this->view->AllState = $allstate = $district->getAllState();
        $state = new Application_Model_States();  
        $this->view->Record = $record = $state->getUnitDetails($unitId);
        
        $successMessage="";
        $errorMessage="";
      
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();
                        if(empty($params['unitId'])){
                        $this->view->errorMessage = "something wroung is going on.";
                        }
                        elseif(empty($params['stateID'])){
                        $this->view->errorMessage = "Kindly select state name";
                        }elseif(empty($params['unittype'])){
                            $this->view->errorMessage = "Kindly select unit type";
                        }elseif(empty($params['unit'])){
                           $this->view->errorMessage = "Kindly enter unit name"; 
                        }else{
                                $UnitData['stateId'] = $params['stateID'];
                                $UnitData['unittype'] = $params['unittype'];
                                $UnitData['unit'] = $params['unit'];
                                $UnitData['disp'] = $params['disp'];
                                $UnitData['acr_val'] = $params['acr_val'];
                                $state->UpdateStateData('logi_units', $UnitData, 'id', $params['unitId']);
                               $this->_redirect('unit/manage-unit/type/success');
                        }
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
