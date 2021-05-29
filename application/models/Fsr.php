<?php

/***************************************************************
 * Appstudioz Technologies Pvt. Ltd.
 * File Name   : Fsr.php
 * File Description  : Fsr 
 * Created By : Praveen Kumar
 * Created Date: 21 October 2013
 ***************************************************************/
 
class Application_Model_Fsr extends Zend_Db_Table_Abstract
{
	public function checkUnique($username)
	{
		$select = $this->_dbTable->select()
		->from($this->_name,array('username'))
		->where('username=?',$username);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		if($result){
			return true;
		}
	return false;
	}

	
   /**
	* getAllRcaArrayList() method is used to get all rca array list
	* @param Array
	* @return Array 
	*/	
	public function getAllRcaArrayList()
	{ 
	    $rcaArray = array(
										'No_Cooling'=>array('Compressor not starting','Compressor tripping','Blower Fan not working'),
										'Less_Cooling'=>array('One AC is not working','Compressor tripping','Shelter Temp not maintaining'),
										'Door_open'=>array('One AC working, Second AC not working','Both AC not working'),
										'Noise'=>array('Running noise From Blower','Noise from body vibration'),
										'Water_Leakges'=>array('Water Throgh from Blower','Water Accumation in coil','Drain problem'),
										'Controller'=>array('Controller bye pass','Display faulty','Setting of paramenter'),
										'Filter'=>array('Filter to be change','filte Cleaning'),
										'Repeat_failure'=>array('After PM AC failed withing a month','Poor work quality','Poor technicians quality'),
										'NO_PM'=>array('Last three month NO PM','No quality PM'),
										'Compressor_not_working'=>array('Low pumping','Earthing','Winding burn','High /low Voltage'),
										'Excessive_Noise'=>array('Loose fitting components','Fan mounting loose'),
										'Gas_Charging'=>array('Gas leak in discharge line','Gas leak in Suction line','Condensor coil joints','Cooling coil Joints','Discharge Valve leak','Suction Valve leak','Multiple failure in sealed system','Choking'),
										'Condenser_Motor'=>array('Earhing','Winding burn'),
										'Blower_Motor'=>array('Earthing','Winding burn'),
										'Blower_Fan'=>array('Broken','alignment'),
										'Capacitor_run'=>array('Burst','weak'),
										'Capacitor_Start'=>array('Burst','weak'),
										'Controller'=>array('Display faulty','Internal card Faulty','Controller Changed'),
										'Conderser_Coil'=>array('Age','Due to envionment','Multiple leakges'),
										'Cooling_Coil'=>array('Age','Due to envionment','Multiple leakges'),
										'Minor_fault'=>array('Electrical wiring','Controler setting','Input power correction','room temp setting','water Leakage'),
										'Any_other_fault'=>array('Pl specify'),
										'Beyond_Repair'=>array('Aging','Multiple failure'),
										'Site_issue'=>array('No access','Equipment in front of AC','No air circlation for condenser','Over loaded','Shelter leakage','Controller Changed'),
										'Frosting_in_Cooling_coil_and_connecting_copper_tubing'=>array('Blower not working','Filter Choke','out side Air blowing over the Cooilng cooling'),
										'Unit_Operates_continuously_or_Cut_of_time_is_too_long'=>array('Shortage of Refrigerant','Sensor position not proper','High Heat Load','Grill temp high'),
										'Rejection_Reason'=>array('No access','Site not under SME','Beyond Repair','Duplicate TT','Theft of parts'),
										'Duplicate_TT'=>array('Already TT logged'),
										'Not_Under_SME_bucket'=>array('Not in list of Sites given by Indus'),
										'Site_Access'=>array('No site access-Intimated Indus'),
										'Site_Deficiency'=>array('No space for working-Safety Issue','Very High over loaded',
										'Site_Beyond_repair-Certificate submitted to Indus'),
										'In_complete_Work'=>array('Quality work not done','Site not visited-False Closure of TT','In complete work','One AC is not working','Controller is byepass','Re-occurance of same fault')
							    );	
								
		return $rcaArray;						
    }		
	
		
	/**
	* getAllPMBDArrayList() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getAllPMBDArrayList()
	{  
        $pmbdArray = array(
										   'No_Output_from_PIU'=>array('Loose Connection','MCB Faulty due to component quality Issue','MCB burnt due to loose connection','MCB burnt due to overload','MCB bypassed','MCB Faulty due to aging','MCB Corrosion due to dust & humudity','Contactor  Faulty due to component quality Issue','Contactor burnt due to loose connection','Contactor burnt due to overload','Contactor bypassed','Contactor faulty due to aging','Contactor Corrosion due to dust & humudity','Contactor coil burnt due to component quality issue','Contactor coil burnt due to high voltage','LCU Burnt due to quality issue','LCU burnt due to overload','SCR Fail','Paramerter Setting Issue','SVR Card Malfunction','Loose Connection in SVR Card','SVR Card Bypassed','LCU Bypassed','No Proper Earthing','Loose Connection','Contactor  Faulty due to component quality Issue','Contactor burnt due to loose connection','Contactor burnt due to overload','Contactor bypassed','Contactor faulty due to aging','Contactor Corrosion due to dust & humudity','Contactor coil burnt due to component quality issue','Contactor coil burnt due to high voltage','Loose Connection','MCB Faulty due to component quality Issue','MCB burnt due to loose connection','MCB burnt due to overload','MCB bypassed','MCB Faulty due to aging','MCB Corrosion due to dust & humudity','Controller PCB Malfunction','Parameter Setting Issue','Loose Connection','No Proper Earthing','Controller Burnt due to PIU Issue','Controller Burnt due to high Voltage'),
										   
										   'Automation_Failure'=>array('Controller PCB Malfunction','Parameter Setting Issue','Loose Connection','No Proper Earthing','Controller Burnt due to PIU Issue','Controller Burnt due to high Voltage','Loose Connection','Contactor  Faulty due to component quality Issue','Contactor burnt due to loose connection','Contactor burnt due to overload','Contactor bypassed','Contactor faulty due to aging','Contactor Corrosion due to dust & humudity','Contactor coil burnt due to component quality issue','Contactor coil burnt due to high voltage','Automation Cable missing','Automation Cable puctured and no continuity','LCU Burnt due to quality issue','LCU burnt due to overload','SCR Fail','Paramerter Setting Issue','SVR Card Malfunction','Loose Connection in SVR Card','SVR Card Bypassed','LCU Bypassed','No Proper Earthing','BC PCB malfunction','BC faulty due to poor repair quality at TRC','BC faulty due to component issue','BC faulty due to dust,humudity and temperature','Battery Failure due to aging > 2.5 Years','Battery Failure due to non-charging for long period','Battery Failure due to battery charger Issue','Battery missing from PIU','Relay Box fauly due to component issue','Relay Box fauly  - sensing missing','BC faulty due to dust,humudity and temperature'),
										   
										   
										   'Alam_Issue'=>array('Fire & Smoke Sensor component issue','Alarm Card faulty','Controller PCB Faulty','Sensor cable faulty / missing','Fire & Smoke Sensor component issue','Alarm Card faulty','Controller PCB Faulty','Sensor cable faulty / missing','Alarm card faulty due to comonent issue','Alarm card malfunction','Controller PCB Malfunction','Parameter Setting Issue','Loose Connection','No Proper Earthing','Controller Burnt due to PIU Issue','Controller Burnt due to high Voltage'),
										  

										  'General'=>array('Cable and Harness Mutilated due to aging','Cable and Harness Mutilated due to temperature','Cable and Harness underrated','Cable and harness functure','SVR Card Issue','LCU Tapping selectiuon Issue','SCR wrong selection/fail','Parameter Setting Issue','AC not working / Cooling not proper','PIU TOP ventilation grill blocked','LCU issue','Overload issue','Fan malfunction','Fan failure due to aging','Supply not extended','LCD PCB component Issue','LCD wrong measurement','Controller Faulty','cabinet dent / broken','Parameter setting Issue','PM defficiency observed - Poor quality PM','Overload beyond LCU Capacity'),
										 

										 'Wrong_assign'=>array('Not in list of Sites given by Indus'),


										 'Site_Access_Issue'=>array('No site access-Intimated Indus'),


										 'Site_Deficiency'=>array('No space for working-Safety Issue','Very High over loaded','Site Beyond repair-Certificate submitted to Indus'),


										 'Tampering_Cases'=>array('Tampering Cases - Issue discussed and acknowledged by Indus OM Head')
									    );
										   
		return $pmbdArray;
		
	}
	
	
	/**
	* getAllACInvestigationArray() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getAllACInvestigationArrayList()
	{  
        $ACInvestigationArray = array('Select','False Complaints without site visit by OME');
										   
		return $ACInvestigationArray;
	}
	
	
	/**
	* getAllPIUInvestigationArray() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getAllPIUInvestigationArrayList()
	{  
        $PIUInvestigationArray = array(
		                                                    'Select',
		                                                    'False Complaints without site visit by OME',
															'Complaints not related to PIU, DG or Automation Cables etc',
															'Automation Failure due to External issue, EB,DG battery, Rodent cased, DG self or solenoid, Tempering ,etc',
															'Education Issue, Means repeated failure due to not understanding of new Product',
															'Issue due PIU Failure, means Card failure(CPU,I/O,MBO,AVR,etc)',
															'Issue due PIU Failure, Control & Switchgears (MCCB, Contactor, etc)',
															'Issue due to LCU Fail, Lcu card ,SCR, Calibration ,etc',
															'Issue due to LCU Fail - Overloading, Bypassing',
															'Automation ,Wiring checked, Calibration done'
															);		   
		return $PIUInvestigationArray;
	}
	
	
	/**
	* getAllSmeTechniciansArrayList() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getAllSmeTechniciansArrayList()
	{  
        $ACSmeTechniciansArray = array('Select','1','2','3','4','5','6','7','8','9','10','11','12');								   
		return $ACSmeTechniciansArray;
	}
	
	
	/**
	* getRemarkList() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getRemarkList()
	{  
        $ACRemarkArray = array('Select','test0','test1','test2','M.Raja3','M.Raja4','M.Raja5','M.Raja6','M.Raja7','M.Raja8','M.Raja9','M.Raja10','M.Raja11','M.Raja12','M.Raja13','M.Raja14','M.Raja15');								   
		return $ACRemarkArray;
	}
	
	
	/**
	* getDefaultList() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getDefaultList()
	{  
        $ACDefaultArray = array(
		                                            'AC'=>array(
													                       'Spinner'=>array(
																		                                '1'=>array(
																										                    'Textheading1'=>'Textheading1',
																										                    'Array1'=>array('1','2','3','4','5','6','7','8','9','10')
																										                  ),
																										'2'=>array(
																										                   'Textheading2'=>'Textheading2',
																										                    'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																														  )
																		                                ),
																			'Radio'=>array(
																			                            '1'=>array(
																									                       'Textheading1'=>'Textheading1',
																													       'DefaultValue1'=>'Yes',
																													       'optionValue1'=>'Yes',
																													       'optionValue2'=>'No'
																									                    ),
																										'2'=>array(
																										                    'Textheading2'=>'Textheading2',
																														    'DefaultValue2'=>'Yes',
																														    'optionValue1'=>'Yes',
																														    'optionValue2'=>'No'
																										                  )				
																			                          ),
                                                                           'TapText'=>array(
																		                                '1'=>array(
																										                    'Textheading1'=>'Textheading1',
																										                    'Array1'=>array('1','2','3','4','5','6','7','8','9','10')
																										                  ),
																										'2'=>array(
																										                   'Textheading2'=>'Textheading2',
																										                    'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																														   )
																		                                )																						  
													                      ),
													'PIU'=>array(
													                        'Checkbox'=>array(
																		                                    '1'=>array(
                                                                                                                                'Textheading1'=>'Textheading1',
																										                        'DefaultValue1'=>'1'
																											                  ),
																										    '2'=>array(
																											                    'Textheading2'=>'Textheading2',
																										                        'DefaultValue2'=>'1'
																											                   )
																		                                ),
																			'Radio'=>array(
																			                            '1'=>array(
																									                       'Textheading1'=>'Textheading1',
																													       'DefaultValue1'=>'Yes',
																													       'optionValue1'=>'Yes',
																													       'optionValue2'=>'No'
																									                    ),
																										'2'=>array(
																										                   'Textheading2'=>'Textheading2',
																														   'DefaultValue2'=>'Yes',
																														   'optionValue1'=>'Yes',
																														   'optionValue2'=>'No'
																										                  )				
																			                          ),
                                                                            'Spinner'=>array(
																		                                '1'=>array(
																										                    'Textheading1'=>'Textheading1',
																										                    'Array1'=>array('1','2','3','4','5','6','7','8','9','10')
																										                  ),
																										'2'=>array(
																										                   'Textheading2'=>'Textheading2',
																										                    'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																														  )
																		                                )
													                       )					  
		                                            );								   
		return $ACDefaultArray;
	}
	
	
	/**
	* getAcHideList() method is used to get all AC Form Hide Values
	* @param Array
	* @return Array 
	*/	
	public function getAcHideList()
	{  
        $AcHideArray = array(
											'Spinner'=>array(
																	'Textheading1'=>'Textheading1',
																	'Array1'=>array('1','2','3','4','5','6','7','8','9','10'),
																	'Textheading2'=>'Textheading2',
																	'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																	),
											'Radio'=>array(

																	'Textheading1'=>'Textheading1',
																    'DefaultValue1'=>'Yes',
																    'optionValue1'=>'Yes',
																    'optionValue2'=>'No',
																    'Textheading2'=>'Textheading2',
																    'DefaultValue2'=>'Yes',
																    'optionValue21'=>'Yes',
																    'optionValue22'=>'No'	
																	),
											'TapText'=>array(
																	'Textheading1'=>'Textheading1',
																	'Array1'=>array('1','2','3','4','5','6','7','8','9','10'),
																	'Textheading2'=>'Textheading2',
																	'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																	)																						  
		                                            );								   
		return $AcHideArray;
	}
	
	
	/**
	* getPiuHideList() method is used to get all PIU Form Hide Values
	* @param Array
	* @return Array 
	*/	
	public function getPiuHideList()
	{  
        $PiuHideArray = array(
											'Checkbox'=>array(
																	'Textheading1'=>'Textheading1',
												                    'DefaultValue1'=>'1',
																	'Textheading2'=>'Textheading2',
																	'DefaultValue2'=>'1'
																	),
											'Radio'=>array(
																	'Textheading1'=>'Textheading1',
																    'DefaultValue1'=>'Yes',
																    'optionValue1'=>'Yes',
																    'optionValue2'=>'No',
																    'Textheading2'=>'Textheading2',
																    'DefaultValue2'=>'Yes',
																    'optionValue21'=>'Yes',
																    'optionValue22'=>'No'	
																	),
											'Spinner'=>array(
																	'Textheading1'=>'Textheading1',
																	'Array1'=>array('1','2','3','4','5','6','7','8','9','10'),
																	'Textheading2'=>'Textheading2',
																	'Array2'=>array('1','2','3','4','5','6','7','8','9','10')
																	)																						  
		                                            );								   
		return $PiuHideArray;
	}
	
	
	/**
	* getRowUpdateList() method is used to get all PMBD array list
	* @param Array
	* @return Array 
	*/	
	public function getRowUpdateList()
	{  
        $getRowUpdateArray = array('rowupdate'=>'0');							   
		return $getRowUpdateArray;
	}
	
	
	/**
	* getHelpContactList() method is used to get all help contact no
	* @param Array
	* @return Array 
	*/	
	public function getHelpContactList()
	{  
        $getHelpContactArray = array('GEMC_Toll_free'=>'4754378348','HR_Contact'=>'4754378348','Emergency_Contact'=>'4754378348','Escalation_L1'=>'4754378348','Escalation_L2'=>'4754378348', 'createjoboption'=>'yes'); // yes, no								   
		return $getHelpContactArray;
	}
	
	
	/**
	* getImageNameList() method is used to get all help contact no
	* @param Array
	* @return Array 
	*/	
	public function getImageNameList()
	{  
        $ImageNameArray = array('Raman','Veer','Image3','Image4','Image5');									   
		return $ImageNameArray;
	}
	
	
	/**
	* getPendingListValuesList() method is used to get all help contact no
	* @param Array
	* @return Array 
	*/	
	public function getPendingListValuesList()
	{  
        $getPendingListValuesArr = array('select','Site inaccessible','Non ACME issue','Customer support required','Spare not available','Fresh Spare issue','Technical assistance required','Product training required','Gas required','EB power issue','OME technician not available','LCU PCB Diode Changed');									   
		return $getPendingListValuesArr;
	}
	
	
	/**
	* getCloseJobWithOutFsrList() method is used to get all help contact no
	* @param Array
	* @return Array 
	*/	
	public function getCloseJobWithOutFsrList()
	{  
        $getPendingListValuesArr = array('select','Tower find','Spare part  available','False Complaint','Non-ACME issue','LCU PCB Diode Changed');									   
		return $getPendingListValuesArr;
	}	
	
	/**
	* getColorConfigaration() method is used to get all color for notificatoins
	* @return Array 
	*/	
	
	public function getColorConfigaration()
	{
		$array = array(
			'fsr_filled'=> array('status'=>'Critical','color'=>'#FF0000'),
			'battery_low'=> array('status'=>'Critical','color'=>'#FF0000'),
			
			'left_site'=> array('status'=>'Major','color'=>'#FFA500'),
			'new_job'=> array('status'=>'Major','color'=>'#FFA500'),
			
			'toward_site'=> array('status'=>'Minor','color'=>'#FFFF00'),
			'at_site'=> array('status'=>'Minor','color'=>'#FFFF00'),
			'verify_site'=> array('status'=>'Minor','color'=>'#FFFF00'),
			
			'battery_mediaum'=> array('status'=>'Warning','color'=>'#00FFFF'),
			
			'battery_full'=> array('status'=>'Status','color'=>'#008000'),
			'job_scheduled'=> array('status'=>'Status','color'=>'#008000'),
			'job_accept'=> array('status'=>'Status','color'=>'#008000'),
			
			'login'=> array('status'=>'Ignore','color'=>''),
			'day_start'=> array('status'=>'Ignore','color'=>''),
			'day_close'=> array('status'=>'Ignore','color'=>''),
			'gps_on'=> array('status'=>'Ignore','color'=>''),
		);
		return $array;
	}
	
	public function getSmtpMailServerSettings()
	{
		$configArr = array(
							'config' => array('auth' => 'login',
									'username' => 'noreply.workflow@acme.co.in',
									'password' => 'Veer@1234' ,'port' => 25),
							'server' => 'smtp.acme.co.in',
							'fromemailconf' => array(
													'fromname' => 'ESR',
													'fromemail' => 'noreply.workflow@acme.co.in'		
												)		
									
						);
		return $configArr;				
	}
        
        
        // Code added by abhishek mishra
        
        /**
	* getDistance() method is used to change the distance in jobs
	* @param Array
	* @return Array 
	*/	
	public function getDistance()
	{  
        $getDistanceValuesArr = array(0.01);									   
		return $getDistanceValuesArr;
	}
        
        
        // COde added by
        /**
	* getCPUCardsList() method is used to get all CPU cad List
	* @param Array
	* @return Array 
	*/	
	public function getCPUCardsList()
	{  
        $CpuListArray = array(
	     'ASA10N0185-ASSY PCB (PB002R4-0408) MAIN CPU CARD FOR INDUS',
            'ASA10N0078-ASSY PCB ( PB004R1-0308) MAIN CARD',
            'ASA10N0001-ASSY PCB (EPCB-SPP-T-SD) FOR MICRO PIU',
            'ASA10N0002-ASSY PCB (EPCB-SPP-T-SD)PIU',
            'ASA10N0024-ASSY PCB MAIN FOR HUTCH',
            'ASA10N0025-ASSY PCB MAIN FOR UNIK',
            'ASA10N0060-ASSY PCB SPC NEW SPC T 03',
            'ASA10N0235-ASSY PCB CONTROL CARD (CPU) - IT FOR MULTIPHASE',
            'Other-if found other please mention in remarks');									   
		return $CpuListArray;
	}
	
}
