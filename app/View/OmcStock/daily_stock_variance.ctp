<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1>Daily Customer Stock Variance <small></small></h1>
    </div>

   <!-- <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php /*echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));*/?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span2" style="width: 80px;">Month:</div>
                    <div class="span2">
                        <?php /*echo $this->Form->input('month', array('id'=>'month', 'class' => '','default'=>$month,'options'=>$month_lists, 'div' => false, 'label' => false,)); */?>
                    </div>

                    <div class="span2" style="width: 80px;">Year:</div>
                    <div class="span2">
                        <?php /*echo $this->Form->input('year', array('id'=>'year', 'class' => 'span2 date-masking validate[required]','default'=>$year,'placeholder'=>'yyyy', 'div' => false, 'label' => false,)); */?>
                    </div>

                    <div class="span2" style="width: 80px;">Customer:</div>
                    <div class="span2">
                        <?php /*echo $this->Form->input('customer', array('id'=>'customer', 'class' => '','default'=>$customer,'options'=>$customer_lists, 'div' => false, 'label' => false,)); */?>
                    </div>
                </div>

                <div class="footer tal">
                    <button class="btn" type="button" id="query-btn">Get Stock History </button>
                </div>
                <?php /*echo $this->Form->end();*/?>
            </div>
         </div>
    </div>-->

    <div class="row-fluid">
        <div class="span8">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span3" style="">Stock Action Indicator:</div>
                    <div class="span4">
                        <?php echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All Orders','red'=>'Emergency Orders Required','yellow_red'=>'New Orders Required'), 'div' => false, 'label' => false,)); ?>
                    </div>

                    <!--<div class="span2" style="width: 80px;">&nbsp;</div>-->
                    <div class="span4">
                       <!-- --><?php /*echo $this->Form->input('indicator', array('id'=>'indicator', 'class' => '','default'=>$indicator,'options'=>array('all'=>'All','red'=>'Red','yellow_red'=>'Yellow & Red'), 'div' => false, 'label' => false,)); */?>
                        <button class="btn" type="submit" id="query-btn">Get Stock Variance </button>
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
                <h1><?php echo $table_title ?> (ltrs)</h1>
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
                        'color'=>'Stocking Action Indicator',
                        'status'=>'Stocking Action Required'
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
                                $tr_str .= "<td>".$controller->formatNumber($v_arr['current_stock_level'],'money',0)."</td>";
                                $tr_str .= "<td>".$controller->formatNumber($v_arr['min_stock_level'],'money',0)."</td>";
                                $tr_str .= "<td>".$controller->formatNumber($v_arr['variance'],'money',0)."</td>";
                                $cl = 'success';
                                if($v_arr['color'] == 'red'){
                                    $cl = 'important';
                                }
                                elseif($v_arr['color'] == 'yellow'){
                                    $cl = 'warning';
                                }
                                $tr_str .= "<td><span class='label label-$cl' style='display: block;'>&nbsp;</span></td>";


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
    echo $this->Html->script('scripts/omc_daily_stock_variance.js');
?>
