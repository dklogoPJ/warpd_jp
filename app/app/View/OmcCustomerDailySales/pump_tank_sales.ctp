<div class="workplace">

    <div class="page-header">
        <h1 style="font-size: 30px;">Pump vs. Tank Sales <small> Dashboard</small></h1>
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
                <h1>Pump vs. Tank Sales Table</h1>
            </div>
            <table id="flex" style="display:none;"></table>

        </div>
    </div>

    <div class="dr"><span></span></div>

    <!--<div class="row-fluid" id="export-form">
        <div class="span3">
            <div class="head clearfix">
                <div class="isw-text_document"></div>
                <h1>Export Data</h1>
            </div>
            <?php /*echo $this->Form->create('Export', array('id' => 'form-export', 'target'=>'ExportWindow' ,'inputDefaults' => array('label' => false,'div' => false)));*/?>
            <div class="block-fluid">
                <div class="row-form clearfix" style="border-top-width: 0px; padding: 5px 16px;">
                    <div class="span5">Start Date:</div>
                    <div class="span5">
                        <?php /*echo $this->Form->input('export_startdt', array('id'=>'export_startdt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); */?>

                    </div>
                    <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                </div>

                <div class="row-form clearfix" style="border-top-width: 0px; padding: 3px 16px;">
                    <div class="span5">End Date:</div>
                    <div class="span5">
                        <?php /*echo $this->Form->input('export_enddt', array('id'=>'export_enddt', 'class' => 'span2 date-masking validate[required]','default'=>date('d-m-Y'),'placeholder'=>'dd-mm-yyyy', 'div' => false, 'label' => false,)); */?>

                    </div>
                    <span>Example: 01-12-2012 (dd-mm-yyyy)</span>
                </div>

                <div class="footer tal">
                    <button class="btn" type="button" id="export-btn">Export</button>
                    <?php /*echo $this->Form->input('export_type', array('type'=>'hidden','id'=>'export_type', 'value'=>$authUser['user_type'])); */?>
                    <?php /*echo $this->Form->input('export_url', array('type'=>'hidden','id'=>'export_url', 'value'=> $this->Html->url(array('controller' => 'OmcDealerOrders', 'action' => 'export_orders')))); */?>
                    <?php /*echo $this->Form->input('action', array('type'=>'hidden','id'=>'action', 'value'=> 'export_me')); */?>
                </div>
                <?php /*echo $this->Form->end();*/?>
            </div>
        </div>
    </div>-->

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="table-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'pump_tank_sales/get')); ?>" />
<input type="hidden" id="table-editable-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'pump_tank_sales/save')); ?>" />
<input type="hidden" id="table-details-url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'pump_tank_sales/load_details')); ?>" />

<input type="hidden" id="grid_load_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'pump_tank_sales/load')); ?>" />
<input type="hidden" id="grid_delete_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'pump_tank_sales/delete')); ?>" />


<!-- This URL will be used by Ajax upload -->
<input type="hidden" id="get_attachments_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'get_attachments')); ?>" />
<input type="hidden" id="ajax_upload_url" value="<?php echo $this->Html->url(array('controller' => 'OmcCustomerDailySales', 'action' => 'attach_files')); ?>" />
<?php echo $this->element('ajax_upload');?>

<script type="text/javascript">
    var products = <?php echo json_encode($products_lists);?>;
    var order_filter = <?php echo json_encode($order_filter);?>;
    var permissions = <?php echo json_encode($permissions); ?>;
    var volumes = <?php echo json_encode($volumes); ?>;
   
</script>

<!-- Le Script -->
<?php
echo $this->Html->script('scripts/omc_customer/pump_tank_sales.js');
?>
