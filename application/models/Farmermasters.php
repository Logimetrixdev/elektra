<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Farmermasters.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date: 25 july 2015

 ***************************************************************/

 

class Application_Model_Farmermasters extends Zend_Db_Table_Abstract{

        
    public function getAllunits(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_units where deleted=0 order by unit asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getLastCrops(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_lastcrop where deleted=0 order by lastcrop asc";  
            return $result = $db->fetchAll($query);
        }
        public function getlastcropDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_lastcrop where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
         public function getRabiCrop(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_crop_rabi where deleted=0";  
            return $result = $db->fetchAll($query);
        }
        
        public function getlastRabicropDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_crop_rabi where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
          public function getKharifCrop(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM last_crop_kharif  where deleted=0";  
            return $result = $db->fetchAll($query);
        }
        
        
         public function getlastKharifcropDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM last_crop_kharif where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        public function getSoilType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_soiltype where deleted=0 order by SoilType asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getSoilTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_soiltype where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
         public function getWayOfIrrigation(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_wayofirrigation where deleted=0 order by WayofIrrigation asc";  
            return $result = $db->fetchAll($query);
        }
        
        public function getIrrigationDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_wayofirrigation where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        
        public function getWayOfShowing(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_wayofshowing where deleted=0 order by WayOfShowing asc";  
            return $result = $db->fetchAll($query);
        }
        
        
        public function getSowingDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_wayofshowing where md5(id)='".$id."'";  
//            echo $query;
//            exit();
            return $result = $db->fetchRow($query);
        }
        
        public function getSourceofWater(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_sourceofwater where deleted=0 order by SourceOfWater asc";  
            return $result = $db->fetchAll($query);
        }
        
