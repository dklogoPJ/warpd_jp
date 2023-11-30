<!-- Le Css -->
<?php

?>

<script type="text/javascript">
    var permissions = [
        {id:'1',name:'Read'},
        {id:'2',name:'Read/Create'},
        {id:'3',name:'Read/Create/Modify'}
    ];

    var user_types = [
        {id:'Admin',name:'Admin'},
        {id:'Operations',name:'Operations'}
    ];

    var account_active = [
        {id:'Active',name:'Active'},
        {id:'Disabled',name:'Disabled'}
    ];

</script>

<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Users</small></h1>
    </div>

    <div class="row-fluid">
        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the New to add new row.
                    Click on Edit to begin editing on a selected row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
                    Click on Delete to delete selected rows.
                </div>
            </div>

            <table id="flex" style="display:none;"></table>

            <div id="reset_modal" class="modal hide fade">
                <form class="form form-horizontal" id="reset_form" method="post" action="" enctype="" style="margin: 0px;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4>Reset User Password</h4>
                    </div>
                    <div class="modal-body">

                        <div class="control-group">
                            <label class="control-label" for="password">* New Password:</label>
                            <div class="controls">
                                <input id="password" name="password" type="password" value="" required="" class="input input-large" />
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id" value="" />

                    </div>
                    <div class="modal-footer">
                        <button type="submit" href="<?php echo $this->Html->url(array('action' => 'index/reset_password')); ?>" id="reset_pass_btn" class="btn btn-primary">Reset Password</button>
                        <a href="#reset_modal" class="btn" data-toggle="modal">Close</a>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'index/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'index/save')); ?>" />
<input type="hidden" id="load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'index/load')); ?>" />
<input type="hidden" id="delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcAdmin', 'action' => 'index/delete')); ?>" />


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/bdc_users.js');
?>
