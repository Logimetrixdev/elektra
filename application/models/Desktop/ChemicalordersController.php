<?php

/***************************************************************
 * Logimetrix Techsolutions Pvt. Ltd.
 * File Name   : StatesController.php
 * File Description  : SeedordersController
 * Created By : Abhishek Kumar Mishra<piyush@logimetrix.in>
 * Created Date: 31 July 2015
 ***************************************************************/
 
class ChemicalordersController extends Zend_Controller_Action
{
    var $dbAdapter;
	
    public function init()
    {
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
				$params = $this->view->params = $this->getRequest()->getParams();
                $seedOrder = new Application_Model_Chemicals();
                 
                $this->view->record = $record = $seedOrder->getAllChemicalOrders();
                $page=$this->_getParam('page',1);
                $paginator = Zend_Paginator::factory($record);      
                $paginator->setCurrentPageNumber($this->getRequest()->getParam('page')); // page number
                $perPage = $paginator->setItemCountPerPage(12); // number of items to show per page
                $this->view->paginator = $paginator;
                $this->view->totalrec = $paginator->getTotalItemCount();
               
              
                if($params['seed'] == 'Generate')
			{
				 
				require_once 'PHPExcel/Classes/PHPExcel.php';

				
					$objPHPExcel = new PHPExcel();
					$objPHPExcel->getProperties()->setCreator("Abhishek Mishra")
								 ->setLastModifiedBy("Abhishek Mishra")
								 ->setTitle("Office 2007 XLSX  Document")
								 ->setSubject("Office 2007 XLSX  Document")
								 ->setDescription("Document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Faulty Incident Details");
					// Add some data
					$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Sr. No.')
												  ->setCellValue('B1', 'Farmer Code')
												  ->setCellValue('C1', 'Farmer Name')
                                                  ->setCellValue('D1', 'Chemical Name')
												  ->setCellValue('E1', 'Chemical Type')
                                                   ->setCellValue('F1', 'Quantity')
												  ->setCellValue('G1', 'Order Date')
												  ->setCellValue('H1', 'Date of Delivery');
												 
												  
							$default_border = array(
									'style' => PHPExcel_Style_Border::BORDER_THIN,
									'color' => array('rgb'=>'000000')
								);
								
								$style_header = array(
									'fill' => array(
										'type' => PHPExcel_Style_Fill::FILL_SOLID,
										'color' => array('rgb'=>'999999')
									)
								);
					$objPHPExcel->getActiveSheet()->getStyle('A1:AI1')->applyFromArray( $style_header );	
					$i = 2;		
					foreach($record as $data)			
					{
//						
						$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A'.$i, $i-1)
								->setCellValue('B'.$i, $data['FarmerCode'])	
								->setCellValue('C'.$i, $data['FarmerName'])
								->setCellValue('D'.$i, $data['chemical_name'])
                                ->setCellValue('E'.$i, $data['chemical_type'])
                               
								->setCellValue('G'.$i, $data['quantity'].' '.$data['unit'])
								->setCellValue('H'.$i, $data['date_of_order'])
								->setCellValue('I'.$i, $data['date_delivery']);
								
								
							$i++;	
					}
					$objPHPExcel->setActiveSheetIndex(0);
					
					// Redirect output to a client’s web browser (Excel2007)
	
					header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
					header('Content-Disposition: attachment;filename="Seedlist.xlsx"');
					header('Cache-Control: max-age=0'); 

	
					$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
					$objWriter->save('php://output');

					exit;

			}
                
                
    }
    
   public function orderDetailsAction() {
		$this->checklogin();
                 $layout = $this->_helper->layout();
                $layout->disableLayout('');
                $OrderID = $this->getRequest()->getParam('data'); 
                $chemicalOrder = new Application_Model_Seeds();
                $this->view->order = $order = $chemicalOrder->getChemicalOrderDetails($OrderID);
                
    }
    
    public function deleteChemicalAction(){
                        $this->checklogin();
                        $chemicalOrder= new Application_Model_Chemicals();
                        $ChemicalId = $this->getRequest()->getParam('chemical_Id'); 
                        
                        if($ChemicalId !="")
                        { 
                            $result = $chemicalOrder->deleteChemical($ChemicalId);
                             $this->_redirect('chemicalorders/index'); 
                          
                        }  

                        
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