         public function getWaterSourceDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_sourceofwater where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        public function getFarmerType(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmertype where deleted=0 order by farmertype asc";  
            return $result = $db->fetchAll($query);
        }
        
        
        public function getFarmerTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmertype where md5(id)='".$id."'";           
            return $result = $db->fetchRow($query);
        }
        
        public function getFarmerPriority(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmer_priority where deleted=0 order by priority asc";  
            return $result = $db->fetchAll($query);
        }
        
        public function getFarmerPriorityDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmer_priority where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        public function getAllReferenceType(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmer_reference where deleted=0 order by referencetype desc";  
            return $result = $db->fetchAll($query);
        }

         public function getReferenceTypeDetails($id){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_farmer_reference where md5(id)='".$id."'";  
            return $result = $db->fetchRow($query);
        }
        
        
        public  function getFarmerCount($LoginID){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT count(FarmerCode) as totalfarmer FROM logi_my_farmers where AllotedFieldEngg='".$LoginID."'";  
            return $result = $db->fetchRow($query);
        }
        
        
        public function UpdateFarmerCode($table='logi_my_farmers', $data, $whereField, $id){ 
		$db = new Zend_Db_table($table);
		$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
		$result = $db->update($data, $where);
	}
        
        /**

	* getAllFarmerByStaffCode() method is used to get all farmers under FE
	* @param Integer
	* @return Array 
	*/	

	public function getAllFarmerByStaffCode($userID)
	{ 
            $db =  Zend_Db_Table::getDefaultAdapter();
	    
            if($userID){
                $cond = " and md5(lf.AllotedFieldEngg)='".$userID."'";
            }
            $query = "SELECT lf.*,
lft.farmertype,lfp.priority,lfs.state,lfd.district_name,lfth.tehsil_name,lfv.village_name FROM logi_my_farmers as lf
left join logi_farmertype as lft on (lf.farmerType = lft.id)
left join logi_farmer_priority as lfp on (lf.farmerPriority = lfp.id)
left join logi_circle as lfs on (lf.stateId = lfs.id)
left join logi_district as lfd on (lf.districtId = lfd.id)
left join logi_tehsil as lfth on (lf.tehsilID = lfth.id)
left join logi_villages as lfv on (lf.villageId = lfv.id)
left join logi_farmer_reference as lfr on (lf.refType = lfr.id) WHERE flag=0 $cond order by lf.id desc";
          return $result = $db->fetchAll($query);  

	}
        
        
       /** 
        * getFarmerDetailsByFarmerCode() method 
	* @param Integer
	* @return Array 
	*/	

	public function getFarmerDetailsByFarmerCode($farmerCode)
	{ 
            $db =  Zend_Db_Table::getDefaultAdapter();
	    
            $query = "SELECT id,FarmerCode,FarmerName from logi_my_farmers WHERE FarmerCode='".$farmerCode."'";
            return $result = $db->fetchRow($query);  

	}
        
        /**

	* getAllFarmerLandDetails() method is used to get farmer land Details
	* @param Integer
	* @return Array 
	*/	

	public function getAllFarmerLandDetails($farmerCode)
	{ 
            $db =  Zend_Db_Table::getDefaultAdapter();
	      $query = "SELECT frm.id,frm.KhasraNumber,frm.LastCrop,frm.TotalAcrage,frm.latitude,frm.longitude,lstrabi.RabiCrop,lstkrp.KharifCrop,
                 solT.SoilType  FROM  logi_farmer_lands as frm
left join last_crop_kharif as lstkrp on (frm.LastKharif = lstkrp.id)
left join logi_crop_rabi as lstrabi on (frm.LastRabi = lstrabi.id)
left join logi_soiltype as solT on (frm.SoilType = solT.id)
WHERE md5(frm.FarmerID)='".$farmerCode."'";  
         
	    return $result = $db->fetchAll($query);

	}
        
        
        
         public function updateMasterData($table, $data, $whereField, $id){ 
		$db = new Zend_Db_table($table);
		$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
		$result = $db->update($data, $where);
	}
        
        
        public function GetFarmerCode($codingData){
            $state = $this->getStateDetails($codingData['stateId']);
            $district = $this->getDistrictDetails($codingData['districtId']);
            $tehsil = $this->getTehsilDetails($codingData['tehsilId']);
            $village = $this->getVillageDetails($codingData['villageId']);
            $farmerType = $this->getFarmerTypeInDetails($codingData['farmerType']);
            $farmerID = str_pad($codingData['farmerID'], 4, '0', STR_PAD_LEFT);
            if(isset($codingData['suffletBGP']) && $codingData['suffletBGP']==1){
                $farmerUniqueCode = 'S'.$state.''.$district.''.$tehsil.''.$village.''.$farmerType.''.$farmerID;
            }elseif(isset($codingData['suffletBGP']) && $codingData['suffletBGP']==0){
                $farmerUniqueCode = $state.''.$district.''.$tehsil.''.$village.''.$farmerType.''.$farmerID;
            }else{
                $farmerUniqueCode = '';
            }
            return $farmerUniqueCode;
        }
        
        
        public  function getStateDetails($stateId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT state FROM logi_circle where  id='".$stateId."'";  
            $result = $db->fetchRow($query, array());
            $stateList = strtoupper(substr($result['state'],0,2));
            return $stateList;
          }
         
         public  function getDistrictDetails($disId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT district_name FROM logi_district where  id='".$disId."'";  
            $result = $db->fetchRow($query, array());
            $districtList = strtoupper(substr($result['district_name'],0,2));
            return $districtList;
         }
         
           public  function getTehsilDetails($tehCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT tehsil_name FROM logi_tehsil where  id='".$tehCode."'";  
            $result = $db->fetchRow($query, array());
            $tehsilList = strtoupper(substr($result['tehsil_name'],0,2));
            return $tehsilList;
         }
           public  function getVillageDetails($villCode){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT village_name FROM logi_villages where  id='".$villCode."'";  
            $result = $db->fetchRow($query, array());
            $villageList = strtoupper(substr($result['village_name'],0,2));
            return $villageList;
         }
         
          public  function getFarmerTypeInDetails($farmerType){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT farmertype FROM logi_farmertype where  id='".$farmerType."'";  
            $result = $db->fetchRow($query, array());
            $FarmerTypeList = strtoupper(substr($result['farmertype'],0,1));
            return $FarmerTypeList;
         }
         
         
         
          public function updateSingleTableData($table, $data, $whereField, $id){ 
			$db = new Zend_Db_table($table);
			$where = $db->getAdapter()->quoteInto(" $whereField = ?", array($id) );		
			$result = $db->update($data, $where);
			}
	
	
			 public function updateOrderId($data,$id){ 
				$db =  Zend_Db_Table::getDefaultAdapter();
				$query = "update logi_farmer_seed_orders set orderId='".$data['orderId']."' where id='".$id."'";
				$db->query($query);
				
			}
			
			public function updateChemicalOrderId($data,$id){ 
				$db =  Zend_Db_Table::getDefaultAdapter();
				$query = "update logi_chemical_orders set orderId='".$data['orderId']."' where id='".$id."'";
				$db->query($query);
				
			}
        

        
        public function getFarmerWayOfSowing($farmerID){
             $db =  Zend_Db_Table::getDefaultAdapter();
              $query = "SELECT lfs.id,lfs.FarmerID,lfs.sourceofwater,lwi.SourceOfWater,lfs.NoOfHarrrow,lfs.NoOfRotavator,lfs.NoOfThresher,lfs.NoOfTractor FROM logi_farmer_showing as lfs
left join logi_sourceofwater as lwi on (lfs.sourceofwater = lwi.id)
 where  md5(lfs.FarmerID)='".$farmerID."'";  
               return $result = $db->fetchRow($query);  
        }
        
        public function  totalRegisteredfarmer(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) as totalregisterfarmer from logi_my_farmers where SuffletBGP=1";
            return $result = $db->fetchRow($query);
        }
        
        public function  totalnonRegisteredfarmer(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(id) as totalnonregisterfarmer from logi_my_farmers where SuffletBGP=0";
            return $result = $db->fetchRow($query);
        }
        
        public function  totalIndividualfarmer(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(farmerType) as totalIndfarmer from logi_my_farmers where farmerType=1";
            return $result = $db->fetchRow($query);
        }
        
        public function  totalLeaderfarmer(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(farmerType) as totalLeadfarmer from logi_my_farmers where farmerType=2";
            return $result = $db->fetchRow($query);
        }
        
        public function  totalStakefarmer(){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select count(farmerType) as totalStakefarmer from logi_my_farmers where farmerType=3";
            return $result = $db->fetchRow($query);
        }
        
         public function  farmerBankInfoExistance($farmerID){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from logi_farmer_bankdeails where FarmerID='".$farmerID."'";
            return $result = $db->fetchRow($query);
        }
        
         public function  farmerBankDetails($bId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "select * from logi_farmer_bankdeails where id='".$bId."'";
            return $result = $db->fetchRow($query);
        }
        
        public function  deleteFarmer($farmerId){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "update logi_my_farmers set flag='1' where id='".$farmerId."'";
            $db->query($query);
            
        }
        
        public function getAllFarmerForApi($loginID){
			
		$db =  Zend_Db_Table::getDefaultAdapter();	
		$query = "SELECT lf.id as FarmerID,lf.FarmerCode, lf.FatherName, lf.FarmerName,lf.MobileNo,lf.TotalAcrage,lf.AllotedFieldEngg,lf.AllotedFieldEnggName,lf.Farmer_Image,lf.RegisterDate,
lft.farmertype,lfp.priority,lfs.state,lfd.district_name,lfth.tehsil_name,lfv.village_name,lfv.pincode FROM logi_my_farmers as lf
left join logi_farmertype as lft on (lf.farmerType = lft.id)
left join logi_farmer_priority as lfp on (lf.farmerPriority = lfp.id)
left join logi_circle as lfs on (lf.stateId = lfs.id)
left join logi_district as lfd on (lf.districtId = lfd.id)
left join logi_tehsil as lfth on (lf.tehsilID = lfth.id)
left join logi_villages as lfv on (lf.villageId = lfv.id)
left join logi_farmer_reference as lfr on (lf.refType = lfr.id) WHERE lf.AllotedFieldEngg='".$loginID."' and lf.flag=0  order by lf.id desc";
		$result = $db->fetchAll($query);
		
		//$i=0;
		$finalArray =  $result;
		$i=0;
		foreach($result as $k){
		   $amount =  $this->getSeedAndChemicalTotalPendingWithFarmer($k['FarmerID']);
		   $finalArray[$i]['outstandingamount'] = $amount;
		   $i++;
		 }
		 
	return $finalArray;
		 
}
		
		
	 public function getSeedAndChemicalTotalPendingWithFarmer($farmerId){
	     $db =  Zend_Db_Table::getDefaultAdapter();
	     $seedTotalOutandingwithFarmer ="SELECT SUM( balance_amount ) as balance_amount  FROM  `logi_farmer_seed_orders` WHERE  `FarmerID` ='".$farmerId."'";
	     $seedTotal = $db->fetchRow($seedTotalOutandingwithFarmer);
	     $chemicalTotalOutandingwithFarmer ="SELECT SUM( finalpaid_amount ) as finalpaid_amount FROM  `logi_chemical_orders` WHERE  `farmerId` ='".$farmerId."'";
	     $chemicalTotal = $db->fetchRow($chemicalTotalOutandingwithFarmer);
	     $totalAmountPending = $seedTotal['balance_amount']+$chemicalTotal['finalpaid_amount'];
	     return $totalAmountPending;
	 }
		
		
		public function getallseedOrderForApi($loginID){
			$db =  Zend_Db_Table::getDefaultAdapter();	
			$query = "SELECT lfs.OrderID as orderId,lf.FarmerCode,lst.seedType,lsst.seedsubtype,lfs.lot_id, lsrt.rowtype,lfs.SeedName,lfs.Qty,lfs.unit,lfs.Rate as totalPayment,lfs.advance_to_be_pay,lfs.payment_recevied,lfs.balance_amount,lfs.dc_name,lfs.OrderDate,lfs.expected_date_of_delivery FROM logi_farmer_seed_orders as lfs
left join logi_my_farmers as lf on (lfs.FarmerID = lf.id)
left join logi_seedtype as lst on (lfs.SeedTypeId = lst.id)
left join logi_seedsubtype as lsst on (lfs.SubTypeId = lsst.id)
left join logi_seedrowtype as lsrt on (lfs.RowTypeId = lsrt.id)
WHERE lfs.AllotedFieldEngg='".$loginID."'";
return $result = $db->fetchAll($query);
		}
		
		public function getAllDepositePayment($loginID){
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT lfep.id,lfep.paymentAmount,lfep.receiptNumber,lfep.paymentDate,lfep.LoginID FROM logi_field_executive_payments as lfep WHERE lfep.LoginID='".$loginID."'";
		return $result = $db->fetchAll($query);	
		}
		
		public function getMisSupportData(){
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT * FROM logi_mis_support";
		return $result = $db->fetchAll($query);	
		}
		
		public function deleteApp($id, $mode){
		$db =  Zend_Db_Table::getDefaultAdapter();
			
			if($mode=='farmertype'){$tbName='logi_farmertype';}elseif($mode=='farmerpriority'){$tbName='logi_farmer_priority';}elseif($mode=='reference'){$tbName='logi_farmer_reference';}
			elseif($mode=='lastcrop'){$tbName='logi_lastcrop';}elseif($mode=='lastrabi'){$tbName='logi_crop_rabi';}
			elseif($mode=='lastkharif'){$tbName='last_crop_kharif';}elseif($mode=='soiltype'){$tbName='logi_soiltype';}
			elseif($mode=='sowing'){$tbName='logi_wayofshowing';}elseif($mode=='irrigation'){$tbName='logi_wayofirrigation';}
			elseif($mode=='source'){$tbName='logi_sourceofwater';}
			
				 $sql = "update ".$tbName." set deleted ='1' where md5(id)='".$id."'";
				 
			$db->query($sql); 
			
		}
                
