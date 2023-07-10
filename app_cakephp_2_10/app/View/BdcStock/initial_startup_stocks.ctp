<div class="workplace">
    <?php //debug($authUser); ?>
    <div class="page-header">
        <h1>Products Initial Stock <small> Setup</small></h1>
    </div>

    <?php
    $error = false;
    if($this->Session->check('process_error')){
        if($this->Session->read('process_error') == 'yes'){
            $error = true;
        }
        $controller->Session->delete('process_error');
    }
    ?>
    <?php
    $flash_msg = $this->Session->read('Message');
    if(isset($flash_msg['flash'])){
        ?>
        <div class="row-fluid">
            <div class="span12">
                <?php
                if($error){
                    echo $this->Message->msg('Status',$this->Session->flash(),'error',true);
                }
                else{
                    echo $this->Message->msg('Status',$this->Session->flash(),'success',true);
                }
                ?>
            </div>
        </div>
    <?php
    }
    ?>


    <div class="row-fluid">
        <div class="span12">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Initial Stock Update Information</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('BdcInitialStockStartup', array('id' => 'form','class'=>'form-horizontal'));?>
                <?php
                $count = 0;
                foreach($depots_products as $depot_id=>$arr){
                    $depot_name = $arr['name'];
                    foreach($arr['products'] as $v_arr){
                        $product_id = $v_arr['id'];
                        $product_name = $v_arr['name'];
                        ?>
                        <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                            <div class="span5"><?php echo $depot_name.'-:- ('.$product_name.')';?>:</div>
                            <div class="span7">
                                <?php echo $this->Form->input("$count.depot_id", array('type'=>'hidden','id' => $count.'depot_id', 'value' =>$depot_id,'class' => '','div' => false, 'label' => false)); ?>
                                <?php echo $this->Form->input("$count.product_type_id", array('type'=>'hidden','id' => $count.'product_type_id', 'value' =>$product_id, 'class' => '','div' => false, 'label' => false)); ?>
                                <!-- --><?php /*echo $this->Form->input("$count.quantity_metric_ton", array('id' => $count.'quantity_metric_ton', 'class' => '','style'=>'width:45%', 'placeholder' => 'Initial quantity in metric tonne', 'div' => false, 'label' => false)); */?>
                                <?php echo $this->Form->input("$count.quantity_ltrs", array('id' => $count.'quantity_ltrs', 'class' => '','onkeypress'=>'return isNumberKey(event)','style'=>'width:45%', 'placeholder' => 'Initial quantity in litres', 'div' => false, 'label' => false)); ?> ltrs
                            </div>
                        </div>
                        <?php
                        $count++;
                    }
                }
                ?>
                <div class="footer tal">
                    <?php
                    if(in_array('A',$permissions) && !empty($depots_products)){
                        ?>
                        <button type="button" class="btn" id='update-btn'>Save Setup</button>
                    <?php
                    }
                    ?>
                    <?php /*echo $this->Form->input('id', array('type'=>'hidden','id'=>'id', 'value'=>$authUser['id'], 'div' => false, 'label' => false)); */?>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>

    <div class="row-fluid">
        <div class="span12">

            <div class="head clearfix">
                <div class="isw-grid"></div>
                <h1>Initial Stock Data</h1>
                <!--<ul class="buttons">
                    <li><a href="#" class="isw-print"></a></li>
                </ul>-->
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
                    <thead>
                    <tr>
                        <th>Date Initialized</th>
                        <th>Depot</th>
                        <th>Product</th>
                        <th>Initial Quantity (ltrs)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($grid_data as $data){
                        $n = $data['BdcInitialStockStartup'];
                        $d = $data['Depot'];
                        $p = $data['ProductType'];
                        ?>
                        <tr>
                            <td><?php echo $this->App->covertDate($n['created'],'mysql_flip'); ?></td>
                            <td><?php echo $d['name'] ;?></td>
                            <td><?php echo $p['name'] ;?></td>
                            <td><?php echo $this->App->formatNumber(preg_replace('/,/','',$n['quantity_ltrs']),'money',0) ;?></td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



</div>

<!-- Le Script -->
<script type="text/javascript">
   // var gbl_tanks_arr = <?php //echo json_encode($fin_tanks);?>;
</script>
<?php
     echo $this->Html->script('scripts/bdc_initial_stock_update.js');
?>
