<?php
  // debug($bdc_depots);
?>
<script type="text/javascript">
    var bardata = <?php echo json_encode($g_data['data']['y-axis']);?>;
    var x_axis = <?php echo json_encode($g_data['data']['x-axis']);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;

    var customers = <?php echo json_encode($omc_customers_lists);?>;
    var bdc = <?php echo json_encode($bdc_list);?>;
    var depot = <?php echo json_encode($depot_lists);?>;
    var products = <?php echo json_encode($products_lists);?>;
    var bdclists = <?php echo json_encode($bdclists);?>;
    var order_filter = <?php echo json_encode($order_filter);?>;
    var bdc_depots_gbl = <?php echo json_encode($bdc_depots);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var volumes = <?php echo json_encode($volumes); ?>;
    var my_bdc_list_ids = <?php echo json_encode($my_bdc_list_ids); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Order Allocations <small> Dashboard</small></h1>
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
                <h1>Order Management Table</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success export-me-btn" type="button" id="" data-filter-elements="BDC:filter_bdc,Status:filter_status" data-url="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'export_orders')); ?>">Export </button>
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
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'order_allocation/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'order_allocation/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'order_allocation/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'order_allocation/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'order_allocation/delete')); ?>" />

<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>


<!-- Le Script -->
<?php
  echo $this->Html->script('scripts/omc/omc_order_allocation.js');
?>
