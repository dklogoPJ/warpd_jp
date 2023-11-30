<!-- Le Css -->
<?php

?>
<script type="text/javascript">

    var omclist_arr = <?php echo json_encode($omclist_arr)?>;

    var account_active = [
        {id:'Active',name:'Active'},
        {id:'Disabled',name:'Disabled'}
    ];

</script>
<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Omcs</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the New to add new row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
                </div>
            </div>

            <table id="flex" style="display:none;"></table>

           <!-- <table id="dg" title="" style="height: 400px;" toolbar="#toolbar" pagination="true" idField="id" rownumbers="true" fitColumns="true" singleSelect="true">
                <thead>
                    <tr>
                        <th field="name"width="50" sortable="false"> Name</th>
                    </tr>
                </thead>
            </table>
            <div class="row-fluid" id="toolbar">
                <div class="span12">
                    <div class="btn-group">
                        <a href="javascript: void(0);" id="new_btn" class="btn" iconCls="icon-add" plain="true"><i class="icon-plus"></i> New</a>
                        <!--<a href="javascript: void(0);" id="delete_btn" class="btn" iconCls="icon-remove" plain="true"><i class="icon-trash"></i> Delete</a>
                    </div>
                </div>
            </div>-->
        </div>
    </div>

    <div class="modals">
        <div id="creat_omc_modal" class="modal hide fade">
            <?php echo $this->Form->create('BdcOmc', array('id' => 'form-bdc-omc','class'=>'form-horizontal'));?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4>Omc Account Info</h4>
                </div>
                <div class="modal-body">

                    <div class="control-group">
                        <label class="control-label">Select Omc</label>
                        <div class="controls">
                            <?php echo $this->Form->input('omc_id', array('id'=>'omc_id' , 'options'=>$omclist_arr, 'div' => false, 'label' => false)); ?>
                        </div>
                    </div>
                    <?php echo $this->Form->input('bdc_id', array('type'=>'hidden','id'=>'bdc_id' , 'value'=>$company_profile['id'], 'div' => false, 'label' => false)); ?>
                    <?php echo $this->Form->input('id', array('type'=>'hidden','id'=>'id' , 'value'=>0, 'div' => false, 'label' => false)); ?>

                </div>
                <div class="modal-footer">
                    <button type="button" href="<?php echo $this->Html->url(array('controller'=>'BdcAdmin','action' => 'admin_omcs/save')); ?>" id="save_account_btn" class="btn btn-primary"> Save Account</button>
                    <a href="#creat_omc_modal" class="btn" data-toggle="modal">Close</a>
                </div>
            <?php echo $this->Form->end();?>
        </div>

    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_omcs/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_omcs/save')); ?>" />
<input type="hidden" id="load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_omcs/load')); ?>" />
<input type="hidden" id="delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'admin_omcs/delete')); ?>" />

<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/bdc_omcs.js');
?>
