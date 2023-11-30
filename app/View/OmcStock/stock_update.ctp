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
       <!-- <div class="span6">
            <div class="profile clearfix">
                <div class="image">
                    <?php /*echo $this->Html->image("sitepages/$company_key/small-bg.jpg", array('alt' =>'Bg','class'=>'img-polaroid')); */?>
                </div>
                <div class="user clearfix">
                    <div class="avatar">
                        <?php /*echo $this->Html->image('user_big.png', array('alt' =>'User name','class'=>'img-polaroid')); */?>
                    </div>
                    <h2><?php /*echo $authUser['fname'].' '.$authUser['lname']; */?></h2>
                    <div class="actions">

                    </div>
                </div>
                <div class="info">
                    <p><span class="icon-globe"></span> <span class="title">Telephone:</span>  <?php /*echo $authUser['telephone']; */?></p>
                    <p><span class="icon-gift"></span> <span class="title">Date of Registration:</span> <?php /*echo date("d M Y, g:ia", strtotime($authUser['created'])); */?></p>
                </div>

            </div>

        </div>-->

        <div class="span6">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Stock Update Information</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('OmcCustomerStock', array('id' => 'form','class'=>'form-horizontal'));?>
                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span4">Tank Name:</div>
                    <div class="span8">
                        <?php echo $this->Form->input('omc_customer_tank_id', array('id' => 'omc_customer_tank_id', 'class' => '', 'options' => $opt_tanks, 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span4">Current Quantity:</div>
                    <div class="span8">
                        <?php echo $this->Form->input('quantity', array('id' => 'quantity', 'class' => 'validate[required onlyNumber]', 'value' => '', 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <button type="button" class="btn" id='update-btn'>Update Stock</button>
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
</script>
<?php
    echo $this->Html->script('scripts/dealers/stock_update.js');
?>
