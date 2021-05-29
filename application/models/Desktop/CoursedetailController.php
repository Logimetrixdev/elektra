<?php

/**
 * File Name: MyspaceController.php
 * Description: MyspaceController  
 * Created By : Abhishek Kumar Mishra 
 * Created date: 16-Oct-2015
 */
	 
class CoursedetailController extends Zend_Controller_Action{
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
		$user = new Application_Model_Usersotc();
		$db = $this->dbAdapter;
		$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->record = $record = $user->getAllcourseDetails();
	               			
    }


    
    public function addSubjectAction(){
		$this->checklogin();
		$user = new Application_Model_Usersotc();
		$db = $this->dbAdapter;
		//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->courseListById  =  $courseListById  = $user->courseListById($params['courseId']);
		$this->view->getsubjectListBycourseId  =  $getsubjectListBycourseId  = $user->getsubjectListBycourseId($params['courseId']);
		$this->view->getsubjectgroupList  =  $getsubjectgroupList  = $user->getsubjectgroupList();
		
		$params = $this->getRequest()->getParams();
		if($this->getRequest()->isPost()){
			       
					$i=0;
					foreach($params['subjectcode'] as $key=>$value){					
							$data[$i]['course_id'] = $params['courseId'];
							$data[$i]['subject_name'] = $params['subjectname'][$i];               
							$data[$i]['subject_code'] = $value;
						
							$db->insert('tbl_subject',$data[$i]);				
					        $i++;
					}
			        $this->_redirect('/coursedetail/add-subject/courseId/'.$params['courseId']); 
       }
    }
	
		public function editSubjectAction(){
			$this->checklogin();
			$user = new Application_Model_Usersotc();
			$db = $this->dbAdapter;
			//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
			$this->view->params = $params = $this->getRequest()->getParams();
			$this->view->courseListById  =  $courseListById  = $user->courseListById($params['courseId']);
			$this->view->getsubjectListBycourseId  =  $getsubjectListBycourseId  = $user->getsubjectListBycourseId($params['courseId']);
			$this->view->getsubjectgroupList  =  $getsubjectgroupList  = $user->getsubjectgroupList();
			
			$params = $this->getRequest()->getParams();
			if($this->getRequest()->isPost()){
			  
			  $pre=explode(',',$params['prv_id']);
			  $res=array_diff($pre,$params['id']);

              $user->deletedataBycourseId($res);
				
						$i=0;
						foreach($params['subjectcode'] as $key=>$value){  
								
                            
						
                            if(in_array($params['id'][$i],$params['id'])){
								    $data[$i]['course_id'] = $params['courseId'];
									$data[$i]['subject_name'] = $params['subjectname'][$i];               
									$data[$i]['subject_code'] = $value;															
									$user->updatesubjectData($data[$i], $params['id'][$i]);
								
							}						
								else{
									$data[$i]['course_id'] = $params['courseId'];
									$data[$i]['subject_name'] = $params['subjectname'][$i];               
									$data[$i]['subject_code'] = $value;															
									$db->insert('tbl_subject',$data[$i]);
								}								
								$i++;
									
						}
						$this->_redirect('/coursedetail/edit-subject/courseId/'.$params['courseId']); 
		   }
		}
		
		public function subjectsdetailAction(){
			$this->checklogin();
			$user = new Application_Model_Usersotc();
			$db = $this->dbAdapter;
			$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
			$this->view->params = $params = $this->getRequest()->getParams();
			$this->view->record = $record = $user->getsubjectListBycourseId($params['courseId']);
								
      }
	  
	  
	  public function addTopicAction(){
		$this->checklogin();
		$user = new Application_Model_Usersotc();
		$db = $this->dbAdapter;
		//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->getSubjectNameById  =  $getSubjectNameById  = $user->getSubjectNameById($params['subjectId']);
	    $this->view->topicsListById  =  $topicsListById  = $user->topicsListById($params['subjectId']);

		//$this->view->getsubjectgroupList  =  $getsubjectgroupList  = $user->getsubjectgroupList();
		

		if($this->getRequest()->isPost()){
			        $deletedataBysubjectId  = $user->deletedataBysubjectId($params['subjectId']);
					$i=0;
					foreach($params['subjectcode'] as $key=>$value){					
							$data[$i]['subject_code'] = $params['subjectcode'][$i];
							$data[$i]['topic_name'] = $params['subjectname'][$i];               
							$data[$i]['subject_id'] = $params['subjectId'];
							$data[$i]['clcd'] = $params['clcd'][$i];
							$data[$i]['demo'] = $params['demo'][$i];
							$data[$i]['oe'] = $params['oe'][$i];
							$data[$i]['visit'] = $params['visit'][$i];
							$db->insert('tbl_topic',$data[$i]);				
					        $i++;
					}
			        $this->_redirect('/coursedetail/add-topic/subjectId/'.$params['subjectId']); 
       }
    }
	
	public function editTopicAction(){
		$this->checklogin();
		$user = new Application_Model_Usersotc();
		$db = $this->dbAdapter;
		//$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
		$this->view->params = $params = $this->getRequest()->getParams();
		$this->view->getSubjectNameById  =  $getSubjectNameById  = $user->getSubjectNameById($params['subjectId']);
	    $this->view->topicsListById  =  $topicsListById  = $user->topicsListById($params['subjectId']);

		//$this->view->getsubjectgroupList  =  $getsubjectgroupList  = $user->getsubjectgroupList();
		

		if($this->getRequest()->isPost()){
			        $deletedataBysubjectId  = $user->deletedataBysubjectId($params['subjectId']);
					$i=0;
					foreach($params['subjectcode'] as $key=>$value){					
							$data[$i]['subject_code'] = $params['subjectcode'][$i];
							$data[$i]['topic_name'] = $params['subjectname'][$i];               
							$data[$i]['subject_id'] = $params['subjectId'];
							$data[$i]['clcd'] = $params['clcd'][$i];
							$data[$i]['demo'] = $params['demo'][$i];
							$data[$i]['oe'] = $params['oe'][$i];
							$data[$i]['visit'] = $params['visit'][$i];
							$db->insert('tbl_topic',$data[$i]);				
					        $i++;
					}
			        $this->_redirect('/coursedetail/edit-topic/subjectId/'.$params['subjectId']); 
       }
    }
	
	public function topicsdetailAction(){
			$this->checklogin();
			$user = new Application_Model_Usersotc();
			$db = $this->dbAdapter;
			$this->view->getAllUser =  $getAllUser = $user->getAllUserList();
			$this->view->params = $params = $this->getRequest()->getParams();
			$this->view->record = $record = $user->topicsListById($params['subjectId']);
								
      }
	  
	  
	  public function printdetailsAction(){
		$this->checklogin();
		$user = new Application_Model_Usersotc();
		$db = $this->dbAdapter;		
	    $params = $this->getRequest()->getParams();
	    $this->view->printcoursename =  $printcoursename = $user->getCoursenameforprint($params['courseId']); 
	 
		$this->view->record = $record = $user->getsubjectListBycourseId($params['courseId']);
	  			
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
