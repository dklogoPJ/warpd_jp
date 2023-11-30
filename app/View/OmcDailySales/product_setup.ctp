<script type="text/javascript">
   var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Setup Product And Services <small> </small></h1>
    </div>

    <div class="row-fluid">

        <div class="span12">

            <div class="demo-info" style="margin-bottom:10px">
                <div class="demo-tip icon-tip"></div>
                <div>
                    Click on a row to select it, click again to deselect it.
                    Click the left Plus icon to expand the row, click again to reduce the row.
                    Click the New to add new row.
                    Click on Edit to begin editing on a selected row.
                    Click Save to save the row.
                    Click on Cancel to quit changes to a row.
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Customize your products and Services</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/load_details')); ?>" />
<input type="hidden" id="table-editable-sub-url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/save-sub')); ?>" />


<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcDailySales', 'action' => 'product_setup/delete')); ?>" />


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/sales_product_setup.js');
?>
