<div class="workplace">

    <div class="page-header">
        <h1>Staff Groups <small> </small></h1>
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
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'groups/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'groups/save')); ?>" />
<input type="hidden" id="load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'groups/load')); ?>" />
<input type="hidden" id="delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcAdmin', 'action' => 'groups/delete')); ?>" />


<!-- Le Script -->
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
    var my_depots = <?php echo json_encode($my_depots); ?>;

</script>
<?php
echo $this->Html->script('scripts/omc_groups.js');
?>
