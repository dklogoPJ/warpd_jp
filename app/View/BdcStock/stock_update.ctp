<div class="workplace">
<?php //debug($authUser); ?>
    <div class="page-header">
        <h1>Product Stock <small> Update</small></h1>
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

        <div class="span6">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Stock Update Information</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('BdcStockUpdate', array('id' => 'form','class'=>'form-horizontal'));?>
                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Supplier:</div>
                    <div class="span6">
                        <?php echo $this->Form->input('supplier', array('id' => 'supplier', 'class' => '', 'required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Ship Name:</div>
                    <div class="span6">
                        <?php echo $this->Form->input('ship_name', array('id' => 'ship_name', 'class' => '', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Delivery Date:</div>
                    <div class="span6">
                        <?php echo $this->Form->input('delivery_date', array('type' => 'text','id' => 'delivery_date', 'readonly','class' => '', 'value' => date('Y-m-d'),'style'=>'width:80%;','required', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Depot:</div>
                    <div class="span6">
                        <?php echo $this->Form->input('depot_id', array('id' => 'depot_id', 'class' => '','options'=>$depot_options, 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Product:</div>
                    <div class="span6">
                        <?php echo $this->Form->input('product_type_id', array('id' => 'product_type_id', 'class' => '','options'=>$product_options, 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Quantity Delivered (in metric ton):</div>
                    <div class="span6">
                        <?php echo $this->Form->input('quantity_metric_ton', array('id' => 'quantity_metric_ton', 'class' => '','onkeypress'=>'return isNumberKey(event)', 'value' => '', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>
                <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                    <div class="span6">Quantity Delivered (in litres):</div>
                    <div class="span6">
                        <?php echo $this->Form->input('quantity_ltrs', array('id' => 'quantity_ltrs', 'class' => '','onkeypress'=>'return isNumberKey(event)','required', 'value' => '', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <?php
                    if(in_array('A',$permissions)){
                        ?>
                        <button type="submit" class="btn" id='update-btn'>Update Stock</button>
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
</div>

<!-- Le Script -->
<script type="text/javascript">
    var gbl_depots_to_products = <?php echo json_encode($depots_to_products);?>;
    var depots = <?php echo json_encode($depot_options);?>;
    var products = <?php echo json_encode($product_options);?>;
</script>
<?php
    echo $this->Html->script('scripts/stock_update.js');
?>
