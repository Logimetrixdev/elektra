<?php

/**
 * File Name: MyspaceController.php
 * Description: MyspaceController  
 * Created By : Abhishek Kumar Mishra 
 * Created date: 16-Oct-2015
 */
	 
class CourseController extends Zend_Controller_Action{
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
		//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->record = $record = $user->getAllcourseDetail();
                
			
    }


    
    public function addCourseAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$this->view->rnk =  $getAllRank = $user->getAllRankDetail();
		$params = $this->getRequest()->getParams();
		 if($this->getRequest()->isPost()){
			 	$data1['course_name'] = $params['course_name'];
			    $data1['duration'] = $params['duration'];
			    $db->insert('tbl_courses',$data1);                  					
				$this->view->successMessage = "Course Name Added successfully."; 
        }
    }
	
	public function editCourseAction(){
		$this->checklogin();
		$user = new Application_Model_Users();
		$db = $this->dbAdapter;
			$this->view->rnk =  $getAllRank = $user->getAllRankDetail();
		$params = $this->view->params = $this->getRequest()->getParams();
		$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
        $this->view->gettaskDetails = $taskDetails = $user->getcourseDetails($params['courseId']);
                if($this->getRequest()->isPost()){
            	$data['course_name'] = $params['course_name'];
                $data['duration'] = $params['duration'];
                  
				$user->updateCoursekData($data,$params['courseId']);                     					
				$this->view->successMessage = "Instiute Detail Updated successfully."; 
				$this->_redirect('/course/index/type/success'); 						 
		}
    }

 	public function reportAction(){		
                $this->checklogin(); 
$user = new Application_Model_Users();
		$db = $this->dbAdapter;
		$layout = $this->_helper->layout();
                $layout->disableLayout('');

                                                                                                
	}
    

    public function deleteCourseAction(){		
                $this->checklogin(); 
                $user = new Application_Model_Users();
				$CourseId = $this->getRequest()->getParam('courseId');
				if($CourseId!=""){ 
					 $user->deleCourseById($CourseId);                                        
					 $this->_redirect('/course/index'); 
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
