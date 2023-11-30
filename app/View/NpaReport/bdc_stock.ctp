<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1><?php echo $bdc_name; ?> Stock Positions <small> </small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span2" style="width: 80px;">Start Date:</div>
                    <div class="span2" style="width: 8%; margin-left: 3px;">
                        <?php echo $this->Form->input('start_dt', array('id'=>'start_dt', 'class' => 'span2 date-masking validate[required]','default'=>$start_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span2" style="width: 80px;">End Date:</div>
                    <div class="span2" style="width: 8%; margin-left: 3px;">
                        <?php echo $this->Form->input('end_dt', array('id'=>'end_dt', 'class' => 'span2 date-masking validate[required]','default'=>$end_dt,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span1" style="width: 3%;">BDC:</div>
                    <div class="span3" style=" margin-left: 3px;">
                        <?php echo $this->Form->input('bdc', array('id'=>'bdc', 'class' => '','default'=>$bdc,'options'=>$bdc_list, 'div' => false, 'label' => false,)); ?>
                    </div>

                    <div class="span3">
                       <!-- --><?php /*echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All','red'=>'Red','yellow_red'=>'Yellow & Red'), 'div' => false, 'label' => false,)); */?>
                        <button class="btn" type="submit" id="query-btn">Get Stock Histories </button>
                    </div>
                </div>


                <!--<div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Get Stock Variance </button>
                </div>-->
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1><?php echo $bdc_name; ?> Stock History (ltrs)</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success" type="button" id="print-btn">Print </button>
                            <button class="btn btn-success" type="button" id="export-btn">Export </button>
                        <?php
                        }
                        ?>
                        <!--<a href="#" class="isw-text_document"> Export</a>-->
                    </li>
                </ul>
            </div>
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
                <?php
                    if($grid){
                ?>
                        <table cellpadding="0" cellspacing="0" width="100%" class="table table-bordered">
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
                    </thead>
                    <tbody>-->
                            <?php
                            $style_td_1 = "style='width:20%; ;vertical-align: middle; font-style: italic;font-family: bookman old style;font-size: 22px; font-weight:bolder;background: #F9F9F9'";
                            $style_td_2 = "style='vertical-align: middle;font-weight:bolder; background:#E8EBF0;'";
                            $style_td_3 = "style='font-size: 10.5px;'";
                            $final_str = "";
                            $headers = $grid['headers'];
                            $t_heads_str = "<tr><th><strong>Depot</strong></th><th><strong>Product</strong></th><th></th>";
                            //create headers first
                            foreach($headers as $h){
                                $t_heads_str .= "<th><strong>$h</strong></th>";
                            }
                            $t_heads_str .= "</tr>";
                            $final_str .= $t_heads_str;
                            foreach($grid['data'] as $depot){
                                $tr_str = "<tr>";
                                $row_span = count($depot['products']) * 4;
                                $tr_str .= "<td rowspan='$row_span' $style_td_1>".$depot['name']."</td>";
                                //treat the first product special
                                $first_pro = array_shift($depot['products']);
                                $tr_str .= "<td rowspan='4' $style_td_2>".$first_pro['name']."</td>";
                                //Initial
                                $tr_str .= "<td $style_td_3>Initial</td>";
                                foreach($headers as $h){
                                    $m = isset($first_pro['data'][$h])? $first_pro['data'][$h]['initial_quantity']:0;
                                    $init = $controller->formatNumber($m,'money',0);
                                    $tr_str .= "<td $style_td_3>$init</td>";
                                }
                                $tr_str .= "</tr>";
                                //Recievings
                                $tr_str .= "<tr><td $style_td_3>Receipts</td>";
                                foreach($headers as $h){
                                    $m = isset($first_pro['data'][$h])?$first_pro['data'][$h]['stock_update_quantity']:0;
                                    $recv = $controller->formatNumber($m,'money',0);
                                    $tr_str .= "<td $style_td_3>$recv</td>";
                                }
                                $tr_str .= "</tr>";
                                //Lifting
                                $tr_str .= "<tr><td $style_td_3>Lifting</td>";
                                foreach($headers as $h){
                                    $m = isset($first_pro['data'][$h])?$first_pro['data'][$h]['lifting_quantity']:0;
                                    $lift = $controller->formatNumber($m,'money',0);
                                    $tr_str .= "<td $style_td_3>$lift</td>";
                                }
                                $tr_str .= "</tr>";
                                //Closing
                                $tr_str .= "<tr><td $style_td_3>Closing</td>";
                                foreach($headers as $h){
                                    $m = isset($first_pro['data'][$h])? $first_pro['data'][$h]['closing_quantity']:0;
                                    $cls = $controller->formatNumber($m,'money',0);
                                    $tr_str .= "<td $style_td_3>$cls</td>";
                                }
                                $tr_str .= "</tr>";

                                //now process the rest of the products
                                foreach($depot['products'] as $v_arr){
                                    $tr_str .= "</tr><td rowspan='4' $style_td_2>".$v_arr['name']."</td>";
                                    //Initial
                                    $tr_str .= "<td $style_td_3>Initial</td>";
                                    foreach($headers as $h){
                                        $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['initial_quantity']:0;
                                        $init = $controller->formatNumber($m,'money',0);
                                        $tr_str .= "<td $style_td_3>$init</td>";
                                    }
                                    $tr_str .= "</tr>";
                                    //Recievings
                                    $tr_str .= "<tr><td $style_td_3>Receipts</td>";
                                    foreach($headers as $h){
                                        $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['stock_update_quantity']:0;
                                        $recv = $controller->formatNumber($m,'money',0);
                                        $tr_str .= "<td $style_td_3>$recv</td>";
                                    }
                                    $tr_str .= "</tr>";
                                    //Lifting
                                    $tr_str .= "<tr><td $style_td_3>Lifting</td>";
                                    foreach($headers as $h){
                                        $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['lifting_quantity']:0;
                                        $lift = $controller->formatNumber($m,'money',0);
                                        $tr_str .= "<td $style_td_3>$lift</td>";
                                    }
                                    $tr_str .= "</tr>";
                                    //Closing
                                    $tr_str .= "<tr><td $style_td_3>Closing</td>";
                                    foreach($headers as $h){
                                        $m = isset($v_arr['data'][$h])?$v_arr['data'][$h]['closing_quantity']:0;
                                        $cls = $controller->formatNumber($m,'money',0);
                                        $tr_str .= "<td $style_td_3>$cls</td>";
                                    }
                                    $tr_str .= "</tr>";
                                    //
                                }
                                $final_str .= $tr_str;
                            }
                            echo $final_str;
                            ?>
                            <!-- </tbody>-->
                        </table>
                <?php
                    }
                    else{

                    }
                ?>
            </div>

        </div>
    </div>

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'NpaReport', 'action' => 'print_export_bdc_stock')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_start" id="data_start" value="" />
        <input type="hidden" name="data_end" id="data_end" value="" />
        <input type="hidden" name="data_bdc" id="data_bdc" value="" />
        <input type="hidden" name="data_bdc_name" id="data_bdc_name" value="" />
    </form>

    <div class="dr"><span></span></div>
</div>

<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/npa_bdc_stock.js');
?>
