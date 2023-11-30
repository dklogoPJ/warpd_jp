<!-- Le Css -->
<?php

?>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo "Manage BDC Connections"; ?> <small></small></h1>
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
                    Check or select the BDCs that you want to connect with. This will allow the BDCs to respond directly to you.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Connect BDCs</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('MyBDCs', array('id' => 'form','class'=>'form-horizontal'));?>
                <?php
                $count = 0;
                foreach($all_bdcs as $arr){
                    $_id = $arr['Bdc']['id'];
                    $_name = $arr['Bdc']['name'];
                    $checked = '';
                    if(in_array($_id,$my_bdc_list_ids)){
                        $checked = "checked";
                    }
                    ?>
                    <div class="row-form clearfix" style="border-top-width: 0px;  padding: 9px 16px;">
                        <div class="span4"><?php echo $_name;?>:</div>
                        <div class="span8">
                            <?php echo $this->Form->input("$count.my_bdc_id", array('type'=>'checkbox','id' => $count.'my_bdc_id', 'value' =>$_id, $checked,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                            &nbsp; Check to connect with this BDC
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
                        <button type="submit" class="btn" id='update-btn'>Update BDCs</button>
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
