<?php

/***************************************************************

 * Logimetrix Techsolution Pvt. Ltd.
 * File Name   : Chemicals.php
 * File Description  : master Methods
 * Created By : abhishek kumar mishra
 * Created Date:17 Aug 2015

 ***************************************************************/

 

class Application_Model_Chemicals extends Zend_Db_Table_Abstract{

    
        public function getAllChemicalOrders(){ 
            $db =  Zend_Db_Table::getDefaultAdapter();
            $query = "SELECT  lco.id,lco.orderId,lco.total_amount,lco.grand_total,lco.paid_amount,lco.finalpaid_amount,lf.FarmerCode,lf.FarmerName,lco.date_of_order,
                      lco.date_delivery FROM logi_chemical_orders as lco left join logi_my_farmers as lf on (lco.farmerId = lf.id) where lco.flag =0 ORDER BY lco.id DESC "; 
            return $result = $db->fetchAll($query);
        }
        
        public function getChemicalOrderDetails($OrderID){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $qu =  "SELECT lco.orderId,lco.grand_total,lco.total_amount,lco.paid_amount,lco.finalpaid_amount,lco.date_of_order,lco.expected_date_delivery,lco.date_delivery,lf.FarmerCode,lf.FarmerName,lf.AllotedFieldEnggName,lco.allotedFieldEngg
                    FROM logi_chemical_orders as lco
                    left join logi_my_farmers as lf on (lco.farmerId  = lf.id)
                    where md5(lco.orderId) ='".$OrderID."'";
            return $result = $db->fetchRow($qu);
        }
        
        public function getChemicalOrderlist($OrderID){
            $db =  Zend_Db_Table::getDefaultAdapter();
            $qu = "SELECT lcl.rate,lcl.allotedFieldEngg,lcl.qty,lcl.total,lct.Chemical_type,lc.chemical_name
                   FROM logi_chemical_order_list as lcl
                   left join logi_chemicals as lc on (lcl.chemicalId = lc.id)
                   left join logi_chemical_type as lct on (lcl.chemicalType_id = lct.id)
                   where md5(lcl.orderId) ='".$OrderID."'";
            return $result = $db->fetchAll($qu);
        }
       
        
        
        public function  deleteChemical($Id){
                $db =  Zend_Db_Table::getDefaultAdapter();
                $query = "update logi_chemical_orders set flag='1' where id='".$Id."'";
                $db->query($query); 
        } 
        
        public  function getAllChemicalOrderApi($uid){
                $db =  Zend_Db_Table::getDefaultAdapter();
                $query = "SELECT * from logi_chemical_orders where allotedFieldEngg = '".$uid."'"; 
                return $result = $db->fetchAll($query, array());
        }
			
	public  function getAllChemicalsOrderListApi($uid){
		$db =  Zend_Db_Table::getDefaultAdapter();
		$query = "SELECT cl.id,cl.orderId,ct.chemical_type,cls.chemical_name,cl.qty,cl.rate,cl.total from logi_chemical_order_list as cl
			left join logi_chemical_type as ct on(cl.chemicalType_id = ct.id)
			left join logi_chemicals as cls on(cl.chemicalId = cls.id)
			where cl.allotedFieldEngg ='".$uid."'"; 
		return $result = $db->fetchAll($query, array());
	}
			
	public  function ChemicalDelivery($orderId){
                $db =  Zend_Db_Table::getDefaultAdapter();
                $query = "SELECT paid_amount from logi_chemical_orders where orderId = '".$orderId."'"; 			
                return $result = $db->fetchRow($query, array());
	}


		 //  public  function updateChemicalDeliveryApi($data,$orderId){
			// 	$db =  Zend_Db_Table::getDefaultAdapter();
			// 	$sql ="update logi_chemical_orders set paid_amount ='".$data['paid_amount']."', date_delivery ='".$data['date_delivery']."' where orderId='".$orderId."'";
			// 	$db->query($sql);
			// }


	public function updateChemicalDeliveryApi($data, $orderId){ 
		$db =  Zend_Db_Table::getDefaultAdapter();
		$db->query("update logi_chemical_orders set finalpaid_amount=? ,paid_amount=? , date_delivery= ? where orderId=? ", array($data['finalpaid_amount'],$data['paid_amount'],$data['date_delivery'],$orderId)); 
	}

 
        
        public function getChemicalreport($chemical){
            $db =  Zend_Db_Table::getDefaultAdapter();
            if($chemical){                    
                $cond .="and lcl.chemicalType_id = '".$chemical."'";
            }
            $qu = " SELECT lcl.id, lcl.orderId, lcl.allotedFieldEngg,lcl.qty,lct.Chemical_type, lc.chemical_name, lco.farmerCode, lco.date_of_order, lfo.FarmerName
                    FROM logi_chemical_order_list AS lcl
                    LEFT JOIN logi_chemicals AS lc ON ( lcl.chemicalId = lc.id )
                    LEFT JOIN logi_chemical_type AS lct ON ( lcl.chemicalType_id = lct.id )
                    LEFT JOIN logi_chemical_orders AS lco ON ( lcl.orderId = lco.orderId )
                    LEFT JOIN logi_my_farmers AS lfo ON ( lco.farmerCode = lfo.FarmerCode ) where 1 $cond
                    order by lcl.id desc";           
            return $result = $db->fetchAll($qu);
        }
        
        
        
        public  function GetlastPaidAmountForChemical($orderId){
                $db =  Zend_Db_Table::getDefaultAdapter();
                $query = "SELECT finalpaid_amount,paid_amount from logi_chemical_orders where orderId = '".$orderId."'"; 			
                return $result = $db->fetchRow($query, array());
	}
			
			
			
			
        public  function GetamountDETAIL($orderId){
                 $db =  Zend_Db_Table::getDefaultAdapter();
                 $query = "SELECT finalpaid_amount,total_amount, grand_total,paid_amount  from logi_chemical_orders where orderId = '".$orderId."'"; 			
                 return $result = $db->fetchRow($query, array());
        }
         public  function getAllChemicalTypeList(){
            $db =  Zend_Db_Table::getDefaultAdapter();
           $query = "SELECT * from logi_chemical_type"; 
         
            return $result = $db->fetchAll($query, array());
        }
          
          
          
          
}


