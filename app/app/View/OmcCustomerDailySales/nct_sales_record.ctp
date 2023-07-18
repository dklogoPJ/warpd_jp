<?php

?>
<script type="text/javascript">
    var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">NCT Sales Record <small> Dashboard</small></h1>
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
                </div>
            </div>

            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>NCT Sales Record Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'nct_sales_record/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'nct_sales_record/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'nct_sales_record/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'nct_sales_record/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'nct_sales_record/delete')); ?>" />
<input type="hidden" id="export_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'export_truck')); ?>" />



<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc_customer/nct_sales_record.js');
?>