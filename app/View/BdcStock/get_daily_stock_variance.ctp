<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1>Daily Stock Variance <small> - <?php echo date("F d, Y", time()); ?></small></h1>
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

                    <div class="span2" style="width: 80px;">Depot:</div>
                    <div class="span2">
                        <?php echo $this->Form->input('depot', array('id'=>'depot', 'class' => '','default'=>$depot,'options'=>$my_depots_opts, 'div' => false, 'label' => false,)); ?>
                    </div>
                    <div class="span2">
                        <!-- --><?php /*echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All','red'=>'Red','yellow_red'=>'Yellow & Red'), 'div' => false, 'label' => false,)); */?>
                        <button class="btn" type="submit" id="query-btn">Get Stock Histories </button>
                    </div>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1>Stock Variance (ltrs)</h1>
                <ul class="buttons">
                    <li>
                       <!-- <button class="btn btn-success" type="button" id="print-btn">Print </button>
                        <button class="btn btn-success" type="button" id="export-btn">Export </button>-->
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
                    </thead>
                    <tbody>-->
                            <?php
                            $style_td = "style='width:20%; vertical-align: middle; font-style: italic;font-family: bookman old style;font-size: 18px; font-weight:bolder;background: #F9F9F9'";
                            $style_td_1 = "style='vertical-align: middle;width:18%;'";
                            $final_str = "";
                            $t_heads_str = "<tr><th><strong>Depot</strong></th><th><strong>Product</strong></th><th><strong>Stock</strong></th><th><strong>Orders</strong></th><th><strong>Variance</strong></th><th><strong>Action</strong></th><th><strong>Status</strong></th></tr>";
                            $final_str .= $t_heads_str;
                            foreach($grid['data'] as $depot){
                                $tr_str = "<tr>";
                                $row_span = count($depot['products']);
                                $tr_str .= "<td rowspan='$row_span' $style_td>".$depot['name']."</td>";
                                //treat the first product special
                                $first_pro = array_shift($depot['products']);
                                $tr_str .= "<td $style_td_1>".$first_pro['name']."</td>";
                                //Stock
                                $stock_tl = $controller->formatNumber($first_pro['data']['total_stock'],'money',0);
                                $tr_str .= "<td>$stock_tl</td>";
                                //Orders
                                $orders_tl = $controller->formatNumber($first_pro['data']['orders_qty'],'money',0);
                                $tr_str .= "<td>$orders_tl</td>";
                                //Variance
                                $variance_tl = $controller->formatNumber($first_pro['data']['variance'],'money',0);
                                $tr_str .= "<td>$variance_tl</td>";
                                //Action
                                $color = $first_pro['data']['color'];
                                if($color == 'red'){
                                    $cl = 'important';
                                }
                                elseif($color == 'success'){
                                    $cl = 'success';
                                }
                                $tr_str .= "<td><span class='label label-$cl' style='display: block;'>&nbsp;</span></td>";
                                $status = $first_pro['data']['status'];
                                $tr_str .= "<td>$status</td>";


                                $tr_str .= "</tr>";

                                //now process the rest of the products
                                foreach($depot['products'] as $v_arr){
                                    $tr_str .= "</tr><td $style_td_1>".$v_arr['name']."</td>";
                                    //Stock
                                    $stock_tl = $controller->formatNumber($v_arr['data']['total_stock'],'money',0);
                                    $tr_str .= "<td>$stock_tl</td>";
                                    //Orders
                                    $orders_tl = $controller->formatNumber($v_arr['data']['orders_qty'],'money',0);
                                    $tr_str .= "<td>$orders_tl</td>";
                                    //Variance
                                    $variance_tl = $controller->formatNumber($v_arr['data']['variance'],'money',0);
                                    $tr_str .= "<td>$variance_tl</td>";
                                    //Action
                                   /* $action = $v_arr['data']['action'];
                                    $tr_str .= "<td>$action</td>";*/

                                    $color = $v_arr['data']['color'];
                                    if($color == 'red'){
                                        $cl = 'important';
                                    }
                                    elseif($color == 'success'){
                                        $cl = 'success';
                                    }
                                    $tr_str .= "<td><span class='label label-$cl' style='display: block;'>&nbsp;</span></td>";
                                    $status = $v_arr['data']['status'];
                                    $tr_str .= "<td>$status</td>";

                                    $tr_str .= "</tr>";
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

    <form id="print-export-form" method="post" action="<?php echo $this->Html->url(array('controller' => 'OmcStock', 'action' => 'print_export_daily_stock_variance')); ?>" target="PrintExportWindow">
        <input type="hidden" name="data_type" id="data_type" value="" />
        <input type="hidden" name="data_indicator" id="data_indicator" value="" />
    </form>

    <div class="dr"><span></span></div>
</div>

<!-- Le Script -->
<?php
    //echo $this->Html->script('scripts/omc_daily_stock_variance.js');
?>
