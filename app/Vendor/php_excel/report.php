<?php 

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */

require_once 'PHPExcel.php';
//creting new phpexcel object

$objPHPExcel = new PHPExcel();


// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                          ->setSize(10);


$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DETB_UPLOAD_MASTER');


$objPHPExcel->getActiveSheet()->getStyle('A2:AD2')->getFont()->setBold(true);




$objPHPExcel->getActiveSheet()->setCellValue('B1', '~~END~~')
                              ->setCellValue('A2', 'BRANCH_CODE')
                              ->setCellValue('B2', 'SOURCE_CODE')
                              ->setCellValue('C2', 'BATCH_NO')
                              ->setCellValue('D2', 'TOTAL_ENTRIES')
                              ->setCellValue('E2', 'UPLOADED_ENTRIES')
                              ->setCellValue('F2', 'BALANCING')
                              ->setCellValue('G2', 'BATCH_DESC')
                              ->setCellValue('H2', 'MIS_REQUIRED')
                              ->setCellValue('I2', 'AUTO_AUTH')
                              ->setCellValue('J2', 'GL_OFFSET_ENTRY_REQD')
                              ->setCellValue('K2', 'UDF_UPLOAD_REQD')
                              ->setCellValue('L2', 'OFFSET_GL')
                              ->setCellValue('M2', 'TXN_CODE')
                              ->setCellValue('N2', 'DR_ENT_TOTAL')
                              ->setCellValue('O2', 'CR_ENT_TOTAL')
                              ->setCellValue('P2', 'USER_ID')
                              ->setCellValue('Q2', 'UPLOAD_STAT')
                              ->setCellValue('R2', 'JOBNO')
                              ->setCellValue('S2', 'SYSTEM_BATCH')
                              ->setCellValue('T2', 'POSITION_REQD')
                              ->setCellValue('U2', 'MAKER_ID')
                              ->setCellValue('V2', 'MAKER_DT_STAMP')
                              ->setCellValue('W2', 'CHECKER_ID')
                              ->setCellValue('X2', 'CHECKER_DT_STAMP')
                              ->setCellValue('Y2', 'MOD_NO')
                              ->setCellValue('Z2', 'AUTH_STAT')
                              ->setCellValue('AA2', 'RECORD_STAT')						  
                              ->setCellValue('AB2', 'ONCE_AUTH')
                              ->setCellValue('AC2', 'UPLOAD_DATE')
                              ->setCellValue('AD2', 'UPLOAD_FILE_NAME');
							  

//writing main data for sheet one 
$objPHPExcel->getActiveSheet()->setCellValue('A3', "'000")
                              ->setCellValue('B3', '~~END~~')      	/* sourcecode = should be same as filename, unique*/
                              ->setCellValue('C3', '~~END~~') 		/* batchcode_should be an auto incrementig serial*/
                              ->setCellValue('D3', '~~END~~')  	    /* total entries = should be total number of transactions */
                              ->setCellValue('E3', '0')     
                              ->setCellValue('F3', 'N')
                              ->setCellValue('G3', '~~END~~')		 /* batch description-- should be the same as filename*/
                              ->setCellValue('H3', 'N')		
                              ->setCellValue('I3', 'N')
                              ->setCellValue('J3', 'N')
                              ->setCellValue('K3', 'N')
                              ->setCellValue('L3', '')
                              ->setCellValue('M3', '534')
                              ->setCellValue('N3', '~~END~~')  		  /* should be total amount debited */
                              ->setCellValue('O3', '~~END~~')		  /* should be total amount credited*/	
                              ->setCellValue('P3', '')
                              ->setCellValue('Q3', 'Y')
                              ->setCellValue('R3', '1')
                              ->setCellValue('S3', 'N')
                              ->setCellValue('T3', 'N')
                              ->setCellValue('U3', '')
                              ->setCellValue('V3', '~~END~~')			/*date file was created format (12-Jun-2012) */
                              ->setCellValue('W3', '')  
                              ->setCellValue('X3', '')
                              ->setCellValue('Y3', '1')
                              ->setCellValue('Z3', 'U')
                              ->setCellValue('AA3', 'O')						  
                              ->setCellValue('AB3', 'Y')
                              ->setCellValue('AC3', '')
                              ->setCellValue('AD3', '')
                              ->setCellValue('AE3', '~~END~~');	


							  
							  

// change styling for a group of cells 							  
$objPHPExcel->getActiveSheet()->getStyle('A4:AE4')->getFont()->setBold(true);	

					  
$objPHPExcel->getActiveSheet()->setCellValue('A4', '~~END~~')
                              ->setCellValue('B4', '~~END~~')
                              ->setCellValue('C4', '~~END~~')
                              ->setCellValue('D4', '~~END~~')
                              ->setCellValue('E4', '~~END~~')
                              ->setCellValue('F4', '~~END~~')
                              ->setCellValue('G4', '~~END~~')
                              ->setCellValue('H4', '~~END~~')
                              ->setCellValue('I4', '~~END~~')
                              ->setCellValue('J4', '~~END~~')
                              ->setCellValue('K4', '~~END~~')
                              ->setCellValue('L4', '~~END~~')
                              ->setCellValue('M4', '~~END~~')
                              ->setCellValue('N4', '~~END~~')
                              ->setCellValue('O4', '~~END~~')
                              ->setCellValue('P4', '~~END~~')
                              ->setCellValue('Q4', '~~END~~')
                              ->setCellValue('R4', '~~END~~')
                              ->setCellValue('S4', '~~END~~')
                              ->setCellValue('T4', '~~END~~')
                              ->setCellValue('U4', '~~END~~')
                              ->setCellValue('V4', '~~END~~')
                              ->setCellValue('W4', '~~END~~')
                              ->setCellValue('X4', '~~END~~')
                              ->setCellValue('Y4', '~~END~~')
                              ->setCellValue('Z4', '~~END~~')
                              ->setCellValue('AA4', '~~END~~')						  
                              ->setCellValue('AB4', '~~END~~')
                              ->setCellValue('AC4', '~~END~~')
                              ->setCellValue('AD4', '~~END~~')
                              ->setCellValue('AE4', '~~END~~');							  
	  

							  
							  
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Upload_Master');

