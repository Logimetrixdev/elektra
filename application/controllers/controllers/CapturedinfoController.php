<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : FqbController.php
 * File Description  : Fqb Controller
 * Created By : Abhishek Kumar Mishra<piyush@logimetrix.in>
 * Created Date: 24 July 2015
 ***************************************************************/

class CapturedinfoController extends Zend_Controller_Action{
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
                $perPage = $paginator->setItemCountPerPage(5); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
            }

            public function viewContactAction(){
                $this->checklogin();
                $params = $this->view->params = $this->getRequest()->getParams();  
                //print_r($params);  
                $auth = Zend_Auth::getInstance();
                $authStorage = $auth->getStorage();
                $WebLoginID = $authStorage->read()->WebLoginID;
                $users = new Application_Model_Users();
                $this->view->totalnum = $params['page'];
                $this->view->getContactInformation = $reportscome= $users->getContactAllInformation($params['name'], $params['mobile'], $params['user'], $params['address'],  $params['polling_both'],$params['under']);              
                $this->view->getAllUserInfo  = $getAllUserInfo= $users->getAllUserInfo();
                $this->view->getUnderUserInfo = $getUnderUserInfo = $users->getUnderUserInfo();

                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($reportscome);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
                
                if($params['type'] == 'generate_contact')
                {
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
                 fputcsv($file, array('Sr. No.', 'Name','Identity' , 'Mobile No', 'Whatsapp No','Profession', 'Cast', 'Address','Polling Both Id','Party Affiliation','Party Name','Availability', 'Date'));

					// Sample data. This can be fetched from mysql too


					// output each row of the data
                 $content = array();
                 $i = 2;	
                 foreach ($reportscome as $rs) {
                     $row = array();
                     $row[] = stripslashes($i-1);
                     $row[] = stripslashes($rs["name"]);
                     $row[] = stripslashes($rs["under"]);

                     $row[] = stripslashes($rs["mobile"]);
                     $row[] = stripslashes($rs["whatsapp"]);
                     $row[] = stripslashes($rs["profession"]);
                     $row[] = stripslashes($rs["cast"]);
                     $row[] = stripslashes($rs['address']);
                     $row[] = stripslashes($rs["polling_both"]);
                     $row[] = stripslashes($rs["party_affilitation"]);
                     $row[] = stripslashes($rs["party_name"]);
                     $row[] = stripslashes($rs["availiability"]);
                     $row[] = stripslashes($rs["offline_sink_datetime"]);
                     $content[] = $row;

                     $i++;}



                     foreach ($content as $res)
                     {
                      fputcsv($file, $res);
                  }

                  exit;

              }
          }

          public function viewUnderAction(){
            $this->checklogin();
            $params = $this->view->params = $this->getRequest()->getParams('data'); 
            $layout = $this->_helper->layout();
            $layout->disableLayout('');
            $auth = Zend_Auth::getInstance();
            $authStorage = $auth->getStorage();
            $WebLoginID = $authStorage->read()->WebLoginID;
            $users = new Application_Model_Users();
            $this->view->getUnderData  = $getUnderData= $users->getUnderData($params['data']); 

            // echo '<pre>';
            // print_r($getUnderData);exit;    
        }

        public function viewPlaceUnderAction(){
            $this->checklogin();
            $params = $this->view->params = $this->getRequest()->getParams('data'); 
            $layout = $this->_helper->layout();
            $layout->disableLayout('');
            $auth = Zend_Auth::getInstance();
            $authStorage = $auth->getStorage();
            $WebLoginID = $authStorage->read()->WebLoginID;
            $users = new Application_Model_Users();
            $this->view->getUnderData  = $getUnderData= $users->getPlaceUnderData($params['data']); 

                // echo '<pre>';
                // print_r($getUnderData);exit;    
        }

        public function viewPlaceAction(){
            $this->checklogin();
            $params = $this->view->params = $this->getRequest()->getParams();    
            $auth = Zend_Auth::getInstance();
            $authStorage = $auth->getStorage();
            $WebLoginID = $authStorage->read()->WebLoginID;
            $users = new Application_Model_Users();
            $this->view->getPlacedInformation  = $getPlacedInformation= $users->getAllPlacedInformation($params['name'], $params['mobile'], $params['user'],$params['under']);              
            $this->view->getAllUserInfo  = $getAllUserInfo= $users->getAllUserInfo();
            $this->view->getUnderUserInfo = $getUnderUserInfo = $users->getPlaceUnderUserInfo();
                // echo '<pre>';
                // print_r($getUnderUserInfo);exit;


            $page=$this->_getParam('page',1);
            $paginator = Zend_Paginator::factory($getPlacedInformation);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(15); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
                if($params['type'] == 'generate_place')
                {
					// echo '<pre />';
					//  print_r($record);
					//  exit();
					// output headers so that the file is downloaded rather than displayed
                 header('Content-type: text/csv');
                 header('Content-Disposition: attachment; filename="place_information_detail.csv"');

					// do not cache the file
                 header('Pragma: no-cache');
                 header('Expires: 0');

					// create a file pointer connected to the output stream
                 $file = fopen('php://output', 'w');

					// send the column headers
                 fputcsv($file, array('Sr. No.', 'Place Description', 'Concerned Person', 'Identity', 'Designation','Contact', 'Address', 'Date'));

					// Sample data. This can be fetched from mysql too


					// output each row of the data
                 $content = array();
                 $i = 2;	
                 foreach ($getPlacedInformation as $rs) {
                     $row = array();
                     $row[] = stripslashes($i-1);
                     $row[] = stripslashes($rs["place_description"]);
                     $row[] = stripslashes($rs["consenred_person"]);
                     $row[] = stripslashes($rs["under"]);

                     $row[] = stripslashes($rs["designation"]);
                     $row[] = stripslashes($rs["contact"]);
                     $row[] = stripslashes($rs["address"]);
                     $row[] = stripslashes($rs['offline_sink_datetime']);
                     $content[] = $row;

                     $i++;}



                     foreach ($content as $res)
                     {
                      fputcsv($file, $res);
                  }

                  exit;

              }



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

        }



        public function locationAction(){
            $this->checklogin();
            $layout = $this->_helper->layout();
            $layout->disableLayout('');
            $Id = $this->getRequest()->getParam('data');          
            $user= new Application_Model_Users();    
            $this->view->viewLocation = $viewLocation = $user->viewLocation($Id);
        }

        public function locationPlaceAction(){
            $this->checklogin();
            $layout = $this->_helper->layout();
            $layout->disableLayout('');
            $Id = $this->getRequest()->getParam('data');          
            $user= new Application_Model_Users();    
            $this->view->viewLocation = $viewLocation = $user->viewLocationPlace($Id);
        }



        public function editFielduserAction(){
            $this->checklogin();
            $db = $this->dbAdapter;
            $auth = Zend_Auth::getInstance();
            $authStorage = $auth->getStorage(); 
            $params = $this->view->params = $this->getRequest()->getParams();
            $user_id=$params['datacode'];
            $user= new Application_Model_Users();



            if($this->getRequest()->isPost()){


               $UsersID=$user->getuser_id($params['user_id']);

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


                    public function checklogin(){		
                        $auth = Zend_Auth::getInstance();	
                        $errorMessage = ""; 
                        /*************** check user identity ************/
                        if(!$auth->hasIdentity()){
                            $this->_redirect('admin/index');  
                        }		
                    }




                }
