<div class="workplace">

    <div class="page-header">
        <h1>Product Distribution <small> Dashboard</small></h1>
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
                <h1>Distribute products to your customers</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/load_details')); ?>" />
<input type="hidden" id="table-editable-sub-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/save-sub')); ?>" />


<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'distribution/delete')); ?>" />

<script type="text/javascript">
    var region = <?php echo json_encode($regions_lists);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc_customer/truck_loading.js');
?>
