<?php
if($download){
    $this->response->type(array('xls' => 'application/vnd.ms-excel'));
    // Set the response Content-Type to xls
    $this->response->type('xls');
    //header('Content-Type: application/vnd.ms-excel');//This fails in safari and other browsers
    header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}
else{
    echo "No Record found.";
}