// create another sheet for upload Details
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'DETB_UPLOAD_DETAIL');

$objPHPExcel->getActiveSheet()->getStyle('A2:BI2')->getFont()->setBold(true);




$objPHPExcel->getActiveSheet()->setCellValue('B1', '~~END~~')
                              ->setCellValue('A2', 'INSTRUMENT_NO')
                              ->setCellValue('B2', 'FIN_CYCLE')
                              ->setCellValue('C2', 'PERIOD_CODE')
                              ->setCellValue('D2', 'MIS_CODE')
                              ->setCellValue('E2', 'REL_CUST')
                              ->setCellValue('F2', 'ADDL_TEXT')
                              ->setCellValue('G2', 'MIS_GROUP')
                              ->setCellValue('H2', 'DW_AC_NO')
                              ->setCellValue('I2', 'ACCOUNT_NEW')
                              ->setCellValue('J2', 'TXN_MIS_1')
                              ->setCellValue('K2', 'TXN_MIS_2')
                              ->setCellValue('L2', 'TXN_MIS_3')
                              ->setCellValue('M2', 'TXN_MIS_4')
                              ->setCellValue('N2', 'TXN_MIS_5')
                              ->setCellValue('O2', 'TXN_MIS_6')
                              ->setCellValue('P2', 'TXN_MIS_7')
                              ->setCellValue('Q2', 'TXN_MIS_8')
                              ->setCellValue('R2', 'TXN_MIS_9')
                              ->setCellValue('S2', 'TXN_MIS_10')
                              ->setCellValue('T2', 'COMP_MIS_1')
                              ->setCellValue('U2', 'COMP_MIS_2')
                              ->setCellValue('V2', 'COMP_MIS_3')
                              ->setCellValue('W2', 'COMP_MIS_4')
                              ->setCellValue('X2', 'COMP_MIS_5')
                              ->setCellValue('Y2', 'COMP_MIS_6')
                              ->setCellValue('Z2', 'COMP_MIS_7')
                              ->setCellValue('AA2', 'COMP_MIS_8')						  
                              ->setCellValue('AB2', 'COMP_MIS_9')
                              ->setCellValue('AC2', 'COMP_MIS_10')
                              ->setCellValue('AD2', 'COST_CODE1')
                              ->setCellValue('AE2', 'COST_CODE1')
                              ->setCellValue('AF2', 'COST_CODE2')
                              ->setCellValue('AG2', 'COST_CODE3')
                              ->setCellValue('AH2', 'COST_CODE4')
                              ->setCellValue('AI2', 'COST_CODE5')
                              ->setCellValue('AJ2', 'MIS_HEAD')
                              ->setCellValue('AK2', 'RELATED_ACCOUNT')
                              ->setCellValue('AL2', 'RELATED_REF')
                              ->setCellValue('AM2', 'POOL_CODE')
                              ->setCellValue('AN2', 'REF_RATE')
                              ->setCellValue('AO2', 'CALC_METHOD')
                              ->setCellValue('AP2', 'BATCH_NO')
                              ->setCellValue('AQ2', 'MIS_FLAG')						  
                              ->setCellValue('AR2', 'BRANCH_CODE')
                              ->setCellValue('AS2', 'SOURCE_CODE')
                              ->setCellValue('AT2', 'CURR_NO')
                              ->setCellValue('AU2', 'UPLOAD_STAT')
                              ->setCellValue('AV2', 'CCY_CD')
                              ->setCellValue('AW2', 'INITIATION_DATE')
                              ->setCellValue('AX2', 'AMOUNT')							  
                              ->setCellValue('AY2', 'ACCOUNT_BRANCH')
                              ->setCellValue('AZ2', 'TXN_CODE')
                              ->setCellValue('BA2', 'DR_CR')
                              ->setCellValue('BB2', 'LCY_EQUIVALENT')
                              ->setCellValue('BC2', 'EXCH_RATE')
                              ->setCellValue('BE2', 'VALUE_DATE')
                              ->setCellValue('BF2', 'EXTERNAL_REF_NO')
                              ->setCellValue('BG2', 'RESERVED_FUNDS_REF')						  
                              ->setCellValue('BH2', 'DELETE_STAT')
                              ->setCellValue('BI2', 'TXT_FILE_NAME');


// Rename worksheet 2 to upload Detail
$objPHPExcel->getActiveSheet()->setTitle('Upload_Detail');

							  

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);							  
                              
//saving as excel 2007 file xls
//$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

//$objWriter->save(str_replace('.php', '.xlsx', __FILE__));


// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="name_of_file.xls"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');



							  
?>