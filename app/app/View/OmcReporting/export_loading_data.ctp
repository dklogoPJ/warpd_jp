<?php
if($download){
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
}
else{
    echo "No Record found.";
}