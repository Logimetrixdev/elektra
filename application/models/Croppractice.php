<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Chemicals.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date:17 Aug 2015

 ***************************************************************/

 

class Application_Model_Croppractice extends Zend_Db_Table_Abstract{
        
    public function getLandinformationDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lco.farmer_id,lco.land_id,lco.land_name,lco.land_area,lco.land_unit,lu.disp,lco.irrigation_facility_id,lws.SourceOfWater,lco.soil_type_id,lst.SoilType,lco.soil_fertility_id,lsf.soilfertility,lpc.previous_crop,lco.previous_crop_id,lco.newly_cultivated_field,lco.rocky_field,lco.topography_id,lt.topography,lco.land_lat,lco.land_long,lco.farmer_code,lco.farmer_name,lco.fe_code,lco.sink_date_time
                FROM logi_land_information as lco
                left join logi_units as lu on (lco.land_unit  = lu.id)
                left join logi_sourceofwater as lws on (lco.irrigation_facility_id  = lws.id)
                left join logi_soiltype as lst on (lco.soil_type_id  = lst.id)
                left join logi_soilfertility as lsf on (lco.soil_fertility_id  = lsf.id)
                left join logi_previouscrop as lpc on (lco.previous_crop_id  = lpc.id)
                left join logi_topography as lt on (lco.topography_id  = lt.id)
                where md5(lco.farmer_id) ='".$FarmerID."' and lco.status !='1' order by lco.land_id desc";
        return $result = $db->fetchAll($qu);
    }

    public function getLandfarmerDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lco.farmer_id,lco.farmer_code,lco.farmer_name,lco.fe_code
                FROM logi_land_information as lco
                where md5(lco.farmer_id) ='".$FarmerID."'";
        return $result = $db->fetchRow($qu);
    }


    public function getLandpreprationDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT llp.farmer_id,llp.land_prepration_id,llp.land_id,lli.land_name,llp.harrow_no,llp.cultivator_no,llp.plowing,llp.rotorator_no,llp.niveling_no,llp.date_of_preshowing_irrigation,llp.quality_of_land_prepration_id,lp.landprepration
               FROM logi_land_prepration as llp
               left join logi_land_information as lli on (llp.land_id  = lli.land_id)
               left join logi_landprepration as lp on (llp.quality_of_land_prepration_id  = lp.id)
               where md5(llp.farmer_id) ='".$FarmerID."' and llp.status !='1' order by llp.land_prepration_id desc";    
        return $result = $db->fetchAll($qu);
    }

    public function getLandfertilizerDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lff.farmer_id,lff.land_id,lli.land_name,lff.land_farmer_id,lff.time_of_application_id,la.types,lff.fertilizer_type_id,lfd.total_n,lfd.total_p,lff.quantity,lff.quantity_unit,lu.disp,lff.organic_mannure_before_rabi,lcr.RabiCrop,lck.KharifCrop,lff.organic_mannure_before_kharif,lff.farmer_used_date_time,lftt.fertility_type
               FROM logi_land_fertilizer as lff
               left join logi_land_information as lli on (lff.land_id  = lli.land_id)
               left join logi_application as la on (lff.time_of_application_id  = la.id)
               left join logi_farmer_fertilizer_detail as lfd on (lff.land_farmer_id  = lfd.id)
               left join logi_fertility_type as lftt on (lff.fertilizer_type_id  = lftt.id)
               left join logi_units as lu on (lff.quantity_unit  = lu.id)
               left join logi_crop_rabi as lcr on (lff.organic_mannure_before_rabi  = lcr.id)
               left join last_crop_kharif as lck on (lff.organic_mannure_before_kharif  = lck.id)
               where md5(lff.farmer_id) ='".$FarmerID."' and lff.status !='1' order by lff.land_farmer_id desc";
        return $result = $db->fetchAll($qu);
    }

    public function getLandirrigationDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lli.farmer_id,lli.land_id,li.land_name,lli.land_irri_id,lli.irri_type_id,lli.days_after_sowing,lwi.WayofIrrigation,lli.irri_date,lli.stage_id,lss.stageofcrop,lli.irri_no_id,lir.irrigation_number
                FROM logi_land_irrigation as lli
                left join logi_land_information as li on (lli.land_id  = li.land_id)
                left join logi_wayofirrigation as lwi on (lli.irri_type_id  = lwi.id)
                left join logi_irrigationnumber as lir on (lli.irri_no_id  = lir.id)
                left join logi_stageofcrop as lss on (lli.stage_id  = lss.id)
                where md5(lli.farmer_id) ='".$FarmerID."' and lli.status !='1'  order by lli.land_irri_id desc";
        return $result = $db->fetchAll($qu);

    }

    public function getLandcropProtectionDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT llc.farmer_id,llc.land_id,lf.land_name,llc.land_protection_id,llc.source_id,ls.types,llc.chemical_type_id,llc.chemical_type,llc.company,lc.company as comp,llc.active_ingredient,li.types as ingre,llc.dose,lu.disp,luu.disp as area,llc.dose_unit_id,llc.area_unit_id,llc.stage_id,llc.protection_date,lss.stageofcrop
                FROM logi_land_cropprotection as llc
                left join logi_land_information as lf on (llc.land_id  = lf.land_id)
                left join logi_source as ls on (llc.source_id  = ls.id)
                left join logi_company as lc on (llc.company  = lc.id)
                left join logi_ingredient as li on (llc.active_ingredient  = li.id)
                left join logi_units as lu on (llc.dose_unit_id  = lu.id)
                left join logi_units as luu on (llc.area_unit_id  = luu.id)
                left join logi_stageofcrop as lss on (llc.stage_id  = lss.id)
                where md5(llc.farmer_id) ='".$FarmerID."' and llc.status !='1'  order by llc.land_protection_id desc";
        return $result = $db->fetchAll($qu);
    }

    public function getLandyieldComponentDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT llyc.farmer_id,llyc.land_id,lli.land_name,llyc.observation_id,llyc.average_no,llyc.yield_id,lyt.component_types
               FROM logi_land_yield_component as llyc
               left join logi_land_information as lli on (llyc.land_id  = lli.land_id)
               left join logi_yieldcomponent_types as lyt on (llyc.observation_id  = lyt.id)
               where md5(llyc.farmer_id) ='".$FarmerID."' and llyc.status !='1' order by llyc.yield_id desc";
        return $result = $db->fetchAll($qu);
    }

    public function getLandvisitDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = " SELECT llv.farmer_id,llv.land_id,lf.land_name,llv.visit_id,llv.disease_score,llv.stage_id,lds.disease_scoring,llv.logging_score,ldg.lodging_scoring,llv.presemption_of_insect,llv.soil_moisture_id,llv.quality_of_crop,llv.image,lss.stageofcrop,lq.qualityofcrop,lsm.soilmoisture
                FROM logi_land_visit as llv
                left join logi_land_information as lf on (llv.land_id  = lf.land_id)
                left join logi_diseasescoring as lds on (llv.disease_score  = lds.id)
                left join logi_lodgingscoring as ldg on (llv.logging_score  = ldg.id)
                left join logi_soilmoisture as lsm on (llv.soil_moisture_id  = lsm.id)
                left join logi_qualityofcrop as lq on (llv.quality_of_crop  = lq.id)
                left join logi_stageofcrop as lss on (llv.stage_id  = lss.id)
                where md5(llv.farmer_id) ='".$FarmerID."' and llv.status !='1' order by llv.visit_id desc";
        return $result = $db->fetchAll($qu);
    }
						
											
    public function getLandsowingDetails($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lls.farmer_id,lls.land_id,lf.land_name,lls.land_sowing_id, lls.lot_no,lls.mou_type_id,lls.variety_id,lls.category_id,ls.seedType,lds.SeedName,lss.seedsubtype
               FROM logi_land_showing as lls
               left join logi_land_information as lf on (lls.land_id  = lf.land_id)
               left join logi_seedtype as ls on (lls.mou_type_id  = ls.id)
               left join logi_seeds as lds on (lls.variety_id  = lds.SeedID)
               left join logi_seedsubtype as lss on (lls.category_id  = lss.id)
               where md5(lls.farmer_id) ='".$FarmerID."' and lls.status !='1' order by lls.land_sowing_id desc";
        return $result = $db->fetchAll($qu);
    }
						
    public function getSowingDetails($ID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $qu = "SELECT lls.land_sowing_id,lls.lot_no,lts.lot_no as lotno,lls.tcw_of_seed,lls.sowing_depth,lls.sowing_depth_unit_id,lls.sowing_date,lls.seed_rate_kg_per_acre,lls.seed_rate_per_m2,lls.sowing_type_id,lls.sowing_moisture_source,lls.row_to_row_diff,lls.row_to_row_diff_unit_id,lsd.showing_depth,lws.WayOfShowing,lsms.sowing_moisturesource,lsdd.showing_depth
               FROM logi_land_showing as lls
               left join logi_showingdepth as lsd on (lls.sowing_depth_unit_id  = lsd.id)
               left join logi_wayofshowing as lws on (lls.sowing_type_id  = lws.id)
               left join logi_tcwofseed as lts on (lls.lot_no  = lts.id)
               left join logi_sowingmoisturesource as lsms on (lls.sowing_moisture_source  = lsms.id)
               left join logi_showingdepth as lsdd on (lls.row_to_row_diff_unit_id  = lsdd.id)
               where md5(lls.land_sowing_id)  ='".$ID."' and lls.status !='1' order by lls.land_sowing_id desc";
        return $result = $db->fetchRow($qu);
    }
												
    public function getLandirrigationId($FarmerID){
        $db =  Zend_Db_Table::getDefaultAdapter();							
        $res=explode(',',$FarmerID);
        $source=array();
        foreach($res as $val){
            $qu = "SELECT * FROM logi_sourceofwater where id='".$val."'";
            $result = $db->fetchRow($qu);
            array_push($source, $result['SourceOfWater']);
        }
        return $source;
    }
												
    public function FarmerlandDetails($ID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $query = "SELECT lco.land_id,lco.land_name,lco.land_area,lco.land_unit,lco.farmer_name,lco.land_long,lco.land_lat
                  FROM logi_land_information as lco
                  WHERE lco.land_id = '".$ID."'";
        return $db->fetchAll($query);
    }
    
    
    public function GetallEngineer($ID){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $query = "SELECT * from logi_field_users";   
        return $db->fetchAll($query);
    }
    
    
    public function GetFarmerList($engg){
        $db =  Zend_Db_Table::getDefaultAdapter();
        $query = "SELECT * from logi_my_farmers where AllotedFieldEngg  = '".$engg."'";   
        return $db->fetchAll($query);
    }
    
    
    public function getFarmerNPdetail($farmer){
        $db =  Zend_Db_Table::getDefaultAdapter();
        
        $get_farmer_code = "SELECT * from logi_my_farmers where md5(id)  = '".$farmer."'";   
        $result_farmer_code =  $db->fetchRow($get_farmer_code);
       $query = "SELECT * from logi_farmer_fertilizer_detail where farmer_code = '".$result_farmer_code['FarmerCode']."'"; 
    
     
        return $db->fetchRow($query);
    }
                 
}


