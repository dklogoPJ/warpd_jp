<?php

?>
<script type="text/javascript">
    var waybill_filter = <?php echo json_encode($waybill_filter);?>;
    var depot_filter = <?php echo json_encode($depot_filter);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
</script>

<div class="workplace">

    <div class="page-header">
        <h1>Way Bill <small> Dashboard</small></h1>
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
                <h1>Waybill Table</h1>
                <ul class="buttons">
                    <li>
                        <?php
                        if(in_array('PX',$permissions)){
                            ?>
                           <!-- <button class="btn btn-success export-me-btn" type="button" id="" data-filter-elements="Status:filter_status" data-url="<?php /*echo $this->Html->url(array('controller' => 'Depot', 'action' => 'export_loading')); */?>">Export </button>-->
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
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'waybills/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'waybills/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'waybills/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'waybills/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'waybills/delete')); ?>" />
<input type="hidden" id="print_waybill_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'print_waybill')); ?>" />

<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'OmcOrders', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>


<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc/waybill.js');
?>
