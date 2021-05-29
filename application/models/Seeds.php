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

    
         public function getAllSeedOrders($seeds){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            
            if($seeds){
               $cond .="and lfs.SeedTypeId ='".$seeds."'";
            }
            $query = "SELECT lfs.id,lfs.orderId,lf.FarmerCode,lf.FarmerName,lst.seedType,lsubt.seedsubtype,lfs.SeedName,lfs.Qty,lfs.unit,lfs.Rate,lfs.advance_to_be_pay,lfs.payment_recevied,lfs.balance_amount,lfs.PaidAmount,date(lfs.OrderDate) as booking,
                      date(lfs.expected_date_of_delivery) as delivery FROM logi_farmer_seed_orders as lfs
                      left join logi_my_farmers as lf on (lfs.FarmerID = lf.id)
                      left join logi_seedtype as lst on (lfs.SeedTypeId = lst.id)
                      left join logi_seedsubtype as lsubt on (lfs.SubTypeId = lsubt.id) where lfs.flag=0 $cond
                      ORDER BY lfs.id DESC ";  
            return $result = $db->fetchAll($query);
        }
        
        public function getSeedOrderDetails($OrderID){
             $db =  Zend_Db_Table::getDefaultAdapter();
            $qu = "SELECT lfs.orderId,lf.FarmerCode,lf.FarmerName,lf.AllotedFieldEnggName,lf.AllotedFieldEngg,lv.village_name,lst.seedType,lsubt.seedsubtype,lfs.SeedName,lfs.Qty,lfs.Rate,lfs.advance_to_be_pay,lfs.payment_recevied,lfs.balance_amount,lfs.unit,lfs.FeSignatureOrder,lfs.FarmerSignatureOrder,lfs.PaidAmount,lfs.DeliveryDate,date(lfs.OrderDate)as booking, date(lfs.expected_date_of_delivery) as delivery,lsrt.rowtype,ldc.distribution_center
FROM logi_farmer_seed_orders as lfs
left join logi_my_farmers as lf on (lfs.FarmerID = lf.id)
left join logi_seedtype as lst on (lfs.SeedTypeId = lst.id)
left join logi_seedsubtype as lsubt on (lfs.SubTypeId = lsubt.id)
left join logi_seedrowtype as lsrt on(lfs.RowTypeId = lsrt.id)
left join logi_villages as lv on(lf.villageId = lv.id)
left join logi_distibution_center as ldc on(lfs.dc_name = ldc.id)
where md5(lfs.orderId)='".$OrderID."'";

             return $result = $db->fetchRow($qu);
        }
        
        public function  deleteSeed($Id){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "update logi_farmer_seed_orders set flag='1' where id='".$Id."'";
            $db->query($query);
            
        }
           
           	
			public  function getAllSeedOrderListApi($uid){
            $db =  Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT * from logi_farmer_seed_orders where AllotedFieldEngg ='".$uid."'"; 
			return $result = $db->fetchAll($query, array());
			}
       
       
            public  function seedDelivery($uid){
				$db =  Zend_Db_Table::getDefaultAdapter();
			$query = "SELECT PaidAmount,DeliveryDate from logi_farmer_seed_orders where orderId = '".$uid."'"; 
			
				return $result = $db->fetchRow($query, array());
			}


		 //  public  function updateSeedDeliveryApi($data,$orderId){
			// 	$db =  Zend_Db_Table::getDefaultAdapter();
			// 	$sql ="update logi_farmer_seed_orders set PaidAmount ='".$data['PaidAmount']."',DeliveryDate ='".$data['DeliveryDate']."' where orderId='".$orderId."'";
			// 	$db->query($sql);
			// }


         public function updateSeedDeliveryApi($data, $orderId){ 
			$db =  Zend_Db_Table::getDefaultAdapter();
			 $sql="update logi_farmer_seed_orders set PaidAmount='".$data['paid_amount']."' ,balance_amount ='".$data['balance_amount']."', DeliveryDate='".$data['DeliveryDate']."' where orderId='".$orderId."' ";
			
			$db->query($sql);
         }
    
		public  function insertPartPaymentData($data){
			$db =  Zend_Db_Table::getDefaultAdapter();
			 $sql='insert into logi_part_payment(order_id,received_amount,date) values("'.$data['order_id'].'","'.$data['received_amount'].'","'.$data['date'].'") ';
			$db->query($sql);
		}
    
    
			public  function GetlastPaidAmountForseed($orderid){
				$db =  Zend_Db_Table::getDefaultAdapter();
			     $query = "SELECT PaidAmount,balance_amount,DeliveryDate from logi_farmer_seed_orders where orderId = '".$orderid."'"; 
			   
				return $result = $db->fetchRow($query, array());
			}
			
			public  function getpartPaymentCount($orderid){
			$db =  Zend_Db_Table::getDefaultAdapter();
			 $query="select count(*) as val from logi_part_payment where order_id = '".$orderid."'"; 
			return $result = $db->fetchRow($query);
		}
    
		public  function getAllSeedType(){
                    $db =  Zend_Db_Table::getDefaultAdapter();
                    $query = "SELECT * from logi_seedtype "; 
                    return $result = $db->fetchAll($query, array());
		}


}
