<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Shortages <small> Dashboard</small></h1>
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
                <h1>Product Shortages Table</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success export-me-btn" type="button" id="" data-url="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'export_shortages')); ?>">Export </button>
                        <?php
                        }
                        ?>
                    </li>
                </ul>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'shortages/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'shortages/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'shortages/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'shortages/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerOrders', 'action' => 'shortages/delete')); ?>" />

<script type="text/javascript">
    var products = <?php echo json_encode($products_lists);?>;
    var order_filter = <?php echo json_encode($order_filter);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var volumes = <?php echo json_encode($volumes); ?>;
</script>
<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc_customer/shortages.js');
?>
