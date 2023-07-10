<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">WARP D Simulator <small></small></h1>
    </div>
    <?php

    $update_options = array(
        'full_stock_update'=>'Full Stock Update',
        'random_stock_update'=>'Random Stock Update',
        'no_stock_update'=>'No Stock Update'
    );

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
                <div class="isw-brush"></div>
                <h1>Data Filter Options</h1>
            </div>
            <?php echo $this->Form->create('Query', array('id' => 'form-query', 'inputDefaults' => array('label' => false,'div' => false)));?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span3" >Next Simulation Date:</div>
                    <div class="span9">
                        <?php echo '<b>'.$next_date.'</b>'; ?>
                        <?php /*echo $this->Form->input('next_dt', array('id'=>'next_dt', 'class' => 'span2 date-masking validate[required] datepicker','default'=>$next_date,'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false)); */?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span3">Enable Order Cancellation:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('cancel_order', array('type'=>'checkbox','id'=>'cancel_order', 'class' => '', 'div' => false, 'label' => false,)); ?>
                        Note: Order Cancellation will be applied randomly.
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span3">Bdc Stock Update:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('bdc_stock_update', array('id'=>'bdc_stock_update', 'class' => 'span3', 'options'=>$update_options,'div' => false, 'label' => false,)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <button class="btn" type="submit" id="query-btn">Simulate </button>
                </div>
                <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="row-fluid">
        <div class="span12">

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>
<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/simulator.js');
?>