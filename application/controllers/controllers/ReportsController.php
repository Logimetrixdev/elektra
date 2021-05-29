<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : ReportsController.php
 * File Description  : Reports Controller
 * Created By : Puneet Mishra
 * Created Date: 09 oct 2016
 ***************************************************************/
 
class ReportsController extends Zend_Controller_Action{
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
   
    public function pollingBothContactInfoAction(){
        $this->checklogin();
        $params = $this->view->params = $this->getRequest()->getParams();  
        //print_r($params);  
        $auth = Zend_Auth::getInstance(); 
        $authStorage = $auth->getStorage();
        $WebLoginID = $authStorage->read()->WebLoginID;
        $users = new Application_Model_Users();
       
        $this->view->getContactInformation = $reportscome= $users->getContactAllInformationByPollingBoth($params['polling_both']);              
        $page=$this->_getParam('page',1);
        $paginator = Zend_Paginator::factory($reportscome);      
        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
        $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
        $this->view->paginator = $paginator;
        $this->view->totalrec = $paginator->getTotalItemCount();
        
        if($params['type'] == 'generate_contact'){
			// echo '<pre />';
			//  print_r($record);
			//  exit();
			// output headers so that the file is downloaded rather than displayed
			header('Content-type: text/csv');
			header('Content-Disposition: attachment; filename="contact_information_detail.csv"');

			// do not cache the file
			header('Pragma: no-cache');
			header('Expires: 0');

			// create a file pointer connected to the output stream
			$file = fopen('php://output', 'w');

			// send the column headers
			fputcsv($file, array('Sr. No.', 'Name', 'Mobile No', 'Designation', 'Polling Both Id', 'Date'));

			// Sample data. This can be fetched from mysql too
			// output each row of the data
			$content = array();
			$i = 2;	
			foreach ($reportscome as $rs) {
				$row = array();
				$row[] = stripslashes($i-1);
				$row[] = stripslashes($rs["name"]);
				$row[] = stripslashes($rs["mobile"]);
				$row[] = stripslashes($rs["designation"]);
				$row[] = stripslashes($rs["polling_both"]);
				$row[] = stripslashes($rs["offline_sink_datetime"]);
				$content[] = $row;
				$i++;
            }

			foreach ($content as $res){
				fputcsv($file, $res);
			}

			exit;
	    }
            
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
