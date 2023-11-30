<?php
/*if($download){
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
}*/

$file="demo.xls";
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=$file");
?>
    <div class="block-fluid">
        <style type="text/css">
            .table th, .table td {
                padding: 2px;
            }
            [class*="block"] .table tr th, [class*="block"] .table tr td {
                border-right: 1px solid #8A8484;
            }
            .table th, .table td {
                border-top: 1px solid #8A8484;
            }
        </style>
        <table cellpadding="0" cellspacing="0" width="100%" class="table">
            <!--<thead>
                    <tr>
                        <?php
                        /*foreach($g_data['t_head'] as $h){
                            */?>
                            <th><?php /*echo $h ;*/?></th>
                        <?php
                        /*}
                        */?>
                    </tr>
                    </thead>-->
            <tbody>
            <?php
            $headers = array(
                'current_stock_level'=>'Stock Levels',
                'min_stock_level'=>'Minimum Stock Level',
                'variance'=>'Variance',
                'color'=>'Action Indicator',
                'status'=>'Action Required'
            );
            $final_str = '';
            foreach($g_data as $tbd_arr){
                //inject the header into the stock data
                $tbd_arr['stock'] = array_merge(array('headers'=>$headers),$tbd_arr['stock']);
                $total_rows = count($tbd_arr['stock']);
                // debug($tbd_arr);
                foreach($tbd_arr['stock'] as $key => $v_arr){
                    $tr_str = "<tr>";
                    if($key == 'headers'){
                        $tr_str .= "<td rowspan='$total_rows' style='vertical-align: middle;font-weight:bolder; background:#E8EBF0;'><strong>".$tbd_arr['info']['name']."</strong></td>";
                        $tr_str .= "<td>&nbsp;</td>";
                        $tr_str .= "<td><strong>".$v_arr['current_stock_level']."</strong></td>";
                        $tr_str .= "<td><strong>".$v_arr['min_stock_level']."</strong></td>";
                        $tr_str .= "<td><strong>".$v_arr['variance']."</strong></td>";
                        $tr_str .= "<td><strong>".$v_arr['color']."</strong></td>";
                        $tr_str .= "<td><strong>".$v_arr['status']."</strong></td>";
                    }
                    else{
                        $tr_str .= "<td>".$key."</td>";
                        $tr_str .= "<td>".$controller->formatNumber($v_arr['current_stock_level'],'number',0)."</td>";
                        $tr_str .= "<td>".$controller->formatNumber($v_arr['min_stock_level'],'number',0)."</td>";
                        $tr_str .= "<td>".$controller->formatNumber($v_arr['variance'],'number',0)."</td>";
                        $cl = '#468847';
                        if($v_arr['color'] == 'red'){
                            $cl = '#b94a48';
                        }
                        elseif($v_arr['color'] == 'yellow'){
                            $cl = '#f89406';
                        }
                        $tr_str .= "<td  style='background:$cl;'>&nbsp;</td>";


                        $tr_str .= "<td>".$v_arr['status']."</td>";
                    }
                    $tr_str .= "</tr>";
                    $final_str .= $tr_str;
                }
            }
            echo $final_str;
            ?>
            </tbody>
        </table>
    </div>
<?php
//$test="<table  ><tr><td>Cell 1</td><td>Cell 2</td></tr></table>";
//echo $test;
