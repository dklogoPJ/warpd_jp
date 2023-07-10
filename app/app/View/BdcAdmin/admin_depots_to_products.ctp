<!-- Le Css -->
<?php

?>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Depots and Products</small></h1>
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

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Check or select the product at each depots.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Company Depots and Product</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('MyDepotToProduct', array('id' => 'form','class'=>'form-horizontal'));?>
                <?php
                foreach($my_depots as $arr){
                    $depot_id = $arr['id'];
                    $depot_name = $arr['name'];
                    $products_checked = isset($depots_to_products[$depot_id])?$depots_to_products[$depot_id]:array();
                    ?>
                    <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                        <div class="span3"><?php echo $depot_name; ?>:</div>
                        <div class="span9">
                        <?php
                            $count = 0;
                            foreach($my_products as $a){
                                $product_id = $a['id'];
                                $product_name = $a['name'];
                                $short_name = $a['short_name'];
                                $checked = '';
                                if(in_array($product_id,$products_checked)){
                                    $checked = "checked";
                                }
                                echo $product_name.' '.$this->Form->input("$depot_id.$count.my_pro_id", array('type'=>'checkbox','id' => $depot_id.$count.'my_pro_id', 'value' =>$product_id, $checked,'class' => 'tank_class','div' => false, 'label' => false));
                                $count++;
                            }
                        ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="footer tal">
                    <?php
                    if(in_array('E',$permissions)){
                        ?>
                        <button type="submit" class="btn" id='update-btn'>Update</button>
                    <?php
                    }
                    ?>

                    <?php //echo $this->Form->input('id', array('type'=>'hidden','id'=>'id', 'value'=>$company_profile['id'], 'div' => false, 'label' => false));?>
                </div>
                <?php echo $this->Form->end();?>
            </div>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots/save')); ?>" />
<input type="hidden" id="load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots/load')); ?>" />
<input type="hidden" id="delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_depots/delete')); ?>" />

<!-- Le Script -->
<?php
   // echo $this->Html->script('scripts/bdc_depots.js');
?>
