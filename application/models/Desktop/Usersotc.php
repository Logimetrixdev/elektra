<?php


class Application_Model_Usersotc extends Zend_Db_Table_Abstract
{
	var $userCondi = " and (role='user' OR role='cluster_incharge') and is_deleted='0' and access_status='0' and StaffStatus='AC' and CMSStaffStockCode!=''";
	public function insertSingleTableData($table='users', $data)
	{
		$db = new Zend_Db_table($table);
		return $db->insert($data);
	}

	/********* check unique id *************/
	public function checkUniqueField($table='users', $field = 'email',$email){
		$db = new Zend_Db_table($table);
		$select = $db->select()->where("$field = ?", $email);
		$eArr = new stdClass();
		$eArr = $db->fetchAll($select);
		if($eArr) {
		 $eArr->toArray();
		}
		else {
			$eArr = array();
		}		
		return count($eArr);

	}



/**

	* getUserLoginDetailByUserUniqueId() method is used to get all users list by email

	* @param String and Array

	* @return Array 

	*/	

                        public function getUserLoginDetailByStaffCode($LoginID)
                        { 
                        $db =  Zend_Db_Table::getDefaultAdapter();
                        $query = "SELECT * FROM user_login_detail WHERE LoginID=?";  
                        return $result = $db->fetchRow($query, array($LoginID));
                        }



	/**

	* updateUserLoginDetailsByLoginID() method is used to update app user details

	* @param Array

	* @return True 

	* add by praveen(03-10-2013)

	*/	

                        public function updateUserLoginDetailsByLoginID($StaffCode,$login_time)
                        { 
                        $db =  Zend_Db_Table::getDefaultAdapter();
                        $db->query("update user_login_detail set login_time=? where LoginID=? ", array($login_time,$StaffCode)); 
                        }	

	/**

	* getUserDataByUserUniqueId() method is used to get all users list by email

	* @param String and Array

	* @return Array 

	*/	






	/**

	* insertUserLoginDetailsByLoginID() method is used to insert user login details 

	* @param Array

	* @return True 

	*/	

                    public function insertUserLoginDetailsByStaffCode($user_login_data = array())
                    { 
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    return $result = $db->insert("user_login_detail",$user_login_data);
                    }   
        /**

	* updateUserLogoutDetailsByLoginID() method is used to update app user details

	* @param Array

	* @return True

	*/	
                    public function updateUserLogoutDetailsByLoginID($LoginID,$logout_time)
                    { 
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $db->query("update user_login_detail set logout_time=? where LoginID=? ", array($logout_time,$LoginID)); 
                    }
                    public function getAllUserList(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select LoginID,StaffName from local_user_mapping  where StaffStatus='AC' and role!='admin' order by LoginID asc";
                    return $result = $db->fetchAll($sql, array());
                    }

                   
                    public function gettaskDetails($instituteId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,institute_name,created from tbl_institute where id='".$instituteId."'";
                    return $result = $db->fetchRow($sql, array());
                    }
                    public function updateTaskData($data,$instituteId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_institute set institute_name ='".$data['institute_name']."'  where id='".$instituteId."'";
                    $db->query($sql); 
                    }
                    public function deleTaskById($instituteId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "delete from tbl_institute where id='".$instituteId."'";
                    $db->query($sql); 
                    }
                    public function getAllInstituteDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,institute_name,created from tbl_institute";
                    return $result = $db->fetchAll($sql, array());
                    }
                    
                    
                    public function getAllRankDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_rank";
                    return $result = $db->fetchAll($sql, array());
                    }
                    
                    public function getRankDetails($rankID){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,rank from tbl_rank where id='".$rankID."'";
                    return $result = $db->fetchRow($sql, array());
                    }


                    public function updateRankData($data,$RankdID){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_rank set rank ='".$data['rank']."'  where id='".$RankdID."'";
                    $db->query($sql); 
                    }

                     public function getAllUnitDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_unit";
                    return $result = $db->fetchAll($sql, array());
                    }

                     public function getUnitDetails($uId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_unit where id='".$uId."'";
                    return $result = $db->fetchRow($sql, array());
                    }


                     public function updateUnitData($data,$unitID){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_unit set unit ='".$data['unit']."'  where id='".$unitID."'";
                    $db->query($sql); 
                    }


                    public function getAllSubjectGroupDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_group";
                    return $result = $db->fetchAll($sql, array());
                    }


                    public function getGroupDetails($gId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_group where id='".$gId."'";
                    return $result = $db->fetchRow($sql, array());
                    }

                       public function updateGroupData($data,$grpId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                     $sql = "update tbl_group set sub_group ='".$data['sub_group']."'  where id='".$grpId."'";
                    
                    $db->query($sql); 
                    }


        
                    public function getAllcourseDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select co.* from tbl_courses as co";
                     return $result = $db->fetchAll($sql, array());
                    }
					
					//    code by puneet //
					
					public function getAllcourseDetails(){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select * from tbl_courses ";
							return $result = $db->fetchAll($sql, array());
                    }
					
					public function courseListById($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select id, course_name from tbl_courses where id='".$id."' ";
							return $result = $db->fetchRow($sql, array());
                    }
					
					public function getsubjectListBycourseId($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select id, subject_code, subject_name, grp_id, subject_marks, periods from tbl_subject where course_id='".$id."' ";							
							return $result = $db->fetchAll($sql, array());
                    }
					
					public function getcountId($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select count(id) as number from tbl_subject where course_id='".$id."' ";							
							return $result = $db->fetchAll($sql, array());
                    }
					
					public function subjectcountListById($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select count(course_id) as number from tbl_subject where course_id='".$id."' ";
						
							return $result = $db->fetchAll($sql, array());
                    }
					
					public function getgroupListBygrpId($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select id, sub_group from tbl_group where id='".$id."' ";
							return $result = $db->fetchRow($sql, array());
                    }
					
					public function getSubjectNameById($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select id, course_id, subject_name from tbl_subject where id='".$id."' ";
							return $result = $db->fetchRow($sql, array());
                    }
					
					public function topicsListById($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select *  from tbl_topic where subject_id='".$id."' ";
							return $result = $db->fetchAll($sql, array());
                    }
					
					
					public function deletedataBysubjectId($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "delete from tbl_topic where subject_id = '".$id."'";
						$db->query($sql); 
                    }
					
					public function gettopicListBysubjectId($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select count(subject_id) as number from tbl_topic where subject_id='".$id."' ";							
							return $result = $db->fetchRow($sql, array());
                    }
					
					public function getCoursenameforprint($id){
							$db =  Zend_Db_Table::getDefaultAdapter();
							$sql = "select id, course_name from tbl_courses where id='".$id."' ";							
							return $result = $db->fetchRow($sql, array());
                    }
