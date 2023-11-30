<?php

?>
<style type="text/css">
    #filter_omc{
        width: 150px;
    }
</style>
<script type="text/javascript">
    var bardata = <?php echo json_encode($g_data['data']['y-axis']);?>;
    var x_axis = <?php echo json_encode($g_data['data']['x-axis']);?>;
    var graph_title = <?php echo json_encode($graph_title);?>;
    var fna_feedback = <?php echo json_encode($fna_feedback);?>;
    var user_type = <?php echo json_encode($user_type);?>;
    var omc_lists = <?php echo json_encode($omc_lists);?>
   // var order_filter = Array;
    var order_filter = <?php echo json_encode($order_filter);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var volumes = <?php echo json_encode($volumes); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Customer Ordering Finance Input<small> Dashboard</small></h1>
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
                <h1>Customer Order Mgt Table</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                            <button class="btn btn-success export-me-btn" type="button" id="" data-filter-elements="OMC:filter_omc,Status:filter_status" data-url="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'export_orders')); ?>">Export </button>
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
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders_finance/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders_finance/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders_finance/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders_finance/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'orders_finance/delete')); ?>" />

<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'BdcOrders', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/bdc/bdc_order_finance.js');
?>