//---------------------------Get  All Master Data-------------------------------------------------//                
                
                public function GetAllSoilTypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		   $query = "SELECT id,SoilType FROM logi_soiltype where deleted ='0'";
                
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function GetAllSoilPotentialData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
	            $query = "SELECT id,soilpotential FROM logi_soilpotential where deleted ='0'";
             
		    return $result = $db->fetchAll($query);	
		}
                
                public function GetAllChemicslData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,chemical_type FROM logi_chemical_type where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function GetAllSeedTreatMentData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,treatment FROM logi_seedtreatment where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function GetAllSeedHerbicideMasterrData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,types FROM logi_herbicide where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                public function GetCompanyMasterData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,company FROM logi_company where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                public function GetMasterIngredienData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,types FROM logi_ingredient where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetMasterSourceData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,types FROM logi_source where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetApplicationData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,types FROM logi_application where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetLastCropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,lastcrop FROM logi_lastcrop where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                 public function GetLastRabiCropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,RabiCrop FROM logi_crop_rabi where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetLastkhreefCropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,KharifCrop FROM last_crop_kharif where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetShowingData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,WayOfShowing FROM logi_wayofshowing where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                 public function GetWayofirregationData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,WayofIrrigation FROM logi_wayofirrigation where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                 public function GetWaterSourceData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,SourceOfWater FROM logi_sourceofwater where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                 public function GetSeedTypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,seedType FROM logi_seedtype where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                
                 public function GetSeedsubTypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,seedsubtype FROM logi_seedsubtype where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		////
		
		
		public function GetSowingmoistureSourceData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,sowing_moisturesource FROM logi_sowingmoisturesource where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetStageofCropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,stageofcrop FROM logi_stageofcrop where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetDiseasescoringData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,disease_scoring FROM logi_diseasescoring where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetLodgingscoringData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,lodging_scoring FROM logi_lodgingscoring where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetQualityofCropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,qualityofcrop FROM logi_qualityofcrop where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetYieldcomponentTypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,component_types FROM logi_yieldcomponent_types where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GettopographyData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,topography FROM logi_topography where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetPreviouscropData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,previous_crop FROM logi_previouscrop where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetLandshowingDepthData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,showing_depth FROM logi_showingdepth where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		///////
		
		public function GetLandunitAcreageData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,unit,disp FROM logi_units where id='2' and deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetLandunitKgData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,unit,disp FROM logi_units where id='12' and deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		public function GetCropunitMlData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,unit,disp FROM logi_units where id='10' OR id='9' and deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		// public function GetCropunitGramsData(){
		//     $db =  Zend_Db_Table::getDefaultAdapter();
		//     $query = "SELECT id,unit,disp FROM logi_units where id='9' and deleted ='0'";
		//     return $result = $db->fetchAll($query);	
		// }
		
		
		public function GetCropunittypesData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,unit,disp FROM logi_units where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
                
                
                 public function GetRowData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,rowtype FROM logi_seedrowtype where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
               
                 public function GetSoilfertilityTypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,soilfertility FROM logi_soilfertility where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		
		       public function GetCropirrigationNumberData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,irrigation_number FROM logi_irrigationnumber where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		
		       public function GetLandpreprationData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,landprepration FROM logi_landprepration where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		
		      public function GetFertilitytypeData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,fertility_type FROM logi_fertility_type where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
		
		
		public function GetSoilmoistureData(){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT id,soilmoisture FROM logi_soilmoisture where deleted ='0'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function TotamountaganistEnggInSeed($login){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT sum(advance_to_be_pay)as tot_seed, sum(payment_recevied)as tot_seed_rcv FROM logi_farmer_seed_orders where AllotedFieldEngg ='".$login."'";
                 
		    return $result = $db->fetchRow($query);	
		}
		
                
                public function totalFieldFxecutivePayments($login){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT sum(paymentAmount)as tot_rcv FROM logi_field_executive_payments where LoginID ='".$login."'";
    
		    return $result = $db->fetchRow($query);	
		}
                
                public function TotamountaganistEnggInChemical($login){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT sum(grand_total)as tot_chl, sum(paid_amount)as tot_chl_rcv FROM logi_chemical_orders where allotedFieldEngg ='".$login."'";
		    return $result = $db->fetchRow($query);	
		}
                
                public function getPartPaymentListBySeedOrder($LoginID){
		    $db =  Zend_Db_Table::getDefaultAdapter();
		    $query = "SELECT seedorder.id as order_id,  seedorder.orderId, seedorder.FarmerID, seedorder.SeedName, seedorder.lot_id, seedorder.Qty, seedorder.Rate, seedorder.unit, seedorder.advance_to_be_pay, seedorder.payment_recevied, seedorder.balance_amount, seedorder.OrderDate, seedorder.DeliveryDate, seedorder.AllotedFieldEngg , partpayment.id as part_payment_id, partpayment.received_amount, partpayment.order_id, partpayment.date  from logi_farmer_seed_orders as seedorder left join logi_part_payment as partpayment on (partpayment.order_id = seedorder.orderId) where seedorder.AllotedFieldEngg = '".$LoginID."'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function getPartPaymentListByChemicalOrder($LoginID){
		    $db =  Zend_Db_Table::getDefaultAdapter();
                    //$params = array('appkey'=>'mys3cr3tk3y', 'LoginID'=>'RET003', 'device_id'=>'357629065152840', 'order_id'=>'ORDER-001',  'order_date'=>'2016-04-15', 'total_amount'=>'1000','item_detail'=>'[{"item_no":"TestITEM4","product_id":"1","sub_product_id":"1","qty":"10","rate":"10","item_amout":"100"},{"item_no":"TestITEM5","product_id":"1","sub_product_id":"1","qty":"10","rate":"100","item_amout":"100"}]');
		    $query = "SELECT chemicalorder.id as chemical_order_id, orderlist.chemicalType_id,  orderlist.chemicalId, chemicalorder.farmerId, chemicalorder.farmerCode, chemicalorder.allotedFieldEngg, chemicalorder.date_of_order, chemicalorder.expected_date_delivery, chemicalorder.total_amount, chemicalorder.paid_amount,  chemicalorder.date_delivery,  partpayment.id as part_payment_id, partpayment.received_amount, partpayment.order_id, partpayment.date  from logi_chemical_orders as chemicalorder left join logi_part_payment as partpayment on (partpayment.order_id = chemicalorder.orderId) left join logi_chemical_order_list as orderlist on(orderlist.orderId = chemicalorder.orderId)  where chemicalorder.AllotedFieldEngg = '".$LoginID."'";
		    return $result = $db->fetchAll($query);	
		}
                
                
                public function getChemicalOrderList($LoginID){
		    $db =  Zend_Db_Table::getDefaultAdapter();
                    $totalOrder = array();
                    $query = " select * from logi_chemical_orders  where allotedFieldEngg='".$LoginID."'" ;    
                    $result = $db->fetchAll($query);
                    foreach($result as $val){                   
                       $queryitem = " select  * from logi_chemical_order_list where orderId = '".$val['orderId']."'" ;    
                       $resultitem = $db->fetchAll($queryitem); 
                       $myArryItmem = array();
                       foreach($resultitem as $item){
                          $item_parse = array("chemicalType_id"=>$item["chemicalType_id"],"chemicalId"=>$item["chemicalId"],"qty"=>$item["qty"],"rate"=>$item["rate"],"total"=>$item["total"]);  
                          array_push($myArryItmem, $item_parse);  
                       }
                       $itmes =   json_encode($myArryItmem);
                       $myArray = array("order_id"=>$val["orderId"], "farmerId"=>$val["farmerId"], "farmerCode"=>$val["farmerCode"], "allotedFieldEngg"=>$val["allotedFieldEngg"], "date_of_order"=>$val["date_of_order"],"expected_date_delivery"=>$val["expected_date_delivery"] ,"total_amount"=>$val["total_amount"]  ,"paid_amount"=>$val["paid_amount"] ,"date_delivery"=>$val["date_delivery"],"vat"=>$val["vat"],"grand_total"=>$val["grand_total"], "item"=>$itmes); 
                       array_push($totalOrder, $myArray);
                    }
                    
                    return $totalOrder;
                    	
		}
		
		
		
		///
        public function getAllHarvestList(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_hervestlist where deleted=0";  
            return $result = $db->fetchAll($query);
        }

        public function getAllHarvestFarmerStichingList(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT * FROM logi_harvest_farmer_stiching where deleted=0";  
            return $result = $db->fetchAll($query);
        }
        
        
        public function getNPCountAgainestFarmer($farmer){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT count(id) as number FROM logi_farmer_fertilizer_detail where farmer_code='".$farmer."'";  
            return $result = $db->fetchRow($query);
        }
                
                
                
          
                
                
              
      
        
        
		
//---------------------------End of Get Master data----------------------------------------------------------------------//		
		
		
}
