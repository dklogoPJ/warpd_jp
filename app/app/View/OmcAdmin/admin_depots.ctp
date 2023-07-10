<!-- Le Css -->
<?php

?>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Depots</small></h1>
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
                    Check or select the depots this company will use.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Company Depots</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('MyDepots', array('id' => 'form','class'=>'form-horizontal'));?>
                <?php
                $count = 0;
                foreach($all_depots as $arr){
                    $depot_id = $arr['id'];
                    $depot_name = $arr['name'];
                    $checked = '';
                    if(in_array($depot_id,$my_depots)){
                        $checked = "checked";
                    }
                    ?>
                    <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                        <div class="span4"><?php echo $depot_name?>:</div>
                        <div class="span8">
                            <?php echo $this->Form->input("$count.my_depot_id", array('type'=>'checkbox','id' => $count.'my_depot_id', 'value' =>$depot_id, $checked,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                        </div>
                    </div>
                    <?php
                    $count++;
                }
                ?>
                <div class="footer tal">
                    <?php
                    if(in_array('E',$permissions)){
                        ?>
                        <button type="submit" class="btn" id='update-btn'>Update Depots</button>
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
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots/save')); ?>" />
<input type="hidden" id="load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots/load')); ?>" />
<input type="hidden" id="delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'admin_depots/delete')); ?>" />

<!-- Le Script -->
<?php
   // echo $this->Html->script('scripts/bdc_depots.js');
?>
