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
                <h1>Daily Stock Update Information</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('OmcCustomerStock', array('id' => 'form','class'=>'form-horizontal'));?>
                <?php
                    $count = 0;
                    foreach($opt_tanks as $key=>$value){
                ?>
                    <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                        <div class="span4"><?php echo $value;?>:</div>
                        <div class="span8">
                            <?php echo $this->Form->input("$count.omc_customer_tank_id", array('type'=>'hidden','id' => $count.'omc_customer_tank_id', 'value' =>$key, 'data-text' =>$value,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                            <?php echo $this->Form->input("$count.quantity", array('id' => $count.'quantity', 'class' => '','style'=>'width:70%', 'onkeypress'=>'return isNumberKey(event)','placeholder' => 'Current Stock Level. eg 500', 'div' => false, 'label' => false)); ?> ltrs
                        </div>
                    </div>
                <?php
                    $count++;
                    }
                ?>
                <div class="footer tal">
                    <?php
                    if(in_array('A',$permissions)){
                        ?>
                        <button type="button" class="btn" id='update-btn'>Update Stock</button>
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
    var gbl_tanks_arr = <?php echo json_encode($fin_tanks);?>;
    //console.log(gbl_tanks_arr);
</script>
<?php
    echo $this->Html->script('scripts/omc_customer/stock_update.js');
?>
