<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : AppsettingController.php
 * File Description  : AppsettingController
 * Created By : Abhishek Kumar Mishra<abhishek@logimetrix.in>
 * Created Date: 1 August 2015
 ***************************************************************/
 
class AppsettingController extends Zend_Controller_Action
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
                $params = $this->view->params = $this->getRequest()->getParams(); 
                $farmermaster = new Application_Model_Farmermasters();
                $this->view->FarmerType = $record = $farmermaster->getFarmerType();
                $this->view->FarmerPriority = $farmerPriority = $farmermaster->getFarmerPriority();
                $this->view->Reference = $Reference = $farmermaster->getAllReferenceType();
    }
    
    public function manageLandMasterAction(){
       
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams(); 
        $farmermaster = new Application_Model_Farmermasters();
        $this->view->LastCrops = $record = $farmermaster->getLastCrops();
        $this->view->SoilType = $soiltype = $farmermaster->getSoilType();
        $this->view->RabiCrop = $rabicrop = $farmermaster->getRabiCrop();
        $this->view->KharifCrop = $kharifcrop = $farmermaster->getKharifCrop();
      
     }
     
        public function manageCropMasterAction(){
       
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams(); 
        $farmermaster = new Application_Model_Farmermasters();
        $this->view->WayofSowing = $record = $farmermaster->getWayOfShowing();
        $this->view->Irrigation = $irrigation = $farmermaster->getWayOfIrrigation();
        $this->view->WaterSource = $sourceWater = $farmermaster->getSourceofWater();
      
     }
    
    
     public function addKeyAction(){       
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams(); 
       
        $db = $this->dbAdapter;        
        $district = new Application_Model_Districts();
        $tehsil = new Application_Model_Tehsils();
        $this->view->AllState = $allstate = $district->getAllState();
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            if($params['keyname']=='lastcrop'){
                 $tabletoinsert = 'logi_lastcrop';
                 $data['lastcrop'] = $params['keydata'];
            }elseif ($params['keyname']=='lastrabi') {
                 $tabletoinsert = 'logi_crop_rabi';
                 $data['RabiCrop'] = $params['keydata'];
            }elseif ($params['keyname']=='lastkharif') {
                 $tabletoinsert = 'last_crop_kharif';
                 $data['KharifCrop'] = $params['keydata'];
            } elseif ($params['keyname']=='soiltype') {
                 $tabletoinsert = 'logi_soiltype';
                 $data['SoilType'] = $params['keydata'];
            } elseif ($params['keyname']=='farmertype') {
                 $tabletoinsert = 'logi_farmertype';
                 $data['farmertype'] = $params['keydata'];
            } elseif ($params['keyname']=='priority') {
                 $tabletoinsert = 'logi_farmer_priority';
                 $data['priority'] = $params['keydata'];
            }elseif ($params['keyname']=='reftype') {
                 $tabletoinsert = 'logi_farmer_reference';
                 $data['referencetype'] = $params['keydata'];
            }elseif ($params['keyname']=='irrigation') {
                 $tabletoinsert = 'logi_wayofirrigation';
                 $data['WayofIrrigation'] = $params['keydata'];
            } elseif ($params['keyname']=='sowing') {
                 $tabletoinsert = 'logi_wayofshowing';
                 $data['WayOfShowing'] = $params['keydata'];
            }  elseif ($params['keyname']=='source') {
                 $tabletoinsert = 'logi_sourceofwater';
                 $data['SourceOfWater'] = $params['keydata'];
            } else {
                
            }
            $db->insert($tabletoinsert, $data);                              
            $this->view->successMessage = "Data has been created."; 
            $this->_redirect('appsetting/add-key/type/success/key/'.$params['keyname']);            
        }
   }
   
   public function getAlltehsilByDistrictAction(){
                    $district_id =  $this->_getParam('district_id'); 
                    $region = new Application_Model_Regions();
                    $teh_list_unformat = $region->getAllTehsilByDistrictID($district_id);
                    $teh_list[] = array("value"=>"",'text'=>"--Select Tehsil--");
                    foreach($teh_list_unformat as $single){
                        $teh_list[] = array("value"=>$single['id'],"text"=>$single['tehsil_name']);
                        }
                    $this->getHelper('Layout')->disableLayout();
                    $this->getHelper('ViewRenderer')->setNoRender();
                    $this->getResponse()->setHeader('Content-Type', 'application/json');
                    echo json_encode(array('options'=>$teh_list));
                    return;	
                } 
                
                
                
    public function editKeyAction(){
	$this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams();  
        $UpdationID = $this->getRequest()->getParam('code'); 
        $selectionLogic = $this->getRequest()->getParam('key'); 
        $db = $this->dbAdapter;        
        $farmerMaster = new Application_Model_Farmermasters();
        if($selectionLogic=='farmertype'){
        $this->view->FarmerTypeDetails = $FarmerTypeDetails = $farmerMaster->getFarmerTypeDetails($UpdationID);
        }elseif($selectionLogic=='priority'){
        $this->view->FarmerPriority = $FarmerPriority = $farmerMaster->getFarmerPriorityDetails($UpdationID);    
        }elseif($selectionLogic=='reftype'){
        $this->view->FarmerRefType = $FarmerRefType = $farmerMaster->getReferenceTypeDetails($UpdationID);    
        }elseif($selectionLogic=='lastcrop'){
        $this->view->LastCrop = $LastCrop = $farmerMaster->getlastcropDetails($UpdationID);    
        }elseif($selectionLogic=='lastrabi'){
        $this->view->LastRabi = $LastRabi = $farmerMaster->getlastRabicropDetails($UpdationID);    
        }elseif($selectionLogic=='lastkharif'){
        $this->view->LastKharif = $LastKharif = $farmerMaster->getlastKharifcropDetails($UpdationID);    
        }elseif($selectionLogic=='soiltype'){
        $this->view->SoilType = $SoilType = $farmerMaster->getSoilTypeDetails($UpdationID);    
        }elseif($selectionLogic=='sowing'){
        $this->view->Sowing = $Sowing = $farmerMaster->getSowingDetails($UpdationID);    
        }elseif($selectionLogic=='irrigation'){
        $this->view->irr = $irr = $farmerMaster->getIrrigationDetails($UpdationID);    
        }elseif($selectionLogic=='source'){
        $this->view->SourceWater = $SourceWater = $farmerMaster->getWaterSourceDetails($UpdationID);    
        }     else {
            
        }
        
        
        if($this->getRequest()->isPost()){
            $successMessage="";
            $errorMessage="";
            $params = $this->view->params = $this->getRequest()->getParams();            
            if($params['keyname']=='lastcrop'){
                 $tabletoinsert = 'logi_lastcrop';
                 $data['lastcrop'] = $params['keydata'];
            }elseif ($params['keyname']=='lastrabi') {
                 $tabletoinsert = 'logi_crop_rabi';
                 $data['RabiCrop'] = $params['keydata'];
            }elseif ($params['keyname']=='lastkharif') {
                 $tabletoinsert = 'last_crop_kharif';
                 $data['KharifCrop'] = $params['keydata'];
            } elseif ($params['keyname']=='soiltype') {
                 $tabletoinsert = 'logi_soiltype';
                 $data['SoilType'] = $params['keydata'];
            } elseif ($params['keyname']=='farmertype') {
                 $tabletoinsert = 'logi_farmertype';
                 $data['farmertype'] = $params['keydata'];
            } elseif ($params['keyname']=='priority') {
                 $tabletoinsert = 'logi_farmer_priority';
                 $data['priority'] = $params['keydata'];
            }elseif ($params['keyname']=='reftype') {
                 $tabletoinsert = 'logi_farmer_reference';
                 $data['referencetype'] = $params['keydata'];
            }elseif ($params['keyname']=='irrigation') {
                 $tabletoinsert = 'logi_wayofirrigation';
                 $data['WayofIrrigation'] = $params['keydata'];
            } elseif ($params['keyname']=='sowing') {
                 $tabletoinsert = 'logi_wayofshowing';
                 $data['WayOfShowing'] = $params['keydata'];
            }  elseif ($params['keyname']=='source') {
                 $tabletoinsert = 'logi_sourceofwater';
                 $data['SourceOfWater'] = $params['keydata'];
            } else {
                
            } 
            
            $updationID = $params['idval'];
            $farmerMaster->updateMasterData($tabletoinsert, $data, 'id', $updationID);
            
            $this->_redirect('appsetting/index/type/success');
                        
        }
       
   }
    
		public function deleteappAction(){
			
			$this->checklogin();
			$farmerMaster = new Application_Model_Farmermasters();
			$AppId = $this->getRequest()->getParam('appId'); 
			$Mode = $this->getRequest()->getParam('mode');
			
			if($AppId!="" && $Mode!='')
			{ 
				$result = $farmerMaster->deleteApp($AppId, $Mode);
				 $this->_redirect('/appsetting'); 
			  
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
