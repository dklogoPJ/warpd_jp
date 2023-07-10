<div class="workplace">

    <div class="page-header">
        <h1>Access Control <small> </small></h1>
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
                   For each Staff Group, select the module (pages) that the group can access. Also set the permissions on each module to be bound to the group.
                </div>
            </div>

            <?php echo $this->Form->create('AccessControl', array('id' => 'form','class'=>'form-horizontal'));?>
                <div class="headInfo">
                    <strong>Staff Group : </strong>&nbsp;&nbsp;
                    <div class="input-append">
                         <?php echo $this->Form->input('group_id', array('id' => 'group_id', 'class' => '', 'options' => $group_options, 'default'=>$group,'div' => false, 'label' => false)); ?>
                        <?php
                        if(in_array('E',$permissions)){
                            ?>
                            <button class="btn" type="submit">Save Access Control Changes</button>
                        <?php
                        }
                        ?>

                    </div>
                    <div class="arrow_down"></div>
                </div>
                <div class="block stream">
                    <?php
                        $count = 0;
                        foreach($menu_data as $menu_group => $arr){
                    ?>
                            <div class="item clearfix">
                                <div class="info" style="margin: 5px;">
                                    <a class="name" href="javascript:void(0);"><?php echo $menu_group; ?></a>
                                    <div class="text friends">
                                        <ul>
                                            <?php
                                                foreach($arr as $menu){
                                                    if(isset($menu['sub'])){
                                                        foreach($menu['sub'] as $menu_sub){
                                                            $checked = '';
                                                            if(in_array($menu_sub['id'],$group_menu_ids)){
                                                                $checked = "checked";
                                                            }
                                                            $permission = isset($group_menu_data[$menu_sub['id']])?$group_menu_data[$menu_sub['id']]['permission']:'';
                                                        ?>
                                                            <li>
                                                                <?php echo $this->Form->input("AccessControl.d.$count.menu_id", array('type'=>'checkbox','id' => $count.'menu_id', 'value' =>$menu_sub['id'], $checked,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                <span><strong><?php echo $menu_sub['title']; ?>: </strong></span> <?php echo $menu_sub['description']; ?>
                                                                <?php
                                                                    if(!empty($menu_sub['permission_controls'])){
                                                                ?>
                                                                        |&nbsp;&nbsp;<span><strong> Permissions: </strong></span>
                                                                        <?php
                                                                        if(stristr($menu_sub['permission_controls'], 'A') !== FALSE) {
                                                                            $chk = '';
                                                                            if(stristr($permission,'A') !== false){
                                                                                $chk = "checked";
                                                                            }
                                                                        ?>
                                                                            Add <?php echo $this->Form->input("AccessControl.d.$count.add", array('type'=>'checkbox','id' => $count.'add', 'value' =>'A',$chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if(stristr($menu_sub['permission_controls'], 'E') !== FALSE) {
                                                                            $chk = '';
                                                                            if(stristr($permission,'E') !== false){
                                                                                $chk = "checked";
                                                                            }
                                                                        ?>
                                                                            Edit <?php echo $this->Form->input("AccessControl.d.$count.edit", array('type'=>'checkbox','id' => $count.'edit', 'value' =>'E',$chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if(stristr($menu_sub['permission_controls'], 'PX') !== FALSE) {
                                                                            $chk = '';
                                                                            if(stristr($permission,'PX') !== false){
                                                                                $chk = "checked";
                                                                            }
                                                                        ?>
                                                                            Print/Export <?php echo $this->Form->input("AccessControl.d.$count.print_export", array('type'=>'checkbox','id' => $count.'print_export', 'value' =>'PX',$chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                        <?php
                                                                        if(stristr($menu_sub['permission_controls'], 'D') !== FALSE) {
                                                                            $chk = '';
                                                                            if(stristr($permission,'D') !== false){
                                                                                $chk = "checked";
                                                                            }
                                                                            ?>
                                                                            Delete <?php echo $this->Form->input("AccessControl.d.$count.delete", array('type'=>'checkbox','id' => $count.'delete', 'value' =>'D',$chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                        <?php
                                                                        }
                                                                        ?>
                                                                <?php
                                                                    }
                                                                ?>
                                                                <?php $count++; ?>
                                                            </li>
                                                        <?php
                                                        }
                                                    }
                                                    else{
                                                        $checked = '';
                                                        if(in_array($menu['id'],$group_menu_ids)){
                                                            $checked = "checked";
                                                        }

                                                        $permission = isset($group_menu_data[$menu['id']])?$group_menu_data[$menu['id']]['permission']:'';
                                                ?>
                                                        <li>
                                                            <?php echo $this->Form->input("AccessControl.d.$count.menu_id", array('type'=>'checkbox','id' => $count.'menu_id', 'value' =>$menu['id'], $checked,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                            <span><strong><?php echo $menu['title']; ?>: </strong></span> <?php echo $menu['description']; ?>
                                                            <?php
                                                            if(!empty($menu['permission_controls'])){
                                                                ?>
                                                                |&nbsp;&nbsp;<span><strong> Permissions: </strong></span>
                                                                <?php
                                                                if(stristr($menu['permission_controls'], 'A') !== FALSE) {
                                                                    $chk = '';
                                                                    if(stristr($permission,'A') !== false){
                                                                        $chk = "checked";
                                                                    }
                                                                ?>
                                                                    Add <?php echo $this->Form->input("AccessControl.d.$count.add", array('type'=>'checkbox','id' => $count.'add','value' =>'A', $chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                <?php
                                                                }
                                                                ?>
                                                                <?php
                                                                if(stristr($menu['permission_controls'], 'E') !== FALSE) {
                                                                    $chk = '';
                                                                    if(stristr($permission,'E') !== false){
                                                                        $chk = "checked";
                                                                    }
                                                                ?>
                                                                    Edit <?php echo $this->Form->input("AccessControl.d.$count.edit", array('type'=>'checkbox','id' => $count.'edit', 'value' =>'E', $chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                <?php
                                                                }
                                                                ?>
                                                                <?php
                                                                if(stristr($menu['permission_controls'], 'PX') !== FALSE) {
                                                                    $chk = '';
                                                                    if(stristr($permission,'PX') !== false){
                                                                        $chk = "checked";
                                                                    }
                                                                ?>
                                                                    Print/Export <?php echo $this->Form->input("AccessControl.d.$count.print_export", array('type'=>'checkbox','id' => $count.'print_export', 'value' =>'PX', $chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                <?php
                                                                }
                                                                ?>
                                                                <?php
                                                                if(stristr($menu['permission_controls'], 'D') !== FALSE) {
                                                                    $chk = '';
                                                                    if(stristr($permission,'D') !== false){
                                                                        $chk = "checked";
                                                                    }
                                                                    ?>
                                                                    Delete <?php echo $this->Form->input("AccessControl.d.$count.delete", array('type'=>'checkbox','id' => $count.'delete', 'value' =>'D', $chk,'class' => 'tank_class','div' => false, 'label' => false)); ?>
                                                                <?php
                                                                }
                                                                ?>
                                                            <?php
                                                            }
                                                            ?>
                                                            <?php $count++; ?>
                                                        </li>
                                                <?php
                                                    }
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    ?>
                </div>
            <?php echo $this->Form->end();?>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="access_control_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'access_control')); ?>" />

<!-- Le Script -->
<script type="text/javascript">
    $(document).ready(function () {
        $("#group_id").change(function(){
            var id = $(this).val();
            var url =  $("#access_control_url").val()+'/'+id;
            window.location = url;
        });
    });
</script>
<?php
//echo $this->Html->script('scripts/bdc_groups.js');
?>