//              code by puneet                     //
                    public function getAllcourseMapping(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select co.course_name,rn.rank from tbl_course_allocation_rank as courseal
                    left join tbl_courses as co on(courseal.course_id = co.id)
                    left join tbl_rank as rn on(courseal.rank_id = rn.id)";
                     return $result = $db->fetchAll($sql, array());
                    }

                    public function getAllCourseByRankID($rankId)
                    {
                       $db =  Zend_Db_Table::getDefaultAdapter();
                       $sql= "select cs.id,cs.course_name from tbl_course_allocation_rank as courseal
                        left join tbl_courses as cs on(courseal.course_id = cs.id)
                        where courseal.rank_id='".$rankId."'";
                        return $result = $db->fetchAll($sql, array());
                    }
                    


                    public function deleCourseById($courseId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "delete from tbl_courses where id='".$courseId."'";                            
                    $db->query($sql); 
                    }
                    public function getcourseDetails($id){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select * from tbl_courses where id = '".$id."'";
                    return $result = $db->fetchRow($sql, array());
                    }
                    public function updateCoursekData($data,$instituteId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                     $sql = "update tbl_courses set course_name ='".$data['course_name']."', duration ='".$data['duration']."' where id='".$instituteId."'";
                   $db->query($sql); 
                    }
                       
                       
                    public function getbatchDetails($batchId){
                            $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,batch_name,created,strength from tbl_batch where id='".$batchId."'";
                            return $result = $db->fetchRow($sql, array());
                    }
                    public function updatebatchData($data,$batchId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_batch set batch_name ='".$data['batch_name']."',strength ='".$data['strength']."'  where id='".$batchId."'";
                    $db->query($sql); 
                    }
                    public function delebatchById($batchId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "delete from tbl_batch where id='".$batchId."'";
                    $db->query($sql); 
                    }
                     public function getAllbatchDetail(){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "SELECT bt.id, cr.course_name, bt.batch_name
FROM tbl_batch_list AS bt
LEFT JOIN tbl_courses AS cr ON ( cr.id = bt.course_id ) " ;
						
						return $result = $db->fetchAll($sql, array());
                    }   
            
                   
                    public function getstudentDetails($studentregistrationId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id, student_name, course_id, rank, unit, personal_id, batch_id, education_detail, password from tbl_student where id='".$studentregistrationId."'";
                   
                    return $result = $db->fetchRow($sql, array());
                    }
                    public function updatestudentData($data,$studentregistrationId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_student set student_name ='".$data['student_name']."',course_id ='".$data['course_id']."',rank ='".$data['rank']."',batch_id ='".$data['batch_id']."',education_detail ='".$data['education_detail']."',unit ='".$data['unit']."',personal_id ='".$data['personal_id']."'where id='".$studentregistrationId."'";
                    $db->query($sql); 
                    }
                    public function delestudentById($studentregistrationId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "delete from tbl_student where id='".$studentregistrationId."'";
                    $db->query($sql); 
                    }
                     public function getAllstudentDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select ut.unit,rt.rank,st.id,st.student_name,st.rank_id,st.unit_id,st.personal_id,st.education_detail,cr.course_name,bt.batch_name
                        from tbl_student as st
                        left join tbl_courses as cr on (cr.id = st.course_id)
                        left join tbl_batch as bt on (bt.id = st.batch_id)
                        left join tbl_unit as ut on (ut.id = st.unit_id)
                        left join tbl_rank as rt on (rt.id = st.rank_id)";
                    return $result = $db->fetchAll($sql, array());
                    }
                       public function lastInsertId(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                   echo $sql = "select id from tbl_student where 1 order by id desc";
                  
                    return $result = $db->fetchAll($sql, array()); 
                    }
                      public function updateData($generated_id,$last_id){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "update tbl_student set generated_id ='".$generated_id."' where id='".$last_id."'";
                    $db->query($sql); 
                    }
                    
                    
                    
                    
                      public function getsubjectDetails($subjectId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id, subject_name, course_id from tbl_subject where id='".$subjectId."'";
                   
                    return $result = $db->fetchRow($sql, array());
                    }
                    
                    public function updatesubjectData($data,$subjectId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                  $sql = "update tbl_subject set subject_name ='".$data['subject_name']."', subject_code ='".$data['subject_code']."', course_id ='".$data['course_id']."' where id='".$subjectId."'";       
                   $db->query($sql); 
                    }
                    public function delesubjectById($subjectId){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "delete from tbl_subject where id='".$subjectId."'";
                    $db->query($sql); 
                    }
                     public function getAllsubjectDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select st.id,st.subject_name,cr.course_name,gr.sub_group,st.subject_marks
                        from tbl_subject as st
                        left join tbl_courses as cr on (cr.id = st.course_id)
                        left join tbl_group as gr on (gr.id = st.grp_id)";
                        
                    return $result = $db->fetchAll($sql, array());
                    }
                    
                         public function getAllsessionDetail(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,session_name,created from tbl_session";
               
                     return $result = $db->fetchAll($sql, array());
                    }
                       public function getsessionDetails($sessionId){
                            $db =  Zend_Db_Table::getDefaultAdapter();
                    $sql = "select id,session_name,created from tbl_session where id='".$sessionId."'";
                            return $result = $db->fetchRow($sql, array());
                    }
                    
                 //      public function updatesessionData($data,$sessionId){
              //      $db =  Zend_Db_Table::getDefaultAdapter();
               //     $sql = "update tbl_session set session_name ='".$data['session_name']."'  where id='".$sessionId."'";
               //     $db->query($sql); 
                //    }
                  //  public function delebatchById($batchId){
                //    $db =  Zend_Db_Table::getDefaultAdapter();
                  //  $sql = "delete from tbl_batch where id='".$batchId."'";
                //    $db->query($sql); 
                //    }
				
					public function getAllotyDetail(){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select id, year_from, year_to from tbl_officer_training_year";
						return $result = $db->fetchAll($sql, array());
					}
					
					public function getofiicerssessionDetails($sessionId){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select id, year_from, year_to from tbl_officer_training_year where id = '".$sessionId."'";
						return $result = $db->fetchRow($sql, array());
					}
					
					public function updateSessionData($data,$sessionId){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "update tbl_officer_training_year set year_from ='".$data['year_from']."', year_to ='".$data['year_to']."' where id='".$sessionId."'";
						$db->query($sql); 
					}
					
					public function getcourseDetail($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select id, course_name from tbl_courses where id = '".$id."'";
						return $result = $db->fetchRow($sql, array());
                    }
					public function groupList(){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select *  from tbl_group";
						return $result = $db->fetchAll($sql, array());
                    }
					
					public function deletedataBycourseId($id){

						$db =  Zend_Db_Table::getDefaultAdapter();
						foreach($id as $key=>$val){
						   $sql_topic = "delete from tbl_topic where subject_id = '".$val."'";
					
					       $db->query($sql_topic); 
						   $sql_sub = "delete from tbl_subject where id = '".$val."'";
						   $db->query($sql_sub); 
						
						}
											
                    }
					
					public function getsubjectgroupList(){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select *  from tbl_group";					
						return $result = $db->fetchAll($sql, array());
                    }
					
					public function clcdsumBysubjectId($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select sum(clcd) as sum_clcd   from tbl_topic where subject_id = '".$id."'";					
						return $result = $db->fetchRow($sql, array());
                    }
					
					public function demoSumBysubjectId($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select sum(demo) as sum_demo  from tbl_topic where subject_id = '".$id."'";					
						return $result = $db->fetchRow($sql, array());
                    }
					
					public function getoeSumBysubjectId($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select sum(oe) as sum_oe  from tbl_topic where subject_id = '".$id."'";					
						return $result = $db->fetchRow($sql, array());
                    }
					
					public function getvisitSumBysubjectId($id){
						$db =  Zend_Db_Table::getDefaultAdapter();
						$sql = "select sum(visit) as sum_visit  from tbl_topic where subject_id = '".$id."'";					
						return $result = $db->fetchRow($sql, array());
                    }
					
					
}

