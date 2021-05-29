<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : AjaxrequestController.php
 * File Description  : Ajax request
 * Created By : Praveen Kumar
 * Created Date: 18 September 2013
 ***************************************************************/
 
class AjaxrequestController extends Zend_Controller_Action
{
    var $dbAdapter;
    var $siteurl; 		
	
    public function init()
    {
        
        $bootstrap = $this->getInvokeArg('bootstrap');
		$aConfig = $bootstrap->getOptions();
                
        /* Initialize action controller here */
        
	    $this->_helper->viewRenderer->setNoRender(true);
             $this->siteurl = $aConfig['api']['site']['url'];
	    $this->dbAdapter = Zend_Db_Table::getDefaultAdapter(); 
            
                 $auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$users = new Application_Model_Users();
		$logout_details = $users->getUserLoginDetailByStaffCode($StaffCode);
		$this->view->last_login = $logout_details['login_time'];
                
    }

    public function indexAction()
    {
        // action body
		exit;
    }
	
	
	/**
	* changeJobPriority() method is used to change job priority AS (Low,Medium,High,Critical)
	* @param Null
	* @return Array 
	*/	
	public function changeJobPriorityAction()
	{
		$job = new Application_Model_Job();
		
		//Change Job Priority Code
		$action =  $this->_getParam('actionString'); 
		$priority =  $this->_getParam('priority');
	    $jobid =  $this->_getParam('jobid');   
		if($action == "job-priority")
		{
			$job->updateJobPriorityByJobId($priority,$jobid);
			//echo $message = "Job Priority Updated Successfully.";
		}
		echo $priority;
		die;
	}
	
	
	/**
	* changeJobSchedule() method is used to change job schedule AS (1,2,3,4,5,6,7,8,9,10)
	* @param Null
	* @return Array 
	*/	
	public function changeJobScheduleAction()
	{
		$this->checklogin();
		$job = new Application_Model_Job();
		$db = $this->dbAdapter;
		//Change Job Priority Code
		$job_action_schedule =  $this->_getParam('job_action_schedule'); 
		$schedule =  $this->_getParam('schedule');
	    $job_id =  $this->_getParam('job_id');   
		if($job_action_schedule == "job-schedule")
		{ 
		    $user_details = $db->fetchRow("select lum.StaffName,lum.StaffCode,lum.device_type,lum.device_token from call_log AS cl INNER JOIN local_user_mapping AS lum ON(cl.curr_Alloted_Eng_Code=lum.CMSStaffStockCode) where md5(concat(cl.Call_Log_No,'sendid')) = '".$job_id."' ");
			$deviceToken = $user_details['device_token'];
			$deviceType = $user_details['device_type']; 
			if($deviceToken && $deviceType)
			{
				$status = "schedule";
				$pushmsg = "schedule";
				$payload['aps'] = array('alert' =>"$pushmsg", 'push_type' =>$status);
				$pushobj = new Application_Model_Sendpush();
				$pushobj->sendPush($deviceToken,$deviceType,$payload);
			}
			
			$job->updateJobScheduleByJobId($schedule,$job_id);
		}
		echo $schedule;
		die;
	}
	
	
	/**
	* deleteUser() method is used to delete user device data
	* @param Null
	* @return Array 
	*/	
	public function deleteUserAction()
	{
		$this->checklogin();
		$users = new Application_Model_Users();
		
		//update user device data
	    $UserId = $this->getRequest()->getParam('uid'); 
		$mode = $this->getRequest()->getParam('mode');  
	
		if($UserId !="" && $mode == "delete")
		{ 
		    $Data = $users->getUserdetailsByUserId($UserId);
		    $UserStaffCode = $Data['StaffCode'];  
			$result = $users->deleteDeviceDataByStaffCode($UserStaffCode);
		}
		
		die;
	}
	
	
	/**
	* getUsersList() method is used to get all users list by circle code
	* @param Null
	* @return Array 
	*/	
	public function getUsersListAction()
	{ 
		$this->checklogin();
		$users = new Application_Model_Users();
		
		$circleCode = $this->getRequest()->getParam('circleCode'); 
		if($circleCode !="")
		{
			$result = $users->getAllUserListByCircleCode($circleCode);
		    $user_list = '<option value="">Select User</option>'; 
			foreach($result as $key=>$val)
			{
			    $StaffName = ucwords(strtolower($val['StaffName'])) ; 
			    $user_list .='<option value='.$val['StaffCode'].'>'.$StaffName.'</option>';
			} 
		}
		echo $user_list;
		die;
	}
	
	
	/**
	* getUserDetails() method is used to get user details by staff code
	* @param Null
	* @return Array 
	*/	
	public function getUserDetailsAction()
	{ 
		$this->checklogin();
		$user = new Application_Model_Users();
		$keywords =  $this->_getParam('userDetails');  
		if($keywords != "") 
		{
			$result = $user->getUserStaffDetailsBySearchStaffCode($keywords);
			$StaffCode = $result['StaffCode'];
			$role = $result['role'];
			$userId = md5($StaffCode.'@@@@@'.$role);
			$constring = "'Are you sure want to remote wipe this user?'";
			$constringd = "'Are you sure want to deactivate this user?'";
			
			if($StaffCode!="")
			{ 
				$details = '<table class="table">
								<tbody>
								  <tr>
									<td colspan="2" style="border-top:none;"><strong>User Details:</strong></td>
								  </tr>
								   <tr>
									<td><strong>Staff Code:</strong></td>
									<td>'.$result['StaffCode'].'</td>
								  </tr>
								  <tr>
									<td><strong>Staff Name:</strong></td>
									<td>'.$result['StaffName'].'</td>
								  </tr>
								  <tr>
									<td><strong>Email:</strong></td>
									<td>'.$result['EMail'].'</td>
								  </tr>
								  <tr>
									<td><strong>Contact Number:</strong></td>
									<td>'.$result['MobileNo'].'</td>
								  </tr>';
				if(trim($result['StaffStatus']) == 'AC')
				{	
					$details .= 	  '<tr>
									<td colspan="2">
									<a href="/accountsetting/remote-wipe/staffCode/'.$userId.'/deactive/deactive" onclick="return confirm('.$constringd.')">
										<button class="btn btn-primary" id="btn">Deactivate</button>
										</a>
										<a href="/accountsetting/remote-wipe/staffCode/'.$userId.'" onclick="return confirm('.$constring.')">
										<button class="btn btn-primary" id="btn">Remote Wipe Out</button>
										</a>
									</td>
								  </tr>';
				}
				else
				{
					$details .= 	  '<tr>
									<td colspan="2">
									<a href="/accountsetting/remote-wipe/staffCode/'.$userId.'/deactive/activate" onclick="return confirm(\'Are you sure want to activate this user.\')">
										<button class="btn btn-primary" id="btn">Active</button>
										</a>
										
									</td>
								  </tr>';
				}
				$details .= '</tbody>
								  </table>';
			}
            else
            {
			$details = '<table class="table">
								<tbody>
								  <tr>
									<td colspan="2" style="border-top:none;"><strong>User Details:</strong></td>
								  </tr>
								   <tr>
									<td colspan="2">No user found</td>
								  </tr>
								  </tbody>
								  </table>';
            }			
		}
		echo $details;
		die;
	}

        
        
        
     
	
        
        
	/**
	* login() method is used to android user login
	* @param String
	* @return JSON 
	*/	
    public function getUserByRoleAction()
    {
	    // Get the Request parameters
		$params = $this->getRequest()->getParams();
		$db = $this->dbAdapter;
		// Genaral Information of the user login details

		$rolename	= isset($params['rolename'])?$params['rolename']:"";
		$error_code = 1;
		$succes = FALSE;
	
		try
		{
                        
			if(!$rolename){
				throw new Exception("Required parameter missing.");
			}		
                        
			$user = new Application_Model_Users();
			$userData = $user->getUserListByRole($rolename);
			$succes = TRUE;	

		}
		catch(Exception $e)
		{
			//Rollback transaction
			$db->rollBack();
			$error= $e->getMessage();
		}
		if($succes == TRUE )
		{   
                        $this->checklogin();
			echo json_encode(array("error_code"=>'0', 'response_string'=>'user_list.', 'user_data'=>$userData));
			exit;
		}
		else
		{
                    
			echo json_encode(array("error_code"=>$error_code, 'response_string'=>$error ));
			exit;
		}
    }
	
	
	/**
    * notify() method is used to get notification
    * @param Username and password
	* @return True 
    */	
    public function notifyAction()
    { 
		$this->checklogin();
		$layout = $this->_helper->layout();
        $layout->disableLayout('');
		$params = $this->getRequest()->getParams();
        $admin = new Application_Model_Admin($params);
		$this->view->params = $params;
		$auth = Zend_Auth::getInstance();
		$authStorage = $auth->getStorage();
		$StaffCode = $authStorage->read()->StaffCode;
		$role = $authStorage->read()->role;
		$ajax_role =  $this->_getParam('role'); 
		$staffUser =  $this->_getParam('staffUser');

		$notificationArr = $admin->getAllNotificationByRoleName($ajax_role,$staffUser);

		$data .= ' <table class="table">
		             <thead>
					  <tr>
						<th colspan="2">Alert / Notification</th>
						<th>Date</th>
					  </tr>
					</thead>
					<tbody>';
					$fsr = new Application_Model_Fsr();
					$noticolor = $fsr->getColorConfigaration();
					if(count($notificationArr)>0) {  foreach($notificationArr as $key=>$val) {
					
					$statustype = $val['statustype'];
					$color = $noticolor[$statustype]['color'];
					$basckgroundstyle = '';
					if($color)
					{
						$basckgroundstyle = 'style="background-color:'.$color.';"';
					}	
					$data .= '<tr>
						<td colspan="2" '.$basckgroundstyle.'>'.$val['message'].'</td>
						<td '.$basckgroundstyle.'>'.$val['noti_date'].'</td>
					  </tr>';
				  }	}	else { 
						  $data .='<tr>
						<td colspan="3">No activity</td>
					  </tr>';
			    }
				$data .= ' </tbody>
                                </table>';
			/* 	if(count($notificationArr)>10) { 
                 $data .= '<p><a href="/dashboard/view-history">More...</a></p>';	
                } */				 
                echo $data; 
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



