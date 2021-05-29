<?php

/**
 * File Name: MyspaceController.php
 * Description: MyspaceController  
 * Created By : Abhishek Kumar Mishra 
 * Created date: 16-Oct-2015
 */
	 
class StudentregistrationController extends Zend_Controller_Action{
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
		$this->view->record = $record = $user->getAllstudentDetail();
               
			
    }


    
    public function addStudentregistrationAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$this->view->getAllBatch =  $getAllBatch = $user->getAllbatchDetail();
		$this->view->getAllCourse =  $getAllCourse = $user->getAllcourseDetail();
		$this->view->org = $record = $user->getAllInstituteDetail();
		$this->view->rnk = $rnk = $user->getAllRankDetail();
		$this->view->uni = $unit = $user->getAllUnitDetail();

		$params = $this->getRequest()->getParams();
               
                
		 if($this->getRequest()->isPost()){
			 
          	                $str = substr(base64_encode(sha1(rand())), 0, 6);
								$data1['student_name'] = $params['student_name'];
                                $data1['course_id'] = $params['course_id'];
                                $data1['personal_id'] = $params['personal_id'];
                                $data1['rank_id'] = $params['rank_id'];
                                $data1['unit_id'] = $params['unit_id'];
                                $data1['batch_id'] = $params['batch_id'];
                                $data1['org_id'] = $params['org_id'];
                                $data1['education_detail'] = $params['education_detail'];
                                $data1['password'] = $str;
                                
                               // print_r($data1);
                                //exit();
                          
								$db->insert('tbl_student',$data1); 
                                
                            //    $lastid=$user->getlastid();
                                
                               $last_id = $db->lastInsertId();

                              $generatedid='OTC'.$last_id;
                               $user->updateData($generatedid,$last_id);  
//                                 
				$this->view->successMessage = "Student Detail Added successfully."; 
        }
    }
	
	public function editStudentregistrationAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$params = $this->view->params = $this->getRequest()->getParams();
                $this->view->getAllBatch =  $getAllBatch = $user->getAllbatchDetail();
                $this->view->getAllCourse =  $getAllCourse = $user->getAllcourseDetail();
                $this->view->gettaskDetails = $taskDetails = $user->getstudentDetails($params['studentregistrationId']);
                if($this->getRequest()->isPost()){
                   	          $data['student_name'] = $params['student_name'];
                                $data['course_id'] = $params['course_id'];
                                $data['personal_id'] = $params['personal_id'];
                                $data['rank'] = $params['rank'];
                                $data['unit'] = $params['unit'];
                                 $data['batch_id'] = $params['batch_id'];
                                $data['education_detail'] = $params['education_detail'];
                                
                              $user->updatestudentData($data,$params['studentregistrationId']);                     					
				$this->view->successMessage = "Student Detail Updated successfully."; 
				$this->_redirect('/studentregistration/index/type/success'); 						 
		}
    }


    

    public function deleteTaskAction(){		
                $this->checklogin(); 
                $user = new Application_Model_Users();
				$studentregis1trationId = $this->getRequest()->getParam('studentregistrationId');
				if($studentregistrationId!=""){ 
					 $user->delestudentById($studentregistrationId);                                        
					 $this->_redirect('/studentregistration/index'); 
				}                                                                                    
	}
	


  	public function reportAction(){		
                $this->checklogin(); 
$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$layout = $this->_helper->layout();
                $layout->disableLayout('');
$this->view->record = $record = $user->getAllstudentDetail();
                                                                                                
	}


	public function getCourseAction(){		
                $this->checklogin(); 
				$rank_id =  $this->_getParam('rank_id'); 
                $user = new Application_Model_Users();
                $engg_list_unformat = $user->getAllCourseByRankID($rank_id);
                $engg_list[] = array("value"=>"",'text'=>"Select Course Name");
                foreach($engg_list_unformat as $single){
                        $engg_list[] = array("value"=>$single['id'],"text"=>$single['course_name']);
                        }
                    $this->getHelper('Layout')->disableLayout();

                    $this->getHelper('ViewRenderer')->setNoRender();

                    $this->getResponse()->setHeader('Content-Type', 'application/json');
                echo json_encode(array('options'=>$engg_list));
                return;	                                                                                                
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
