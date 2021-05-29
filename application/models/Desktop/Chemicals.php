<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Seeds.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date:17 Aug 2015

 ***************************************************************/

 

class Application_Model_Seeds extends Zend_Db_Table_Abstract

{

    
         public function getAllSeedOrders(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT lfs.OrderID,lf.FarmerCode,lf.FarmerName,lst.seedType,lsubt.seedsubtype,lfs.SeedName,lfs.Qty,lfs.unit,date(lfs.OrderDate) as booking,
                date(lfs.expected_date_of_delivery) as delivery FROM logi_farmer_seed_orders as lfs
left join logi_my_farmers as lf on (lfs.FarmerID = lf.id)
left join logi_seedtype as lst on (lfs.SeedTypeId = lst.id)
left join logi_seedsubtype as lsubt on (lfs.SubTypeId = lsubt.id) where lfs.flag=0
ORDER BY lfs.expected_date_of_delivery DESC ";  
            return $result = $db->fetchAll($query);
        }
        
        public function getSeedOrderDetails($orderID){
             $db =  Zend_Db_Table::getDefaultAdapter();
            $qu = "SELECT lfs.OrderID,lf.FarmerCode,lf.FarmerName,lf.AllotedFieldEnggName,lf.AllotedFieldEngg,lv.village_name,lst.seedType,lsubt.seedsubtype,lfs.SeedName,lfs.Qty,lfs.Rate,lfs.advance_to_be_pay,lfs.payment_recevied,lfs.balance_amount,lfs.unit,lfs.FeSignatureOrder,lfs.FarmerSignatureOrder,date(lfs.OrderDate)as booking, date(lfs.expected_date_of_delivery) as delivery,lsrt.rowtype,ldc.distribution_center
FROM logi_farmer_seed_orders as lfs
left join logi_my_farmers as lf on (lfs.FarmerID = lf.id)
left join logi_seedtype as lst on (lfs.SeedTypeId = lst.id)
left join logi_seedsubtype as lsubt on (lfs.SubTypeId = lsubt.id)
left join logi_seedrowtype as lsrt on(lfs.RowTypeId = lsrt.id)
left join logi_villages as lv on(lf.villageId = lv.id)
left join logi_distibution_center as ldc on(lfs.dc_name = ldc.id)
where md5(lfs.OrderID)='".$orderID."'";
             return $result = $db->fetchRow($qu);
        }
        
        public function  deleteSeed($Id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "update logi_farmer_seed_orders set flag='1' where OrderID='".$Id."'";
            $db->query($query);
            
        }
           
}
